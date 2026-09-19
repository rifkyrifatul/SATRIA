<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Letter;
use App\Models\Category;
use App\Models\StaffDivision;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Carbon\Carbon;

class ArchiveController extends Controller
{
    /**
     * Tampilkan seluruh surat yang telah disetujui secara final (Arsip).
     */
    public function index(Request $request): View
    {
        $query = $this->buildRecapQuery($request);

        $letters = $query->paginate(15)->withQueryString();

        $categories = Category::orderBy('name')->get();
        $divisions = StaffDivision::orderBy('name')->get();

        return view('admin.archives.index', compact('letters', 'categories', 'divisions'));
    }

    /**
     * Export rekap arsip surat bulanan ke PDF.
     */
    public function exportMonthlyPdf(Request $request)
    {
        $letters = $this->buildRecapQuery($request)->get();

        $monthNum = $request->filled('month') ? (int)$request->month : date('n');
        $yearNum  = $request->filled('year') ? (int)$request->year : date('Y');
        
        $monthName = Carbon::createFromDate($yearNum, $monthNum, 1)->locale('id')->isoFormat('MMMM');

        $pdf = Pdf::loadView('admin.archives.recap_pdf', compact('letters', 'monthName', 'yearNum'))
                  ->setPaper('a4', 'landscape');

        $fileName = 'rekap_arsip_surat_' . strtolower($monthName) . '_' . $yearNum . '.pdf';

        return $pdf->download($fileName);
    }

    /**
     * Export rekap arsip surat bulanan ke Excel / CSV.
     */
    public function exportMonthlyExcel(Request $request): StreamedResponse
    {
        $letters = $this->buildRecapQuery($request)->get();

        $monthNum = $request->filled('month') ? (int)$request->month : date('n');
        $yearNum  = $request->filled('year') ? (int)$request->year : date('Y');
        
        $monthName = Carbon::createFromDate($yearNum, $monthNum, 1)->locale('id')->isoFormat('MMMM');

        $fileName = 'rekap_arsip_surat_' . strtolower($monthName) . '_' . $yearNum . '.csv';

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename={$fileName}",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['No', 'Nomor Surat', 'Judul Surat', 'Alur Pengajuan', 'Pengaju', 'Divisi Pengaju', 'Tanggal Pengajuan', 'Tanggal Disetujui'];

        $callback = function() use ($letters, $columns) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");
            fputcsv($file, $columns, ',');

            foreach ($letters as $index => $letter) {
                $row = [
                    $index + 1,
                    $letter->letter_number ?? 'Belum bernomor',
                    $letter->title,
                    $letter->category->name ?? '-',
                    $letter->creator->name ?? '-',
                    $letter->creator->division->name ?? 'Staff',
                    $letter->created_at->format('Y-m-d H:i:s'),
                    $letter->updated_at->format('Y-m-d H:i:s')
                ];
                fputcsv($file, $row, ',');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Hapus surat dari arsip internal (soft delete ke Tong Sampah).
     */
    public function destroy(Request $request, Letter $letter): RedirectResponse
    {
        $user = $request->user();

        abort_unless(
            $user->isSuperAdmin() || ($user->isAdmin() && $user->admin_level === 'admin_3'),
            403,
            'Anda tidak memiliki akses untuk menghapus arsip surat ini.'
        );

        $letter->delete();

        $redirectRoute = $user->isSuperAdmin() ? 'super_admin.archives.index' : 'admin.archives.index';

        return redirect()
            ->route($redirectRoute)
            ->with('success', 'Surat berhasil dipindahkan dari arsip ke Tong Sampah.');
    }

    /**
     * Helper untuk membuild query pencarian dan filter rekap arsip.
     */
    private function buildRecapQuery(Request $request)
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

        $query = Letter::with(['creator.division', 'category'])
            ->where('status', Letter::STATUS_APPROVED)
            ->where($visibilityFilter)
            ->latest('updated_at');

        // Filter berdasarkan Bulan (jika diisi)
        if ($request->filled('month')) {
            $query->whereMonth('updated_at', $request->month);
        }

        // Filter berdasarkan Tahun (jika diisi)
        if ($request->filled('year')) {
            $query->whereYear('updated_at', $request->year);
        }

        // Filter opsional berdasarkan keyword (judul / nomor surat)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('letter_number', 'like', "%{$search}%");
            });
        }

        // Filter berdasarkan kategori
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filter berdasarkan divisi pengaju
        if ($request->filled('division')) {
            $query->whereHas('creator', function ($q) use ($request) {
                $q->where('division_id', $request->division);
            });
        }

        return $query;
    }
}
