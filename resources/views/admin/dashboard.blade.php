@extends('layouts.app')

@section('title', 'DASHBOARD')

@section('content')
<div class="max-w-7xl mx-auto space-y-8">

    {{-- ========================================================= --}}
    {{-- 1. RINGKASAN STATISTIK GLOBAL --}}
    {{-- ========================================================= --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <!-- Metrik 1: Total Seluruh Surat Masuk -->
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border-2 border-slate-300 dark:border-slate-600 border-l-4 border-l-indigo-500 p-6 flex items-center gap-4 hover:shadow-md hover:border-indigo-400 transition-all">
            <div class="w-14 h-14 rounded-full bg-slate-100 dark:bg-slate-700/50 flex items-center justify-center flex-shrink-0 text-slate-500">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Surat Masuk</p>
                <p class="text-2xl font-extrabold text-slate-800 dark:text-slate-200">{{ number_format($stats['total_letters']) }}</p>
            </div>
        </div>

        <!-- Metrik 2: Dalam Proses Verifikasi -->
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border-2 border-slate-300 dark:border-slate-600 border-l-4 border-l-amber-500 p-6 flex items-center gap-4 hover:shadow-md hover:border-amber-400 transition-all">
            <div class="w-14 h-14 rounded-full bg-amber-50 flex items-center justify-center flex-shrink-0 text-amber-500">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Sedang Verifikasi</p>
                <p class="text-2xl font-extrabold text-slate-800 dark:text-slate-200">{{ number_format($stats['pending']) }}</p>
            </div>
        </div>

        <!-- Metrik 3: Ditolak / Revisi -->
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border-2 border-slate-300 dark:border-slate-600 border-l-4 border-l-rose-500 p-6 flex items-center gap-4 hover:shadow-md hover:border-rose-400 transition-all">
            <div class="w-14 h-14 rounded-full bg-rose-50 flex items-center justify-center flex-shrink-0 text-rose-500">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Ditolak / Revisi</p>
                <p class="text-2xl font-extrabold text-slate-800 dark:text-slate-200">{{ number_format($stats['revision']) }}</p>
            </div>
        </div>

        <!-- Metrik 4: Selesai / Approved -->
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border-2 border-slate-300 dark:border-slate-600 border-l-4 border-l-emerald-500 p-6 flex items-center gap-4 hover:shadow-md hover:border-emerald-400 transition-all">
            <div class="w-14 h-14 rounded-full bg-emerald-50 flex items-center justify-center flex-shrink-0 text-emerald-500">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Selesai / Approved</p>
                <p class="text-2xl font-extrabold text-slate-800 dark:text-slate-200">{{ number_format($stats['approved']) }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- ========================================================= --}}
        {{-- TABEL SURAT MENUNGGU REVIEW --}}
        {{-- ========================================================= --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-slate-800 border-2 border-slate-300 dark:border-slate-600 rounded-xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-indigo-700 dark:border-indigo-900 flex items-center justify-between bg-indigo-600 dark:bg-indigo-800">
                    <h3 class="text-base font-extrabold text-white uppercase tracking-wider flex items-center gap-2">
                        <div class="w-1.5 h-5 bg-white/30 rounded-full"></div>
                        Surat Butuh Perhatian (Pending)
                    </h3>
                    <a href="{{ Auth::user()->isSuperAdmin() ? route('super_admin.letters.index') : route('admin.reviews.index') }}" class="text-xs font-bold text-indigo-100 hover:text-white">Lihat Semua &rarr;</a>
                </div>
                <div class="p-0">
                    @if($pendingLetters->isEmpty())
                        <div class="p-8 text-center">
                            <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <p class="text-slate-500 text-sm font-medium">Bagus! Tidak ada antrean surat saat ini.</p>
                        </div>
                    @else
                        <ul class="divide-y-2 divide-slate-200 dark:divide-slate-700">
                            @foreach($pendingLetters as $letter)
                                <li>
                                    <a href="{{ Auth::user()->isSuperAdmin() ? route('super_admin.letters.show', $letter) : route('admin.letters.show', $letter) }}" class="flex items-start gap-4 p-5 hover:bg-slate-50 transition-colors group">
                                        {{-- Avatar Pengirim --}}
                                        <div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-sm flex-shrink-0">
                                            {{ substr($letter->creator->name ?? 'S', 0, 1) }}
                                        </div>
                                        
                                        <div class="flex-1 min-w-0">
                                            {{-- Baris 1: Judul dan Waktu --}}
                                            <div class="flex items-start justify-between gap-3">
                                                <div class="min-w-0">
                                                    <h4 class="text-base font-extrabold text-slate-900 dark:text-white truncate group-hover:text-indigo-600 transition-colors">{{ $letter->title }}</h4>
                                                    <p class="text-xs font-medium text-slate-500 mt-1 truncate">
                                                        <span class="text-indigo-600 font-bold bg-indigo-50 dark:bg-indigo-900/30 px-1.5 py-0.5 rounded">{{ $letter->letter_number ?? 'Belum bernomor' }}</span>
                                                        @if($letter->category)
                                                            <span class="mx-1.5 text-slate-300">•</span>
                                                            <span class="text-slate-600 dark:text-slate-400">{{ $letter->category->name }}</span>
                                                        @endif
                                                    </p>
                                                </div>
                                                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider whitespace-nowrap bg-slate-100 dark:bg-slate-700 px-2.5 py-1 rounded-md">{{ $letter->created_at->diffForHumans() }}</span>
                                            </div>
                                            
                                            {{-- Baris 2: Status dan Pengirim --}}
                                            <div class="flex flex-wrap items-center gap-3 mt-4">
                                                <x-status-badge :status="$letter->status" />
                                                
                                                <div class="flex items-center text-xs text-slate-600 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-md px-2.5 py-1">
                                                    <svg class="w-3.5 h-3.5 mr-1.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                                    <span class="font-bold text-slate-800 dark:text-slate-200">{{ $letter->creator->name ?? '-' }}</span> 
                                                    <span class="mx-1.5 text-slate-300">/</span>
                                                    <span>{{ $letter->creator->division->name ?? 'Staff' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex-shrink-0 mt-3">
                                            <svg class="w-5 h-5 text-slate-300 group-hover:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                        </div>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                        @if($hasMorePending)
                            <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-700/50 text-center pb-4">
                                <a href="{{ Auth::user()->isSuperAdmin() ? route('super_admin.letters.index') : route('admin.reviews.index') }}" class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 uppercase tracking-wider flex items-center justify-center gap-1 transition-colors group">
                                    Lihat Selengkapnya 
                                    <svg class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </a>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        {{-- ========================================================= --}}
        {{-- 2. PANEL LOG AKTIVITAS GLOBAL --}}
        {{-- ========================================================= --}}
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-slate-800 border-2 border-slate-300 dark:border-slate-600 rounded-xl shadow-sm h-full flex flex-col">
                <div class="px-6 py-4 border-b border-indigo-700 dark:border-indigo-900 bg-indigo-600 dark:bg-indigo-800">
                    <h3 class="text-base font-extrabold text-white uppercase tracking-wider flex items-center gap-2">
                        <div class="w-1.5 h-5 bg-white/30 rounded-full"></div>
                        Aktivitas Sistem Terbaru
                    </h3>
                </div>
                
                <div class="flex-1 p-6 overflow-y-auto" style="max-height: 400px;">
                    @if($recentLogs->isEmpty())
                        <p class="text-sm text-slate-500 text-center py-4">Belum ada aktivitas.</p>
                    @else
                        <div class="space-y-0 px-2">
                            @foreach($recentLogs as $log)
                                @php
                                    $iconInfo = match($log->action) {
                                        'submit' => ['text' => 'text-blue-500', 'bg' => 'bg-blue-50', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>'],
                                        'approve' => ['text' => 'text-emerald-500', 'bg' => 'bg-emerald-50', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>'],
                                        'force_approve_by_super_admin' => ['text' => 'text-emerald-600', 'bg' => 'bg-emerald-100', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>'],
                                        'request_revision' => ['text' => 'text-amber-500', 'bg' => 'bg-amber-50', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>'],
                                        'reject' => ['text' => 'text-rose-500', 'bg' => 'bg-rose-50', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>'],
                                        default => ['text' => 'text-slate-400', 'bg' => 'bg-slate-50', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>']
                                    };
                                    $actionText = match($log->action) {
                                        'submit' => 'mengajukan',
                                        'approve' => 'menyetujui',
                                        'force_approve_by_super_admin' => 'menyetujui (force approve)',
                                        'request_revision' => 'meminta revisi',
                                        'reject' => 'menolak',
                                        default => strtolower($log->action_label ?? str_replace('_', ' ', $log->action))
                                    };
                                @endphp
                                <div class="flex items-center gap-3 py-3 border-b border-slate-100 dark:border-slate-700/50 last:border-0 last:pb-0">
                                    <div class="flex items-center justify-center w-7 h-7 rounded-full {{ $iconInfo['bg'] }} {{ $iconInfo['text'] }} shrink-0">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $iconInfo['icon'] !!}</svg>
                                    </div>
                                    <div class="flex-1 min-w-0 flex flex-col xl:flex-row xl:items-center justify-between gap-1 xl:gap-4">
                                        <p class="text-sm text-slate-600 dark:text-slate-400 truncate">
                                            <span class="font-bold text-slate-900 dark:text-white">{{ $log->user->name ?? 'Sistem' }}</span> 
                                            {{ $actionText }}
                                            @if($log->letter)
                                                <a href="{{ Auth::user()->isSuperAdmin() ? route('super_admin.letters.show', $log->letter_id) : route('admin.letters.show', $log->letter_id) }}" class="font-bold text-indigo-600 hover:text-indigo-800 hover:underline">"{{ Str::limit($log->letter->title, 35) }}"</a>
                                            @endif
                                        </p>
                                        <span class="text-[11px] font-medium text-slate-400 whitespace-nowrap shrink-0">{{ $log->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-700/50 text-center">
                            <a href="{{ Auth::user()->isSuperAdmin() ? route('super_admin.activity_logs.index') : '#' }}" class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 uppercase tracking-wider flex items-center justify-center gap-1 transition-colors group">
                                Lihat Selengkapnya 
                                <svg class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
