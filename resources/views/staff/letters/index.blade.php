@extends('layouts.app')

@section('title', 'DAFTAR SURAT')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">Kelola Pengajuan Surat</h1>
            <p class="text-sm font-medium text-slate-500 mt-1">Daftar semua surat yang pernah Anda ajukan ke sistem.</p>
        </div>
        <a href="{{ route('staff.letters.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-900 text-white text-sm font-bold rounded-xl hover:bg-slate-800 transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Ajukan Surat Baru
        </a>
    </div>



    {{-- ── Filter Bar ── --}}
    <form method="GET" action="{{ route('staff.letters.index') }}" class="flex flex-col sm:flex-row gap-3 bg-white dark:bg-slate-800 p-4 rounded-xl border-2 border-slate-300 dark:border-slate-600 shadow-sm mb-6">
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
            <a href="{{ route('staff.letters.index') }}" class="px-5 py-2.5 text-sm font-bold text-slate-600 dark:text-slate-400 hover:text-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 flex items-center shadow-sm transition-colors whitespace-nowrap">Reset</a>
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
                        <th class="px-6 py-4 text-xs font-extrabold text-white uppercase tracking-wider">Informasi Surat</th>
                        <th class="px-6 py-4 text-xs font-extrabold text-white uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-xs font-extrabold text-white uppercase tracking-wider hidden md:table-cell">Tgl. Pengajuan</th>
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
                                <div class="flex items-start gap-3">
                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0 {{ $letter->file_extension === 'pdf' ? 'bg-rose-100 text-rose-600 border-rose-200' : 'bg-indigo-100 text-indigo-600 border-indigo-200' }} border shadow-sm mt-0.5">
                                        <span class="text-[10px] font-extrabold uppercase tracking-widest">{{ $letter->file_extension }}</span>
                                    </div>
                                    <div>
                                        <a href="{{ route('staff.tracking.show', $letter) }}" class="font-bold text-slate-900 dark:text-white hover:text-indigo-600 transition-colors line-clamp-1">
                                            {{ $letter->title }}
                                        </a>
                                        @if($letter->letter_number)
                                            <p class="text-xs text-indigo-600 font-mono font-bold mt-0.5">{{ $letter->letter_number }}</p>
                                        @else
                                            <p class="text-xs font-medium text-slate-400 mt-0.5">Belum ada nomor</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <x-status-badge :status="$letter->status" />
                            </td>
                            <td class="px-6 py-4 hidden md:table-cell text-slate-500 text-xs font-medium">
                                <span class="font-bold text-slate-700 dark:text-slate-300">{{ $letter->created_at->format('d M Y') }}</span><br>
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block mt-0.5">{{ $letter->created_at->format('H:i') }} WIB</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2 transition-opacity">
                                    <a href="{{ route('staff.tracking.show', $letter) }}" title="Detail & Tracking" class="p-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 transition-colors shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    @if($letter->isRevision())
                                        <a href="{{ route('staff.letters.edit', $letter) }}" title="Resubmit (Revisi)" class="p-2 bg-amber-50 border border-amber-200 rounded-lg text-amber-600 hover:text-amber-700 hover:bg-amber-100 transition-colors shadow-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </a>
                                    @endif
                                    <form action="{{ route('staff.letters.destroy', $letter) }}" method="POST" onsubmit="confirmDelete(event, 'Apakah Anda yakin ingin menghapus surat ini? Surat akan dipindahkan ke Tong Sampah.');" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-rose-500 hover:bg-rose-50 hover:border-rose-200 transition-colors shadow-sm" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-16 px-6 text-center">
                                <div class="w-16 h-16 bg-slate-50 dark:bg-slate-900/50 border border-slate-100 rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm">
                                    <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                                <p class="text-slate-700 dark:text-slate-300 font-bold">Belum ada surat</p>
                                <p class="text-slate-500 font-medium text-sm mt-1 mb-6">Anda belum pernah mengajukan surat apapun.</p>
                                <a href="{{ route('staff.letters.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 text-sm font-bold rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors shadow-sm">
                                    Mulai Unggah Surat
                                </a>
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


