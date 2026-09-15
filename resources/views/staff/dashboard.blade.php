@extends('layouts.app')

@section('title', 'DASHBOARD')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <div class="mb-6">
        <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">Ikhtisar Surat Anda</h1>
        <p class="text-sm font-medium text-slate-500 mt-1">Pantau status seluruh surat dan dokumen yang telah Anda ajukan.</p>
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        {{-- Total --}}
        <div class="bg-white dark:bg-slate-800 p-6 rounded-xl border-2 border-slate-300 dark:border-slate-600 shadow-sm flex items-center gap-4 hover:shadow-md transition-shadow group">
            <div class="w-14 h-14 rounded-full bg-slate-900 text-white flex items-center justify-center shadow-sm group-hover:scale-105 transition-transform">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            </div>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Total Surat</p>
                <p class="text-3xl font-extrabold text-slate-900 dark:text-white mt-0.5">{{ $stats['total'] }}</p>
            </div>
        </div>
        
        {{-- Pending --}}
        <div class="bg-white dark:bg-slate-800 p-6 rounded-xl border border-amber-200 shadow-sm flex items-center gap-4 hover:shadow-md hover:border-amber-300 transition-all group">
            <div class="w-14 h-14 rounded-full bg-amber-50 text-amber-500 border border-amber-200 flex items-center justify-center shadow-sm group-hover:scale-105 transition-transform">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-amber-600">Menunggu</p>
                <p class="text-3xl font-extrabold text-amber-900 mt-0.5">{{ $stats['pending'] }}</p>
            </div>
        </div>

        {{-- Revision --}}
        <div class="bg-white dark:bg-slate-800 p-6 rounded-xl border border-rose-200 shadow-sm flex items-center gap-4 hover:shadow-md hover:border-rose-300 transition-all group">
            <div class="w-14 h-14 rounded-full bg-rose-50 text-rose-500 border border-rose-200 flex items-center justify-center shadow-sm group-hover:scale-105 transition-transform">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            </div>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-rose-600">Perlu Direvisi</p>
                <p class="text-3xl font-extrabold text-rose-900 mt-0.5">{{ $stats['revision'] }}</p>
            </div>
        </div>

        {{-- Approved --}}
        <div class="bg-white dark:bg-slate-800 p-6 rounded-xl border border-emerald-200 shadow-sm flex items-center gap-4 hover:shadow-md hover:border-emerald-300 transition-all group">
            <div class="w-14 h-14 rounded-full bg-emerald-50 text-emerald-500 border border-emerald-200 flex items-center justify-center shadow-sm group-hover:scale-105 transition-transform">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-600">Disetujui</p>
                <p class="text-3xl font-extrabold text-emerald-900 mt-0.5">{{ $stats['approved'] }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
        {{-- ========================================================= --}}
        {{-- PANEL SURAT TERBARU (KIRI - COL 2) --}}
        {{-- ========================================================= --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-slate-800 border-2 border-slate-300 dark:border-slate-600 rounded-xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-indigo-700 dark:border-indigo-900 flex flex-col sm:flex-row items-start sm:items-center justify-between bg-indigo-600 dark:bg-indigo-800 gap-4">
                    <h3 class="text-base font-extrabold text-white uppercase tracking-wider flex items-center gap-2">
                        <div class="w-1.5 h-5 bg-white/30 rounded-full"></div>
                        Surat Terbaru Anda
                    </h3>
                    <div class="flex items-center gap-3 w-full sm:w-auto">
                        <form action="{{ route('staff.dashboard') }}" method="GET" class="w-full sm:w-auto">
                            <select name="category_id" onchange="this.form.submit()"
                                    class="w-full text-xs font-bold border-slate-300 dark:border-slate-600 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-slate-600 dark:text-slate-400 bg-white dark:bg-slate-800 shadow-sm py-1.5 px-3">
                                <option value="">Semua Alur Pengajuan</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                </div>

                <div class="p-0">
                    @if($letters->isEmpty())
                        <div class="p-8 text-center">
                            <div class="w-16 h-16 bg-slate-50 dark:bg-slate-900/50 rounded-full flex items-center justify-center mx-auto mb-4 border border-slate-100 shadow-sm">
                                <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <p class="text-slate-700 dark:text-slate-300 font-bold">Belum ada surat</p>
                            <p class="text-slate-500 font-medium text-sm mt-1">Anda belum pernah mengajukan surat apapun.</p>
                        </div>
                    @else
                        <ul class="divide-y-2 divide-slate-200 dark:divide-slate-700">
                            @foreach($letters as $letter)
                                <li>
                                    <div class="flex items-start gap-4 p-5 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group relative">
                                        <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 {{ $letter->file_extension === 'pdf' ? 'bg-rose-100 text-rose-600 border border-rose-200' : 'bg-indigo-100 text-indigo-600 border border-indigo-200' }} shadow-sm">
                                            <span class="text-[10px] font-extrabold uppercase tracking-widest">{{ $letter->file_extension }}</span>
                                        </div>
                                        
                                        <div class="flex-1 min-w-0">
                                            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3">
                                                <div class="min-w-0">
                                                    <a href="{{ route('staff.letters.show', $letter) }}" class="text-base font-extrabold text-slate-900 dark:text-white truncate group-hover:text-indigo-600 transition-colors before:absolute before:inset-0 block">{{ $letter->title }}</a>
                                                    <p class="text-xs font-medium text-slate-500 mt-1 truncate">
                                                        @if($letter->letter_number)
                                                            <span class="text-indigo-600 font-bold bg-indigo-50 dark:bg-indigo-900/30 px-1.5 py-0.5 rounded border border-indigo-100 dark:border-indigo-800">{{ $letter->letter_number }}</span>
                                                        @else
                                                            <span class="text-slate-400 italic">Belum bernomor</span>
                                                        @endif
                                                        @if($letter->category)
                                                            <span class="mx-1.5 text-slate-300">•</span>
                                                            <span class="text-slate-600 dark:text-slate-400">{{ $letter->category->name }}</span>
                                                        @endif
                                                    </p>
                                                </div>
                                                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider whitespace-nowrap bg-slate-100 dark:bg-slate-700 px-2.5 py-1 rounded-md border border-slate-200 dark:border-slate-600">{{ $letter->created_at->diffForHumans() }}</span>
                                            </div>
                                            
                                            <div class="flex flex-wrap items-center gap-3 mt-4 z-10 relative">
                                                <x-status-badge :status="$letter->status" />
                                                

                                                <a href="{{ route('staff.letters.show', $letter) }}" 
                                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 hover:text-indigo-600 transition-colors shadow-sm ml-auto">
                                                    Detail
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
                
                @if($hasMoreLetters)
                    <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-700/50 text-center pb-4">
                        <a href="{{ route('staff.letters.index') }}" class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 uppercase tracking-wider flex items-center justify-center gap-1 transition-colors group">
                            Lihat Selengkapnya 
                            <svg class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    </div>
                @endif
            </div>
        </div>

        {{-- ========================================================= --}}
        {{-- PANEL TEMPLATE SURAT (KANAN - COL 1) --}}
        {{-- ========================================================= --}}
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-slate-800 border-2 border-slate-300 dark:border-slate-600 rounded-xl shadow-sm flex flex-col">
                <div class="px-6 py-4 border-b border-indigo-700 dark:border-indigo-900 flex items-center justify-between bg-indigo-600 dark:bg-indigo-800">
                    <h3 class="text-base font-extrabold text-white uppercase tracking-wider flex items-center gap-2">
                        <div class="w-1.5 h-5 bg-white/30 rounded-full"></div>
                        Template Surat
                    </h3>
                    <a href="{{ route('staff.letter_templates.index') }}" class="text-xs font-bold text-indigo-100 hover:text-white">Semua &rarr;</a>
                </div>
                
                <div class="flex-1 p-6 overflow-y-auto" style="max-height: 500px;">
                    @if($letterTemplates && $letterTemplates->isNotEmpty())
                        <ul class="space-y-4">
                            @foreach($letterTemplates->take(5) as $template)
                                <li class="flex items-start gap-3 p-4 border border-slate-200 dark:border-slate-700 rounded-xl hover:border-indigo-300 hover:bg-indigo-50/50 dark:hover:bg-indigo-900/20 transition-all group relative">
                                    <div class="flex-shrink-0 w-10 h-10 rounded-xl {{ $template->file_extension === 'pdf' ? 'bg-rose-50 text-rose-600 border border-rose-200' : 'bg-indigo-50 text-indigo-600 border border-indigo-200' }} flex items-center justify-center shadow-sm">
                                        <span class="text-[10px] font-black uppercase tracking-widest">{{ $template->file_extension }}</span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-sm font-bold text-slate-900 dark:text-white truncate group-hover:text-indigo-700 transition-colors">
                                            {{ $template->title }}
                                        </h4>
                                        @if($template->description)
                                            <p class="text-[11px] font-medium text-slate-500 mt-0.5 line-clamp-2 leading-relaxed">{{ $template->description }}</p>
                                        @endif
                                        <a href="{{ route('staff.letter_templates.download', $template) }}" onclick="Toast.fire({icon: 'success', title: 'File berhasil diunduh!'})" class="mt-2.5 inline-flex items-center gap-1.5 text-[10px] uppercase font-bold text-indigo-600 bg-indigo-50 dark:bg-indigo-900/30 px-2 py-1 rounded border border-indigo-100 hover:bg-indigo-600 hover:text-white hover:border-indigo-600 transition-all z-10 relative">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                            Unduh
                                        </a>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-center py-8">
                            <div class="w-12 h-12 bg-slate-50 dark:bg-slate-900/50 rounded-full flex items-center justify-center mx-auto mb-3 border border-slate-100 dark:border-slate-700 shadow-sm">
                                <svg class="w-6 h-6 text-slate-300 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/></svg>
                            </div>
                            <p class="text-slate-600 dark:text-slate-400 font-bold text-sm">Belum ada template</p>
                            <p class="text-slate-500 font-medium text-xs mt-1">Admin belum menambahkan template surat.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
