<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Letter;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ArchiveController extends Controller
{
    /**
     * Tampilkan daftar surat milik staff yang sudah berstatus 'approved' (Disetujui).
     */
    public function index(Request $request): View
    {
        $query = Letter::where('created_by', $request->user()->id)
            ->where('status', Letter::STATUS_APPROVED)
            ->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('letter_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('review_type')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('review_type', $request->review_type);
            });
        }

        $letters = $query->paginate(10)->withQueryString();

        return view('staff.archives.index', compact('letters'));
    }

    /**
     * Hapus surat dari arsip staff (soft delete ke Tong Sampah).
     */
    public function destroy(Request $request, Letter $letter): RedirectResponse
    {
        abort_if(
            $letter->created_by !== $request->user()->id,
            403,
            'Anda tidak berhak menghapus surat ini.'
        );

        $letter->delete();

        return redirect()
            ->route('staff.archives.index')
            ->with('success', 'Surat berhasil dipindahkan ke Tong Sampah.');
    }
}
