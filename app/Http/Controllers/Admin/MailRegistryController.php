<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MailRegistry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Carbon\Carbon;

class MailRegistryController extends Controller
{
    private const STORAGE_DISK = 'private';
    private const STORAGE_DIR = 'mail_registries';

    public function index(Request $request)
    {
        $mailRegistries = $this->buildRecapQuery($request)->paginate(15)->withQueryString();

        return view('admin.mail_registries.index', compact('mailRegistries'));
    }

    /**
     * Export rekap agenda surat eksternal bulanan ke PDF.
     */
    public function exportMonthlyPdf(Request $request)
    {
        $mailRegistries = $this->buildRecapQuery($request)->get();

        $monthNum = $request->filled('month') ? (int)$request->month : date('n');
        $yearNum  = $request->filled('year') ? (int)$request->year : date('Y');

        $monthName = Carbon::createFromDate($yearNum, $monthNum, 1)->locale('id')->isoFormat('MMMM');

        $pdf = Pdf::loadView('admin.mail_registries.recap_pdf', compact('mailRegistries', 'monthName', 'yearNum'))
                  ->setPaper('a4', 'landscape');

        $fileName = 'rekap_surat_eksternal_' . strtolower($monthName) . '_' . $yearNum . '.pdf';

        return $pdf->download($fileName);
    }

