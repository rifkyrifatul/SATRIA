@extends('layouts.app')

@section('title', 'TEMPLATE SURAT')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <div class="mb-6">
        <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">Daftar Template</h1>
        <p class="text-sm font-medium text-slate-500 mt-1">Unduh format baku dan contoh dokumen untuk mempermudah pembuatan surat Anda.</p>
    </div>

    {{-- ── Filter Bar ── --}}
    <form method="GET" action="{{ route('staff.letter_templates.index') }}" class="flex flex-col sm:flex-row gap-3 bg-white dark:bg-slate-800 p-4 rounded-xl border-2 border-slate-300 dark:border-slate-600 shadow-sm mb-6">
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
            <option value="pdf" {{ request('format') == 'pdf' ? 'selected' : '' }}>Format PDF</option>
            <option value="doc" {{ request('format') == 'doc' ? 'selected' : '' }}>Format DOC</option>
            <option value="docx" {{ request('format') == 'docx' ? 'selected' : '' }}>Format DOCX</option>
            <option value="xls" {{ request('format') == 'xls' ? 'selected' : '' }}>Format XLS</option>
            <option value="xlsx" {{ request('format') == 'xlsx' ? 'selected' : '' }}>Format XLSX</option>
            <option value="jpg" {{ request('format') == 'jpg' ? 'selected' : '' }}>Format JPG</option>
            <option value="jpeg" {{ request('format') == 'jpeg' ? 'selected' : '' }}>Format JPEG</option>
            <option value="png" {{ request('format') == 'png' ? 'selected' : '' }}>Format PNG</option>
        </select>
        
        @if(request('search') || request('format'))
            <a href="{{ route('staff.letter_templates.index') }}" class="px-5 py-2.5 text-sm font-bold text-slate-600 dark:text-slate-400 hover:text-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 flex items-center shadow-sm transition-colors whitespace-nowrap">Reset</a>
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
                        <th class="px-6 py-4 text-xs font-extrabold text-white uppercase tracking-wider">Template Surat</th>
                        <th class="px-6 py-4 text-xs font-extrabold text-white uppercase tracking-wider">Deskripsi</th>
                        <th class="px-6 py-4 text-xs font-extrabold text-white uppercase tracking-wider hidden md:table-cell">Tanggal Diunggah</th>
                        <th class="px-6 py-4 text-xs font-extrabold text-white uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                    @forelse($templates as $index => $template)
                            <tr class="border-b border-slate-200 dark:border-slate-700 hover:bg-slate-50/50 transition-colors group last:border-0">
                                <td class="px-6 py-4 text-slate-400 font-mono font-bold text-xs">
                                    {{ $templates->firstItem() + $index }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-start gap-3">
                                        <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0 {{ in_array($template->file_extension, ['jpg', 'jpeg', 'png']) ? 'bg-emerald-100 text-emerald-600 border-emerald-200' : ($template->file_extension === 'pdf' ? 'bg-rose-100 text-rose-600 border-rose-200' : 'bg-indigo-100 text-indigo-600 border-indigo-200') }} border shadow-sm mt-0.5">
                                            <span class="text-[10px] font-extrabold uppercase tracking-widest">{{ $template->file_extension }}</span>
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-900 dark:text-white line-clamp-1" title="{{ $template->title }}">{{ $template->title }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-slate-500 text-xs font-medium">
                                    <p class="line-clamp-2">{{ $template->description ?: '-' }}</p>
                                </td>
                                <td class="px-6 py-4 hidden md:table-cell text-slate-500 text-xs font-medium">
                                    <span class="font-bold text-slate-700 dark:text-slate-300">{{ $template->created_at->format('d M Y') }}</span><br>
                                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">{{ $template->created_at->format('H:i') }} WIB</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('staff.letter_templates.download', $template) }}" onclick="Toast.fire({icon: 'success', title: 'File berhasil diunduh!'})" class="inline-flex items-center justify-center p-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 transition-colors shadow-sm" title="Unduh Dokumen">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                        </svg>
                                    </a>
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
                                <p class="text-slate-700 dark:text-slate-300 font-bold">Belum ada template yang tersedia</p>
                                <p class="text-slate-500 font-medium text-sm mt-1 mb-6">Admin belum mengunggah format surat apapun.</p>
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
