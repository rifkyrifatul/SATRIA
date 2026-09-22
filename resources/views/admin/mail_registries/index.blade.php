@extends('layouts.app')

@section('title', 'ARSIP SURAT EKSTERNAL')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">Daftar Surat Terdaftar</h1>
            <p class="text-sm text-slate-500 mt-1">Daftar rekapan surat masuk dan surat keluar.</p>
        </div>
        @if(Auth::user()->isSuperAdmin() || (Auth::user()->isAdmin() && Auth::user()->admin_level === 'admin_3'))
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ (Auth::user()->isSuperAdmin() ? route('super_admin.mail_registries.recap_pdf') : route('admin.mail_registries.recap_pdf')) . '?' . http_build_query(request()->all()) }}"
               class="px-4 py-2.5 text-sm font-bold bg-rose-600 hover:bg-rose-700 text-white rounded-xl transition-all shadow-sm flex items-center gap-2"
               title="Unduh Rekap Bulanan Format PDF">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                Rekap PDF
            </a>
            <a href="{{ (Auth::user()->isSuperAdmin() ? route('super_admin.mail_registries.recap_excel') : route('admin.mail_registries.recap_excel')) . '?' . http_build_query(request()->all()) }}"
               class="px-4 py-2.5 text-sm font-bold bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl transition-all shadow-sm flex items-center gap-2"
               title="Unduh Rekap Bulanan Format Excel/CSV">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Rekap Excel
            </a>
            <a href="{{ Auth::user()->isSuperAdmin() ? route('super_admin.mail_registries.create') : route('admin.mail_registries.create') }}" class="px-5 py-2.5 text-sm font-bold bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-colors shadow-sm inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Surat Baru
            </a>
        </div>
        @endif
    </div>

    {{-- Filter Bar --}}
    <form method="GET" action="{{ Auth::user()->isSuperAdmin() ? route('super_admin.mail_registries.index') : route('admin.mail_registries.index') }}"
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
        <div class="relative w-full md:w-36">
            <select name="type" class="w-full appearance-none pl-4 pr-9 py-2.5 text-sm border border-slate-300 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-slate-50 dark:bg-slate-900/50 text-slate-600 dark:text-slate-400 transition-colors">
                <option value="">Semua Jenis</option>
                <option value="masuk" {{ request('type') == 'masuk' ? 'selected' : '' }}>Surat Masuk</option>
                <option value="keluar" {{ request('type') == 'keluar' ? 'selected' : '' }}>Surat Keluar</option>
            </select>
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </div>
        </div>

        {{-- Filter Bulan --}}
        <div class="relative w-full md:w-40">
            <select name="month" class="w-full appearance-none pl-4 pr-9 py-2.5 text-sm border border-slate-300 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-slate-50 dark:bg-slate-900/50 text-slate-600 dark:text-slate-400 transition-colors">
                <option value="">Semua Bulan</option>
                @foreach(range(1, 12) as $m)
                    <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                @endforeach
            </select>
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </div>
        </div>

        {{-- Filter Tahun --}}
        <div class="relative w-full md:w-36">
            <select name="year" class="w-full appearance-none pl-4 pr-9 py-2.5 text-sm border border-slate-300 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-slate-50 dark:bg-slate-900/50 text-slate-600 dark:text-slate-400 transition-colors">
                <option value="">Semua Tahun</option>
                @php $currentYear = date('Y'); @endphp
                @for($y = $currentYear; $y >= $currentYear - 5; $y--)
                    <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </div>
        </div>

        <div class="flex gap-2">
            @if(request('search') || request('type') || request('month') || request('year'))
                <a href="{{ Auth::user()->isSuperAdmin() ? route('super_admin.mail_registries.index') : route('admin.mail_registries.index') }}" class="px-5 py-2.5 text-sm font-bold text-slate-600 hover:text-slate-900 border border-slate-200 rounded-xl bg-white hover:bg-slate-100 flex items-center shadow-sm">Reset</a>
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
                        <th class="text-left px-6 py-3.5 text-[10px] font-extrabold text-white uppercase tracking-wider hidden lg:table-cell">Tanggal Surat</th>
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
                                <span class="text-xs text-slate-700 dark:text-slate-300 font-medium">
                                    {{ $mail->origin_destination }}
                                </span>
                            </td>
                            <td class="px-6 py-4 hidden lg:table-cell">
                                <div class="text-xs font-bold text-slate-800 dark:text-slate-200">
                                    {{ $mail->date->format('d M Y') }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ Auth::user()->isSuperAdmin() ? route('super_admin.mail_registries.download', $mail) : route('admin.mail_registries.download', $mail) }}"
                                       onclick="Toast.fire({icon: 'success', title: 'File berhasil diunduh!'})"
                                       class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-emerald-50 text-emerald-600 hover:text-white hover:bg-emerald-600 transition-all shadow-sm"
                                       title="Unduh Surat">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    </a>
                                    
                                    @if(Auth::user()->isSuperAdmin() || (Auth::user()->isAdmin() && Auth::user()->admin_level === 'admin_3'))
                                    <a href="{{ Auth::user()->isSuperAdmin() ? route('super_admin.mail_registries.disposition', $mail) : route('admin.mail_registries.disposition', $mail) }}"
                                       class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-indigo-50 text-indigo-600 hover:text-white hover:bg-indigo-600 transition-all shadow-sm"
                                       title="Disposisi Surat">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                    </a>
                                    <a href="{{ Auth::user()->isSuperAdmin() ? route('super_admin.mail_registries.edit', $mail) : route('admin.mail_registries.edit', $mail) }}"
                                       class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-amber-50 text-amber-600 hover:text-white hover:bg-amber-600 transition-all shadow-sm"
                                       title="Edit Data">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form action="{{ Auth::user()->isSuperAdmin() ? route('super_admin.mail_registries.destroy', $mail) : route('admin.mail_registries.destroy', $mail) }}" method="POST" class="inline" onsubmit="return confirmDelete(event, 'Yakin ingin menghapus surat ini dari Buku Agenda?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-rose-50 text-rose-600 hover:text-white hover:bg-rose-600 transition-all shadow-sm" title="Hapus Data">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-16 px-6 text-center">
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
