@extends('layouts.app')

@section('title', 'BUAT SURAT BARU')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="mb-6">
        <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">Formulir Pengajuan Dokumen</h1>
        <p class="text-sm font-medium text-slate-500 mt-1">Isi detail di bawah ini untuk mengajukan surat atau dokumen baru.</p>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl border-2 border-slate-300 dark:border-slate-600 shadow-sm overflow-hidden">
        <div class="p-6 sm:p-8">
            <form id="uploadLetterForm" action="{{ route('staff.letters.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                
                {{-- Info Box --}}
                <div class="flex gap-4 p-5 bg-indigo-50 border border-indigo-100 rounded-xl">
                    <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div class="text-sm text-indigo-900">
                        <p class="font-bold mb-1">Ketentuan Dokumen:</p>
                        <ul class="list-disc list-inside space-y-1 text-indigo-700/80 font-medium">
                            <li>Format yang didukung: <span class="font-bold text-indigo-800">.pdf, .doc, .docx</span></li>
                            <li>Ukuran maksimal: <span class="font-bold text-indigo-800">10 MB</span></li>
                        </ul>
                    </div>
                </div>

                {{-- Kategori Surat --}}
                <div>
                    <label for="category_id" class="block text-sm font-bold text-slate-800 dark:text-slate-200 mb-2">Kategori Surat <span class="text-rose-500">*</span></label>
                    <select id="category_id" name="category_id" required
                            class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-300 dark:border-slate-600 rounded-xl text-sm 
                                   focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                        <option value="">-- Pilih Kategori --</option>
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
                    <label for="title" class="block text-sm font-bold text-slate-800 dark:text-slate-200 mb-2">Judul Surat <span class="text-rose-500">*</span></label>
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
                    <label for="file" class="block text-sm font-bold text-slate-800 dark:text-slate-200 mb-2">Pilih Dokumen <span class="text-rose-500">*</span></label>
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
                
                // Tunggu sebentar agar toast terlihat, lalu redirect
                setTimeout(() => {
                    window.location.href = data.redirect;
                }, 1500);
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
