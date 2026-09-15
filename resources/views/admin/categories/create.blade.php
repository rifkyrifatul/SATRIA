@extends('layouts.app')

@section('title', 'TAMBAH ALUR PENGAJUAN')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route((Auth::user()->role === 'super_admin' ? 'super_admin' : 'admin') . '.categories.index') }}" class="p-2 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/50 text-slate-500 transition-colors shadow-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">Formulir Alur Pengajuan Baru</h1>
            <p class="text-sm text-slate-500 mt-1">Lengkapi informasi dan tentukan alur verifikasi birokrasi.</p>
        </div>
    </div>

    <form action="{{ route((Auth::user()->role === 'super_admin' ? 'super_admin' : 'admin') . '.categories.store') }}" method="POST" class="bg-white dark:bg-slate-800 rounded-xl border-2 border-slate-300 dark:border-slate-600 shadow-sm p-6 sm:p-8">
        @csrf

        {{-- Input Nama Alur Pengajuan --}}
        <div class="mb-8">
            <label for="name" class="block text-sm font-bold text-slate-800 dark:text-slate-200 mb-2">Nama Alur Pengajuan <span class="text-rose-500">*</span></label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                   class="w-full rounded-xl border-slate-300 dark:border-slate-600 focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5 px-4 bg-slate-50 dark:bg-slate-900/50 focus:bg-white transition-colors"
                   placeholder="Misal: Surat Keputusan (SK), Surat Edaran, dll">
            @error('name')
                <p class="mt-2 text-xs text-rose-600 font-bold flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Custom Radio / Card Selection untuk Review Type --}}
        <div x-data="{ reviewType: '{{ old('review_type', 'bertahap') }}' }" class="mb-8">
            <label class="block text-sm font-bold text-slate-800 dark:text-slate-200 mb-3">Tentukan Alur Review Birokrasi <span class="text-rose-500">*</span></label>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Opsi 1: Bertahap --}}
                <label class="relative cursor-pointer group" @click="reviewType = 'bertahap'">
                    <input type="radio" name="review_type" value="bertahap" class="sr-only" x-model="reviewType">
                    <div :class="reviewType === 'bertahap' ? 'border-indigo-500 bg-indigo-50/50 shadow-md ring-1 ring-indigo-500' : 'border-slate-200 dark:border-slate-700 hover:border-indigo-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 bg-white dark:bg-slate-800'" 
                         class="p-5 rounded-xl border-2 transition-all h-full relative overflow-hidden">
                        
                        <!-- Dekorasi -->
                        <div class="absolute -right-4 -top-4 w-16 h-16 rounded-full bg-indigo-100 opacity-50 blur-xl"></div>

                        <div class="flex items-start justify-between mb-3 relative z-10">
                            <div class="flex items-center gap-3">
                                <div :class="reviewType === 'bertahap' ? 'border-indigo-500 bg-indigo-500' : 'border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800'" 
                                     class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition-colors shadow-sm">
                                    <div :class="reviewType === 'bertahap' ? 'scale-100' : 'scale-0'" 
                                         class="w-2 h-2 rounded-full bg-white dark:bg-slate-800 transition-transform duration-200"></div>
                                </div>
                                <span class="font-extrabold text-slate-800 dark:text-slate-200" :class="reviewType === 'bertahap' ? 'text-indigo-900' : ''">Alur Bertahap</span>
                            </div>
                            <span class="px-2.5 py-1 bg-indigo-100 text-indigo-700 text-[10px] uppercase font-bold rounded-md tracking-wider">Standar</span>
                        </div>
                        <p class="text-xs text-slate-500 mt-2 pl-8 leading-relaxed relative z-10">
                            Surat akan direview berjenjang mulai dari <span class="font-bold text-slate-700 dark:text-slate-300">PROGAR &rarr; PEKAS &rarr; SETUM</span>. Cocok untuk dokumen umum yang butuh verifikasi anggaran ketat.
                        </p>
                    </div>
                </label>

                {{-- Opsi 2: Langsung SETUM --}}
                <label class="relative cursor-pointer group" @click="reviewType = 'langsung_admin_3'">
                    <input type="radio" name="review_type" value="langsung_admin_3" class="sr-only" x-model="reviewType">
                    <div :class="reviewType === 'langsung_admin_3' ? 'border-emerald-500 bg-emerald-50/50 shadow-md ring-1 ring-emerald-500' : 'border-slate-200 dark:border-slate-700 hover:border-emerald-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 bg-white dark:bg-slate-800'" 
                         class="p-5 rounded-xl border-2 transition-all h-full relative overflow-hidden">
                        
                        <!-- Dekorasi -->
                        <div class="absolute -right-4 -top-4 w-16 h-16 rounded-full bg-emerald-100 opacity-50 blur-xl"></div>

                        <div class="flex items-start justify-between mb-3 relative z-10">
                            <div class="flex items-center gap-3">
                                <div :class="reviewType === 'langsung_admin_3' ? 'border-emerald-500 bg-emerald-500' : 'border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800'" 
                                     class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition-colors shadow-sm">
                                    <div :class="reviewType === 'langsung_admin_3' ? 'scale-100' : 'scale-0'" 
                                         class="w-2 h-2 rounded-full bg-white dark:bg-slate-800 transition-transform duration-200"></div>
                                </div>
                                <span class="font-extrabold text-slate-800 dark:text-slate-200" :class="reviewType === 'langsung_admin_3' ? 'text-emerald-900' : ''">Langsung SETUM</span>
                            </div>
                            <span class="px-2.5 py-1 bg-emerald-100 text-emerald-700 text-[10px] uppercase font-bold rounded-md tracking-wider">Cepat (Bypass)</span>
                        </div>
                        <p class="text-xs text-slate-500 mt-2 pl-8 leading-relaxed relative z-10">
                            Surat <span class="font-bold text-rose-600">melewati</span> PROGAR dan PEKAS, dan langsung masuk ke meja <span class="font-bold text-emerald-700">SETUM</span>. Cocok untuk dokumen darurat/mendesak.
                        </p>
                    </div>
                </label>
            </div>
            @error('review_type')
                <p class="mt-2 text-xs text-rose-600 font-bold flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <div class="pt-6 border-t border-slate-100 flex justify-end gap-3">
            <a href="{{ route((Auth::user()->role === 'super_admin' ? 'super_admin' : 'admin') . '.categories.index') }}" class="px-6 py-2.5 text-sm font-bold text-slate-600 dark:text-slate-400 hover:text-slate-900 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-xl transition-colors">Batal</a>
            <button type="submit" class="px-6 py-2.5 bg-slate-900 text-white text-sm font-bold rounded-xl hover:bg-slate-800 transition-colors shadow-sm">
                Simpan & Daftarkan Alur Pengajuan
            </button>
        </div>
    </form>
</div>
@endsection
