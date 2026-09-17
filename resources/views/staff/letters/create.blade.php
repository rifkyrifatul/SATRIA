@extends('layouts.app')

@section('title', 'BUAT SURAT BARU')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">Formulir Pengajuan Dokumen</h1>
            <p class="text-sm font-medium text-slate-500 mt-1">Isi detail di bawah ini untuk mengajukan surat atau dokumen baru.</p>
        </div>
        <a href="{{ route('staff.letters.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-slate-500 hover:text-indigo-600 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Daftar
        </a>
    </div>

    {{-- Banner Panduan & Petunjuk Pengajuan --}}
    <div class="bg-gradient-to-r from-indigo-900 via-slate-900 to-indigo-950 rounded-2xl p-6 text-white shadow-lg relative overflow-hidden border border-indigo-800/40">
        <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-indigo-500/10 rounded-full blur-2xl pointer-events-none"></div>
        <div class="flex items-start gap-4 relative z-10">
            <div class="w-10 h-10 rounded-xl bg-indigo-500/20 border border-indigo-400/30 flex items-center justify-center flex-shrink-0 text-indigo-300 shadow-inner">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="flex-1">
                <h3 class="font-extrabold text-base text-white flex items-center gap-2">
                    Panduan & Cara Pengajuan Surat
                </h3>
                <p class="text-xs text-indigo-200/90 mt-1">Ikuti 4 langkah mudah di bawah ini agar surat Anda cepat diproses dan diverifikasi:</p>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mt-4">
                    <div class="bg-white/10 backdrop-blur-sm border border-white/10 p-3 rounded-xl flex flex-col justify-between">
                        <div>
                            <div class="flex items-center gap-2 font-bold text-indigo-200 text-xs mb-1">
                                <span class="w-5 h-5 rounded-full bg-indigo-500 text-white flex items-center justify-center text-[10px] font-extrabold">1</span>
                                Pilih Alur Surat
                            </div>
                            <p class="text-slate-300 text-[11px] leading-snug">
                                <strong class="text-indigo-200">• Alur Bertahap:</strong> Proggar &rarr; Pekas &rarr; SETUM &rarr; Kasek.<br>
                                <strong class="text-indigo-200">• Alur Langsung SETUM:</strong> Langsung ke SETUM &rarr; Kasek (tanpa Proggar & Pekas).
                            </p>
                        </div>
                    </div>

                    <div class="bg-white/10 backdrop-blur-sm border border-white/10 p-3 rounded-xl flex flex-col justify-between">
                        <div>
                            <div class="flex items-center gap-2 font-bold text-indigo-200 text-xs mb-1">
                                <span class="w-5 h-5 rounded-full bg-indigo-500 text-white flex items-center justify-center text-[10px] font-extrabold">2</span>
                                Judul Surat
                            </div>
                            <p class="text-slate-300 text-[11px] leading-snug">Ketikkan perihal/nama dokumen dengan jelas agar mudah diidentifikasi.</p>
                        </div>
                    </div>

                    <div class="bg-white/10 backdrop-blur-sm border border-white/10 p-3 rounded-xl flex flex-col justify-between">
                        <div>
                            <div class="flex items-center gap-2 font-bold text-indigo-200 text-xs mb-1">
                                <span class="w-5 h-5 rounded-full bg-indigo-500 text-white flex items-center justify-center text-[10px] font-extrabold">3</span>
                                Unggah Dokumen
                            </div>
                            <p class="text-slate-300 text-[11px] leading-snug">File format <strong>PDF, DOC, DOCX</strong> dengan ukuran maksimal <strong>10 MB</strong>.</p>
                        </div>
                    </div>

                    <div class="bg-white/10 backdrop-blur-sm border border-white/10 p-3 rounded-xl flex flex-col justify-between">
                        <div>
                            <div class="flex items-center gap-2 font-bold text-indigo-200 text-xs mb-1">
                                <span class="w-5 h-5 rounded-full bg-indigo-500 text-white flex items-center justify-center text-[10px] font-extrabold">4</span>
                                Pantau Posisi
                            </div>
                            <p class="text-slate-300 text-[11px] leading-snug">Cek perkembangan verifikasi & posisi surat di menu <strong>Posisi Surat</strong>.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl border-2 border-slate-300 dark:border-slate-600 shadow-sm overflow-hidden">
        <div class="p-6 sm:p-8">
            <form id="uploadLetterForm" action="{{ route('staff.letters.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                {{-- Alur Pengajuan --}}
                <div>
                    <label for="category_id" class="block text-sm font-bold text-slate-800 dark:text-slate-200 mb-1">Alur Pengajuan <span class="text-rose-500">*</span></label>
                    <p class="text-xs text-slate-500 mb-2">Pilih alur persetujuan surat yang sesuai:</p>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mb-3 text-xs">
                        <div class="p-3 bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 rounded-xl">
                            <span class="font-bold text-indigo-600 dark:text-indigo-400 block mb-0.5">1. Alur Bertahap</span>
                            <span class="text-slate-600 dark:text-slate-400 leading-relaxed">Surat akan melewati pemeriksaan berjenjang: <strong>Proggar &rarr; Pekas &rarr; SETUM &rarr; Kasek</strong>.</span>
                        </div>
                        <div class="p-3 bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 rounded-xl">
                            <span class="font-bold text-indigo-600 dark:text-indigo-400 block mb-0.5">2. Alur Langsung SETUM</span>
                            <span class="text-slate-600 dark:text-slate-400 leading-relaxed">Surat langsung masuk ke <strong>SETUM &rarr; Kasek</strong> (tanpa harus melewati Proggar & Pekas).</span>
                        </div>
                    </div>
                    <select id="category_id" name="category_id" required
                            class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-300 dark:border-slate-600 rounded-xl text-sm 
                                   focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                        <option value="">-- Pilih Alur Pengajuan --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="mt-2 text-xs text-rose-600 font-bold flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>{{ $message }}</p>
                    @enderror
                </div>

                {{-- Judul Surat --}}
                <div>
                    <label for="title" class="block text-sm font-bold text-slate-800 dark:text-slate-200 mb-1">Judul Surat <span class="text-rose-500">*</span></label>
                    <p class="text-xs text-slate-500 mb-2">Masukan perihal atau nama surat secara singkat dan spesifik.</p>
                    <input type="text" id="title" name="title" value="{{ old('title') }}" required autofocus
                           placeholder="Contoh: Permohonan Cuti Tahunan 2026"
                           class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-300 dark:border-slate-600 rounded-xl text-sm 
                                  focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors placeholder:text-slate-400">
                    @error('title')
                        <p class="mt-2 text-xs text-rose-600 font-bold flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>{{ $message }}</p>
                    @enderror
                </div>

                {{-- File --}}
                <div>
                    <label for="file" class="block text-sm font-bold text-slate-800 dark:text-slate-200 mb-1">Pilih Dokumen Surat <span class="text-rose-500">*</span></label>
                    <p class="text-xs text-slate-500 mb-2">Unggah draf surat Anda. Ekstensi yang diizinkan: <strong>.pdf, .doc, .docx</strong> (Maksimal 10 MB).</p>
                    <div class="relative">
                        <input type="file" id="file" name="file" required accept=".pdf,.doc,.docx"
                               class="block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-slate-900 file:text-white hover:file:bg-slate-800 file:transition-colors border border-slate-300 dark:border-slate-600 rounded-xl bg-slate-50 dark:bg-slate-900/50 cursor-pointer focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    @error('file')
                        <p class="mt-2 text-xs text-rose-600 font-bold flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>{{ $message }}</p>
                    @enderror
                </div>

                {{-- Submit --}}
                <div class="pt-6 mt-8 flex items-center justify-end gap-3 border-t border-slate-100">
                    <a href="{{ route('staff.dashboard') }}" class="px-6 py-2.5 text-sm font-bold text-slate-600 dark:text-slate-400 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">Batal</a>
                    <button type="submit" id="submitBtn" class="px-6 py-2.5 text-sm font-bold text-white bg-slate-900 rounded-xl hover:bg-slate-800 transition-colors shadow-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Unggah Dokumen
                    </button>
                </div>
            </form>
            
        </div>
    </div>
