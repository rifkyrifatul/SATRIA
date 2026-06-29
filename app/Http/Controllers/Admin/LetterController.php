<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ApproveLetterRequest;
use App\Http\Requests\Admin\RequestRevisionRequest;
use App\Models\Letter;
use App\Models\LetterLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Mail\LetterApprovedMail;
use App\Mail\LetterRejectedMail;

class LetterController extends Controller
{
    /**
     * Disk private — identik dengan Staff\LetterController.
     * File surat hanya boleh diakses via controller, tidak via URL.
     */
    private const STORAGE_DISK = 'private';
    private const STORAGE_DIR  = 'letters';

    // =========================================================================
    // INDEX — Daftar semua surat (full visibility untuk admin)
    // =========================================================================

    /**
     * GET /admin/letters
     *
     * Tampilkan seluruh surat dari semua staff, bisa difilter by status.
     * Admin memiliki visibilitas penuh terhadap semua surat di sistem.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $isSuperAdmin = $user->isSuperAdmin();
        $isAdmin3 = $user->admin_level === 'admin_3';

        $visibilityFilter = function($query) use ($isSuperAdmin, $isAdmin3) {
            if (!$isSuperAdmin && !$isAdmin3) {
                $query->whereHas('category', function($q) {
                    $q->where('review_type', '!=', 'langsung_admin_3')->orWhereNull('review_type');
                });
            }
        };

        $query = Letter::with(['category', 'creator.division', 'updater'])
            ->where(function ($q) {
                $q->where('status', '!=', Letter::STATUS_APPROVED)
                  ->orWhere(function ($q2) {
                      $q2->where('status', Letter::STATUS_APPROVED)
                         ->where('created_at', '>=', now()->subDays(7));
                  });
            })
            ->where($visibilityFilter)
            ->latest();

        // Filter opsional berdasarkan status
        if ($request->filled('status')) {
            if ($request->status === 'pending') {
                $query->whereIn('status', [
                    Letter::STATUS_PENDING_ADMIN_1,
                    Letter::STATUS_PENDING_ADMIN_2,
                    Letter::STATUS_PENDING_ADMIN_3,
                    Letter::STATUS_PENDING_KABAGUM,
                    Letter::STATUS_PENDING_KASEK,
                ]);
            } elseif (in_array($request->status, Letter::STATUSES, strict: true)) {
                $query->where('status', $request->status);
            }
        }

        // Filter opsional berdasarkan keyword (judul / nomor surat)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('letter_number', 'like', "%{$search}%");
            });
        }

        $letters = $query->paginate(15)->withQueryString();

        $statusCounts = [
            'all'      => Letter::where(function ($q) {
                $q->where('status', '!=', Letter::STATUS_APPROVED)
                  ->orWhere(function ($q2) {
                      $q2->where('status', Letter::STATUS_APPROVED)
                         ->where('created_at', '>=', now()->subDays(7));
                  });
            })->where($visibilityFilter)->count(),
            'pending'  => Letter::whereIn('status', [
                Letter::STATUS_PENDING_ADMIN_1,
                Letter::STATUS_PENDING_ADMIN_2,
                Letter::STATUS_PENDING_ADMIN_3,
                Letter::STATUS_PENDING_KABAGUM,
                Letter::STATUS_PENDING_KASEK,
            ])->where($visibilityFilter)->count(),
            'revision' => Letter::where('status', Letter::STATUS_REVISION)->where($visibilityFilter)->count(),
            'approved' => Letter::where('status', Letter::STATUS_APPROVED)
                ->where('created_at', '>=', now()->subDays(7))
                ->where($visibilityFilter)
                ->count(),
        ];

        return view('admin.letters.index', compact('letters', 'statusCounts'));
    }

    // =========================================================================
    // SHOW — Detail surat + seluruh riwayat log
    // =========================================================================

    /**
     * GET /admin/letters/{letter}
     *
     * Tampilkan detail surat beserta riwayat log lengkap.
     * Admin bisa melihat surat dari staff manapun.
     */
    public function show(Request $request, Letter $letter): View
    {
        $this->authorizeVisibility($request->user(), $letter);
        $letter->load(['logs.user', 'creator', 'updater']);

        return view('admin.letters.show', compact('letter'));
    }

    // =========================================================================
    // DOWNLOAD — Unduh file surat secara aman dengan verifikasi role
    // =========================================================================

