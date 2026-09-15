<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Spj;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SpjController extends Controller
{
    private const STORAGE_DIR = 'spjs';

    private function authorizeSpjAccess(Spj $spj)
    {
        $user = auth()->user();
        if ($user->role === 'super_admin') {
            return;
        }
        if ($spj->admin_level !== $user->admin_level) {
            abort(403, 'Anda tidak memiliki akses ke berkas SPJ ini.');
        }
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Spj::with('uploader')->latest();

        // Scope filter: Admin (PROGAR/PEKAS) hanya dapat melihat berkas unit mereka sendiri
        if ($user->role === 'admin') {
            $query->where('admin_level', $user->admin_level);
        } elseif ($request->filled('admin_level')) {
            $query->where('admin_level', $request->admin_level);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('spj_number', 'like', "%{$search}%")
                  ->orWhere('file_original_name', 'like', "%{$search}%");
            });
        }

        // Filter tanggal mulai dan tanggal akhir
        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        $spjs = $query->paginate(15)->withQueryString();

        return view('admin.spjs.index', compact('spjs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'spj_number'  => 'nullable|string|max:255',
            'date'        => 'required|date',
            'description' => 'nullable|string',
            'file'        => 'required|file|mimes:pdf|max:25600', // max 25MB, format PDF
        ], [
            'title.required' => 'Judul SPJ wajib diisi.',
            'date.required'  => 'Tanggal SPJ wajib diisi.',
            'file.required'  => 'File berkas SPJ format PDF wajib diunggah.',
            'file.mimes'     => 'Format file berkas SPJ harus berformat PDF.',
            'file.max'       => 'Ukuran file PDF tidak boleh melebihi 25MB.',
        ]);

        $file = $request->file('file');
        $filePath = $file->store(self::STORAGE_DIR, 'public');

        $user = auth()->user();

        Spj::create([
            'title'              => $validated['title'],
            'spj_number'         => $validated['spj_number'] ?? null,
            'type'               => 'Dokumen SPJ',
            'date'               => $validated['date'],
            'description'        => $validated['description'] ?? null,
            'file_path'          => $filePath,
            'file_original_name' => $file->getClientOriginalName(),
            'file_size'          => $file->getSize(),
            'admin_level'        => $user->admin_level ?? ($user->role === 'super_admin' ? 'super_admin' : 'admin_1'),
            'uploaded_by'        => $user->id,
        ]);

        $prefix = $user->role === 'super_admin' ? 'super_admin' : 'admin';
        return redirect()->route($prefix . '.spjs.index')->with('success', 'Berkas SPJ PDF berhasil diunggah dan disimpan.');
    }

    public function update(Request $request, Spj $spj)
    {
        $this->authorizeSpjAccess($spj);

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'spj_number'  => 'nullable|string|max:255',
            'date'        => 'required|date',
            'description' => 'nullable|string',
            'file'        => 'nullable|file|mimes:pdf|max:25600',
        ], [
            'title.required' => 'Judul SPJ wajib diisi.',
            'date.required'  => 'Tanggal SPJ wajib diisi.',
            'file.mimes'     => 'Format file berkas SPJ harus berformat PDF.',
            'file.max'       => 'Ukuran file PDF tidak boleh melebihi 25MB.',
        ]);

        if ($request->hasFile('file')) {
            if (Storage::disk('public')->exists($spj->file_path)) {
                Storage::disk('public')->delete($spj->file_path);
            }

            $file = $request->file('file');
            $validated['file_path'] = $file->store(self::STORAGE_DIR, 'public');
            $validated['file_original_name'] = $file->getClientOriginalName();
            $validated['file_size'] = $file->getSize();
        }

        $spj->update($validated);

        $prefix = auth()->user()->role === 'super_admin' ? 'super_admin' : 'admin';
        return redirect()->route($prefix . '.spjs.index')->with('success', 'Data berkas SPJ berhasil diperbarui.');
    }

    public function destroy(Spj $spj)
    {
        $this->authorizeSpjAccess($spj);

        if (Storage::disk('public')->exists($spj->file_path)) {
            Storage::disk('public')->delete($spj->file_path);
        }

        $spj->forceDelete();

        $prefix = auth()->user()->role === 'super_admin' ? 'super_admin' : 'admin';
        return redirect()->route($prefix . '.spjs.index')->with('success', 'Berkas SPJ berhasil dihapus.');
    }

    public function download(Spj $spj)
    {
        $this->authorizeSpjAccess($spj);

        if (!Storage::disk('public')->exists($spj->file_path)) {
            abort(404, 'File berkas SPJ tidak ditemukan.');
        }

        return Storage::disk('public')->download($spj->file_path, $spj->file_original_name);
    }

    public function preview(Spj $spj)
    {
        $this->authorizeSpjAccess($spj);

        if (!Storage::disk('public')->exists($spj->file_path)) {
            abort(404, 'File berkas SPJ tidak ditemukan.');
        }

        return Storage::disk('public')->response($spj->file_path);
    }
}
