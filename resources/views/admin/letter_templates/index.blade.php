@extends('layouts.app')

@section('title', 'TEMPLATE SURAT')

@section('content')
@php $routePrefix = Auth::user()->isSuperAdmin() ? 'super_admin' : 'admin'; @endphp
<div class="max-w-7xl mx-auto space-y-6">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">Daftar Template</h1>
            <p class="text-sm text-slate-500 mt-1">Daftar template dokumen/surat edaran yang dapat diunduh oleh Staff.</p>
        </div>

        @if(Auth::user()->canManageTemplates())
            <a href="{{ route("{$routePrefix}.letter_templates.create") }}" class="px-5 py-2.5 text-sm font-bold bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-colors shadow-sm whitespace-nowrap flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Unggah Template Baru
            </a>
        @endif
    </div>

    {{-- ── Filter Bar ── --}}
    <form method="GET" action="{{ route("{$routePrefix}.letter_templates.index") }}" class="flex flex-col sm:flex-row gap-3 bg-white dark:bg-slate-800 p-4 rounded-xl border-2 border-slate-300 dark:border-slate-600 shadow-sm mb-6">
        <div class="relative flex-1">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari judul atau deskripsi template..."
                   class="w-full pl-10 pr-4 py-2.5 text-sm border border-slate-300 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 dark:bg-slate-900/50 placeholder:text-slate-400 transition-colors">
        </div>
        
        <select name="format" onchange="this.form.submit()" class="w-full sm:w-48 py-2.5 px-4 text-sm border border-slate-300 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 dark:bg-slate-900/50 text-slate-600 dark:text-slate-400 transition-colors">
            <option value="">Semua Format</option>
            <option value="pdf" {{ request('format') == 'pdf' ? 'selected' : '' }}>PDF</option>
            <option value="doc" {{ request('format') == 'doc' ? 'selected' : '' }}>DOC</option>
            <option value="docx" {{ request('format') == 'docx' ? 'selected' : '' }}>DOCX</option>
            <option value="xls" {{ request('format') == 'xls' ? 'selected' : '' }}>XLS</option>
            <option value="xlsx" {{ request('format') == 'xlsx' ? 'selected' : '' }}>XLSX</option>
            <option value="jpg" {{ request('format') == 'jpg' ? 'selected' : '' }}>JPG</option>
            <option value="jpeg" {{ request('format') == 'jpeg' ? 'selected' : '' }}>JPEG</option>
            <option value="png" {{ request('format') == 'png' ? 'selected' : '' }}>PNG</option>
        </select>
        
        @if(request('search') || request('format'))
            <a href="{{ route("{$routePrefix}.letter_templates.index") }}" class="px-5 py-2.5 text-sm font-bold text-slate-600 dark:text-slate-400 hover:text-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 flex items-center shadow-sm transition-colors whitespace-nowrap">Reset</a>
        @endif

        <button type="submit"
                class="px-5 py-2.5 text-sm font-bold bg-slate-900 text-white rounded-xl hover:bg-slate-800 transition-colors shadow-sm whitespace-nowrap">
            Cari
        </button>
    </form>

    <div class="bg-white dark:bg-slate-800 rounded-xl border-2 border-slate-300 dark:border-slate-600 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left border-collapse">
                <thead>
                    <tr class="bg-indigo-600 dark:bg-indigo-800">
                        <th class="text-center px-4 py-4 text-[10px] font-extrabold text-white uppercase tracking-wider w-12 rounded-tl-lg">No</th>
                        <th class="text-left px-6 py-4 text-xs font-extrabold text-white uppercase tracking-wider">Template</th>
                        <th class="text-left px-6 py-4 text-xs font-extrabold text-white uppercase tracking-wider">Deskripsi</th>
                        <th class="text-left px-6 py-4 text-xs font-extrabold text-white uppercase tracking-wider hidden md:table-cell">Diunggah Oleh</th>
                        <th class="text-right px-6 py-4 text-xs font-extrabold text-white uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                    @forelse($templates as $template)
                        <tr class="border-b border-slate-200 dark:border-slate-700 hover:bg-slate-50/50 transition-colors last:border-0">
                            <td class="px-4 py-4 text-center text-slate-400 font-mono text-xs font-semibold group-hover:text-indigo-500 transition-colors">
                                {{ $templates->firstItem() + $loop->index }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if(in_array($template->file_extension, ['jpg', 'jpeg', 'png']))
                                        <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600 flex-shrink-0 shadow-sm">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        </div>
                                    @else
                                        <div class="w-10 h-10 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 flex-shrink-0 shadow-sm">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                    <div>
                                        <p class="font-bold text-slate-900 dark:text-white">{{ $template->title }}</p>
                                        <p class="text-[10px] text-slate-500 font-bold mt-0.5 uppercase tracking-widest">{{ $template->file_extension }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm text-slate-600 dark:text-slate-400 line-clamp-2 max-w-xs font-medium">{{ $template->description ?: '-' }}</p>
                            </td>
                            <td class="px-6 py-4 hidden md:table-cell">
                                <p class="text-xs font-bold text-slate-800 dark:text-slate-200">{{ $template->uploader ? $template->uploader->name : 'Sistem' }}</p>
                                <p class="text-[10px] text-slate-400 font-medium mt-1">{{ $template->created_at->format('d M Y') }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    {{-- Download --}}
                                    @php
                                        $downloadRoute = route("{$routePrefix}.letter_templates.download", $template);
                                    @endphp
                                    <a href="{{ $downloadRoute }}" onclick="Toast.fire({icon: 'success', title: 'File berhasil diunduh!'})"
                                        class="p-2 rounded-lg text-indigo-600 hover:bg-indigo-50 transition-colors"
                                        title="Download Template">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                        </svg>
                                    </a>

                                    @if(Auth::user()->canManageTemplates())
                                        {{-- Edit --}}
                                        <a href="{{ route("{$routePrefix}.letter_templates.edit", $template) }}"
                                            class="p-2 rounded-lg text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors"
                                            title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </a>

                                        {{-- Delete --}}
                                        <form action="{{ route("{$routePrefix}.letter_templates.destroy", $template) }}" method="POST" onsubmit="confirmDelete(event, 'Apakah Anda yakin ingin menghapus template ini?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors" title="Hapus">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
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
                                <p class="text-slate-700 dark:text-slate-300 font-bold">Belum ada template surat</p>
                                <p class="text-slate-500 font-medium text-sm mt-1 mb-6">Template yang diunggah akan muncul di sini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($templates->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $templates->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
