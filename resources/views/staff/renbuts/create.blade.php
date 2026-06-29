@extends('layouts.app')

@section('title', 'Buat Pengajuan Renbut')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <div class="mb-6">
        <a href="{{ route('staff.renbuts.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-slate-500 hover:text-indigo-600 transition-colors mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali
        </a>
        <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">Form Pengajuan Renbut</h1>
        <p class="text-sm text-slate-500 mt-1">Isi formulir di bawah ini untuk mengajukan rencana kebutuhan barang/anggaran ke bagian PROGAR.</p>
    </div>

    <form action="{{ route('staff.renbuts.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6" id="renbutForm">
        @csrf

        {{-- Header Info --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border-2 border-slate-300 dark:border-slate-600 shadow-sm p-6 space-y-4">
            <div>
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Judul Pengajuan <span class="text-rose-500">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" required
                       placeholder="Contoh: Pengajuan ATK Bulan Agustus"
                       class="w-full px-4 py-2.5 text-sm border border-slate-300 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-slate-50 dark:bg-slate-900/50">
                @error('title') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Keterangan / Deskripsi (Opsional)</label>
                <textarea name="description" rows="3"
                          placeholder="Tambahkan informasi pendukung pengajuan..."
                          class="w-full px-4 py-2.5 text-sm border border-slate-300 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-slate-50 dark:bg-slate-900/50">{{ old('description') }}</textarea>
                @error('description') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
            </div>
        </div>

        {{-- Upload & Budget --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border-2 border-slate-300 dark:border-slate-600 shadow-sm p-6 space-y-4">
            <div>
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Unggah Rincian Kebutuhan (Excel/PDF) <span class="text-rose-500">*</span></label>
                <p class="text-[11px] text-slate-500 mb-2">Lampirkan file daftar barang, harga, atau spesifikasi. Format yang diizinkan: .pdf, .xls, .xlsx, .doc, .docx (Maks. 10MB)</p>
                <input type="file" name="file_attachment" required accept=".pdf,.doc,.docx,.xls,.xlsx"
                       class="block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 border border-slate-300 dark:border-slate-600 rounded-xl bg-slate-50 dark:bg-slate-900/50 transition-colors">
                @error('file_attachment') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Estimasi Total Keseluruhan <span class="text-rose-500">*</span></label>
                <p class="text-[11px] text-slate-500 mb-2">Tuliskan total angka rupiah dari seluruh barang yang Anda ajukan di dalam lampiran.</p>
                <div class="relative max-w-md">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-500 text-sm font-bold font-mono">Rp</span>
                    <input type="number" name="total_estimated_budget" value="{{ old('total_estimated_budget') }}" required min="0"
                           placeholder="0"
                           class="w-full pl-12 pr-4 py-2.5 text-lg font-bold font-mono border border-slate-300 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-slate-50 dark:bg-slate-900/50 text-indigo-700 dark:text-indigo-400">
                </div>
                @error('total_estimated_budget') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
            </div>
        </div>
        
        <div class="flex justify-end gap-3">
            <a href="{{ route('staff.renbuts.index') }}" class="px-6 py-2.5 font-bold text-sm text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-colors">Batal</a>
            <button type="submit" class="px-6 py-2.5 font-bold text-sm text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-sm transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Ajukan Renbut
            </button>
        </div>
    </form>

</div>
@endsection