    /**
     * GET /admin/letters/{letter}/download
     *
     * Layani download file surat dari private storage.
     *
     * Alur keamanan:
     * 1. Middleware 'role:admin' memastikan hanya admin yang sampai ke sini
     * 2. Controller memverifikasi file benar-benar ada di storage
     * 3. File distream langsung dari storage ke client — tidak melalui URL publik
     *
     * Header 'Content-Disposition: attachment' memaksa browser mendownload
     * bukan menampilkan file di tab baru.
     */
    public function download(Request $request, Letter $letter): StreamedResponse
    {
        $this->authorizeVisibility($request->user(), $letter);

        // --- FILE VERSIONING ---
        // Jika request meminta attachment spesifik
        $attachmentId = $request->query('attachment_id');
        if ($attachmentId) {
            $attachment = $letter->attachments()->find($attachmentId);
            abort_unless($attachment, 404, 'File riwayat tidak ditemukan.');
            
            abort_unless(
                Storage::disk(self::STORAGE_DISK)->exists($attachment->file_path),
                404,
                'File riwayat tidak ditemukan di server.'
            );
            
            $downloadName = \Illuminate\Support\Str::slug($letter->title)
                . '_V' . $attachment->id . '_' . $attachment->created_at->format('Ymd')
                . '.' . pathinfo($attachment->file_path, PATHINFO_EXTENSION);
                
            return Storage::disk(self::STORAGE_DISK)->download(
                path: $attachment->file_path,
                name: $downloadName,
            );
        }

        // Guard: verifikasi file eksis di storage sebelum proses apapun
        abort_unless(
            Storage::disk(self::STORAGE_DISK)->exists($letter->file_path),
            404,
            'File surat tidak ditemukan di server. Hubungi administrator sistem.'
        );

        $downloadName = $this->buildDownloadFileName($letter);

        Log::info('Admin mengunduh file surat', [
            'admin_id'  => $request->user()->id,
            'letter_id' => $letter->id,
            'file'      => $letter->file_path,
        ]);

        return Storage::disk(self::STORAGE_DISK)->download(
            path: $letter->file_path,
            name: $downloadName,
        );
    }

    /**
     * GET /admin/letters/{letter}/download-final
     *
     * Layani download file surat KOREKSI (final_file_path) dari private storage.
     */
    public function downloadFinal(Request $request, Letter $letter): StreamedResponse
    {
        $this->authorizeVisibility($request->user(), $letter);

        abort_unless(
            $letter->final_file_path && Storage::disk(self::STORAGE_DISK)->exists($letter->final_file_path),
            404,
            'File koreksi surat tidak ditemukan di server.'
        );

        $slug   = Str::slug($letter->title);
        $date   = $letter->created_at->format('Ymd');
        $id     = str_pad((string) $letter->id, 3, '0', STR_PAD_LEFT);
        
        $extension = pathinfo($letter->final_file_path, PATHINFO_EXTENSION);
        $downloadName = "{$slug}_{$date}_{$id}_FINAL.{$extension}";

        Log::info('Admin mengunduh file koreksi surat', [
            'admin_id'  => $request->user()->id,
            'letter_id' => $letter->id,
            'file'      => $letter->final_file_path,
        ]);

        return Storage::disk(self::STORAGE_DISK)->download(
            path: $letter->final_file_path,
            name: $downloadName,
        );
    }

    /**
     * GET /admin/letters/{letter}/preview
     *
     * Tampilkan file secara inline di browser (khusus PDF).
     */
    public function preview(Request $request, Letter $letter)
    {
        $this->authorizeVisibility($request->user(), $letter);

        // --- FILE VERSIONING ---
        $attachmentId = $request->query('attachment_id');
        $isFinal = $request->query('is_final');
        
        $filePath = $letter->file_path;
        $extension = $letter->file_extension;
        
        if ($attachmentId) {
            $attachment = $letter->attachments()->find($attachmentId);
            abort_unless($attachment, 404, 'File riwayat tidak ditemukan.');
            $filePath = $attachment->file_path;
            $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        } elseif ($isFinal && $letter->final_file_path) {
            $filePath = $letter->final_file_path;
            $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        }

        abort_unless(
            Storage::disk(self::STORAGE_DISK)->exists($filePath),
            404,
            'File tidak ditemukan di server.'
        );

        if (strtolower($extension) !== 'pdf') {
            // Jika bukan PDF, paksa download
            return Storage::disk(self::STORAGE_DISK)->download($filePath);
        }

        $file = Storage::disk(self::STORAGE_DISK)->get($filePath);
        $type = Storage::disk(self::STORAGE_DISK)->mimeType($filePath);

        return response($file, 200)->header('Content-Type', $type)->header('Content-Disposition', 'inline; filename="preview.pdf"');
    }


