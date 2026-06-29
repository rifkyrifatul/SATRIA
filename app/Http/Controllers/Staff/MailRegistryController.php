<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\MailRegistry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MailRegistryController extends Controller
{
    private const STORAGE_DISK = 'private';
    
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $query = MailRegistry::with(['uploader', 'dispositions' => function($q) use ($userId) {
            $q->where('user_id', $userId);
        }])
        ->whereHas('dispositions', function($q) use ($userId) {
            $q->where('user_id', $userId);
        })
        ->latest('date');

        if ($request->filled('type') && in_array($request->type, ['masuk', 'keluar'])) {
            $query->where('type', $request->type);
        }

        if ($request->filled('year')) {
            $query->whereYear('date', $request->year);
        }

        if ($request->filled('month')) {
            $query->whereMonth('date', $request->month);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('origin_destination', 'like', "%{$search}%");
            });
        }

        $mailRegistries = $query->paginate(15)->withQueryString();

        return view('staff.mail_registries.index', compact('mailRegistries'));
    }

    public function download(Request $request, MailRegistry $mailRegistry)
    {
        abort_unless(
            Storage::disk(self::STORAGE_DISK)->exists($mailRegistry->file_path),
            404,
            'File surat tidak ditemukan.'
        );

        // Tracking status "dibaca" telah dihapus sesuai permintaan.

        $downloadName = Str::slug($mailRegistry->subject) . '_' . $mailRegistry->date->format('Ymd') . '.pdf';

        return Storage::disk(self::STORAGE_DISK)->download(
            $mailRegistry->file_path,
            $downloadName
        );
    }
}
