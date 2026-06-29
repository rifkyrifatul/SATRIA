@extends('layouts.app')

@section('title', 'Disposisi Surat Masuk & Keluar')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="mb-6">
        <a href="{{ route('admin.mail_registries.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-slate-500 hover:text-indigo-600 transition-colors mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Daftar
        </a>
        <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">Formulir Disposisi Surat</h1>
        <p class="text-sm text-slate-500 mt-1">Teruskan surat (No: {{ $mailRegistry->reference_number }}) kepada Staff terkait.</p>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl border-2 border-slate-300 dark:border-slate-600 shadow-sm p-6">
        <form action="{{ route('admin.mail_registries.disposition.store', $mailRegistry) }}" method="POST" class="space-y-6">
            @csrf

            <div class="space-y-4" x-data="{
                toggleAll(type) {
                    const checkboxes = document.querySelectorAll('.disposisi-checkbox');
                    let targetCheckboxes = [];
                    if (type === 'all') {
                        targetCheckboxes = Array.from(checkboxes);
                    } else if (type === 'admin') {
                        targetCheckboxes = Array.from(checkboxes).filter(cb => cb.dataset.role === 'admin' || cb.dataset.role === 'super_admin');
                    } else if (type === 'staff') {
                        targetCheckboxes = Array.from(checkboxes).filter(cb => cb.dataset.role === 'staff');
                    }
                    
                    const allChecked = targetCheckboxes.length > 0 && targetCheckboxes.every(cb => cb.checked);
                    targetCheckboxes.forEach(cb => cb.checked = !allChecked);
                }
            }">
                <div>
                    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Pilih Penerima Disposisi</label>
                            <p class="text-xs text-slate-500">Pilih satu atau lebih staff yang akan menerima surat ini.</p>
                        </div>
                        
                        <div class="flex items-center gap-2">
                            <button type="button" @click="toggleAll('all')" class="px-4 py-2 bg-white dark:bg-slate-700 hover:bg-slate-50 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 text-sm font-bold rounded-lg border border-slate-300 dark:border-slate-600 shadow-sm transition-all hover:shadow hover:border-indigo-300">
                                Semua Staff & Admin
                            </button>
                            <button type="button" @click="toggleAll('admin')" class="px-4 py-2 bg-white dark:bg-slate-700 hover:bg-slate-50 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 text-sm font-bold rounded-lg border border-slate-300 dark:border-slate-600 shadow-sm transition-all hover:shadow hover:border-indigo-300">
                                Semua Admin
                            </button>
                            <button type="button" @click="toggleAll('staff')" class="px-4 py-2 bg-white dark:bg-slate-700 hover:bg-slate-50 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 text-sm font-bold rounded-lg border border-slate-300 dark:border-slate-600 shadow-sm transition-all hover:shadow hover:border-indigo-300">
                                Semua Staff
                            </button>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 max-h-60 overflow-y-auto p-4 border border-slate-300 dark:border-slate-600 rounded-xl bg-slate-50 dark:bg-slate-900/50">
                        @foreach($users as $user)
                            <label class="flex items-center gap-3 p-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg cursor-pointer hover:border-indigo-500 transition-colors">
                                <input type="checkbox" name="user_ids[]" value="{{ $user->id }}" data-role="{{ strtolower($user->role) }}" class="disposisi-checkbox w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                <div>
                                    <span class="block text-sm font-semibold text-slate-700 dark:text-slate-200">{{ $user->name }}</span>
                                    <span class="block text-xs text-slate-500">{{ Str::title(str_replace('_', ' ', $user->role)) }}</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    @error('user_ids') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Catatan / Instruksi Disposisi (Opsional)</label>
                    <textarea name="note" rows="3" class="w-full px-4 py-3 text-sm border border-slate-300 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-slate-50 dark:bg-slate-900/50 placeholder-slate-400" placeholder="Contoh: Tolong pelajari dan siapkan draft balasannya..."></textarea>
                    @error('note') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-200 dark:border-slate-700">
                <a href="{{ route('admin.mail_registries.index') }}" class="px-5 py-2.5 text-sm font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-xl transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-sm hover:shadow-md transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    Kirim Disposisi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