    // =========================================================================
    // REQUEST REVISION — Tolak surat & minta perbaikan dari staff
    // =========================================================================

    /**
     * PATCH /admin/letters/{letter}/revision
     *
     * Ubah status surat menjadi 'revision' dan simpan catatan revisi.
     *
     * Business rules yang dijaga:
     * - Hanya surat berstatus 'pending' yang bisa diminta revisi
     * - Catatan revisi (notes) WAJIB diisi — staff harus tahu apa yang perlu diperbaiki
     * - Log dicatat dengan action 'request_revision'
     *
     * Alur:
     * 1. Validasi status surat (harus 'pending')
     * 2. Update status → 'revision' & catat updater
     * 3. Simpan log ke letter_logs
     * 4. Semua dalam DB transaction
     */
    public function requestRevision(RequestRevisionRequest $request, Letter $letter): RedirectResponse
    {
        // Guard: hanya surat pending yang bisa diminta revisi
        abort_unless(
            $letter->isPending(),
            422,
            'Hanya surat dengan status "Menunggu" yang dapat diminta revisi.'
        );

        $adminLevel = $request->user()->admin_level;
        $expectedStatus = match ($adminLevel) {
            'admin_1' => Letter::STATUS_PENDING_ADMIN_1,
            'admin_2' => Letter::STATUS_PENDING_ADMIN_2,
            'admin_3' => Letter::STATUS_PENDING_ADMIN_3,
            default => null,
        };

        abort_unless(
            $letter->status === $expectedStatus,
            403,
            'Anda tidak berhak memproses surat ini karena surat tidak berada pada antrean Anda.'
        );

        DB::beginTransaction();

        try {
            // ── 1. Update status surat (Optimistic Locking) ─────────────────────────────────────────
            $affectedRows = Letter::where('id', $letter->id)
                                  ->where('version', $letter->version)
                                  ->update([
                                      'status'               => Letter::STATUS_REVISION,
                                      'last_rejected_status' => $letter->status, // Simpan status penolak agar resubmit kembali ke admin ini
                                      'updated_by'           => $request->user()->id,
                                      'version'              => $letter->version + 1,
                                  ]);

            if ($affectedRows === 0) {
                DB::rollBack();
                return back()->with('error', 'Gagal memproses. Surat ini baru saja selesai diproses oleh Admin Backup lainnya.');
            }

            // ── 2. Catat log revisi ────────────────────────────────────────────
            LetterLog::create([
                'letter_id' => $letter->id,
                'action'    => LetterLog::ACTION_REQUEST_REVISION,
                'notes'     => $request->validated('notes'),
                'user_id'   => $request->user()->id,
            ]);

            DB::commit();

            try {
                if ($letter->creator) {
                    if ($letter->creator->email) {
                        // Mail::to($letter->creator->email)->send(new LetterRejectedMail($letter));
                    }
                    // Notif untuk staff pembuat
                    $letter->creator->notify(new \App\Notifications\SystemNotification(
                        'Revisi Diperlukan',
                        'Surat Anda: "' . $letter->title . '" dikembalikan untuk direvisi.',
                        route('staff.letters.show', $letter->id),
                        'danger'
                    ));
                    
                    // --- BROADCAST REVERB EVENT ---
                    event(new \App\Events\LetterStatusChanged(
                        message: "Surat \"{$letter->title}\" dikembalikan untuk direvisi.",
                        url: route('staff.letters.show', $letter->id),
                        type: "danger",
                        recipientType: "staff",
                        recipientId: $letter->creator->id
                    ));
                }
            } catch (\Throwable $e) {
                Log::warning('Gagal mengirim notifikasi revisi surat: ' . $e->getMessage());
            }

            return redirect()
                ->route('admin.reviews.index')
                ->with('success', 'Surat berhasil dikembalikan untuk direvisi. Staff akan mendapat notifikasi.');

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Gagal memproses permintaan revisi surat', [
                'admin_id'  => $request->user()->id,
                'letter_id' => $letter->id,
                'error'     => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Gagal memproses permintaan revisi. Silakan coba lagi.');
        }
    }

    // =========================================================================
    // APPROVE — Setujui surat & tetapkan nomor surat resmi
    // =========================================================================

