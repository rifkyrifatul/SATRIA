<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\Staff\ResubmitLetterRequest;
use App\Http\Requests\Staff\StoreLetterRequest;
use App\Models\Letter;
use App\Models\LetterLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use App\Models\User;
use App\Mail\LetterSubmittedMail;

class LetterController extends Controller
{
    /**
     * Disk private yang digunakan untuk menyimpan file surat.
     * Semua file disimpan di storage/app/private/letters — tidak bisa diakses via URL publik.
     */
    private const STORAGE_DISK = 'private';
    private const STORAGE_DIR  = 'letters';

    // =========================================================================
    // INDEX — Daftar surat milik staff yang sedang login
    // =========================================================================

    /**
     * GET /staff/letters
     *
     * Tampilkan semua surat yang dibuat oleh staff yang sedang login,
     * disertai log terbaru masing-masing surat (untuk tampilkan catatan revisi).
     */
    public function index(Request $request): View
    {
        $query = Letter::with(['category', 'creator.division', 'logs' => fn ($q) => $q->latest()->limit(1)])
            ->where('created_by', $request->user()->id)
            ->where('status', '!=', Letter::STATUS_APPROVED)
            ->latest();

        // Filter opsional berdasarkan keyword (judul / nomor surat)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('letter_number', 'like', "%{$search}%");
            });
        }

        // Filter opsional berdasarkan alur review
        if ($request->filled('review_type')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('review_type', $request->review_type);
            });
        }

        $letters = $query->paginate(10)->withQueryString();

        return view('staff.letters.index', compact('letters'));
    }

    // =========================================================================
    // CREATE — Form upload surat baru
    // =========================================================================

    /**
     * GET /staff/letters/create
     *
     * Tampilkan form untuk mengunggah surat baru.
     */
    public function create(): View
    {
        $categories = \App\Models\Category::all();
        return view('staff.letters.create', compact('categories'));
    }

    // =========================================================================
    // STORE — Proses upload surat baru
    // =========================================================================

    /**
     * POST /staff/letters
     *
     * Proses upload surat baru oleh staff.
     *
     * Alur:
     * 1. Validasi input (via StoreLetterRequest)
     * 2. Simpan file ke disk private (storage/app/private/letters)
     * 3. Buat record Letter di database
     * 4. Catat aksi 'submit' ke letter_logs
     * 5. Semua dalam satu DB transaction untuk menjaga konsistensi
     */
    public function store(StoreLetterRequest $request): \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
    {
        DB::beginTransaction();

        try {
            // ── 1. Simpan file ke private storage ─────────────────────────────
            $uploadedFile  = $request->file('file');
            $fileExtension = strtolower($uploadedFile->getClientOriginalExtension());

            // Nama file: timestamp_randomstring.ext — mencegah collision & kebocoran nama asli
            $fileName = now()->format('Ymd_His') . '_' . \Illuminate\Support\Str::random(12) . '.' . $fileExtension;

            // Simpan ke: storage/app/private/letters/{fileName}
            $filePath = $uploadedFile->storeAs(
                path: self::STORAGE_DIR,
                name: $fileName,
                options: self::STORAGE_DISK,
            );

            if ($filePath === false) {
                throw new \RuntimeException('Gagal menyimpan file ke storage.');
            }

            // Set initial status based on category review type
            $category = \App\Models\Category::find($request->input('category_id'));
            $initialStatus = ($category && $category->review_type === 'langsung_admin_3')
                ? Letter::STATUS_PENDING_ADMIN_3
                : Letter::STATUS_PENDING_ADMIN_1;

            // ── 2. Buat record Letter ──────────────────────────────────────────
            $letter = Letter::create([
                'category_id'    => $request->input('category_id'),
                'title'          => $request->validated('title'),
                'file_path'      => $filePath,
                'file_extension' => $fileExtension,
                'status'         => $initialStatus,
                'created_by'     => $request->user()->id,
                'updated_by'     => null,
            ]);

            // ── 2.5 Buat riwayat file (File Versioning) ────────────────────────
            \App\Models\LetterAttachment::create([
                'letter_id' => $letter->id,
                'user_id'   => $request->user()->id,
                'file_path' => $filePath,
                'file_type' => 'original',
            ]);

            // ── 3. Catat log 'submit' ──────────────────────────────────────────
            $statusLabel = $initialStatus === Letter::STATUS_PENDING_ADMIN_3 ? 'SETUM' : 'PROGAR';
            LetterLog::create([
                'letter_id' => $letter->id,
                'action'    => LetterLog::ACTION_SUBMIT,
                'notes'     => "Surat diajukan oleh staff (Menunggu {$statusLabel}).",
                'user_id'   => $request->user()->id,
            ]);

            DB::commit();

            // Kirim notifikasi setelah commit — wrap try-catch agar email
            // failure tidak membatalkan proses upload yang sudah berhasil.
            try {
                $this->notifyAdmins($letter);
            } catch (\Throwable $notifErr) {
                Log::warning('Gagal mengirim notifikasi setelah upload surat', [
                    'letter_id' => $letter->id,
                    'error'     => $notifErr->getMessage(),
                ]);
            }

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Surat berhasil diajukan dan sedang menunggu persetujuan Admin.',
                    'redirect' => route('staff.letters.show', $letter)
                ]);
            }

            return redirect()
                ->route('staff.letters.show', $letter)
                ->with('success', 'Surat berhasil diajukan dan sedang menunggu persetujuan Admin.');

        } catch (\Throwable $e) {
            DB::rollBack();

            // Hapus file yang sudah terlanjur tersimpan jika transaksi gagal
            if (isset($filePath) && $filePath !== false) {
                Storage::disk(self::STORAGE_DISK)->delete($filePath);
            }

            Log::error('Gagal mengupload surat', [
                'user_id' => $request->user()->id,
                'error'   => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat mengunggah surat. Silakan coba lagi.'
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat mengunggah surat. Silakan coba lagi.');
        }
    }

    // =========================================================================
    // SHOW — Detail surat + riwayat log
    // =========================================================================

    /**
     * GET /staff/letters/{letter}
     *
     * Tampilkan detail surat beserta seluruh riwayat log aktivitasnya.
     * Staff hanya bisa melihat surat miliknya sendiri.
     */
    public function show(Request $request, Letter $letter): RedirectResponse
    {
        return redirect()->route('staff.tracking.show', $letter);
    }

    // =========================================================================
    // EDIT — Form resubmit surat revisi
    // =========================================================================

    /**
     * GET /staff/letters/{letter}/edit
     *
     * Tampilkan form resubmit (hanya bisa diakses jika surat berstatus 'revision').
     */
    public function edit(Request $request, Letter $letter): View
    {
        abort_if(
            $letter->created_by !== $request->user()->id,
            403,
            'Anda tidak berhak mengakses surat ini.'
        );

        abort_if(
            ! $letter->isRevision(),
            403,
            'Surat ini tidak dalam status revisi. Tidak dapat dilakukan resubmit.'
        );

        // Load log revisi terbaru untuk tampilkan catatan dari admin
        $latestRevisionLog = $letter->logs()
            ->where('action', LetterLog::ACTION_REQUEST_REVISION)
            ->latest()
            ->first();

        return view('staff.letters.edit', compact('letter', 'latestRevisionLog'));
    }

    // =========================================================================
    // RESUBMIT — Proses pengiriman ulang surat revisi
    // =========================================================================

    /**
     * PUT /staff/letters/{letter}
     *
     * Proses resubmit surat yang sedang dalam status 'revision'.
     *
     * Alur:
     * 1. Validasi & otorisasi (via ResubmitLetterRequest)
     * 2. Jika ada file baru → hapus file lama, simpan file baru
     * 3. Update record Letter (status → pending, judul, file jika ada)
     * 4. Catat aksi 'submit' baru ke letter_logs
     * 5. Semua dalam satu DB transaction
     */
    public function update(ResubmitLetterRequest $request, Letter $letter): RedirectResponse
    {
        DB::beginTransaction();

        try {
            $category = $letter->category;
            
            // Tentukan status kembalian. Jika ada last_rejected_status, kembali ke sana.
            if ($letter->last_rejected_status) {
                $initialStatus = $letter->last_rejected_status;
            } else {
                $initialStatus = ($category && $category->review_type === 'langsung_admin_3')
                    ? Letter::STATUS_PENDING_ADMIN_3
                    : Letter::STATUS_PENDING_ADMIN_1;
            }

            $updateData = [
                'title'                => $request->validated('title'),
                'status'               => $initialStatus,
                'last_rejected_status' => null, // Reset setelah disubmit ulang
                'updated_by'           => $request->user()->id,
                'created_at'           => now(), // Reset waktu surat menjadi saat dire-submit
            ];

            // ── 1. Proses file baru (jika ada) ────────────────────────────────
            if ($request->hasFile('file')) {
                $uploadedFile  = $request->file('file');
                $fileExtension = strtolower($uploadedFile->getClientOriginalExtension());

                $fileName = now()->format('Ymd_His') . '_' . \Illuminate\Support\Str::random(12) . '.' . $fileExtension;

                $newFilePath = $uploadedFile->storeAs(
                    path: self::STORAGE_DIR,
                    name: $fileName,
                    options: self::STORAGE_DISK,
                );

                if ($newFilePath === false) {
                    throw new \RuntimeException('Gagal menyimpan file baru ke storage.');
                }

                // --- FILE VERSIONING ---
                // JANGAN hapus file lama untuk menjaga jejak audit fisik.
                // $this->deleteFileIfExists($letter->file_path);

                $updateData['file_path']      = $newFilePath;
                $updateData['file_extension'] = $fileExtension;

                // ── Simpan riwayat file baru ──────────────────────────────
                \App\Models\LetterAttachment::create([
                    'letter_id' => $letter->id,
                    'user_id'   => $request->user()->id,
                    'file_path' => $newFilePath,
                    'file_type' => 'staff_revision',
                ]);
            }

            // ── 2. Update Letter ───────────────────────────────────────────────
            $letter->update($updateData);

            // ── 3. Catat log resubmit ──────────────────────────────────────────
            $statusLabel = match ($initialStatus) {
                Letter::STATUS_PENDING_ADMIN_1 => 'PROGAR',
                Letter::STATUS_PENDING_ADMIN_2 => 'PEKAS',
                Letter::STATUS_PENDING_ADMIN_3 => 'SETUM',
                default => 'Admin',
            };
            $fileNote = $request->hasFile('file') ? 'dengan file baru' : '(file tidak diganti)';
            
            LetterLog::create([
                'letter_id' => $letter->id,
                'action'    => LetterLog::ACTION_SUBMIT,
                'notes'     => "Surat diajukan ulang oleh staff {$fileNote} (Menunggu {$statusLabel}).",
                'user_id'   => $request->user()->id,
            ]);

            DB::commit();

            // Kirim notifikasi setelah commit — wrap try-catch agar email
            // failure tidak membatalkan proses resubmit yang sudah berhasil.
            try {
                $this->notifyAdmins($letter);
            } catch (\Throwable $notifErr) {
                Log::warning('Gagal mengirim notifikasi setelah resubmit surat', [
                    'letter_id' => $letter->id,
                    'error'     => $notifErr->getMessage(),
                ]);
            }

            return redirect()
                ->route('staff.letters.show', $letter)
                ->with('success', 'Surat berhasil diajukan ulang dan sedang menunggu persetujuan Admin.');

        } catch (\Throwable $e) {
            DB::rollBack();

            // Hapus file baru yang sudah terlanjur tersimpan jika transaksi gagal
            if (isset($newFilePath) && $newFilePath !== false) {
                Storage::disk(self::STORAGE_DISK)->delete($newFilePath);
            }

            Log::error('Gagal resubmit surat', [
                'user_id'   => $request->user()->id,
                'letter_id' => $letter->id,
                'error'     => $e->getMessage(),
                'trace'     => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat mengajukan ulang surat. Silakan coba lagi.');
        }
    }

    // =========================================================================
    // DOWNLOAD — Unduh file surat secara aman
    // =========================================================================

    /**
     * GET /staff/letters/{letter}/download
     *
     * Layani file download surat secara aman.
     * File tidak pernah diekspos via URL publik — selalu melalui controller ini.
     *
     * Laravel akan menyajikan file dengan header Content-Disposition: attachment
     * sehingga browser langsung mendownload, bukan menampilkan di tab baru.
     */
    public function download(Request $request, Letter $letter): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        abort_if(
            $letter->created_by !== $request->user()->id,
            403,
            'Anda tidak berhak mengunduh file ini.'
        );

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

        // --- Fallback ke file berjalan ---
        abort_unless(
            Storage::disk(self::STORAGE_DISK)->exists($letter->file_path),
            404,
            'File tidak ditemukan di server.'
        );

        // Bangun nama file yang informatif untuk user
        $downloadName = \Illuminate\Support\Str::slug($letter->title)
            . '_' . $letter->created_at->format('Ymd')
            . '.' . $letter->file_extension;

        return Storage::disk(self::STORAGE_DISK)->download(
            path: $letter->file_path,
            name: $downloadName,
        );
    }

    /**
     * GET /staff/letters/{letter}/preview
     *
     * Tampilkan file secara inline di browser (khusus PDF).
     */
    public function preview(Request $request, Letter $letter)
    {
        abort_if(
            $letter->created_by !== $request->user()->id,
            403,
            'Anda tidak berhak melihat file ini.'
        );

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
    // PRINT — Unduh dokumen final untuk dicetak
    // =========================================================================

    /**
     * GET /staff/letters/{letter}/print
     *
     * Unduh file final_file_path (jika ada) atau file_path jika status approved.
     */
    public function print(Request $request, Letter $letter): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        abort_if(
            $letter->created_by !== $request->user()->id,
            403,
            'Anda tidak berhak mengunduh file ini.'
        );

        $isPrintable = $letter->isApproved() || $letter->isManualPending();
        abort_unless(
            $isPrintable,
            403,
            'Surat belum disetujui SETUM, tidak dapat dicetak.'
        );

        $pathToDownload = $letter->final_file_path ?? $letter->file_path;

        abort_unless(
            Storage::disk(self::STORAGE_DISK)->exists($pathToDownload),
            404,
            'File tidak ditemukan di server.'
        );

        $downloadName = \Illuminate\Support\Str::slug($letter->title)
            . '_FINAL_' . $letter->created_at->format('Ymd')
            . '.' . pathinfo($pathToDownload, PATHINFO_EXTENSION);

        return Storage::disk(self::STORAGE_DISK)->download(
            path: $pathToDownload,
            name: $downloadName,
        );
    }

    // =========================================================================
    // DESTROY — Hapus surat (Soft Delete)
    // =========================================================================

    /**
     * DELETE /staff/letters/{letter}
     *
     * Hapus surat (dipindahkan ke tong sampah / soft delete).
     */
    public function destroy(Request $request, Letter $letter): RedirectResponse
    {
        abort_if(
            $letter->created_by !== $request->user()->id,
            403,
            'Anda tidak berhak menghapus surat ini.'
        );

        $letter->delete(); // Soft delete

        return redirect()
            ->route('staff.letters.index')
            ->with('success', 'Surat berhasil dipindahkan ke Tong Sampah.');
    }

    // =========================================================================
    // Private Helpers
    // =========================================================================

    /**
     * Hapus file dari private storage jika file tersebut ada.
     */
    private function deleteFileIfExists(?string $filePath): void
    {
        if ($filePath && Storage::disk(self::STORAGE_DISK)->exists($filePath)) {
            Storage::disk(self::STORAGE_DISK)->delete($filePath);
        }
    }

    /**
     * Kirim email dan notifikasi ke Admin yang bertugas.
     */
    private function notifyAdmins(Letter $letter): void
    {
        $targetAdminLevel = match ($letter->status) {
            Letter::STATUS_PENDING_ADMIN_1 => 1,
            Letter::STATUS_PENDING_ADMIN_2 => 2,
            Letter::STATUS_PENDING_ADMIN_3 => 3,
            default => 1,
        };
        $admins = User::where('role', 'admin')
            ->where('admin_level', 'admin_' . $targetAdminLevel)
            ->get();
        
        // Broadcast email ke seluruh admin terkait (Dimatikan untuk Shared Hosting)
        // foreach ($admins as $admin) {
        //     Mail::to($admin->email)->send(new LetterSubmittedMail($letter));
        // }

        // Broadcast Database Notification ke seluruh admin sekaligus
        $levelName = match ($targetAdminLevel) {
            1 => 'PROGAR',
            2 => 'PEKAS',
            3 => 'SETUM',
            default => 'Admin',
        };
        \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\SystemNotification(
            "Surat Baru: {$levelName}",
            "Surat \"{$letter->title}\" diajukan dan menunggu persetujuan Anda.",
            route('admin.reviews.index'),
            'info'
        ));

        // --- BROADCAST REVERB EVENT ---
        try {
            event(new \App\Events\LetterStatusChanged(
                message: "Surat \"{$letter->title}\" diajukan dan menunggu persetujuan Anda.",
                url: route('admin.reviews.index'),
                type: "info",
                recipientType: "admin",
                adminLevel: $targetAdminLevel
            ));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning("Broadcast failed: " . $e->getMessage());
        }
    }

    /**
     * GET /staff/posisi-surat
     *
     * Halaman khusus tracking posisi & alur persetujuan surat pengajuan milik staff.
     * Langsung mengarahkan ke tampilan detail tracking surat terbaru.
     */
    public function tracking(Request $request): \Illuminate\Http\RedirectResponse
    {
        $user = $request->user() ?? auth()->user();
        if (!$user) {
            return redirect()->route('login');
        }

        $latestLetter = Letter::where('created_by', $user->id)->latest()->first();

        if ($latestLetter) {
            return redirect()->route('staff.tracking.show', $latestLetter);
        }

        return redirect()->route('staff.letters.create')->with('info', 'Belum ada surat yang diajukan. Silakan ajukan surat terlebih dahulu.');
    }

    /**
     * GET /staff/posisi-surat/{letter}
     *
     * Tampilkan detail tracking posisi surat di dalam menu Posisi Surat.
     */
    public function trackingShow(Request $request, Letter $letter): View
    {
        $user = $request->user() ?? auth()->user();

        abort_if(
            !$user || $letter->created_by !== $user->id,
            403,
            'Anda tidak berhak mengakses surat ini.'
        );

        $letter->load(['logs.user', 'creator', 'updater', 'category', 'attachments']);
        $allLetters = Letter::where('created_by', $user->id)->latest()->get();

        return view('staff.letters.tracking_show', compact('letter', 'allLetters'));
    }
}
