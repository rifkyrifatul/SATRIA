<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Renbut;
use App\Models\RenbutItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class RenbutController extends Controller
{
    public function index(Request $request)
    {
        $renbuts = Renbut::where('user_id', $request->user()->id)->latest()->paginate(10);
        return view('staff.renbuts.index', compact('renbuts'));
    }

    public function create()
    {
        return view('staff.renbuts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'total_estimated_budget' => 'required|numeric|min:0',
            'file_attachment' => 'required|file|mimes:pdf,xls,xlsx,doc,docx|max:10240', // 10MB max
        ]);

        $filePath = $request->file('file_attachment')->store('renbut_attachments', 'private');

        Renbut::create([
            'user_id' => $request->user()->id,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'status' => 'pending',
            'total_estimated_budget' => $validated['total_estimated_budget'],
            'file_path' => $filePath,
        ]);

        return redirect()->route('staff.renbuts.index')->with('success', 'Rencana Kebutuhan berhasil diajukan.');
    }

    public function show(Renbut $renbut)
    {
        abort_unless($renbut->user_id === request()->user()->id, 403);
        // $renbut->load('items'); // No longer needed
        return view('staff.renbuts.show', compact('renbut'));
    }

    public function downloadAttachment(Renbut $renbut)
    {
        abort_unless($renbut->user_id === request()->user()->id, 403);
        abort_unless($renbut->file_path && Storage::disk('private')->exists($renbut->file_path), 404, 'File lampiran tidak ditemukan.');

        return Storage::disk('private')->download($renbut->file_path);
    }

    public function downloadResponse(Renbut $renbut)
    {
        abort_unless($renbut->user_id === request()->user()->id, 403);
        abort_unless($renbut->response_file_path && Storage::disk('private')->exists($renbut->response_file_path), 404, 'File balasan tidak ditemukan.');

        return Storage::disk('private')->download($renbut->response_file_path);
    }
}
