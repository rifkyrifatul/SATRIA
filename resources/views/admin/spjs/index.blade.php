@extends('layouts.app')

@section('title', 'BERKAS SPJ PDF')

@section('content')
@php
    $adminLevel = Auth::user()->admin_level;
    $roleName = match($adminLevel) {
        'admin_1' => 'PROGAR',
        'admin_2' => 'PEKAS',
        'admin_3' => 'SETUM',
        default => 'Super Admin'
    };
@endphp

<div x-data="{ 
    uploadModal: false, 
    editModal: false, 
    previewModal: false,
    previewUrl: '',
    previewTitle: '',
    editData: { id: '', title: '', spj_number: '', date: '', description: '' }
}" class="max-w-7xl mx-auto space-y-6">

    {{-- ── Header Section ── --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-2">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white flex items-center gap-3">
                <div class="p-2.5 bg-indigo-600 text-white rounded-xl shadow-md">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 01-2 h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                @if(Auth::user()->role === 'super_admin')
                    Berkas SPJ (PROGAR & PEKAS)
                @else
                    Berkas SPJ {{ $roleName }}
                @endif
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 pl-12">
                @if(Auth::user()->role === 'super_admin')
                    Kelola seluruh berkas SPJ berformat PDF dari unit PROGAR & PEKAS.
                @else
                    Unggah dan simpan seluruh berkas SPJ berformat PDF khusus untuk unit <span class="font-bold text-indigo-600 dark:text-indigo-400">{{ $roleName }}</span>.
                @endif
            </p>
        </div>

        {{-- Tombol Utama Upload SPJ PDF (Di Luar & Di Atas Header) --}}
        <button type="button" @click="uploadModal = true" 
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 text-white text-sm font-extrabold rounded-xl hover:bg-indigo-700 transition-all shadow-md hover:shadow-indigo-500/20 whitespace-nowrap active:scale-95">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Upload SPJ PDF
        </button>
    </div>

    {{-- ── Alert Messages ── --}}
    @if(session('success'))
        <div class="p-4 bg-emerald-50 dark:bg-emerald-950/40 border-2 border-emerald-500/30 text-emerald-800 dark:text-emerald-300 rounded-xl font-bold text-sm flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">&times;</button>
        </div>
    @endif

    {{-- ── Filter Bar (Persis Contoh Halaman Surat Masuk) ── --}}
    <form method="GET" action="{{ route((Auth::user()->role === 'super_admin' ? 'super_admin' : 'admin') . '.spjs.index') }}" 
          class="flex flex-col sm:flex-row gap-3 bg-white dark:bg-slate-800 p-4 rounded-xl border-2 border-slate-300 dark:border-slate-600 shadow-sm mb-6">
        
        {{-- Search Input --}}
        <div class="relative flex-1">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari judul atau nomor SPJ..."
                   class="w-full pl-10 pr-4 py-2.5 text-sm border border-slate-300 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 dark:bg-slate-900/50 placeholder:text-slate-400 transition-colors">
        </div>

        {{-- Filter Tanggal Mulai --}}
        <input type="date" name="date_from" value="{{ request('date_from') }}" title="Tanggal Mulai"
               class="w-full sm:w-48 py-2.5 px-4 text-sm border border-slate-300 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 dark:bg-slate-900/50 text-slate-600 dark:text-slate-400 transition-colors">

        {{-- Filter Tanggal Akhir --}}
        <input type="date" name="date_to" value="{{ request('date_to') }}" title="Tanggal Akhir"
               class="w-full sm:w-48 py-2.5 px-4 text-sm border border-slate-300 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 dark:bg-slate-900/50 text-slate-600 dark:text-slate-400 transition-colors">

        @if(Auth::user()->role === 'super_admin')
            <select name="admin_level" class="w-full sm:w-48 py-2.5 px-4 text-sm border border-slate-300 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 dark:bg-slate-900/50 text-slate-600 dark:text-slate-400 transition-colors">
                <option value="">Semua Unit</option>
                <option value="admin_1" {{ request('admin_level') == 'admin_1' ? 'selected' : '' }}>PROGAR</option>
                <option value="admin_2" {{ request('admin_level') == 'admin_2' ? 'selected' : '' }}>PEKAS</option>
            </select>
        @endif

        @if(request('search') || request('date_from') || request('date_to') || request('admin_level'))
            <a href="{{ route((Auth::user()->role === 'super_admin' ? 'super_admin' : 'admin') . '.spjs.index') }}" 
               class="px-5 py-2.5 text-sm font-bold text-slate-600 dark:text-slate-400 hover:text-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 flex items-center shadow-sm transition-colors whitespace-nowrap">
                Reset
            </a>
        @endif

        <button type="submit"
                class="px-5 py-2.5 text-sm font-bold bg-slate-900 text-white rounded-xl hover:bg-slate-800 transition-colors shadow-sm whitespace-nowrap">
            Cari & Filter
        </button>
    </form>

    {{-- ── Table SPJ ── --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl border-2 border-slate-300 dark:border-slate-600 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-indigo-600 dark:bg-indigo-800 text-white">
                        <th class="px-6 py-4 text-xs font-extrabold uppercase tracking-wider w-16 text-center">No</th>
                        <th class="px-6 py-4 text-xs font-extrabold uppercase tracking-wider">Judul & Nomor SPJ</th>
                        <th class="px-6 py-4 text-xs font-extrabold uppercase tracking-wider">Tanggal Berkas</th>
                        <th class="px-6 py-4 text-xs font-extrabold uppercase tracking-wider">Pengunggah</th>
                        <th class="px-6 py-4 text-xs font-extrabold uppercase tracking-wider text-center">Berkas PDF</th>
                        <th class="px-6 py-4 text-xs font-extrabold uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y-2 divide-slate-200 dark:divide-slate-700/50">
                    @forelse($spjs as $spj)
                        <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-700/30 transition-colors group">
                            <td class="px-6 py-4 text-center">
                                <span class="text-sm font-bold text-slate-400">{{ $spjs->firstItem() + $loop->index }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-extrabold text-slate-900 dark:text-white group-hover:text-indigo-600 transition-colors line-clamp-1">
                                    {{ $spj->title }}
                                </div>
                                <div class="text-xs text-slate-400 mt-0.5 font-mono">
                                    {{ $spj->spj_number ?? 'Tanpa Nomor SPJ' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-xs font-semibold text-slate-600 dark:text-slate-400">
                                {{ $spj->date->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    @if($spj->admin_level === 'admin_1')
                                        <span class="px-2 py-0.5 text-[10px] font-black uppercase rounded bg-blue-100 text-blue-700 border border-blue-200">PROGAR</span>
                                    @elseif($spj->admin_level === 'admin_2')
                                        <span class="px-2 py-0.5 text-[10px] font-black uppercase rounded bg-emerald-100 text-emerald-700 border border-emerald-200">PEKAS</span>
                                    @else
                                        <span class="px-2 py-0.5 text-[10px] font-black uppercase rounded bg-slate-100 text-slate-700 border border-slate-200">{{ $spj->admin_level_label }}</span>
                                    @endif
                                    <span class="text-xs font-bold text-slate-700 dark:text-slate-300">{{ $spj->uploader->name ?? 'User' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="inline-flex flex-col items-center">
                                    <span class="px-2.5 py-1 rounded-md bg-rose-50 text-rose-700 dark:bg-rose-950/50 dark:text-rose-300 text-xs font-extrabold border border-rose-200 dark:border-rose-800 flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-rose-500" fill="currentColor" viewBox="0 0 20 20"><path d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"/></svg>
                                        PDF ({{ $spj->formatted_file_size }})
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    {{-- Preview --}}
                                    <button @click="previewTitle = '{{ addslashes($spj->title) }}'; previewUrl = '{{ route((Auth::user()->role === 'super_admin' ? 'super_admin' : 'admin') . '.spjs.preview', $spj) }}'; previewModal = true" 
                                            class="p-2 rounded-xl bg-slate-100 dark:bg-slate-700 hover:bg-indigo-100 hover:text-indigo-600 text-slate-600 dark:text-slate-300 transition-colors" title="Pratinjau PDF">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </button>

                                    {{-- Download --}}
                                    <a href="{{ route((Auth::user()->role === 'super_admin' ? 'super_admin' : 'admin') . '.spjs.download', $spj) }}" 
                                       class="p-2 rounded-xl bg-slate-100 dark:bg-slate-700 hover:bg-emerald-100 hover:text-emerald-600 text-slate-600 dark:text-slate-300 transition-colors" title="Unduh PDF">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    </a>

                                    {{-- Edit --}}
                                    <button @click="editData = { 
                                                id: '{{ $spj->id }}', 
                                                title: '{{ addslashes($spj->title) }}', 
                                                spj_number: '{{ addslashes($spj->spj_number) }}', 
                                                date: '{{ $spj->date->format('Y-m-d') }}', 
                                                description: '{{ addslashes($spj->description) }}' 
                                            }; editModal = true"
                                            class="p-2 rounded-xl bg-slate-100 dark:bg-slate-700 hover:bg-amber-100 hover:text-amber-600 text-slate-600 dark:text-slate-300 transition-colors" title="Edit Data">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>

                                    {{-- Delete --}}
                                    <form action="{{ route((Auth::user()->role === 'super_admin' ? 'super_admin' : 'admin') . '.spjs.destroy', $spj) }}" 
                                          method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus berkas SPJ ini?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 rounded-xl bg-slate-100 dark:bg-slate-700 hover:bg-rose-100 hover:text-rose-600 text-slate-600 dark:text-slate-300 transition-colors" title="Hapus Berkas">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-16 px-6 text-center">
                                <div class="w-16 h-16 bg-rose-50 dark:bg-slate-900 border border-rose-100 dark:border-slate-700 rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm">
                                    <svg class="w-8 h-8 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 01-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <p class="text-slate-800 dark:text-slate-200 font-extrabold text-base">Belum Ada Berkas SPJ {{ Auth::user()->role === 'super_admin' ? '' : $roleName }} PDF</p>
                                <p class="text-slate-500 text-sm mt-1">Belum ada dokumen berkas SPJ yang diunggah.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($spjs->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50">
                {{ $spjs->links() }}
            </div>
        @endif
    </div>

    {{-- ── MODAL 1: Upload Modal ── --}}
    <div x-show="uploadModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="uploadModal = false" class="bg-white dark:bg-slate-800 rounded-3xl border-2 border-slate-300 dark:border-slate-600 shadow-2xl max-w-xl w-full p-6 sm:p-8 relative">
            
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-700 mb-6">
                <div class="flex items-center gap-3">
                    <div class="p-2.5 bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 rounded-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-slate-900 dark:text-white">Upload Berkas SPJ {{ Auth::user()->role === 'super_admin' ? '' : $roleName }} (PDF)</h3>
                        <p class="text-xs text-slate-500">Lengkapi formulir dan pilih berkas PDF yang akan disimpan.</p>
                    </div>
                </div>
                <button @click="uploadModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-white text-2xl font-bold">&times;</button>
            </div>

            <form action="{{ route((Auth::user()->role === 'super_admin' ? 'super_admin' : 'admin') . '.spjs.store') }}" 
                  method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">Judul SPJ <span class="text-rose-500">*</span></label>
                    <input type="text" name="title" required placeholder="Misal: SPJ Operasional Program Triwulan I" 
                           class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-300 dark:border-slate-600 rounded-xl text-sm font-medium focus:ring-2 focus:ring-indigo-500">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">Nomor SPJ / Referensi</label>
                        <input type="text" name="spj_number" placeholder="Misal: SPJ/{{ $roleName }}/2026/001" 
                               class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-300 dark:border-slate-600 rounded-xl text-sm font-medium focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">Tanggal Berkas SPJ <span class="text-rose-500">*</span></label>
                        <input type="date" name="date" required value="{{ date('Y-m-d') }}" 
                               class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-300 dark:border-slate-600 rounded-xl text-sm font-medium focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">Pilih File PDF <span class="text-rose-500">*</span></label>
                    <input type="file" name="file" accept=".pdf" required
                           class="w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-extrabold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 bg-slate-50 dark:bg-slate-900/50 border border-slate-300 dark:border-slate-600 rounded-xl">
                    <p class="text-[11px] text-slate-400 mt-1.5 font-medium">Hanya format file **.pdf** yang diperbolehkan. Maksimal 25 MB.</p>
                </div>

                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">Catatan / Keterangan Tambahan</label>
                    <textarea name="description" rows="3" placeholder="Tambahkan rincian atau keterangan dokumen SPJ..." 
                              class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-300 dark:border-slate-600 rounded-xl text-sm font-medium focus:ring-2 focus:ring-indigo-500"></textarea>
                </div>

                <div class="pt-4 border-t border-slate-100 dark:border-slate-700 flex justify-end gap-3">
                    <button type="button" @click="uploadModal = false" class="px-5 py-2.5 text-sm font-bold text-slate-600 hover:bg-slate-100 dark:text-slate-400 rounded-xl">
                        Batal
                    </button>
                    <button type="submit" class="px-6 py-2.5 text-sm font-extrabold bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 shadow-md">
                        Simpan & Upload SPJ
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── MODAL 2: Edit Modal ── --}}
    <div x-show="editModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="editModal = false" class="bg-white dark:bg-slate-800 rounded-3xl border-2 border-slate-300 dark:border-slate-600 shadow-2xl max-w-xl w-full p-6 sm:p-8 relative">
            
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-700 mb-6">
                <div class="flex items-center gap-3">
                    <div class="p-2.5 bg-amber-100 text-amber-600 rounded-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-slate-900 dark:text-white">Edit Informasi Berkas SPJ</h3>
                        <p class="text-xs text-slate-500">Perbarui rincian atau ganti berkas PDF SPJ.</p>
                    </div>
                </div>
                <button @click="editModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-white text-2xl font-bold">&times;</button>
            </div>

            <form :action="'{{ url(Auth::user()->role === 'super_admin' ? 'super-admin' : 'admin') }}/spjs/' + editData.id" 
                  method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">Judul SPJ <span class="text-rose-500">*</span></label>
                    <input type="text" name="title" x-model="editData.title" required
                           class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-300 dark:border-slate-600 rounded-xl text-sm font-medium focus:ring-2 focus:ring-indigo-500">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">Nomor SPJ / Referensi</label>
                        <input type="text" name="spj_number" x-model="editData.spj_number" 
                               class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-300 dark:border-slate-600 rounded-xl text-sm font-medium focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">Tanggal Berkas SPJ <span class="text-rose-500">*</span></label>
                        <input type="date" name="date" x-model="editData.date" required 
                               class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-300 dark:border-slate-600 rounded-xl text-sm font-medium focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">Ganti File PDF (Opsional)</label>
                    <input type="file" name="file" accept=".pdf"
                           class="w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-extrabold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100 bg-slate-50 dark:bg-slate-900/50 border border-slate-300 dark:border-slate-600 rounded-xl">
                    <p class="text-[11px] text-slate-400 mt-1.5 font-medium">Kosongkan jika tidak ingin mengganti file PDF yang sudah ada.</p>
                </div>

                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">Catatan / Keterangan Tambahan</label>
                    <textarea name="description" x-model="editData.description" rows="3" 
                              class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-300 dark:border-slate-600 rounded-xl text-sm font-medium focus:ring-2 focus:ring-indigo-500"></textarea>
                </div>

                <div class="pt-4 border-t border-slate-100 dark:border-slate-700 flex justify-end gap-3">
                    <button type="button" @click="editModal = false" class="px-5 py-2.5 text-sm font-bold text-slate-600 hover:bg-slate-100 dark:text-slate-400 rounded-xl">
                        Batal
                    </button>
                    <button type="submit" class="px-6 py-2.5 text-sm font-extrabold bg-slate-900 text-white rounded-xl hover:bg-slate-800 shadow-md">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── MODAL 3: Preview PDF Modal ── --}}
    <div x-show="previewModal" x-cloak class="fixed inset-0 z-50 overflow-hidden bg-slate-900/80 backdrop-blur-md flex flex-col items-center justify-center p-4">
        <div @click.away="previewModal = false" class="bg-white dark:bg-slate-800 rounded-3xl shadow-2xl w-full max-w-5xl h-[85vh] flex flex-col overflow-hidden border-2 border-slate-300 dark:border-slate-600">
            
            {{-- Modal Topbar --}}
            <div class="px-6 py-4 bg-slate-900 text-white flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="px-2.5 py-1 bg-rose-500 text-white text-[10px] font-black uppercase rounded">PDF</span>
                    <h3 class="text-sm font-bold truncate max-w-md" x-text="previewTitle"></h3>
                </div>
                <div class="flex items-center gap-3">
                    <a :href="previewUrl" target="_blank" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-xs font-bold rounded-lg transition-colors flex items-center gap-1.5 text-slate-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        Buka di Tab Baru
                    </a>
                    <button @click="previewModal = false" class="text-slate-400 hover:text-white text-2xl font-bold leading-none">&times;</button>
                </div>
            </div>

            {{-- Viewer iframe --}}
            <div class="flex-1 bg-slate-100 dark:bg-slate-900 relative">
                <template x-if="previewUrl">
                    <iframe :src="previewUrl" class="w-full h-full border-0"></iframe>
                </template>
            </div>
        </div>
    </div>

</div>
@endsection
