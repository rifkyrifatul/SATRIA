<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Letter;
use App\Models\Category;
use App\Models\StaffDivision;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ArchiveController extends Controller
{
    /**
     * Tampilkan seluruh surat yang telah disetujui secara final (Arsip).
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

        $query = Letter::with(['creator.division', 'category'])
            ->where('status', Letter::STATUS_APPROVED)
            ->where($visibilityFilter)
            ->latest();

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

        $letters = $query->paginate(15)->withQueryString();

        $categories = Category::orderBy('name')->get();
        $divisions = StaffDivision::orderBy('name')->get();

        return view('admin.archives.index', compact('letters', 'categories', 'divisions'));
    }
}
