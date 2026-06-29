@extends('layouts.app')

@section('title', 'TONG SAMPAH SURAT')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">Dokumen Terhapus</h1>
            <p class="text-sm text-slate-500 mt-1">Daftar surat yang dihapus sementara (Soft Delete). Anda dapat memulihkannya atau menghapusnya secara permanen dari server.</p>
        </div>
        
        <a href="{{ route('super_admin.letters.index') }}" class="px-5 py-2.5 text-sm font-bold bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors flex items-center gap-2 shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Daftar Surat
        </a>
    </div>

    {{-- ========================================================= --}}
    {{-- TABEL SOFT DELETE --}}
    {{-- ========================================================= --}}
    <div class="bg-white dark:bg-slate-800 rounded-xl border-2 border-slate-300 dark:border-slate-600 shadow-sm overflow-hidden">
    <div class="bg-white dark:bg-slate-800 rounded-xl border-2 border-slate-300 dark:border-slate-600 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-rose-50/50 border-b border-rose-100 text-slate-600 dark:text-slate-400 font-semibold text-xs uppercase tracking-wider">
                    <tr>
                        <th class="text-center px-4 py-4 w-12">No</th>
                        <th class="px-6 py-4">Informasi Surat</th>
                        <th class="px-6 py-4 hidden md:table-cell">Kategori & Status Asal</th>
                        <th class="px-6 py-4 hidden lg:table-cell">Dihapus Pada</th>
                        <th class="px-6 py-4 text-right">Tindakan Khusus</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($letters as $letter)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors group">
                                <td class="px-4 py-4 text-center text-slate-400 font-mono text-xs font-semibold group-hover:text-indigo-500 transition-colors">
                                    {{ method_exists($letters, 'firstItem') ? $letters->firstItem() + $loop->index : $loop->iteration }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-lg bg-rose-100 text-rose-600 flex items-center justify-center flex-shrink-0">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-900 dark:text-white line-clamp-1">{{ $letter->title }}</p>
                                            <p class="text-xs text-slate-500 mt-1 flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                                {{ $letter->creator->name ?? 'Unknown' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 hidden md:table-cell">
                                    <div class="flex flex-col items-start gap-1">
                                        <span class="px-2 py-1 rounded-md text-[10px] font-bold bg-slate-100 dark:bg-slate-700/50 text-slate-600 dark:text-slate-400 uppercase border border-slate-200 dark:border-slate-700">
                                            {{ $letter->category->name ?? 'Umum' }}
                                        </span>
                                        <span class="px-2 py-1 rounded-md text-[10px] font-bold bg-rose-50 text-rose-600 uppercase border border-rose-100">
                                            {{ str_replace('_', ' ', $letter->status) }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 hidden lg:table-cell">
                                    <p class="text-slate-600 dark:text-slate-400 text-sm font-medium">{{ $letter->deleted_at->format('d M Y') }}</p>
                                    <p class="text-slate-400 text-xs mt-0.5">{{ $letter->deleted_at->format('H:i') }} WIB</p>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        
                                        {{-- Tombol 1: Restore --}}
                                        <form method="POST" action="{{ route('super_admin.letters.restore', $letter->id) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" 
                                                    class="inline-flex items-center gap-1.5 bg-indigo-50 text-indigo-600 border border-indigo-200 hover:bg-indigo-100 text-xs px-3 py-2 rounded-lg font-bold transition-colors shadow-sm">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                                                Pulihkan
                                            </button>
                                        </form>

                                        {{-- Tombol 2: Force Delete --}}
                                        <form method="POST" action="{{ route('super_admin.letters.force_delete', $letter->id) }}" onsubmit="confirmDelete(event, 'PERINGATAN KRITIS: Anda yakin ingin menghapus surat ini SECARA PERMANEN? File dokumen di server juga akan dilenyapkan dan tidak dapat dikembalikan lagi.');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="inline-flex items-center gap-1.5 bg-rose-600 hover:bg-rose-700 text-white text-xs px-3 py-2 rounded-lg font-bold transition-colors shadow-sm">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                Hapus Permanen
                                            </button>
                                        </form>

                                    </div>
                                </td>
                            </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-16 px-6 text-center">
                                <div class="w-16 h-16 bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700 rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm">
                                    <svg class="w-8 h-8 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </div>
                                <p class="text-slate-700 dark:text-slate-300 font-bold">Tong Sampah Kosong</p>
                                <p class="text-slate-500 font-medium text-sm mt-1 mb-6">Tidak ada surat yang dihapus sementara saat ini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($letters, 'hasPages') && $letters->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $letters->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
