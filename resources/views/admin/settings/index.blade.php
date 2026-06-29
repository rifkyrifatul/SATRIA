@extends('layouts.app')

@section('title', 'KONFIGURASI SURAT')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">Konfigurasi Surat</h1>
        <p class="text-sm text-slate-500 mt-1 font-medium">Atur konfigurasi default untuk format surat, nama instansi, dan penanda tangan.</p>
    </div>

    <form action="{{ route('super_admin.settings.update') }}" method="POST" class="bg-white dark:bg-slate-800 rounded-xl border-2 border-slate-300 dark:border-slate-600 shadow-sm p-6 sm:p-8 space-y-6">
        @csrf

        <div class="grid grid-cols-1 gap-6">
            @forelse($settings as $setting)
                <div>
                    <label for="{{ $setting->key }}" class="block text-sm font-bold text-slate-800 dark:text-slate-200 mb-2">{{ $setting->label }}</label>
                    
                    @if($setting->type === 'textarea')
                        <textarea name="{{ $setting->key }}" id="{{ $setting->key }}" rows="3"
                                  class="w-full rounded-xl border-slate-300 dark:border-slate-600 focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5 px-4 bg-slate-50 dark:bg-slate-900/50 focus:bg-white transition-colors">{{ old($setting->key, $setting->value) }}</textarea>
                    @else
                        <input type="{{ $setting->type === 'number' ? 'number' : 'text' }}" 
                               name="{{ $setting->key }}" id="{{ $setting->key }}" 
                               value="{{ old($setting->key, $setting->value) }}"
                               class="w-full rounded-xl border-slate-300 dark:border-slate-600 focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5 px-4 bg-slate-50 dark:bg-slate-900/50 focus:bg-white transition-colors">
                    @endif
                </div>
            @empty
                <div class="p-6 rounded-xl bg-amber-50 border border-amber-100 flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center flex-shrink-0 text-amber-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-amber-800">Konfigurasi Kosong</h3>
                        <p class="text-xs text-amber-700 mt-1">Belum ada konfigurasi yang ditambahkan ke database. Silakan jalankan seeder pengaturan.</p>
                    </div>
                </div>
            @endforelse
        </div>

        @if($settings->count() > 0)
        <div class="pt-6 border-t border-slate-100 flex justify-end">
            <button type="submit" class="px-6 py-2.5 bg-slate-900 text-white text-sm font-bold rounded-xl hover:bg-slate-800 transition-colors shadow-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Simpan Konfigurasi
            </button>
        </div>
        @endif
    </form>
</div>
@endsection
