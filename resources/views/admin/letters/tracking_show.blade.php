@extends('layouts.app')

@section('title', 'POSISI SURAT & TRACKING')

@section('content')
<div class="max-w-7xl mx-auto space-y-6" x-data="{ showPdfModal: false, pdfUrl: '', pdfTitle: '' }">

    @php
        $backRoute = Auth::user()->isSuperAdmin() ? route('super_admin.dashboard') : route('admin.reviews.index');
        $showRouteName = Auth::user()->isSuperAdmin() ? 'super_admin.tracking.show' : 'admin.tracking.show';
    @endphp

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div class="flex items-center gap-4">
            <a href="{{ $backRoute }}" class="p-2.5 bg-white dark:bg-slate-800 text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-slate-700/50 rounded-xl border-2 border-slate-200 dark:border-slate-700 transition-colors shadow-sm" title="Kembali">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                    <svg class="w-7 h-7 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                    Monitoring Tracking Posisi Surat
                </h1>
                <p class="text-sm font-medium text-slate-500 mt-1">Lihat posisi tahapan verifikasi, data pengaju, dan riwayat jejak audit surat ini.</p>
            </div>
        </div>

        @if(isset($allLetters) && $allLetters->count() > 1)
            <div class="flex items-center gap-2">
                <label for="letterSelectAdmin" class="text-xs font-bold text-slate-500 dark:text-slate-400 whitespace-nowrap">Pilih Surat:</label>
                <select id="letterSelectAdmin" onchange="if(this.value) window.location.href=this.value" 
                        class="px-3.5 py-2 bg-white dark:bg-slate-800 border-2 border-slate-300 dark:border-slate-600 rounded-xl text-xs font-bold text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 shadow-sm">
                    @foreach($allLetters as $item)
                        <option value="{{ route($showRouteName, $item) }}" {{ $item->id === $letter->id ? 'selected' : '' }}>
                            {{ Str::limit($item->title, 40) }} ({{ $item->status_label }})
                        </option>
                    @endforeach
                </select>
            </div>
        @endif
    </div>

    {{-- Info Card --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl border-2 border-slate-300 dark:border-slate-600 shadow-sm overflow-hidden">
        <div class="p-5 sm:p-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-5">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center flex-shrink-0 {{ $letter->file_extension === 'pdf' ? 'bg-rose-100 text-rose-600 border-2 border-rose-200' : 'bg-indigo-100 text-indigo-600 border-2 border-indigo-200' }} shadow-sm">
                        <span class="text-xs font-black uppercase tracking-widest">{{ $letter->file_extension }}</span>
                    </div>
                    <div>
                        <h2 class="text-lg font-black text-slate-900 dark:text-white mb-2">{{ $letter->title }}</h2>
                        <div class="flex flex-wrap items-center gap-3">
                            <x-status-badge :status="$letter->status" />
                            @if($letter->letter_number)
                                <span class="text-xs font-bold text-indigo-600 bg-indigo-50 border border-indigo-100 px-2.5 py-1 rounded-md font-mono uppercase tracking-wider">
                                    No. {{ $letter->letter_number }}
                                </span>
                            @endif
                            <span class="text-xs font-bold text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 px-2.5 py-1 rounded-md border border-slate-200 dark:border-slate-600">
                                Alur: {{ $letter->category->name ?? 'Surat Umum' }}
                            </span>
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 px-2.5 py-1 rounded-md border border-slate-200 dark:border-slate-600">
                                Pengaju: {{ $letter->creator->name ?? 'Staff' }}
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full md:w-auto">
                    @php
                        $detailRoute = Auth::user()->isSuperAdmin() ? route('super_admin.letters.show', $letter) : route('admin.letters.show', $letter);
                    @endphp
                    <a href="{{ $detailRoute }}" class="inline-flex justify-center items-center gap-2 px-5 py-2.5 text-xs uppercase tracking-wider font-bold bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-colors shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        Buka Review Surat
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Stepper Progress Tracking (ALUR POSISI SURAT) --}}
    <x-letter-stepper :letter="$letter" />

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        {{-- KIRI: TIMELINE LOG (2 KOLOM) --}}
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-slate-800 rounded-2xl border-2 border-slate-300 dark:border-slate-600 shadow-sm overflow-hidden h-full">
                <div class="px-6 py-4 border-b border-indigo-700 dark:border-indigo-900 bg-indigo-600 dark:bg-indigo-800">
                    <h3 class="text-base font-extrabold text-white uppercase tracking-wider flex items-center gap-2">
                        <div class="w-1.5 h-5 bg-white/30 rounded-full"></div>
                        Jejak Audit & Verifikasi
                    </h3>
                </div>
                
                <div class="relative px-5 py-6">
                    <div class="absolute left-[2.15rem] top-8 bottom-6 w-1 bg-slate-100 dark:bg-slate-700 rounded-full"></div>
                    <ul class="space-y-5">
                        @foreach($letter->logs->sortByDesc('created_at') as $log)
                            @php
                                $iconCfg = match($log->action) {
                                    'submit'           => ['bg' => 'bg-indigo-500', 'icon' => 'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12'],
                                    'approve'          => ['bg' => 'bg-emerald-500', 'icon' => 'M5 13l4 4L19 7'],
                                    'request_revision' => ['bg' => 'bg-rose-500', 'icon' => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z'],
                                    default            => ['bg' => 'bg-slate-400', 'icon' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                                };
                            @endphp
                            <li class="relative flex items-start gap-6 group">
                                <div class="flex-shrink-0 w-10 h-10 rounded-full {{ $iconCfg['bg'] }} border-4 border-white dark:border-slate-800 shadow flex items-center justify-center z-10 mt-1 transition-transform group-hover:scale-110">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $iconCfg['icon'] }}"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0 bg-white dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-700 rounded-2xl px-4 py-3 shadow-sm group-hover:border-indigo-300 dark:group-hover:border-indigo-600 transition-colors">
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 mb-1.5">
                                        <h4 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-wider">{{ $log->action_label }}</h4>
                                        <span class="text-[10px] font-black uppercase tracking-widest text-slate-500 bg-slate-100 dark:bg-slate-700/50 px-2.5 py-1 rounded-md border border-slate-200 dark:border-slate-600">{{ $log->created_at->format('d M Y, H:i') }}</span>
                                    </div>
                                    
                                    <div class="text-[11px] font-bold text-slate-500 dark:text-slate-400 flex items-center gap-1.5 mb-2">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        Oleh <span class="text-slate-800 dark:text-white">{{ $log->user->name ?? 'Sistem' }}</span>
                                    </div>

                                    @if($log->notes)
                                        <div class="px-3 py-2 bg-amber-50 dark:bg-amber-900/10 rounded-xl border border-amber-200 dark:border-amber-800 text-[11px] font-medium text-amber-900 dark:text-amber-200 leading-relaxed shadow-inner">
                                            "{{ $log->notes }}"
                                        </div>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        {{-- KANAN: FILE VERSIONING (1 KOLOM) --}}
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-slate-800 rounded-2xl border-2 border-slate-300 dark:border-slate-600 shadow-sm overflow-hidden h-full">
                <div class="px-6 py-4 border-b border-indigo-700 dark:border-indigo-900 flex items-center justify-between bg-indigo-600 dark:bg-indigo-800">
                    <h3 class="text-base font-extrabold text-white uppercase tracking-wider flex items-center gap-2">
                        <div class="w-1.5 h-5 bg-white/30 rounded-full"></div>
                        Riwayat Berkas
                    </h3>
                    <span class="text-[10px] font-black tracking-wider uppercase bg-indigo-500 text-white border border-indigo-400 px-2.5 py-1 rounded-md">{{ $letter->attachments->count() }} Versi</span>
                </div>
                
                <div class="p-5">
                    @if($letter->attachments->isEmpty())
                        <p class="text-sm text-slate-500 italic text-center py-4">Riwayat versi tidak tersedia untuk surat ini.</p>
                    @else
                        <div class="space-y-3">
                            @foreach($letter->attachments as $index => $attachment)
                                @php
                                    $typeLabel = match($attachment->file_type) {
                                        'original' => 'Draf Awal (Orisinal)',
                                        'staff_revision' => 'Revisi Staff',
                                        'progar_correction' => 'Koreksi PROGAR',
                                        'pekas_correction' => 'Koreksi PEKAS',
                                        'setum_final' => 'Dokumen Final',
                                        'superadmin_force_final' => 'Bypass Admin',
                                        default => 'Pembaruan'
                                    };
                                    $isLatest = $index === $letter->attachments->count() - 1;
                                    $downloadRoute = Auth::user()->isSuperAdmin() ? route('super_admin.letters.download', ['letter' => $letter->id, 'attachment_id' => $attachment->id]) : route('admin.letters.download', ['letter' => $letter->id, 'attachment_id' => $attachment->id]);
                                    $previewRoute = Auth::user()->isSuperAdmin() ? route('super_admin.letters.preview', ['letter' => $letter->id, 'attachment_id' => $attachment->id]) : route('admin.letters.preview', ['letter' => $letter->id, 'attachment_id' => $attachment->id]);
                                @endphp
                                <div class="flex flex-col gap-2.5 p-3.5 rounded-xl border-2 {{ $isLatest ? 'border-indigo-200 dark:border-indigo-800 bg-indigo-50/50 dark:bg-indigo-900/20' : 'border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50' }}">
                                    <div class="flex items-start gap-3">
                                        <div class="w-10 h-10 rounded-lg {{ $isLatest ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-200 dark:bg-slate-700 text-slate-500' }} flex items-center justify-center font-black text-xs flex-shrink-0">
                                            V{{ $index + 1 }}
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2 mb-1 flex-wrap">
                                                <p class="text-sm font-bold {{ $isLatest ? 'text-indigo-900 dark:text-indigo-100' : 'text-slate-800 dark:text-slate-200' }}">
                                                    {{ $typeLabel }}
                                                </p>
                                                @if($isLatest) 
                                                    <span class="text-[9px] font-black bg-indigo-100 text-indigo-700 border border-indigo-200 px-2 py-0.5 rounded-md uppercase tracking-widest">Baru</span> 
                                                @endif
                                            </div>
                                            <p class="text-[10px] font-medium text-slate-500 uppercase tracking-wider mb-1">
                                                {{ $attachment->created_at->format('d M Y, H:i') }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 mt-1">
                                        @if(strtolower(pathinfo($attachment->file_path, PATHINFO_EXTENSION)) === 'pdf')
                                            <button @click.prevent="pdfUrl = '{{ $previewRoute }}'; pdfTitle = '{{ $typeLabel }}'; showPdfModal = true"
                                                    class="inline-flex justify-center items-center gap-1 px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-700 hover:bg-indigo-100 border border-indigo-200 text-xs font-bold transition-colors shadow-sm flex-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                Lihat
                                            </button>
                                        @endif
                                        <a href="{{ $downloadRoute }}" class="inline-flex justify-center items-center gap-1 px-3 py-1.5 rounded-lg bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 text-xs font-bold transition-colors shadow-sm flex-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                            Unduh
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- PDF VIEWER MODAL --}}
    <div x-show="showPdfModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/80 backdrop-blur-sm" x-transition.opacity style="display: none;">
        <div class="bg-white dark:bg-slate-800 rounded-2xl w-full max-w-5xl h-[90vh] flex flex-col shadow-2xl overflow-hidden mx-4" @click.away="showPdfModal = false">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50">
                <div>
                    <h3 class="text-base font-bold text-slate-800 dark:text-white" x-text="pdfTitle">Preview Dokumen</h3>
                    <p class="text-xs text-slate-500">{{ $letter->title }}</p>
                </div>
                <button @click="showPdfModal = false" class="text-slate-400 hover:text-rose-500 p-2 rounded-xl transition-colors focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="flex-1 bg-slate-100 dark:bg-slate-900 relative">
                <iframe :src="pdfUrl" class="w-full h-full border-none relative z-10" title="PDF Viewer" x-show="pdfUrl"></iframe>
            </div>
        </div>
    </div>

</div>
@endsection
