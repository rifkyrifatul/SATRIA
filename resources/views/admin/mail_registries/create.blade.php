@extends('layouts.app')

@section('title', 'Tambah Arsip Surat Eksternal')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="mb-6">
        <a href="{{ route('admin.mail_registries.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-slate-500 hover:text-indigo-600 transition-colors mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Daftar
        </a>
        <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">Formulir Registrasi Baru</h1>
        <p class="text-sm text-slate-500 mt-1">Isi form di bawah ini untuk meregistrasi surat masuk atau surat keluar.</p>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl border-2 border-slate-300 dark:border-slate-600 shadow-sm p-6">
        <form action="{{ route('admin.mail_registries.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Jenis Surat</label>
                    <select name="type" required class="w-full px-4 py-2.5 text-sm border border-slate-300 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-slate-50 dark:bg-slate-900/50">
                        <option value="">-- Pilih Jenis --</option>
                        <option value="masuk" {{ old('type') == 'masuk' ? 'selected' : '' }}>Surat Masuk</option>
                        <option value="keluar" {{ old('type') == 'keluar' ? 'selected' : '' }}>Surat Keluar</option>
                    </select>
                    @error('type') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Tanggal Surat</label>
                    <input type="date" name="date" value="{{ old('date') }}" required class="w-full px-4 py-2.5 text-sm border border-slate-300 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-slate-50 dark:bg-slate-900/50">
                    @error('date') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Nomor Surat</label>
                    <input type="text" name="reference_number" value="{{ old('reference_number') }}" required placeholder="Contoh: B/123/IV/2026" class="w-full px-4 py-2.5 text-sm border border-slate-300 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-slate-50 dark:bg-slate-900/50">
                    @error('reference_number') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Asal / Tujuan</label>
                    <input type="text" name="origin_destination" value="{{ old('origin_destination') }}" required placeholder="Pengirim atau penerima surat..." class="w-full px-4 py-2.5 text-sm border border-slate-300 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-slate-50 dark:bg-slate-900/50">
                    @error('origin_destination') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Perihal / Subject</label>
                <input type="text" name="subject" value="{{ old('subject') }}" required placeholder="Perihal surat..." class="w-full px-4 py-2.5 text-sm border border-slate-300 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-slate-50 dark:bg-slate-900/50">
                @error('subject') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">File Surat (PDF, maks 10MB)</label>
                <input type="file" name="file" accept=".pdf" required class="w-full px-4 py-2 text-sm border border-slate-300 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-slate-50 dark:bg-slate-900/50 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                @error('file') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-slate-200 dark:border-slate-700 mt-6">
                <a href="{{ route('admin.mail_registries.index') }}" class="px-5 py-2.5 text-sm font-bold text-slate-600 border border-slate-300 rounded-xl hover:bg-slate-50">Batal</a>
                <button type="submit" class="px-5 py-2.5 text-sm font-bold bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-colors">Simpan Surat</button>
            </div>
        </form>
    </div>
</div>
@endsection
