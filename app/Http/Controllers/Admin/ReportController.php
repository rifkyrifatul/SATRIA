<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Letter;
use App\Models\StaffDivision;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    /**
     * Tampilkan halaman Dashboard Laporan
     */
    public function index(Request $request): View
    {
        $query = $this->buildQuery($request);

        // Preview data untuk tabel (paginate)
        $letters = $query->paginate(15)->withQueryString();

        // Data untuk dropdown filter
        $divisions = StaffDivision::all();

        // Statistik summary — satu query GROUP BY, jauh lebih efisien dari 4 COUNT terpisah
        $statusGroups = $this->buildQuery($request)
            ->reorder() // Menghapus order by created_at dari buildQuery agar groupBy tidak error
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $pendingStatuses = [
            Letter::STATUS_PENDING_ADMIN_1,
            Letter::STATUS_PENDING_ADMIN_2,
            Letter::STATUS_PENDING_ADMIN_3,
        ];

        $stats = [
            'total'    => $statusGroups->sum(),
            'approved' => $statusGroups->get(Letter::STATUS_APPROVED, 0),
            'revision' => $statusGroups->get(Letter::STATUS_REVISION, 0),
            'pending'  => $statusGroups->only($pendingStatuses)->sum(),
        ];

        return view('admin.reports.index', compact('letters', 'divisions', 'stats'));
    }

    /**
     * Export data ke CSV (Native PHP, support MS Excel)
     */
    public function exportExcel(Request $request): StreamedResponse
    {
        $query = $this->buildQuery($request);
        $letters = $query->get();

        $fileName = 'laporan_surat_' . date('Ymd_His') . '.csv';

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename={$fileName}",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['ID', 'Nomor Surat', 'Judul Surat', 'Alur Pengajuan', 'Pembuat', 'Divisi', 'Status', 'Tanggal Diajukan'];

        $callback = function() use($letters, $columns) {
            $file = fopen('php://output', 'w');
            
            // Tambahkan BOM untuk excel agar bisa baca karakter khusus dengan benar
            fputs($file, "\xEF\xBB\xBF");
            
            // Menggunakan pemisah koma (lebih global) atau titik koma (tergantung region).
            // Default CSV reader excel biasanya bergantung pada pemisah list windows. Kita gunakan comma standar.
            fputcsv($file, $columns, ',');

            foreach ($letters as $letter) {
                $row = [
                    $letter->id,
                    $letter->letter_number ?? '-',
                    $letter->title,
                    $letter->category->name ?? '-',
                    $letter->creator->name ?? '-',
                    $letter->creator->division->name ?? '-',
                    $this->formatStatus($letter->status),
                    $letter->created_at->format('Y-m-d H:i:s')
                ];
                fputcsv($file, $row, ',');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export data ke PDF
     */
    public function exportPdf(Request $request)
    {
        $query = $this->buildQuery($request);
        $letters = $query->get();

        $pdf = Pdf::loadView('admin.reports.pdf', compact('letters'))
                  ->setPaper('a4', 'landscape');

        $fileName = 'laporan_surat_' . date('Ymd_His') . '.pdf';

        return $pdf->download($fileName);
    }

    /**
     * Helper untuk membuild query pencarian dan filter
     */
    private function buildQuery(Request $request)
    {
        $query = Letter::with(['creator.division', 'category'])->latest();

        // Filter: Dari Tanggal
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        // Filter: Sampai Tanggal
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Filter: Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter: Divisi (via relasi user creator)
        if ($request->filled('division_id')) {
            $query->whereHas('creator', function ($q) use ($request) {
                $q->where('division_id', $request->division_id);
            });
        }

        return $query;
    }

    private function formatStatus($status)
    {
        $map = [
            Letter::STATUS_PENDING_ADMIN_1 => 'Menunggu PROGAR',
            Letter::STATUS_PENDING_ADMIN_2 => 'Menunggu PEKAS',
            Letter::STATUS_PENDING_ADMIN_3 => 'Menunggu SETUM',
            Letter::STATUS_PENDING_KABAGUM => 'Menunggu KABAGUM',
            Letter::STATUS_PENDING_KASEK   => 'Menunggu KASEK',
            Letter::STATUS_APPROVED        => 'Disetujui',
            Letter::STATUS_REVISION        => 'Revisi',
        ];
        
        return $map[$status] ?? $status;
    }
}
