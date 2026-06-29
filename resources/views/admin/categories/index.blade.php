@extends('layouts.app')

@section('title', 'KELOLA SURAT')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">Daftar Kategori</h1>
            <p class="text-sm text-slate-500 mt-1">Kelola jenis kategori beserta alur review yang diterapkan pada kategori tersebut.</p>
        </div>
        <a href="{{ route((Auth::user()->role === 'super_admin' ? 'super_admin' : 'admin') . '.categories.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-bold rounded-xl hover:bg-indigo-700 transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Kategori
        </a>
    </div>

    {{-- ── Filter Bar ── --}}
    <form method="GET" action="{{ route((Auth::user()->role === 'super_admin' ? 'super_admin' : 'admin') . '.categories.index') }}" class="flex flex-col sm:flex-row gap-3 bg-white dark:bg-slate-800 p-4 rounded-xl border-2 border-slate-300 dark:border-slate-600 shadow-sm mb-6">
        {{-- Search --}}
        <div class="relative flex-1">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari kategori surat..."
                   class="w-full pl-10 pr-4 py-2.5 text-sm border border-slate-300 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 dark:bg-slate-900/50 placeholder:text-slate-400 transition-colors">
        </div>
        
        {{-- Filter Alur Review --}}
        <select name="review_type" onchange="this.form.submit()" class="w-full sm:w-48 py-2.5 px-4 text-sm border border-slate-300 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 dark:bg-slate-900/50 text-slate-600 dark:text-slate-400 transition-colors">
            <option value="">Semua Alur</option>
            <option value="bertahap" {{ request('review_type') == 'bertahap' ? 'selected' : '' }}>Bertahap</option>
            <option value="langsung_admin_3" {{ request('review_type') == 'langsung_admin_3' ? 'selected' : '' }}>Langsung</option>
        </select>
        
        @if(request('search') || request('review_type'))
            <a href="{{ route((Auth::user()->role === 'super_admin' ? 'super_admin' : 'admin') . '.categories.index') }}" class="px-5 py-2.5 text-sm font-bold text-slate-600 dark:text-slate-400 hover:text-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 flex items-center shadow-sm transition-colors whitespace-nowrap">Reset</a>
        @endif
        <button type="submit"
                class="px-5 py-2.5 text-sm font-bold bg-slate-900 text-white rounded-xl hover:bg-slate-800 transition-colors shadow-sm whitespace-nowrap">
            Cari
        </button>
    </form>

    <div class="bg-white dark:bg-slate-800 rounded-xl border-2 border-slate-300 dark:border-slate-600 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-indigo-600 dark:bg-indigo-800">
                        <th class="px-6 py-4 text-xs font-extrabold text-white uppercase tracking-wider w-16 text-center">No</th>
                        <th class="px-6 py-4 text-xs font-extrabold text-white uppercase tracking-wider">Kategori Surat</th>
                        <th class="px-6 py-4 text-xs font-extrabold text-white uppercase tracking-wider text-center">Jumlah Surat</th>
                        <th class="px-6 py-4 text-xs font-extrabold text-white uppercase tracking-wider">Alur Review</th>
                        <th class="px-6 py-4 text-xs font-extrabold text-white uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y-2 divide-slate-200 dark:divide-slate-700/50">
                    @forelse($categories as $category)
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            <td class="px-6 py-4 text-center">
                                <span class="text-sm font-bold text-slate-500">{{ $loop->iteration }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-slate-900 dark:text-white group-hover:text-indigo-600 transition-colors">{{ $category->name }}</div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center justify-center px-2.5 py-1 text-xs font-bold rounded-full bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300">
                                    {{ $category->letters_count }} Surat
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($category->review_type === 'langsung_admin_3')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-700 text-[10px] font-bold uppercase tracking-wider border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        Langsung SETUM
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-indigo-50 text-indigo-700 text-[10px] font-bold uppercase tracking-wider border border-indigo-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                                        Bertahap (1 &rarr; 2 &rarr; 3)
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route((Auth::user()->role === 'super_admin' ? 'super_admin' : 'admin') . '.categories.edit', $category) }}" class="text-sm font-bold text-slate-400 hover:text-indigo-600 transition-colors">Edit</a>
                                    
                                    <form action="{{ route((Auth::user()->role === 'super_admin' ? 'super_admin' : 'admin') . '.categories.destroy', $category) }}" method="POST" onsubmit="confirmDelete(event, 'Apakah Anda yakin ingin menghapus kategori ini?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm font-bold text-slate-400 hover:text-rose-600 transition-colors">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-16 px-6 text-center">
                                <div class="w-16 h-16 bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700 rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm">
                                    <svg class="w-8 h-8 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <p class="text-slate-700 dark:text-slate-300 font-bold">Belum ada kategori surat</p>
                                <p class="text-slate-500 font-medium text-sm mt-1 mb-6">Kategori yang ditambahkan akan muncul di sini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection


