<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Letter;
use Illuminate\Http\Request;

class LetterReviewController extends Controller
{
    /**
     * LOGIKA DASHBOARD FILTERING (index)
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $adminLevel = $user->admin_level; // 'admin_1', 'admin_2', 'admin_3'

        // Muat relasi category, creator (owner), dan logs agar efisien
        $query = Letter::with(['category', 'creator.division', 'logs.user'])->latest();

        // Lakukan query filtering berdasarkan admin_level
        if ($adminLevel === 'admin_1') {
            $query->where('status', Letter::STATUS_PENDING_ADMIN_1);
        } elseif ($adminLevel === 'admin_2') {
            $query->where('status', Letter::STATUS_PENDING_ADMIN_2);
        } elseif ($adminLevel === 'admin_3') {
            $query->where('status', Letter::STATUS_PENDING_ADMIN_3);
        } else {
            // Fallback jika tidak ada level admin yang valid, kembalikan kosong (safety)
            $query->where('id', 0);
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

        $letters = $query->paginate(15)->withQueryString();

        $categories = \App\Models\Category::orderBy('name')->get();
        $divisions = \App\Models\StaffDivision::orderBy('name')->get();

        // Diasumsikan view akan ada di admin.reviews.index (silakan disesuaikan)
        return view('admin.reviews.index', compact('letters', 'adminLevel', 'categories', 'divisions'));
    }

    /**
     * Tampilkan Riwayat Review (Surat yang pernah diproses oleh Admin ini)
     */
    public function history(Request $request)
    {
        $user = $request->user();
        $adminLevel = $user->admin_level; // 'admin_1', 'admin_2', 'admin_3'

        // Muat relasi category, creator (owner), dan logs agar efisien
        // Ambil surat yang PERNAH diinteraksi oleh admin ini (ada di log-nya)
        $query = Letter::with(['category', 'creator.division', 'logs.user'])
            ->whereHas('logs', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
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

        $categories = \App\Models\Category::orderBy('name')->get();
        $divisions = \App\Models\StaffDivision::orderBy('name')->get();

        return view('admin.reviews.history', compact('letters', 'adminLevel', 'categories', 'divisions'));
    }

}
