@extends('layouts.app')

@section('title', 'Detail Pengajuan Renbut')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <a href="{{ route('staff.renbuts.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-slate-500 hover:text-indigo-600 transition-colors mb-4">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali
            </a>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">{{ $renbut->title }}</h1>
            <div class="flex flex-wrap items-center gap-2 mt-2">
                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-md text-[11px] font-medium border border-slate-200 dark:border-slate-700">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>Diajukan pada {{ $renbut->created_at->format('d M Y, H:i') }}</span>
                </div>
            </div>
        </div>
        <div>
            @if($renbut->status === 'pending')
                <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold bg-amber-100 text-amber-700 uppercase tracking-wider border border-amber-200">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Menunggu Persetujuan
                </span>
            @elseif($renbut->status === 'approved')
                <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold bg-emerald-100 text-emerald-700 uppercase tracking-wider border border-emerald-200">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Disetujui
                </span>
            @else
                <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold bg-rose-100 text-rose-700 uppercase tracking-wider border border-rose-200">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    Ditolak
                </span>
            @endif
        </div>
    </div>

    @if($renbut->description)
    <div class="bg-white dark:bg-slate-800 rounded-xl border-2 border-slate-300 dark:border-slate-600 shadow-sm p-6">
        <h3 class="text-xs font-extrabold text-slate-500 uppercase tracking-wider mb-2">Deskripsi / Keterangan</h3>
        <p class="text-sm text-slate-700 dark:text-slate-300 whitespace-pre-wrap">{{ $renbut->description }}</p>
    </div>
    @endif

    {{-- Attachment & Budget --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-slate-800 rounded-xl border-2 border-slate-300 dark:border-slate-600 shadow-sm p-6 flex flex-col justify-center">
            <h3 class="text-xs font-extrabold text-slate-500 uppercase tracking-wider mb-2">Estimasi Total Anggaran</h3>
            <p class="text-2xl font-extrabold font-mono text-indigo-600 dark:text-indigo-400">Rp {{ number_format($renbut->total_estimated_budget, 0, ',', '.') }}</p>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-xl border-2 border-slate-300 dark:border-slate-600 shadow-sm p-6">
            <h3 class="text-xs font-extrabold text-slate-500 uppercase tracking-wider mb-2">Lampiran Rincian</h3>
            @if($renbut->file_path)
                <a href="{{ route('staff.renbuts.download_attachment', $renbut) }}" class="inline-flex items-center gap-3 p-3 bg-slate-50 hover:bg-slate-100 dark:bg-slate-900/50 dark:hover:bg-slate-900/80 border border-slate-200 dark:border-slate-700 rounded-xl transition-colors group w-full">
                    <div class="w-10 h-10 bg-white dark:bg-slate-800 text-slate-500 rounded-lg flex items-center justify-center shadow-sm border border-slate-200 dark:border-slate-700 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    </div>
                    <div>
                        <p class="font-bold text-slate-800 dark:text-slate-200 text-sm group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">Unduh File Lampiran</p>
                        <p class="text-[11px] text-slate-500 mt-0.5">Berisi daftar rinci pengajuan</p>
                    </div>
                </a>
            @else
                <p class="text-sm text-slate-500 italic">Tidak ada file lampiran.</p>
            @endif
        </div>
    </div>

    {{-- PROGAR Response Area --}}
    @if($renbut->status !== 'pending')
    <div class="bg-white dark:bg-slate-800 rounded-xl border-2 border-slate-300 dark:border-slate-600 shadow-sm overflow-hidden">
        <div class="p-4 bg-slate-50 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-700 flex items-center gap-2">
            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
            <h3 class="font-bold text-slate-800 dark:text-white">Tanggapan PROGAR</h3>
        </div>
        <div class="p-6 space-y-6">
            <div>
                <h4 class="text-xs font-extrabold text-slate-500 uppercase tracking-wider mb-2">Catatan dari PROGAR:</h4>
                <div class="p-4 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-700 dark:text-slate-300">
                    {{ $renbut->response_note ?: 'Tidak ada catatan khusus.' }}
                </div>
            </div>

            @if($renbut->response_file_path)
            <div>
                <h4 class="text-xs font-extrabold text-slate-500 uppercase tracking-wider mb-2">Dokumen / File Balasan:</h4>
                <a href="{{ route('staff.renbuts.download_response', $renbut) }}" class="inline-flex items-center gap-3 p-4 bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-900/30 dark:hover:bg-indigo-900/50 border border-indigo-200 dark:border-indigo-800 rounded-xl transition-colors group">
                    <div class="w-10 h-10 bg-indigo-600 text-white rounded-lg flex items-center justify-center shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    </div>
                    <div>
                        <p class="font-bold text-indigo-900 dark:text-indigo-300 text-sm group-hover:underline">Unduh File Dokumen</p>
                        <p class="text-xs text-indigo-600/70 dark:text-indigo-400/70 mt-0.5">Disertakan oleh Admin PROGAR</p>
                    </div>
                </a>
            </div>
            @endif
        </div>
    </div>
    @endif

</div>
@endsection
