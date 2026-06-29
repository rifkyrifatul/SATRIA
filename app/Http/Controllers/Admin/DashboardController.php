<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Letter;
use App\Models\LetterLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Tampilkan dashboard admin.
     *
     * Admin memiliki visibilitas penuh ke semua surat dan aktivitas sistem.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        
        $pendingStatuses = [
            Letter::STATUS_PENDING_ADMIN_1,
            Letter::STATUS_PENDING_ADMIN_2,
            Letter::STATUS_PENDING_ADMIN_3
        ];
        
        $adminLevelStatus = null;
        $isAdmin3 = false;
        if ($user->isAdmin()) {
            $adminLevelStatus = match ($user->admin_level) {
                'admin_1' => Letter::STATUS_PENDING_ADMIN_1,
                'admin_2' => Letter::STATUS_PENDING_ADMIN_2,
                'admin_3' => Letter::STATUS_PENDING_ADMIN_3,
                default => null,
            };
            $isAdmin3 = $user->admin_level === 'admin_3';
        }

        $isSuperAdmin = $user->isSuperAdmin();

        // Base query for letters that respects visibility rules
        $visibilityFilter = function($query) use ($isSuperAdmin, $isAdmin3) {
            if (!$isSuperAdmin && !$isAdmin3) {
                $query->whereHas('category', function($q) {
                    $q->where('review_type', '!=', 'langsung_admin_3')->orWhereNull('review_type');
                });
            }
        };

        $stats = [
            'total_letters'   => Letter::where($visibilityFilter)->count(),
            'pending'         => Letter::whereIn('status', array_merge($pendingStatuses, [
                Letter::STATUS_PENDING_KABAGUM,
                Letter::STATUS_PENDING_KASEK
            ]))->where($visibilityFilter)->count(),
            'revision'        => Letter::where('status', Letter::STATUS_REVISION)->where($visibilityFilter)->count(),
            'approved'        => Letter::where('status', Letter::STATUS_APPROVED)->where($visibilityFilter)->count(),
            'total_staff'     => User::where('role', 'staff')->count(),
            'total_admin'     => User::where('role', 'admin')->count(),
        ];

        // Surat terbaru untuk di-review
        $pendingQuery = Letter::with(['category', 'creator.division']);
        if ($isSuperAdmin) {
            $pendingQuery->whereIn('status', $pendingStatuses);
        } else {
            $pendingQuery->where('status', $adminLevelStatus);
            $pendingQuery->where($visibilityFilter);
        }
        
        if ($request->filled('category_id')) {
            $pendingQuery->where('category_id', $request->category_id);
        }
        $hasMorePending = $pendingQuery->count() > 5;
        $pendingLetters = $pendingQuery->orderBy('updated_at', 'desc')->limit(5)->get();

        // Aktivitas log terbaru
        $recentLogs = LetterLog::with(['letter', 'user'])
            ->whereHas('letter', $visibilityFilter)
            ->latest()
            ->limit(5)
            ->get();

        // Semua surat dengan pagination
        $lettersQuery = Letter::with(['category', 'creator.division', 'updater'])->where($visibilityFilter);
        if ($request->filled('category_id')) {
            $lettersQuery->where('category_id', $request->category_id);
        }
        $letters = $lettersQuery->latest()->paginate(15);
        
        $categories = \App\Models\Category::all();
        $letterTemplates = \App\Models\LetterTemplate::latest()->get();

        return view('admin.dashboard', compact('stats', 'pendingLetters', 'hasMorePending', 'recentLogs', 'letters', 'categories', 'letterTemplates'));
    }
}
