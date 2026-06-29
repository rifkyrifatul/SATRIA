<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Letter;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Tampilkan dashboard staff.
     *
     * Staff hanya melihat surat yang mereka buat sendiri.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $query = Letter::with(['category', 'creator.division'])
            ->where('created_by', $user->id)
            ->when($request->query('category_id'), function ($q) use ($request) {
                return $q->where('category_id', $request->query('category_id'));
            });

        $hasMoreLetters = $query->count() > 5;
        $letters = $query->orderBy('updated_at', 'desc')->limit(5)->get();
            
        $categories = \App\Models\Category::all();

        $stats = [
            'total'    => Letter::where('created_by', $user->id)->count(),
            'pending'  => Letter::where('created_by', $user->id)
                ->whereIn('status', [
                    Letter::STATUS_PENDING_ADMIN_1,
                    Letter::STATUS_PENDING_ADMIN_2,
                    Letter::STATUS_PENDING_ADMIN_3,
                    Letter::STATUS_PENDING_KABAGUM,
                    Letter::STATUS_PENDING_KASEK
                ])->count(),
            'revision' => Letter::where('created_by', $user->id)->where('status', Letter::STATUS_REVISION)->count(),
            'approved' => Letter::where('created_by', $user->id)->where('status', Letter::STATUS_APPROVED)->count(),
        ];
        
        $letterTemplates = \App\Models\LetterTemplate::latest()->get();

        return view('staff.dashboard', compact('letters', 'hasMoreLetters', 'stats', 'categories', 'letterTemplates'));
    }
}
