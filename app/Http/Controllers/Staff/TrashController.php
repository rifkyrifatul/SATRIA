<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Category;
use App\Models\Letter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TrashController extends Controller
{
    // ==========================================
    // LETTERS
    // ==========================================
    public function letters(Request $request)
    {
        $query = Letter::onlyTrashed()
            ->where('created_by', $request->user()->id)
            ->with(['category', 'creator', 'updater']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('letter_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('review_type')) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('review_type', $request->review_type);
            });
        }

        $letters = $query->latest('deleted_at')->paginate(10)->withQueryString();
        
        return view('staff.trash.index', compact('letters'));
    }

    public function restoreLetter(Request $request, $id)
    {
        $letter = Letter::onlyTrashed()->where('created_by', $request->user()->id)->findOrFail($id);
        $letter->restore();

        return redirect()->route('staff.trash.letters')->with('success', 'Surat berhasil dikembalikan.');
    }

    public function forceDeleteLetter(Request $request, $id)
    {
        $letter = Letter::onlyTrashed()->where('created_by', $request->user()->id)->with('attachments')->findOrFail($id);

        // Hapus record surat dari database. Event forceDeleted di Model akan otomatis menghapus file fisik.
        $letter->forceDelete();

        return redirect()->route('staff.trash.letters')->with('success', 'Surat beserta seluruh file riwayatnya telah dihapus permanen dari server.');
    }

    public function emptyTrash(Request $request)
    {
        // Ambil semua surat di tong sampah milik user ini
        $letters = Letter::onlyTrashed()->where('created_by', $request->user()->id)->get();
        
        // Loop dan forceDelete agar event model berjalan dan menghapus file fisik
        foreach ($letters as $letter) {
            $letter->forceDelete();
        }

        return redirect()->route('staff.trash.letters')->with('success', 'Semua sampah surat berhasil dibersihkan.');
    }
}
