@extends('layouts.app')

@section('title', 'PERBAIKI SURAT')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="mb-6">
        <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">Resubmit Dokumen Revisi</h1>
        <p class="text-sm font-medium text-slate-500 mt-1">Perbarui judul atau lampirkan dokumen baru yang telah diperbaiki.</p>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl border-2 border-slate-300 dark:border-slate-600 shadow-sm overflow-hidden">
        <div class="p-6 sm:p-8">
            <form action="{{ route('staff.letters.update', $letter) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')
                
                {{-- Alert Revisi --}}
                <div class="p-5 bg-amber-50 border border-amber-200 rounded-xl">
                    <div class="flex gap-3 mb-3">
                        <div class="w-8 h-8 rounded-full bg-amber-100 flex items-center justify-center flex-shrink-0 text-amber-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-extrabold text-amber-900 mt-1">Catatan Revisi dari Admin</h3>
                        </div>
                    </div>
                    <p class="text-sm text-amber-800 ml-11 font-medium bg-amber-100/50 p-3 rounded-lg border border-amber-200/50">{{ $latestRevisionLog->notes ?? 'Mohon perbaiki dokumen Anda sesuai instruksi sebelumnya.' }}</p>
                </div>

                {{-- Judul Surat --}}
                <div>
                    <label for="title" class="block text-sm font-bold text-slate-800 dark:text-slate-200 mb-2">Judul Surat <span class="text-rose-500">*</span></label>
                    <input type="text" id="title" name="title" value="{{ old('title', $letter->title) }}" required
                           class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-300 dark:border-slate-600 rounded-xl text-sm 
                                  focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                    @error('title')
                        <p class="mt-2 text-xs text-rose-600 font-bold flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>{{ $message }}</p>
                    @enderror
                </div>

                {{-- File (Optional) --}}
                <div class="p-5 border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900/50">
                    <label for="file" class="block text-sm font-bold text-slate-800 dark:text-slate-200 mb-1">Dokumen Baru <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 ml-1">(Opsional)</span></label>
                    <p class="text-xs font-medium text-slate-500 mb-4">Jika dokumen fisik diperbaiki, silakan unggah versi terbarunya di sini.</p>
                    
                    <div class="relative">
                        <input type="file" id="file" name="file" accept=".pdf,.doc,.docx"
                               class="block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-white file:text-slate-700 file:border file:border-slate-300 hover:file:bg-slate-100 border border-slate-300 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-800 cursor-pointer focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                    </div>
                    @error('file')
                        <p class="mt-2 text-xs text-rose-600 font-bold flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>{{ $message }}</p>
                    @enderror
                </div>

                {{-- Submit --}}
                <div class="pt-6 mt-8 flex items-center justify-end gap-3 border-t border-slate-100">
                    <a href="{{ route('staff.letters.show', $letter) }}" class="px-6 py-2.5 text-sm font-bold text-slate-600 dark:text-slate-400 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">Batal</a>
                    <button type="submit" class="px-6 py-2.5 text-sm font-bold text-white bg-slate-900 rounded-xl hover:bg-slate-800 transition-colors shadow-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        Ajukan Ulang Surat
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
