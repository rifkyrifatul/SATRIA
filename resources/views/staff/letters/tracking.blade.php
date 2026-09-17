@extends('layouts.app')

@section('title', 'POSISI SURAT & TRACKING')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                <svg class="w-7 h-7 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                Posisi Surat & Tracking Alur
            </h1>
            <p class="text-sm font-medium text-slate-500 mt-1">Pantau posisi tahapan persetujuan dan riwayat alur pengajuan surat Anda secara real-time.</p>
        </div>
        <a href="{{ route('staff.letters.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-900 text-white text-sm font-bold rounded-xl hover:bg-slate-800 transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Ajukan Surat Baru
        </a>
    </div>

    {{-- ── Stat Counters ── --}}
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
        <a href="{{ route('staff.tracking.index') }}" class="bg-white dark:bg-slate-800 p-4 rounded-xl border-2 {{ !request('status') ? 'border-indigo-500 bg-indigo-50/20' : 'border-slate-200 dark:border-slate-700' }} shadow-sm transition-all hover:shadow-md">
            <p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Total Surat</p>
            <p class="text-2xl font-black text-slate-900 dark:text-white mt-1">{{ $stats['total'] }}</p>
        </a>
        <a href="{{ route('staff.tracking.index', ['status' => 'pending']) }}" class="bg-white dark:bg-slate-800 p-4 rounded-xl border-2 {{ request('status') === 'pending' ? 'border-indigo-500 bg-indigo-50/20' : 'border-slate-200 dark:border-slate-700' }} shadow-sm transition-all hover:shadow-md">
            <p class="text-[10px] font-extrabold uppercase tracking-wider text-amber-600">Diproses</p>
            <p class="text-2xl font-black text-amber-600 mt-1">{{ $stats['pending'] }}</p>
        </a>
        <a href="{{ route('staff.tracking.index', ['status' => 'revision']) }}" class="bg-white dark:bg-slate-800 p-4 rounded-xl border-2 {{ request('status') === 'revision' ? 'border-rose-500 bg-rose-50/20' : 'border-slate-200 dark:border-slate-700' }} shadow-sm transition-all hover:shadow-md">
            <p class="text-[10px] font-extrabold uppercase tracking-wider text-rose-600">Perlu Revisi</p>
            <p class="text-2xl font-black text-rose-600 mt-1">{{ $stats['revision'] }}</p>
        </a>
        <a href="{{ route('staff.tracking.index', ['status' => 'approved']) }}" class="bg-white dark:bg-slate-800 p-4 rounded-xl border-2 {{ request('status') === 'approved' ? 'border-emerald-500 bg-emerald-50/20' : 'border-slate-200 dark:border-slate-700' }} shadow-sm transition-all hover:shadow-md">
            <p class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-600">Disetujui</p>
            <p class="text-2xl font-black text-emerald-600 mt-1">{{ $stats['approved'] }}</p>
        </a>
        <a href="{{ route('staff.tracking.index', ['status' => 'rejected']) }}" class="bg-white dark:bg-slate-800 p-4 rounded-xl border-2 {{ request('status') === 'rejected' ? 'border-rose-500 bg-rose-50/20' : 'border-slate-200 dark:border-slate-700' }} shadow-sm transition-all hover:shadow-md col-span-2 sm:col-span-1">
            <p class="text-[10px] font-extrabold uppercase tracking-wider text-rose-700">Ditolak</p>
            <p class="text-2xl font-black text-rose-700 mt-1">{{ $stats['rejected'] }}</p>
        </a>
    </div>

    {{-- ── Filter Bar ── --}}
    <form method="GET" action="{{ route('staff.tracking.index') }}" class="flex flex-col sm:flex-row gap-3 bg-white dark:bg-slate-800 p-4 rounded-xl border-2 border-slate-300 dark:border-slate-600 shadow-sm">
        {{-- Search --}}
        <div class="relative flex-1">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari judul atau nomor surat..."
                   class="w-full pl-10 pr-4 py-2.5 text-sm border border-slate-300 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 dark:bg-slate-900/50 placeholder:text-slate-400 transition-colors">
        </div>
        
        {{-- Filter Category --}}
        <select name="category_id" onchange="this.form.submit()" class="w-full sm:w-48 py-2.5 px-4 text-sm border border-slate-300 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 dark:bg-slate-900/50 text-slate-600 dark:text-slate-400 transition-colors">
            <option value="">Semua Alur Pengajuan</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
            @endforeach
        </select>

        {{-- Filter Status --}}
        <select name="status" onchange="this.form.submit()" class="w-full sm:w-44 py-2.5 px-4 text-sm border border-slate-300 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 dark:bg-slate-900/50 text-slate-600 dark:text-slate-400 transition-colors">
            <option value="">Semua Status</option>
            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Diproses</option>
            <option value="revision" {{ request('status') === 'revision' ? 'selected' : '' }}>Revisi</option>
            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Disetujui</option>
            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
        </select>

        @if(request('search') || request('category_id') || request('status'))
            <a href="{{ route('staff.tracking.index') }}" class="px-5 py-2.5 text-sm font-bold text-slate-600 dark:text-slate-400 hover:text-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 flex items-center shadow-sm transition-colors whitespace-nowrap">Reset</a>
        @endif
        <button type="submit" class="px-5 py-2.5 text-sm font-bold bg-slate-900 text-white rounded-xl hover:bg-slate-800 transition-colors shadow-sm whitespace-nowrap">
            Cari
        </button>
    </form>

    {{-- ── Letters Tracking List ── --}}
    <div class="space-y-6">
        @forelse($letters as $letter)
            <div class="bg-white dark:bg-slate-800 rounded-2xl border-2 border-slate-300 dark:border-slate-600 shadow-sm overflow-hidden p-5 sm:p-6 transition-all hover:border-indigo-400">
                
                {{-- Letter Header Details --}}
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 mb-4 border-b border-slate-200 dark:border-slate-700">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 {{ $letter->file_extension === 'pdf' ? 'bg-rose-100 text-rose-600 border border-rose-200' : 'bg-indigo-100 text-indigo-600 border border-indigo-200' }} shadow-sm mt-0.5">
                            <span class="text-[10px] font-extrabold uppercase tracking-widest">{{ $letter->file_extension }}</span>
                        </div>
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <a href="{{ route('staff.tracking.show', $letter) }}" class="text-lg font-black text-slate-900 dark:text-white hover:text-indigo-600 transition-colors">
                                    {{ $letter->title }}
                                </a>
                                <x-status-badge :status="$letter->status" />
                            </div>
                            <div class="flex items-center gap-3 text-xs text-slate-500 mt-1 flex-wrap font-medium">
                                @if($letter->letter_number)
                                    <span class="text-indigo-600 font-bold bg-indigo-50 dark:bg-indigo-900/30 px-2 py-0.5 rounded border border-indigo-100 dark:border-indigo-800 font-mono">{{ $letter->letter_number }}</span>
                                @else
                                    <span class="text-slate-400 italic">Belum bernomor</span>
                                @endif
                                <span class="text-slate-300">•</span>
                                <span class="text-slate-700 dark:text-slate-300 font-semibold">{{ $letter->category->name ?? 'Surat Umum' }}</span>
                                <span class="text-slate-300">•</span>
                                <span>Diajukan {{ $letter->created_at->format('d M Y, H:i') }} WIB</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 justify-end">
                        <a href="{{ route('staff.tracking.show', $letter) }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 border border-indigo-200 rounded-xl text-xs font-bold transition-colors shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            Detail Surat
                        </a>
                        @if($letter->isRevision())
                            <a href="{{ route('staff.letters.edit', $letter) }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-amber-500 text-white hover:bg-amber-600 rounded-xl text-xs font-bold transition-colors shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                Perbaiki Revisi
                            </a>
                        @endif
                    </div>
                </div>

                {{-- Letter Tracking Stepper --}}
                <div class="mt-2">
                    <x-letter-stepper :letter="$letter" />
                </div>

                {{-- Latest Log Catatan --}}
                @php $latestLog = $letter->logs->first(); @endphp
                @if($latestLog && $latestLog->notes)
                    <div class="mt-2 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl p-3 flex items-start gap-3">
                        <svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                        <div class="text-xs">
                            <span class="font-extrabold text-slate-800 dark:text-slate-200">Catatan Terbaru ({{ $latestLog->user->name ?? 'Sistem' }} - {{ $latestLog->created_at->diffForHumans() }}):</span>
                            <p class="text-slate-600 dark:text-slate-400 font-medium italic mt-0.5">"{{ $latestLog->notes }}"</p>
                        </div>
                    </div>
                @endif
            </div>
        @empty
            <div class="bg-white dark:bg-slate-800 rounded-2xl border-2 border-slate-300 dark:border-slate-600 shadow-sm p-12 text-center">
                <div class="w-16 h-16 bg-indigo-50 dark:bg-slate-900 rounded-full flex items-center justify-center mx-auto mb-4 border border-indigo-100">
                    <svg class="w-8 h-8 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                </div>
                <h3 class="text-slate-900 dark:text-white font-extrabold text-base">Tidak Ada Data Tracking Surat</h3>
                <p class="text-slate-500 font-medium text-sm mt-1 mb-6">Belum ada surat yang sesuai dengan filter atau kata kunci pencarian Anda.</p>
                <a href="{{ route('staff.letters.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 text-white text-xs font-bold rounded-xl hover:bg-indigo-700 transition-colors shadow-sm">
                    Unggah Surat Pengajuan Baru
                </a>
            </div>
        @endforelse
    </div>

    @if($letters->hasPages())
        <div class="mt-6">
            {{ $letters->links() }}
        </div>
    @endif

</div>
@endsection
