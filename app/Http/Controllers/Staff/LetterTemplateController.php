<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\LetterTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LetterTemplateController extends Controller
{
    /**
     * Display a listing of the templates for staff.
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

        $templates = $query->latest()->paginate(12)->withQueryString();
        $formats = LetterTemplate::select('file_extension')->distinct()->whereNotNull('file_extension')->pluck('file_extension');

        return view('staff.letter_templates.index', compact('templates', 'formats'));
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
