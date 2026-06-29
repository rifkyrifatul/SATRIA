<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Renbut;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RenbutApprovalController extends Controller
{
    private const STORAGE_DISK = 'private';
    private const STORAGE_DIR = 'renbut_responses';

    public function index(Request $request)
    {
        $this->authorizeProgar(request()->user());
        
        $query = Renbut::with('user')->latest();
        
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $renbuts = $query->paginate(10)->withQueryString();
        $users = \App\Models\User::whereHas('renbuts')->orderBy('name')->get();

        return view('admin.renbuts.index', compact('renbuts', 'users'));
    }

    public function show(Renbut $renbut)
    {
        $this->authorizeProgar(request()->user());
        $renbut->load('user'); // removed 'items'
        return view('admin.renbuts.show', compact('renbut'));
    }

    public function downloadAttachment(Renbut $renbut)
    {
        $this->authorizeProgar(request()->user());
        abort_unless($renbut->file_path && Storage::disk(self::STORAGE_DISK)->exists($renbut->file_path), 404, 'File lampiran tidak ditemukan.');

        return Storage::disk(self::STORAGE_DISK)->download($renbut->file_path);
    }

    public function update(Request $request, Renbut $renbut)
    {
        $this->authorizeProgar($request->user());

        $validated = $request->validate([
            'status' => 'required|in:approved,rejected',
            'response_note' => 'nullable|string',
            'response_file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:10240',
        ]);

        if ($request->hasFile('response_file')) {
            if ($renbut->response_file_path && Storage::disk(self::STORAGE_DISK)->exists($renbut->response_file_path)) {
                Storage::disk(self::STORAGE_DISK)->delete($renbut->response_file_path);
            }

            $file = $request->file('response_file');
            $fileName = now()->format('Ymd_His') . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs(self::STORAGE_DIR, $fileName, self::STORAGE_DISK);
            $validated['response_file_path'] = $path;
        }

        unset($validated['response_file']);
        $renbut->update($validated);

        return redirect()->route('admin.renbuts.index')->with('success', 'Pengajuan Renbut berhasil di-' . $validated['status'] . '.');
    }

    private function authorizeProgar($user)
    {
        abort_unless(
            $user->isAdmin() && $user->admin_level === 'admin_1',
            403,
            'Hanya Admin PROGAR yang memiliki akses persetujuan Renbut.'
        );
    }
}