    /**
     * PATCH /admin/letters/{letter}/approve
     *
     * Setujui surat dan tetapkan nomor surat resmi.
     *
     * Business rules yang dijaga:
     * - Hanya surat berstatus 'pending' yang bisa disetujui
     * - Nomor surat (letter_number) WAJIB diisi & harus unik — dijaga di ApproveLetterRequest
     * - Log dicatat dengan action 'approve'
     * - Setelah disetujui, surat tidak bisa diubah lagi (final state)
     *
     * Alur:
     * 1. Validasi status surat (harus 'pending')
     * 2. Update: status → 'approved', letter_number diisi, updater dicatat
     * 3. Simpan log ke letter_logs
     * 4. Semua dalam DB transaction
     */
    public function approve(ApproveLetterRequest $request, Letter $letter): RedirectResponse
    {
        // Guard: hanya surat pending yang bisa disetujui
        abort_unless(
            $letter->isPending(),
            422,
            'Hanya surat dengan status "Menunggu" yang dapat disetujui.'
        );

        $adminLevel = $request->user()->admin_level;
        $expectedStatus = match ($adminLevel) {
            'admin_1' => Letter::STATUS_PENDING_ADMIN_1,
            'admin_2' => Letter::STATUS_PENDING_ADMIN_2,
            'admin_3' => Letter::STATUS_PENDING_ADMIN_3,
            default => null,
        };

        abort_unless(
            $letter->status === $expectedStatus,
            403,
            'Anda tidak berhak menyetujui surat ini karena surat tidak berada pada antrean Anda.'
        );

        DB::beginTransaction();

        try {
            // ── 1. Tentukan Status Selanjutnya berdasarkan State Machine ───────────
            $nextStatus = $letter->getNextStatus();

            // ── 2. Proses final_file (jika ada) ──────────────────────────────────
            $finalFilePath = $letter->final_file_path; // Tetap kosong/null jika belum ada

            if ($request->hasFile('final_file')) {
                $uploadedFile  = $request->file('final_file');
                $fileExtension = strtolower($uploadedFile->getClientOriginalExtension());
                $fileName = now()->format('Ymd_His') . '_final_' . \Illuminate\Support\Str::random(12) . '.' . $fileExtension;

                $storedPath = $uploadedFile->storeAs(
                    path: self::STORAGE_DIR,
                    name: $fileName,
                    options: self::STORAGE_DISK,
                );

                if ($storedPath !== false) {
                    $finalFilePath = $storedPath;
                    
                    // --- FILE VERSIONING ---
                    // Tentukan file_type berdasarkan level admin yang mengupload
                    $adminLevel = $request->user()->admin_level;
                    $fileType = match($adminLevel) {
                        'admin_1' => 'progar_correction',
                        'admin_2' => 'pekas_correction',
                        'admin_3' => 'setum_final',
                        default   => 'admin_correction'
                    };

                    \App\Models\LetterAttachment::create([
                        'letter_id' => $letter->id,
                        'user_id'   => $request->user()->id,
                        'file_path' => $storedPath,
                        'file_type' => $fileType,
                    ]);
                }
            }

            // ── 3. Update surat: approved + nomor surat resmi + final_file_path + Optimistic Locking ──────────────────
            $updateData = [
                'status'          => $nextStatus,
                'final_file_path' => $finalFilePath,
                'updated_by'      => $request->user()->id,
                'version'         => $letter->version + 1,
            ];

            if ($request->filled('letter_number')) {
                $updateData['letter_number'] = $request->validated('letter_number');
            }

            $affectedRows = Letter::where('id', $letter->id)
                                  ->where('version', $letter->version)
                                  ->update($updateData);

            if ($affectedRows === 0) {
                DB::rollBack();
                // Bersihkan file yang terlanjur di-upload jika terjadi race condition
                if (isset($storedPath) && $storedPath !== false) {
                    Storage::disk(self::STORAGE_DISK)->delete($storedPath);
                }
                return back()->with('error', 'Gagal memproses. Surat ini baru saja selesai diproses oleh Admin Backup lainnya.');
            }

            // ── 4. Catat log persetujuan ───────────────────────────────────────
            $actionNotes = ($nextStatus === Letter::STATUS_PENDING_KABAGUM)
                ? $this->buildApprovalNotes($request->validated('letter_number') ?? '-', $request->validated('notes'))
                : "Surat disetujui untuk diteruskan ke tahap selanjutnya. Catatan: " . ($request->validated('notes') ?? '-');

            LetterLog::create([
                'letter_id' => $letter->id,
                'action'    => LetterLog::ACTION_APPROVE,
                'notes'     => $actionNotes,
                'user_id'   => $request->user()->id,
            ]);

            DB::commit();

            // ── 5. Kirim Notifikasi ke Admin Selanjutnya (Berjenjang & Broadcast) ──
            try {
                if ($nextStatus === Letter::STATUS_PENDING_ADMIN_2) {
                    $nextAdmins = \App\Models\User::where('role', 'admin')->where('admin_level', 'admin_2')->get();
                    \Illuminate\Support\Facades\Notification::send($nextAdmins, new \App\Notifications\SystemNotification(
                        'Review Lanjutan: PEKAS',
                        'Surat "' . $letter->title . '" telah lolos tahap 1 dan menunggu review Anda.',
                        route('admin.reviews.index'),
                        'warning'
                    ));
                    
                    // --- BROADCAST REVERB EVENT ---
                    event(new \App\Events\LetterStatusChanged(
                        message: "Surat \"{$letter->title}\" telah lolos tahap 1 dan menunggu review Anda.",
                        url: route('admin.reviews.index'),
                        type: "warning",
                        recipientType: "admin",
                        adminLevel: 2
                    ));
                    
                } elseif ($nextStatus === Letter::STATUS_PENDING_ADMIN_3) {
                    $nextAdmins = \App\Models\User::where('role', 'admin')->where('admin_level', 'admin_3')->get();
                    \Illuminate\Support\Facades\Notification::send($nextAdmins, new \App\Notifications\SystemNotification(
                        'Review Final: SETUM',
                        'Surat "' . $letter->title . '" telah lolos tahap 2 dan menunggu persetujuan final Anda.',
                        route('admin.reviews.index'),
                        'warning'
                    ));
                    
                    // --- BROADCAST REVERB EVENT ---
                    event(new \App\Events\LetterStatusChanged(
                        message: "Surat \"{$letter->title}\" telah lolos tahap 2 dan menunggu persetujuan final Anda.",
                        url: route('admin.reviews.index'),
                        type: "warning",
                        recipientType: "admin",
                        adminLevel: 3
                    ));
                }
            } catch (\Throwable $e) {
                Log::warning('Gagal mengirim notifikasi persetujuan surat: ' . $e->getMessage());
            }

            $msg = ($nextStatus === Letter::STATUS_PENDING_KABAGUM)
                ? "Surat berhasil disetujui secara sistem dan dilanjutkan ke proses cetak fisik (Menunggu KABAGUM)."
                : "Surat berhasil diteruskan ke tahap selanjutnya.";

            return redirect()
                ->route('admin.reviews.index')
                ->with('success', $msg);

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Gagal menyetujui surat', [
                'admin_id'  => $request->user()->id,
                'letter_id' => $letter->id,
                'error'     => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Gagal menyetujui surat. Silakan coba lagi.');
        }
    }

