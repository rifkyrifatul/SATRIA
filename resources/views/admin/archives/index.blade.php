@extends('layouts.app')

@section('title', 'ARSIP SURAT INTERNAL')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">Dokumen Tersimpan</h1>
            <p class="text-sm text-slate-500 mt-1">Arsip surat yang telah disetujui (Approved) secara final dan disimpan secara permanen.</p>
        </div>

        {{-- Tombol Export Rekap Bulanan --}}
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ (Auth::user()->isSuperAdmin() ? route('super_admin.archives.recap_pdf') : route('admin.archives.recap_pdf')) . '?' . http_build_query(request()->all()) }}"
               class="px-4 py-2.5 text-sm font-bold bg-rose-600 hover:bg-rose-700 text-white rounded-xl transition-all shadow-sm flex items-center gap-2"
               title="Unduh Rekap Bulanan Format PDF">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                Rekap PDF
            </a>
            <a href="{{ (Auth::user()->isSuperAdmin() ? route('super_admin.archives.recap_excel') : route('admin.archives.recap_excel')) . '?' . http_build_query(request()->all()) }}"
               class="px-4 py-2.5 text-sm font-bold bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl transition-all shadow-sm flex items-center gap-2"
               title="Unduh Rekap Bulanan Format Excel/CSV">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Rekap Excel
            </a>
        </div>
    </div>

    {{-- ── Filter Bar ── --}}
    <form method="GET" action="{{ Auth::user()->isSuperAdmin() ? route('super_admin.archives.index') : route('admin.archives.index') }}"
          class="flex flex-col md:flex-row gap-3 bg-white dark:bg-slate-800 p-4 rounded-xl border-2 border-slate-300 dark:border-slate-600 shadow-sm mb-6">
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
        
        {{-- Filter Bulan --}}
        <select name="month" class="w-full md:w-32 py-2.5 px-4 text-sm border border-slate-300 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 dark:bg-slate-900/50 text-slate-600 dark:text-slate-400 transition-colors">
            <option value="">Semua Bulan </option>
            @php
                $months = [
                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                ];
            @endphp
            @foreach($months as $num => $name)
                <option value="{{ $num }}" {{ request('month') == $num ? 'selected' : '' }}>{{ $name }}</option>
            @endforeach
        </select>

        {{-- Filter Tahun --}}
        <select name="year" class="w-full md:w-32 py-2.5 px-4 text-sm border border-slate-300 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 dark:bg-slate-900/50 text-slate-600 dark:text-slate-400 transition-colors">
            <option value="">Semua Tahun</option>
            @php $currentYear = date('Y'); @endphp
            @for($y = $currentYear; $y >= $currentYear - 5; $y--)
                <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endfor
        </select>

        {{-- Filter Alur Pengajuan --}}
        <select name="category" class="w-full md:w-36 py-2.5 px-4 text-sm border border-slate-300 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 dark:bg-slate-900/50 text-slate-600 dark:text-slate-400 transition-colors">
            <option value="">Semua Alur Pengajuan </option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>

        {{-- Filter Divisi --}}
        <select name="division" class="w-full md:w-36 py-2.5 px-4 text-sm border border-slate-300 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 dark:bg-slate-900/50 text-slate-600 dark:text-slate-400 transition-colors">
            <option value="">Semua Divisi </option>
            @foreach($divisions as $division)
                <option value="{{ $division->id }}" {{ request('division') == $division->id ? 'selected' : '' }}>
                    {{ $division->name }}
                </option>
            @endforeach
        </select>

        <div class="flex gap-2 whitespace-nowrap">
            @if(request('search') || request('month') || request('year') || request('category') || request('division'))
                <a href="{{ Auth::user()->isSuperAdmin() ? route('super_admin.archives.index') : route('admin.archives.index') }}" class="px-5 py-2.5 text-sm font-bold text-slate-600 dark:text-slate-400 hover:text-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 flex items-center shadow-sm transition-colors">Reset</a>
            @endif

            <button type="submit"
                    class="px-5 py-2.5 text-sm font-bold bg-slate-900 text-white rounded-xl hover:bg-slate-800 transition-colors shadow-sm">
                Filter
            </button>
        </div>
    </form>

    {{-- ── Tabel ── --}}
    <div class="bg-white dark:bg-slate-800 rounded-xl border-2 border-slate-300 dark:border-slate-600 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-indigo-600 dark:bg-indigo-800">
                        <th class="text-center px-4 py-3.5 text-[10px] font-extrabold text-white uppercase tracking-wider w-12 rounded-tl-lg">No</th>
                        <th class="text-left px-6 py-3.5 text-[10px] font-extrabold text-white uppercase tracking-wider">Judul Surat</th>
                        <th class="text-left px-6 py-3.5 text-[10px] font-extrabold text-white uppercase tracking-wider">Alur Pengajuan</th>
                        <th class="text-left px-6 py-3.5 text-[10px] font-extrabold text-white uppercase tracking-wider">No Surat</th>
                        <th class="text-left px-6 py-3.5 text-[10px] font-extrabold text-white uppercase tracking-wider hidden md:table-cell">Pengaju</th>
                        <th class="text-left px-6 py-3.5 text-[10px] font-extrabold text-white uppercase tracking-wider hidden lg:table-cell">Waktu Selesai</th>
                        <th class="text-left px-6 py-3.5 text-[10px] font-extrabold text-white uppercase tracking-wider">Status</th>
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
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 shadow-sm border
                                                {{ $letter->file_extension === 'pdf' ? 'bg-rose-50 border-rose-100 text-rose-500 group-hover:bg-rose-500 group-hover:text-white' : 'bg-indigo-50 border-indigo-100 text-indigo-500 group-hover:bg-indigo-500 group-hover:text-white' }} transition-colors">
                                        <span class="text-[10px] font-black uppercase tracking-widest">
                                            {{ $letter->file_extension }}
                                        </span>
                                    </div>
                                    <div>
                                        <a href="{{ Auth::user()->isSuperAdmin() ? route('super_admin.letters.show', $letter) : route('admin.letters.show', $letter) }}"
                                           class="text-sm font-extrabold text-slate-900 dark:text-white hover:text-indigo-600 transition-colors line-clamp-2">
                                            {{ $letter->title }}
                                        </a>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($letter->category)
                                    <span class="text-[10px] bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 px-2.5 py-1 rounded-md font-bold whitespace-nowrap">{{ $letter->category->name }}</span>
                                @else
                                    <span class="text-xs text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($letter->letter_number)
                                    <span class="text-xs bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 px-2.5 py-1 rounded-md font-mono font-bold border border-slate-200 dark:border-slate-700 shadow-sm whitespace-nowrap">{{ $letter->letter_number }}</span>
                                @else
                                    <span class="text-[10px] bg-slate-50 dark:bg-slate-800 text-slate-400 px-2 py-1 rounded-md italic border border-slate-100 dark:border-slate-700 whitespace-nowrap">Belum bernomor</span>
                                @endif
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
                                    <div class="w-7 h-7 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center border border-emerald-100 dark:border-emerald-800 flex-shrink-0">
                                        <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    </div>
                                    <div class="flex flex-col">
                                        <p class="text-slate-800 dark:text-slate-200 font-bold text-xs">{{ $letter->updated_at->format('d M Y') }}</p>
                                        <p class="text-slate-500 text-[10px] font-bold mt-0.5">{{ $letter->updated_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <x-status-badge :status="$letter->status" />
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ Auth::user()->isSuperAdmin() ? route('super_admin.letters.show', $letter) : route('admin.letters.show', $letter) }}"
                                       class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 hover:border-indigo-200 dark:hover:bg-indigo-900/30 transition-all shadow-sm"
                                       title="Lihat Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    @if($letter->final_file_path)
                                    <a href="{{ Auth::user()->isSuperAdmin() ? route('super_admin.letters.download_final', $letter) : route('admin.letters.download_final', $letter) }}"
                                       class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 text-emerald-600 hover:text-white hover:bg-emerald-600 hover:border-emerald-600 transition-all shadow-sm"
                                       title="Unduh Dokumen Final">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                        </svg>
                                    </a>
                                    @else
                                    <a href="{{ Auth::user()->isSuperAdmin() ? route('super_admin.letters.download', $letter) : route('admin.letters.download', $letter) }}"
                                       class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 text-emerald-600 hover:text-white hover:bg-emerald-600 hover:border-emerald-600 transition-all shadow-sm"
                                       title="Unduh Dokumen">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                        </svg>
                                    </a>
                                    @endif
                                    @if(Auth::user()->isSuperAdmin() || (Auth::user()->isAdmin() && Auth::user()->admin_level === 'admin_3'))
                                    <form action="{{ Auth::user()->isSuperAdmin() ? route('super_admin.archives.destroy', $letter) : route('admin.archives.destroy', $letter) }}"
                                          method="POST" class="inline" onsubmit="return confirmDelete(event, 'Yakin ingin menghapus surat ini dari arsip? Surat akan dipindahkan ke Tong Sampah.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-rose-50 dark:bg-rose-900/30 border border-rose-200 dark:border-rose-800 text-rose-600 hover:text-white hover:bg-rose-600 hover:border-rose-600 transition-all shadow-sm"
                                                title="Hapus dari Arsip">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-16 px-6 text-center">
                                <div class="w-16 h-16 bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700 rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm">
                                    <svg class="w-8 h-8 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <p class="text-slate-700 dark:text-slate-300 font-bold">Tidak ada surat di dalam arsip</p>
                                <p class="text-slate-500 font-medium text-sm mt-1 mb-6">Belum ada surat yang disetujui atau kata kunci pencarian tidak cocok.</p>
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
