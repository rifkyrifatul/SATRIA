<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    /**
     * Display a listing of the activity logs.
     */
    public function index()
    {
        $logs = Activity::with('causer')
            ->latest()
            ->paginate(15);

        return view('admin.activity_logs.index', compact('logs'));
    }
}
