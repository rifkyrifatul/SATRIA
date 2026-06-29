@extends('layouts.app')

@section('title', 'SURAT MASUK & KELUAR')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">Daftar Surat Terdaftar</h1>
            <p class="text-sm text-slate-500 mt-1">Daftar rekapan surat masuk dan surat keluar.</p>
        </div>
    </div>

    {{-- Filter Bar --}}
    <form method="GET" action="{{ route('staff.mail_registries.index') }}"
          class="flex flex-col md:flex-row gap-3 bg-white dark:bg-slate-800 p-4 rounded-xl border-2 border-slate-300 dark:border-slate-600 shadow-sm mb-6">
        
        {{-- Search --}}
        <div class="relative flex-1">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari nomor, perihal, asal/tujuan..."
                   class="w-full pl-10 pr-4 py-2.5 text-sm border border-slate-300 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-slate-50 dark:bg-slate-900/50">
        </div>
        
        {{-- Filter Jenis --}}
        <select name="type" class="w-full md:w-32 py-2.5 px-4 text-sm border border-slate-300 dark:border-slate-600 rounded-xl bg-slate-50 dark:bg-slate-900/50">
            <option value="">Semua Jenis</option>
            <option value="masuk" {{ request('type') == 'masuk' ? 'selected' : '' }}>Surat Masuk</option>
            <option value="keluar" {{ request('type') == 'keluar' ? 'selected' : '' }}>Surat Keluar</option>
        </select>

        {{-- Filter Bulan --}}
        <select name="month" class="w-full md:w-32 py-2.5 px-4 text-sm border border-slate-300 dark:border-slate-600 rounded-xl bg-slate-50 dark:bg-slate-900/50">
            <option value="">Semua Bulan</option>
            @foreach(range(1, 12) as $m)
                <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
            @endforeach
        </select>

        {{-- Filter Tahun --}}
        <select name="year" class="w-full md:w-32 py-2.5 px-4 text-sm border border-slate-300 dark:border-slate-600 rounded-xl bg-slate-50 dark:bg-slate-900/50">
            <option value="">Semua Tahun</option>
            @php $currentYear = date('Y'); @endphp
            @for($y = $currentYear; $y >= $currentYear - 5; $y--)
                <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endfor
        </select>

        <div class="flex gap-2">
            @if(request('search') || request('type') || request('month') || request('year'))
                <a href="{{ route('staff.mail_registries.index') }}" class="px-5 py-2.5 text-sm font-bold text-slate-600 hover:text-slate-900 border border-slate-200 rounded-xl bg-white hover:bg-slate-100 flex items-center shadow-sm">Reset</a>
            @endif
            <button type="submit" class="px-5 py-2.5 text-sm font-bold bg-slate-900 text-white rounded-xl hover:bg-slate-800 transition-colors shadow-sm">Filter</button>
        </div>
    </form>

    {{-- Tabel --}}
    <div class="bg-white dark:bg-slate-800 rounded-xl border-2 border-slate-300 dark:border-slate-600 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-indigo-600 dark:bg-indigo-800">
                        <th class="text-center px-4 py-3.5 text-[10px] font-extrabold text-white uppercase tracking-wider w-12 rounded-tl-lg">No</th>
                        <th class="text-left px-6 py-3.5 text-[10px] font-extrabold text-white uppercase tracking-wider">Tipe</th>
                        <th class="text-left px-6 py-3.5 text-[10px] font-extrabold text-white uppercase tracking-wider">No. Surat & Perihal</th>
                        <th class="text-left px-6 py-3.5 text-[10px] font-extrabold text-white uppercase tracking-wider hidden md:table-cell">Asal / Tujuan</th>
                        <th class="text-left px-6 py-3.5 text-[10px] font-extrabold text-white uppercase tracking-wider hidden lg:table-cell w-64">Catatan Disposisi</th>
                        <th class="text-left px-6 py-3.5 text-[10px] font-extrabold text-white uppercase tracking-wider hidden xl:table-cell">Tanggal Surat</th>
                        <th class="text-right px-6 py-3.5 text-[10px] font-extrabold text-white uppercase tracking-wider rounded-tr-lg">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                    @forelse($mailRegistries as $mail)
                        <tr class="border-b border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-all duration-200 group last:border-0">
                            <td class="px-4 py-4 text-center text-slate-400 font-mono text-xs font-semibold">
                                {{ $mailRegistries->firstItem() + $loop->index }}
                            </td>
                            <td class="px-6 py-4">
                                @if($mail->type === 'masuk')
                                    <span class="px-2 py-1 bg-emerald-100 text-emerald-700 text-[10px] font-bold rounded-md uppercase tracking-wider">Surat Masuk</span>
                                @else
                                    <span class="px-2 py-1 bg-amber-100 text-amber-700 text-[10px] font-bold rounded-md uppercase tracking-wider">Surat Keluar</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-extrabold text-slate-900 dark:text-white line-clamp-2">
                                    {{ $mail->subject }}
                                </div>
                                <div class="text-[11px] font-mono font-bold text-slate-500 mt-1">
                                    {{ $mail->reference_number }}
                                </div>
                            </td>
                            <td class="px-6 py-4 hidden md:table-cell">
                                <span class="text-xs text-slate-700 dark:text-slate-300 font-medium block">
                                    {{ $mail->origin_destination }}
                                </span>
                            </td>
                            <td class="px-6 py-4 hidden lg:table-cell">
                                @php
                                    $disposition = $mail->dispositions->first();
                                @endphp
                                @if($disposition)
                                    <div class="p-2 bg-indigo-50/50 dark:bg-indigo-900/20 rounded border border-indigo-100 dark:border-indigo-800/50">
                                        <p class="text-[11px] text-indigo-700 dark:text-indigo-300 leading-relaxed">{{ $disposition->note ?: '-' }}</p>
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400 italic">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 hidden xl:table-cell">
                                <div class="text-xs font-bold text-slate-800 dark:text-slate-200">
                                    {{ $mail->date->format('d M Y') }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('staff.mail_registries.download', $mail) }}"
                                       onclick="Toast.fire({icon: 'success', title: 'File berhasil diunduh!'})"
                                       class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-emerald-50 text-emerald-600 hover:text-white hover:bg-emerald-600 transition-all shadow-sm"
                                       title="Unduh Surat">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-16 px-6 text-center">
                                <div class="w-16 h-16 bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700 rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm">
                                    <svg class="w-8 h-8 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <p class="text-slate-700 dark:text-slate-300 font-bold">Belum ada surat terdaftar</p>
                                <p class="text-slate-500 font-medium text-sm mt-1 mb-6">Belum ada data surat masuk atau keluar.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($mailRegistries->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $mailRegistries->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
