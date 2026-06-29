@extends('layouts.app')

@section('title', 'RIWAYAT REVIEW SURAT')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <div class="mb-6">
        <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">Catatan Tinjauan</h1>
        <p class="text-sm text-slate-500 mt-1">Daftar histori keputusan (Disetujui/Ditolak) yang pernah Anda berikan pada pengajuan surat.</p>
    </div>

    {{-- ── Filter Bar ── --}}
    <form method="GET" action="{{ route('admin.reviews.history') }}"
          class="flex flex-col sm:flex-row gap-3 bg-white dark:bg-slate-800 p-4 rounded-xl border-2 border-slate-300 dark:border-slate-600 shadow-sm mb-6">
        {{-- Search --}}
        <div class="relative flex-1">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari judul atau nomor surat..."
                   class="w-full pl-10 pr-4 py-2.5 text-sm border border-slate-300 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 dark:bg-slate-900/50 placeholder:text-slate-400 transition-colors">
        </div>
        
        {{-- Filter Kategori --}}
        <select name="category" class="w-full sm:w-48 py-2.5 px-4 text-sm border border-slate-300 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 dark:bg-slate-900/50 text-slate-600 dark:text-slate-400 transition-colors">
            <option value="">Semua Kategori</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>

        {{-- Filter Divisi --}}
        <select name="division" class="w-full sm:w-48 py-2.5 px-4 text-sm border border-slate-300 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 dark:bg-slate-900/50 text-slate-600 dark:text-slate-400 transition-colors">
            <option value="">Semua Divisi</option>
            @foreach($divisions as $division)
                <option value="{{ $division->id }}" {{ request('division') == $division->id ? 'selected' : '' }}>
                    {{ $division->name }}
                </option>
            @endforeach
        </select>

        @if(request('search') || request('category') || request('division'))
            <a href="{{ route('admin.reviews.history') }}" class="px-5 py-2.5 text-sm font-bold text-slate-600 dark:text-slate-400 hover:text-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 flex items-center shadow-sm transition-colors whitespace-nowrap">Reset</a>
        @endif

        <button type="submit"
                class="px-5 py-2.5 text-sm font-bold bg-slate-900 text-white rounded-xl hover:bg-slate-800 transition-colors shadow-sm whitespace-nowrap">
            Cari & Filter
        </button>
    </form>

    {{-- ── Tabel ── --}}
    <div class="bg-white dark:bg-slate-800 rounded-xl border-2 border-slate-300 dark:border-slate-600 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-indigo-600 dark:bg-indigo-800">
                        <th class="text-center px-4 py-3.5 text-[10px] font-extrabold text-white uppercase tracking-wider w-12 rounded-tl-lg">No</th>
                        <th class="text-left px-6 py-3.5 text-[10px] font-extrabold text-white uppercase tracking-wider">Informasi Surat</th>
                        <th class="text-left px-6 py-3.5 text-[10px] font-extrabold text-white uppercase tracking-wider hidden md:table-cell">Pengaju</th>
                        <th class="text-left px-6 py-3.5 text-[10px] font-extrabold text-white uppercase tracking-wider hidden lg:table-cell">Waktu Dibuat</th>
                        <th class="text-left px-6 py-3.5 text-[10px] font-extrabold text-white uppercase tracking-wider">Status Terkini</th>
                        <th class="text-right px-6 py-3.5 text-[10px] font-extrabold text-white uppercase tracking-wider rounded-tr-lg">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                    @forelse($letters as $letter)
                            <tr class="border-b border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-all duration-200 group last:border-0">
                                <td class="px-4 py-4 text-center text-slate-400 font-mono text-xs font-semibold group-hover:text-indigo-500 transition-colors">
                                    {{ $letters->firstItem() + $loop->index }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-start gap-4">
                                        <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 shadow-sm border
                                                    {{ $letter->file_extension === 'pdf' ? 'bg-rose-50 border-rose-100 text-rose-500 group-hover:bg-rose-500 group-hover:text-white' : 'bg-indigo-50 border-indigo-100 text-indigo-500 group-hover:bg-indigo-500 group-hover:text-white' }} transition-colors">
                                            <span class="text-[10px] font-black uppercase tracking-widest">
                                                {{ $letter->file_extension }}
                                            </span>
                                        </div>
                                        <div>
                                            <a href="{{ route('admin.letters.show', $letter) }}"
                                               class="text-sm font-extrabold text-slate-900 dark:text-white hover:text-indigo-600 transition-colors line-clamp-1 mb-1">
                                                {{ $letter->title }}
                                            </a>
                                            <div class="flex items-center gap-2 mt-1">
                                                @if($letter->letter_number)
                                                    <span class="text-[10px] bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 px-2 py-0.5 rounded-md font-mono font-bold border border-slate-200 dark:border-slate-700 shadow-sm">{{ $letter->letter_number }}</span>
                                                @else
                                                    <span class="text-[10px] bg-slate-50 dark:bg-slate-800 text-slate-400 px-2 py-0.5 rounded-md italic border border-slate-100 dark:border-slate-700">Belum bernomor</span>
                                                @endif
                                                
                                                @if($letter->category)
                                                    <span class="text-[10px] bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 px-2 py-0.5 rounded-md font-bold">{{ $letter->category->name }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 hidden md:table-cell">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 flex items-center justify-center text-slate-700 dark:text-slate-300 text-xs font-extrabold shadow-sm flex-shrink-0">
                                            {{ substr($letter->creator->name ?? '?', 0, 1) }}
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-slate-900 dark:text-white text-xs font-extrabold truncate max-w-[150px]">
                                                {{ $letter->creator->name ?? '-' }}
                                            </span>
                                            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider truncate max-w-[150px] mt-0.5">
                                                {{ $letter->creator->division->name ?? 'Staff' }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 hidden lg:table-cell">
                                    <div class="flex items-start gap-2.5">
                                        <div class="w-7 h-7 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center border border-indigo-100 dark:border-indigo-800 flex-shrink-0">
                                            <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        </div>
                                        <div class="flex flex-col">
                                            <p class="text-slate-800 dark:text-slate-200 font-bold text-xs">{{ $letter->created_at->format('d M Y') }}</p>
                                            <p class="text-slate-500 text-[10px] font-bold mt-0.5">{{ $letter->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <x-status-badge :status="$letter->status" />
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.letters.show', $letter) }}"
                                           class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 hover:border-indigo-200 dark:hover:bg-indigo-900/30 transition-all shadow-sm"
                                           title="Lihat Detail">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-16 px-6 text-center">
                                <div class="w-16 h-16 bg-slate-50 dark:bg-slate-900/50 border border-slate-100 rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm">
                                    <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <p class="text-slate-700 dark:text-slate-300 font-bold">Belum ada riwayat review surat.</p>
                                <p class="text-slate-500 font-medium text-sm mt-1 mb-6">Surat yang Anda proses akan otomatis muncul di sini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($letters->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $letters->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