    /**
     * Export rekap agenda surat eksternal bulanan ke Excel / CSV.
     */
    public function exportMonthlyExcel(Request $request): StreamedResponse
    {
        $mailRegistries = $this->buildRecapQuery($request)->get();

        $monthNum = $request->filled('month') ? (int)$request->month : date('n');
        $yearNum  = $request->filled('year') ? (int)$request->year : date('Y');

        $monthName = Carbon::createFromDate($yearNum, $monthNum, 1)->locale('id')->isoFormat('MMMM');

        $fileName = 'rekap_surat_eksternal_' . strtolower($monthName) . '_' . $yearNum . '.csv';

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename={$fileName}",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['No', 'Jenis Surat', 'Nomor Surat', 'Perihal', 'Asal / Tujuan', 'Tanggal Surat', 'Pengunggah'];

        $callback = function() use ($mailRegistries, $columns) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");
            fputcsv($file, $columns, ',');

            foreach ($mailRegistries as $index => $mail) {
                $row = [
                    $index + 1,
                    $mail->type === 'masuk' ? 'Surat Masuk' : 'Surat Keluar',
                    $mail->reference_number,
                    $mail->subject,
                    $mail->origin_destination,
                    $mail->date ? $mail->date->format('Y-m-d') : '-',
                    $mail->uploader->name ?? '-'
                ];
                fputcsv($file, $row, ',');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function create()
    {
        $this->authorizeAdminSetum(request()->user());
        return view('admin.mail_registries.create');
    }

    public function store(Request $request)
    {
        $this->authorizeAdminSetum($request->user());

        $validated = $request->validate([
            'type' => 'required|in:masuk,keluar',
            'reference_number' => 'required|string|max:255',
            'date' => 'required|date',
            'origin_destination' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf|max:10240', // max 10MB
        ]);

        $validated['uploaded_by'] = $request->user()->id;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = now()->format('Ymd_His') . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs(self::STORAGE_DIR, $fileName, self::STORAGE_DISK);

            if ($path === false) {
                return back()->withInput()->with('error', 'Gagal menyimpan file ke server. Silakan coba lagi atau hubungi administrator.');
            }

            $validated['file_path'] = $path;
        }

        MailRegistry::create($validated);

        $route = $request->user()->isSuperAdmin() ? 'super_admin.mail_registries.index' : 'admin.mail_registries.index';
        return redirect()->route($route)->with('success', 'Surat berhasil ditambahkan ke Buku Agenda.');
    }

    public function edit(MailRegistry $mailRegistry)
    {
        $this->authorizeAdminSetum(request()->user());
        return view('admin.mail_registries.edit', compact('mailRegistry'));
    }

    public function update(Request $request, MailRegistry $mailRegistry)
    {
        $this->authorizeAdminSetum($request->user());

        $validated = $request->validate([
            'type' => 'required|in:masuk,keluar',
            'reference_number' => 'required|string|max:255',
            'date' => 'required|date',
            'origin_destination' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'file' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        if ($request->hasFile('file')) {
            // Delete old file
            if (Storage::disk(self::STORAGE_DISK)->exists($mailRegistry->file_path)) {
                Storage::disk(self::STORAGE_DISK)->delete($mailRegistry->file_path);
            }

            $file = $request->file('file');
            $fileName = now()->format('Ymd_His') . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs(self::STORAGE_DIR, $fileName, self::STORAGE_DISK);
            $validated['file_path'] = $path;
        }

        $mailRegistry->update($validated);

        $route = $request->user()->isSuperAdmin() ? 'super_admin.mail_registries.index' : 'admin.mail_registries.index';
        return redirect()->route($route)->with('success', 'Data surat berhasil diperbarui.');
    }

    public function destroy(MailRegistry $mailRegistry)
    {
        $this->authorizeAdminSetum(request()->user());

        if (Storage::disk(self::STORAGE_DISK)->exists($mailRegistry->file_path)) {
            Storage::disk(self::STORAGE_DISK)->delete($mailRegistry->file_path);
        }

        $mailRegistry->delete();

        $route = request()->user()->isSuperAdmin() ? 'super_admin.mail_registries.index' : 'admin.mail_registries.index';
        return redirect()->route($route)->with('success', 'Data surat berhasil dihapus.');
    }

    public function download(MailRegistry $mailRegistry)
    {
        abort_unless(
            Storage::disk(self::STORAGE_DISK)->exists($mailRegistry->file_path),
            404,
            'File surat tidak ditemukan.'
        );

        $downloadName = Str::slug($mailRegistry->subject) . '_' . $mailRegistry->date->format('Ymd') . '.pdf';

        return Storage::disk(self::STORAGE_DISK)->download(
            $mailRegistry->file_path,
            $downloadName
        );
    }

    public function dispositionForm(MailRegistry $mailRegistry)
    {
        $this->authorizeAdminSetum(request()->user());
        
        $users = \App\Models\User::where('id', '!=', auth()->id())
                    ->whereIn('role', ['staff', 'admin'])
                    ->orderBy('name')
                    ->get();
                    
        return view('admin.mail_registries.disposition', compact('mailRegistry', 'users'));
    }

    public function storeDisposition(Request $request, MailRegistry $mailRegistry)
    {
        $this->authorizeAdminSetum($request->user());

        $validated = $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'note' => 'nullable|string',
        ]);

        foreach ($validated['user_ids'] as $userId) {
            // Cek jika sudah ada disposisi untuk user ini di surat yang sama, abaikan atau update?
            // Kita buat disposisi baru saja atau update jika ada.
            \App\Models\MailRegistryDisposition::updateOrCreate(
                [
                    'mail_registry_id' => $mailRegistry->id,
                    'user_id' => $userId,
                ],
                [
                    'note' => $validated['note'],
                    'read_at' => null, // reset read status
                ]
            );

            // Send notification
            $user = \App\Models\User::find($userId);
            if ($user) {
                $user->notify(new \App\Notifications\MailRegistryDispositionNotification($mailRegistry, $request->user()->name));
            }
        }

        $route = $request->user()->isSuperAdmin() ? 'super_admin.mail_registries.index' : 'admin.mail_registries.index';
        return redirect()->route($route)->with('success', 'Surat berhasil didisposisikan.');
    }

    /**
     * Helper untuk membuild query pencarian dan filter rekap agenda surat eksternal.
     */
    private function buildRecapQuery(Request $request)
    {
        $query = MailRegistry::with('uploader')->latest('date');

        if ($request->filled('type') && in_array($request->type, ['masuk', 'keluar'])) {
            $query->where('type', $request->type);
        }

        if ($request->filled('year')) {
            $query->whereYear('date', $request->year);
        }

        if ($request->filled('month')) {
            $query->whereMonth('date', $request->month);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('origin_destination', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    private function authorizeAdminSetum($user)
    {
        abort_unless(
            $user->isSuperAdmin() || ($user->isAdmin() && $user->admin_level === 'admin_3'),
            403,
            'Hanya Admin SETUM atau Super Admin yang memiliki akses untuk mengelola data ini.'
        );
    }
}