</div>

<script>
    document.getElementById('uploadLetterForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const form = e.target;
        const submitBtn = document.getElementById('submitBtn');
        const originalBtnHtml = submitBtn.innerHTML;
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></div> Mengunggah...';

        // Clear previous errors
        document.querySelectorAll('.ajax-error').forEach(el => el.remove());

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await response.json();

            if (response.ok) {
                Toast.fire({
                    icon: 'success',
                    title: data.message
                });
                window.location.href = data.redirect;
            } else {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnHtml;

                if (response.status === 422) {
                    // Validation errors
                    const errors = data.errors;
                    for (const field in errors) {
                        const input = form.querySelector(`[name="${field}"]`);
                        if (input) {
                            const errorP = document.createElement('p');
                            errorP.className = 'ajax-error mt-2 text-xs text-rose-600 font-bold flex items-center gap-1';
                            errorP.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>${errors[field][0]}`;
                            input.parentElement.appendChild(errorP);
                        }
                    }
                    Toast.fire({
                        icon: 'error',
                        title: 'Validasi Gagal! Periksa kembali isian Anda.'
                    });
                } else {
                    Toast.fire({
                        icon: 'error',
                        title: data.message || 'Terjadi kesalahan.'
                    });
                }
            }
        } catch (error) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnHtml;
            Toast.fire({
                icon: 'error',
                title: 'Terjadi kesalahan jaringan.'
            });
        }
    });
</script>
@endsection
