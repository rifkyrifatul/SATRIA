@extends('layouts.app')

@section('title', 'SAMPAH SURAT')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <div class="mb-6 flex flex-col sm:flex-row sm:items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">Dokumen Terhapus</h1>
            <p class="text-sm font-medium text-slate-500 mt-1">Surat-surat yang ada di sini adalah surat yang telah dihapus (soft delete). Anda dapat memulihkannya atau menghapusnya secara permanen.</p>
        </div>
        @if($letters->total() > 0)
        <form action="{{ route('staff.trash.letters.empty_trash') }}" method="POST" onsubmit="confirmDelete(event, 'Peringatan: SELURUH surat dan file di tong sampah akan dihapus PERMANEN. Anda yakin?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-xl transition-colors shadow-sm whitespace-nowrap">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                Bersihkan Sampah
            </button>
        </form>
        @endif
    </div>

    {{-- ── Filter Bar ── --}}
    <form method="GET" action="{{ route('staff.trash.letters') }}" class="flex flex-col sm:flex-row gap-3 bg-white dark:bg-slate-800 p-4 rounded-xl border-2 border-slate-300 dark:border-slate-600 shadow-sm mb-6">
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
        
        {{-- Filter Alur Review --}}
        <select name="review_type" onchange="this.form.submit()" class="w-full sm:w-48 py-2.5 px-4 text-sm border border-slate-300 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 dark:bg-slate-900/50 text-slate-600 dark:text-slate-400 transition-colors">
            <option value="">Semua Alur</option>
            <option value="bertahap" {{ request('review_type') == 'bertahap' ? 'selected' : '' }}>Alur 1-2-3 (Bertahap)</option>
            <option value="langsung_admin_3" {{ request('review_type') == 'langsung_admin_3' ? 'selected' : '' }}>Alur Langsung 3</option>
        </select>
        
        @if(request('search') || request('review_type'))
            <a href="{{ route('staff.trash.letters') }}" class="px-5 py-2.5 text-sm font-bold text-slate-600 dark:text-slate-400 hover:text-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 flex items-center shadow-sm transition-colors whitespace-nowrap">Reset</a>
        @endif
        <button type="submit"
                class="px-5 py-2.5 text-sm font-bold bg-slate-900 text-white rounded-xl hover:bg-slate-800 transition-colors shadow-sm whitespace-nowrap">
            Cari
        </button>
    </form>


    <div class="bg-white dark:bg-slate-800 rounded-xl border-2 border-slate-300 dark:border-slate-600 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr class="bg-indigo-600 dark:bg-indigo-800">
                        <th class="px-6 py-4 text-xs font-extrabold text-white uppercase tracking-wider w-16">No</th>
                        <th class="px-6 py-4 text-xs font-extrabold text-white uppercase tracking-wider">Surat</th>
                        <th class="px-6 py-4 text-xs font-extrabold text-white uppercase tracking-wider">Status Terakhir</th>
                        <th class="px-6 py-4 text-xs font-extrabold text-white uppercase tracking-wider">Dihapus Pada</th>
                        <th class="px-6 py-4 text-xs font-extrabold text-white uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                    @forelse($letters as $index => $letter)
                        <tr class="border-b border-slate-200 dark:border-slate-700 hover:bg-slate-50/50 transition-colors group last:border-0">
                            <td class="px-6 py-4 text-slate-400 font-mono font-bold text-xs">
                                {{ $letters->firstItem() + $index }}
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-bold text-slate-900 dark:text-white group-hover:text-indigo-600 transition-colors">{{ $letter->title }}</p>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-500 bg-slate-100 dark:bg-slate-700/50 px-2 py-0.5 rounded border border-slate-200 dark:border-slate-700">{{ $letter->category->name ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <x-status-badge :status="$letter->status" />
                            </td>
                            <td class="px-6 py-4 text-slate-500 text-xs font-medium">
                                <span class="font-bold text-slate-700 dark:text-slate-300">{{ $letter->deleted_at->format('d M Y') }}</span><br>
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">{{ $letter->deleted_at->format('H:i') }} WIB</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2 transition-opacity">
                                    {{-- Restore --}}
                                    <form action="{{ route('staff.trash.letters.restore', $letter->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="p-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-emerald-500 hover:bg-emerald-50 hover:text-emerald-600 hover:border-emerald-200 transition-colors shadow-sm" title="Pulihkan">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                                            </svg>
                                        </button>
                                    </form>

                                    {{-- Force Delete --}}
                                    <form action="{{ route('staff.trash.letters.force_delete', $letter->id) }}" method="POST" class="inline-block" onsubmit="confirmDelete(event, 'Peringatan: Surat ini dan file lampirannya akan dihapus SELAMANYA. Anda yakin?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-rose-500 hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200 transition-colors shadow-sm" title="Hapus Permanen">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-20 px-4 text-center">
                                <div class="w-16 h-16 bg-slate-50 dark:bg-slate-900/50 border border-slate-100 rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm">
                                    <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </div>
                                <p class="text-sm font-bold text-slate-700 dark:text-slate-300">Tong sampah surat kosong</p>
                                <p class="text-xs font-medium text-slate-500 mt-1">Tidak ada surat yang dihapus saat ini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($letters->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $letters->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
