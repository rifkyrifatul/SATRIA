@extends('layouts.app')

@section('title', 'UNGGAH TEMPLATE SURAT')

@section('content')
@php $routePrefix = Auth::user()->isSuperAdmin() ? 'super_admin' : 'admin'; @endphp
<div class="max-w-3xl mx-auto space-y-6">

    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route("{$routePrefix}.letter_templates.index") }}" class="p-2 bg-white dark:bg-slate-800 text-slate-500 hover:text-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/50 rounded-xl border border-slate-200 dark:border-slate-700 transition-colors shadow-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">Formulir Unggah Template</h1>
            <p class="text-sm text-slate-500 mt-1">Unggah file template surat edaran untuk diakses oleh admin dan staff.</p>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl border-2 border-slate-300 dark:border-slate-600 shadow-sm overflow-hidden">
        <form action="{{ route("{$routePrefix}.letter_templates.store") }}" method="POST" enctype="multipart/form-data" class="p-6 sm:p-8 space-y-6">
            @csrf

            <div>
                <label for="title" class="block text-sm font-bold text-slate-800 dark:text-slate-200 mb-2">Nama / Judul Template <span class="text-rose-500">*</span></label>
                <input type="text" name="title" id="title" value="{{ old('title') }}" 
                       class="w-full rounded-xl border-slate-300 dark:border-slate-600 focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5 px-4 bg-slate-50 dark:bg-slate-900/50 focus:bg-white transition-colors" required>
                @error('title')
                    <p class="mt-2 text-xs text-rose-600 font-bold flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-bold text-slate-800 dark:text-slate-200 mb-2">Deskripsi <span class="text-[10px] uppercase tracking-wider text-slate-400 font-bold ml-1">(Opsional)</span></label>
                <textarea name="description" id="description" rows="3"
                          class="w-full rounded-xl border-slate-300 dark:border-slate-600 focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5 px-4 bg-slate-50 dark:bg-slate-900/50 focus:bg-white transition-colors">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-2 text-xs text-rose-600 font-bold flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div>
                <label for="file" class="block text-sm font-bold text-slate-800 dark:text-slate-200 mb-2">Pilih File Document <span class="text-rose-500">*</span></label>
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-300 dark:border-slate-600 border-dashed rounded-xl hover:border-indigo-500 hover:bg-indigo-50 transition-colors cursor-pointer bg-slate-50 dark:bg-slate-900/50" onclick="document.getElementById('file').click()">
                    <div class="space-y-1 text-center">
                        <div class="w-16 h-16 mx-auto bg-white dark:bg-slate-800 rounded-full flex items-center justify-center border border-slate-200 dark:border-slate-700 shadow-sm mb-4">
                            <svg class="h-8 w-8 text-indigo-500" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                        <div class="flex text-sm text-slate-600 dark:text-slate-400 justify-center">
                            <label for="file" class="relative cursor-pointer font-bold text-indigo-600 hover:text-indigo-700">
                                <span>Pilih dari komputer</span>
                                <input id="file" name="file" type="file" class="sr-only" required accept=".doc,.docx,.pdf,.xls,.xlsx,.jpg,.jpeg,.png">
                            </label>
                            <p class="pl-1 font-medium">atau tarik dan lepas</p>
                        </div>
                        <p class="text-xs font-semibold text-slate-400 mt-2">Format PDF, DOC, DOCX, XLS, XLSX, JPG, JPEG, PNG (Max 10MB)</p>
                        <div id="file-name-container" class="hidden mt-4 bg-indigo-50 border border-indigo-100 rounded-lg p-3 inline-block">
                            <p id="file-name" class="text-xs font-bold text-indigo-700 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span></span>
                            </p>
                        </div>
                    </div>
                </div>
                @error('file')
                    <p class="mt-2 text-xs text-rose-600 font-bold flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div class="pt-6 border-t border-slate-100 flex justify-end gap-3">
                <a href="{{ route("{$routePrefix}.letter_templates.index") }}" class="px-6 py-2.5 text-sm font-bold text-slate-600 dark:text-slate-400 hover:text-slate-900 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-xl transition-colors">Batal</a>
                <button type="submit" class="px-6 py-2.5 bg-slate-900 text-white text-sm font-bold rounded-xl hover:bg-slate-800 transition-colors shadow-sm">
                    Simpan Template
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('file').addEventListener('change', function(e) {
        var fileName = e.target.files[0].name;
        var display = document.getElementById('file-name');
        var container = document.getElementById('file-name-container');
        display.querySelector('span').textContent = fileName;
        container.classList.remove('hidden');
    });
</script>
@endsection