    // =========================================================================
    // MANUAL PROGRESS (Admin SETUM) - KABAGUM & KASEK
    // =========================================================================

    /**
     * POST /admin/letters/{letter}/manual-progress
     *
     * Perbarui status fisik surat secara manual oleh Admin SETUM.
     */
    public function manualProgress(Request $request, Letter $letter): RedirectResponse
    {
        // Guard: Hanya Admin SETUM
        abort_unless(
            $request->user()->isAdmin() && $request->user()->admin_level === 'admin_3',
            403,
            'Hanya Admin SETUM yang dapat melakukan tindakan ini.'
        );

        abort_unless(
            $letter->isManualPending(),
            422,
            'Surat tidak dalam status manual (KABAGUM/KASEK).'
        );

        DB::beginTransaction();

        try {
            if ($letter->status === Letter::STATUS_PENDING_KABAGUM) {
                $nextStatus = Letter::STATUS_PENDING_KASEK;
                $action = LetterLog::ACTION_FORWARD_KASEK;
                $notes = 'Surat fisik (hard file) telah diproses dan diteruskan ke KASEK.';
                $successMsg = 'Status berhasil diperbarui menjadi Menunggu KASEK.';
            } else {
                $nextStatus = Letter::STATUS_APPROVED;
                $action = LetterLog::ACTION_MARK_FINISHED;
                $notes = 'Surat fisik (hard file) telah ditandatangani oleh KASEK dan berstatus Selesai.';
                $successMsg = 'Surat berhasil ditandai sebagai Selesai.';
            }

            // ── 1. Perbarui status surat (Optimistic Locking) ─────────────────────────────────────────
            $affectedRows = Letter::where('id', $letter->id)
                                  ->where('version', $letter->version)
                                  ->update([
                                      'status'            => $nextStatus,
                                      'updated_by'        => $request->user()->id,
                                      'version'           => $letter->version + 1,
                                  ]);

            if ($affectedRows === 0) {
                DB::rollBack();
                return back()->with('error', 'Gagal memproses. Surat ini baru saja diubah statusnya oleh Admin lainnya.');
            }

            // ── 2. Catat log ─────────────────────────────────────────────────────
            LetterLog::create([
                'letter_id' => $letter->id,
                'action'    => $action,
                'notes'     => $notes,
                'user_id'   => $request->user()->id,
            ]);

            DB::commit();

            // ── 3. Kirim Email/Notif Jika Selesai ────────────────────────────────
            try {
                if ($nextStatus === Letter::STATUS_APPROVED && $letter->creator) {
                    if ($letter->creator->email) {
                        // Mail::to($letter->creator->email)->send(new LetterApprovedMail($letter));
                    }
                    
                    // Notif untuk staff pembuat
                    $letter->creator->notify(new \App\Notifications\SystemNotification(
                        'Surat Disetujui Sepenuhnya',
                        'Surat Anda: "' . $letter->title . '" telah ditandatangani oleh KASEK dan selesai sepenuhnya.',
                        route('staff.letters.show', $letter->id),
                        'success'
                    ));
                    
                    // --- BROADCAST REVERB EVENT ---
                    event(new \App\Events\LetterStatusChanged(
                        message: "Surat Anda: \"{$letter->title}\" telah ditandatangani dan selesai sepenuhnya.",
                        url: route('staff.letters.show', $letter->id),
                        type: "success",
                        recipientType: "staff",
                        recipientId: $letter->creator->id
                    ));
                }
            } catch (\Throwable $e) {
                Log::warning('Gagal mengirim notifikasi status selesai: ' . $e->getMessage());
            }

            return redirect()
                ->route(
                    $request->user()->isSuperAdmin()
                        ? 'super_admin.letters.show'
                        : 'admin.letters.show',
                    $letter
                )
                ->with('success', $successMsg);

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Gagal memperbarui progres manual surat', [
                'super_admin_id' => $request->user()->id,
                'letter_id'      => $letter->id,
                'error'          => $e->getMessage(),
            ]);

