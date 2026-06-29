@extends('layouts.app')

@section('title', 'SURAT MASUK')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    
    <div class="mb-6">
        <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">Daftar Review Surat</h1>
        <p class="text-sm text-slate-500 mt-1">Anda masuk sebagai <span class="font-bold uppercase text-indigo-600 dark:text-indigo-400">{{ auth()->user()->admin_level_label }}</span>. Berikut adalah daftar surat yang menunggu persetujuan Anda saat ini.</p>
    </div>

    {{-- ── Filter Bar ── --}}
    <form method="GET" action="{{ route('admin.reviews.index') }}"
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
            <a href="{{ route('admin.reviews.index') }}" class="px-5 py-2.5 text-sm font-bold text-slate-600 dark:text-slate-400 hover:text-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 flex items-center shadow-sm transition-colors whitespace-nowrap">Reset</a>
        @endif

        <button type="submit"
                class="px-5 py-2.5 text-sm font-bold bg-slate-900 text-white rounded-xl hover:bg-slate-800 transition-colors shadow-sm whitespace-nowrap">
            Cari & Filter
        </button>
    </form>

    {{-- Tabel Data --}}
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border-2 border-slate-300 dark:border-slate-600 overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex flex-col md:flex-row justify-between items-center gap-4">
            <h2 class="font-bold text-slate-800 dark:text-slate-200 flex items-center gap-2">
                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Menunggu Persetujuan Anda
            </h2>
            <div class="text-[10px] font-bold uppercase tracking-wider bg-indigo-50 text-indigo-700 px-3 py-1.5 rounded-full border border-indigo-100 shadow-sm">
                Total: {{ $letters->total() }} Surat
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600 dark:text-slate-400">
                <thead>
                    <tr class="bg-indigo-600 dark:bg-indigo-800">
                        <th class="text-center px-4 py-4 text-[10px] font-extrabold text-white uppercase tracking-wider w-12 rounded-tl-lg">No</th>
                        <th class="px-6 py-4 text-xs font-extrabold text-white uppercase tracking-wider">Judul Surat & Kategori</th>
                        <th class="px-6 py-4 text-xs font-extrabold text-white uppercase tracking-wider hidden md:table-cell">Diajukan Oleh</th>
                        <th class="px-6 py-4 text-xs font-extrabold text-white uppercase tracking-wider hidden lg:table-cell">Tanggal</th>
                        <th class="px-6 py-4 text-xs font-extrabold text-white uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-xs font-extrabold text-white uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                    @forelse($letters as $letter)
                        <tr class="border-b border-slate-200 dark:border-slate-700 hover:bg-slate-50/50 transition-colors group last:border-0">
                            <td class="px-4 py-4 text-center text-slate-400 font-mono text-xs font-semibold group-hover:text-indigo-500 transition-colors">
                                {{ $letters->firstItem() + $loop->index }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900 dark:text-white group-hover:text-indigo-600 transition-colors line-clamp-1">
                                    {{ $letter->title }}
                                </div>
                                <div class="text-xs text-slate-400 mt-0.5 font-medium">
                                    {{ $letter->category->name ?? 'Tanpa Kategori' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap hidden md:table-cell">
                                <div class="flex items-center gap-2">
                                    @if($letter->creator && $letter->creator->profile_photo)
                                        <img src="{{ asset('storage/' . $letter->creator->profile_photo) }}" alt="Profile" class="w-6 h-6 rounded-full object-cover border border-slate-200 dark:border-slate-700">
                                    @else
                                        <div class="w-6 h-6 rounded-full bg-slate-900 flex items-center justify-center text-white font-bold text-xs shadow-sm">
                                            {{ substr($letter->creator->name ?? '?', 0, 1) }}
                                        </div>
                                    @endif
                                    <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $letter->creator->name ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-500 hidden lg:table-cell">
                                <span class="font-semibold">{{ $letter->created_at->format('d M Y') }}</span>
                                <span class="text-[10px] text-slate-400 block mt-0.5">{{ $letter->created_at->format('H:i') }} WIB</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <x-status-badge :status="$letter->status" />
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <a href="{{ route('admin.letters.show', $letter) }}" 
                                   class="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-900 text-white hover:bg-slate-800 rounded-xl text-xs font-bold transition-colors shadow-sm">
                                    <span>Review Dokumen</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 bg-slate-50 dark:bg-slate-900/50 border border-slate-100 rounded-full flex items-center justify-center mb-4">
                                        <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </div>
                                    <p class="text-sm font-bold text-slate-600 dark:text-slate-400">Tidak ada surat untuk direview</p>
                                    <p class="text-xs font-medium text-slate-500 mt-1">Anda sudah menyelesaikan semua tugas review saat ini.</p>
                                </div>
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
