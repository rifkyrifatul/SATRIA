<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LetterTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class LetterTemplateController extends Controller
{
    /**
     * Display a listing of the templates.
     */
    public function index(Request $request)
    {
        $query = LetterTemplate::with('uploader');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('format')) {
            $query->where('file_extension', $request->format);
        }

        $templates = $query->latest()->paginate(10)->withQueryString();
        $formats = LetterTemplate::select('file_extension')->distinct()->whereNotNull('file_extension')->pluck('file_extension');

        return view('admin.letter_templates.index', compact('templates', 'formats'));
    }

    /**
     * Show the form for creating a new template.
     */
    public function create()
    {
        // Hanya user dengan wewenang mengelola template (Super Admin / Admin SETUM)
        if (!Auth::user()->canManageTemplates()) {
            abort(403, 'Unauthorized action.');
        }

        return view('admin.letter_templates.create');
    }

    /**
     * Store a newly created template in storage.
     */
    public function store(Request $request)
    {
        if (!Auth::user()->canManageTemplates()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:10240', // max 10MB
        ]);

        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();
        $path = $file->store('letter_templates', 'private');

        LetterTemplate::create([
            'title' => $request->title,
            'description' => $request->description,
            'file_path' => $path,
            'file_extension' => strtolower($extension),
            'uploaded_by' => Auth::id(),
        ]);

        $routePrefix = Auth::user()->isSuperAdmin() ? 'super_admin' : 'admin';
        return redirect()->route("{$routePrefix}.letter_templates.index")->with('success', 'Template surat berhasil diunggah.');
    }

    /**
     * Show the form for editing the specified template.
     */
    public function edit(LetterTemplate $letterTemplate)
    {
        if (!Auth::user()->canManageTemplates()) {
            abort(403, 'Unauthorized action.');
        }

        return view('admin.letter_templates.edit', compact('letterTemplate'));
    }

    /**
     * Update the specified template in storage.
     */
    public function update(Request $request, LetterTemplate $letterTemplate)
    {
        if (!Auth::user()->canManageTemplates()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:10240',
        ]);

        $data = [
            'title' => $request->title,
            'description' => $request->description,
        ];

        if ($request->hasFile('file')) {
            // Hapus file lama jika ada
            if ($letterTemplate->file_path && Storage::disk('private')->exists($letterTemplate->file_path)) {
                Storage::disk('private')->delete($letterTemplate->file_path);
            }

            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            $path = $file->store('letter_templates', 'private');

            $data['file_path'] = $path;
            $data['file_extension'] = strtolower($extension);
        }

        $letterTemplate->update($data);

        $routePrefix = Auth::user()->isSuperAdmin() ? 'super_admin' : 'admin';
        return redirect()->route("{$routePrefix}.letter_templates.index")->with('success', 'Template surat berhasil diperbarui.');
    }

    /**
     * Remove the specified template from storage.
     */
    public function destroy(LetterTemplate $letterTemplate)
    {
        if (!Auth::user()->canManageTemplates()) {
            abort(403, 'Unauthorized action.');
        }

        $letterTemplate->delete();

        $routePrefix = Auth::user()->isSuperAdmin() ? 'super_admin' : 'admin';
        return redirect()->route("{$routePrefix}.letter_templates.index")->with('success', 'Template surat berhasil dihapus ke tong sampah.');
    }

    /**
     * Download the specified template file.
     */
    public function download(LetterTemplate $letterTemplate)
    {
        if (!Storage::disk('private')->exists($letterTemplate->file_path)) {
            return back()->with('error', 'File tidak ditemukan.');
        }

        return Storage::disk('private')->download($letterTemplate->file_path, $letterTemplate->title . '.' . $letterTemplate->file_extension);
    }
}