            return back()->with('error', 'Gagal memproses pembaruan status surat. Silakan coba lagi.');
        }
    }

    // =========================================================================
    // Private Helpers
    // =========================================================================

    /**
     * Bangun nama file download yang informatif dan aman.
     * Contoh output: "permohonan-cuti-tahunan_20260526_001.pdf"
     */
    private function buildDownloadFileName(Letter $letter): string
    {
        $slug   = Str::slug($letter->title);
        $date   = $letter->created_at->format('Ymd');
        $id     = str_pad((string) $letter->id, 3, '0', STR_PAD_LEFT);

        return "{$slug}_{$date}_{$id}.{$letter->file_extension}";
    }

    /**
     * Bangun pesan notes untuk log persetujuan.
     * Selalu sertakan nomor surat, tambahkan catatan admin jika ada.
     */
    private function buildApprovalNotes(string $letterNumber, ?string $adminNotes): string
    {
        $base = "Surat disetujui dengan nomor: {$letterNumber}.";

        if (! empty($adminNotes)) {
            return $base . " Catatan: {$adminNotes}";
        }

        return $base;
    }

    /**
     * Pastikan Admin hanya bisa melihat surat jika surat tersebut
     * sedang di antreannya atau admin tersebut pernah berinteraksi dengannya.
     */
    private function authorizeVisibility($user, Letter $letter): void
    {
        if ($user->role === 'admin') {
            $adminLevel = $user->admin_level;
            
            $isAtCurrentStage = match ($adminLevel) {
                'admin_1' => $letter->status === Letter::STATUS_PENDING_ADMIN_1,
                'admin_2' => $letter->status === Letter::STATUS_PENDING_ADMIN_2,
                'admin_3' => $letter->status === Letter::STATUS_PENDING_ADMIN_3,
                default => false,
            };

            $hasInteracted = $letter->logs()->where('user_id', $user->id)->exists();

            abort_unless(
                $isAtCurrentStage || $hasInteracted,
                403,
                'Anda tidak memiliki akses ke surat ini karena tidak berada di antrean divisi Anda.'
            );
        }
    }
}
