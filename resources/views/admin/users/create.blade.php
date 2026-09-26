@extends('layouts.app')

@section('title', 'TAMBAH PENGGUNA BARU')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="mb-6">
        <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">Formulir Pendaftaran Akun</h1>
        <p class="text-sm text-slate-500 mt-1 font-medium">Buat akun baru untuk Staff, Admin, atau Super Admin.</p>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl border-2 border-slate-300 dark:border-slate-600 shadow-sm overflow-hidden">
        <div class="p-6 sm:p-8">
            <form method="POST" action="{{ route('super_admin.users.store') }}">
                @csrf
                
                <div class="space-y-6" x-data="{ role: '{{ old('role', 'staff') }}' }">
                    {{-- Nama --}}
                    <div>
                        <label for="name" class="block text-sm font-bold text-slate-800 dark:text-slate-200 mb-2">Nama Lengkap <span class="text-rose-500">*</span></label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                               class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-300 dark:border-slate-600 rounded-xl text-sm 
                                      focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors
                                      @error('name') border-rose-300 focus:ring-rose-400 @enderror"
                               placeholder="Masukkan nama lengkap">
                        @error('name')
                            <p class="mt-2 text-xs text-rose-600 font-bold flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-bold text-slate-800 dark:text-slate-200 mb-2">Alamat Email <span class="text-rose-500">*</span></label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required
                               class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-300 dark:border-slate-600 rounded-xl text-sm 
                                      focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors
                                      @error('email') border-rose-300 focus:ring-rose-400 @enderror"
                               placeholder="contoh@email.com">
                        @error('email')
                            <p class="mt-2 text-xs text-rose-600 font-bold flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Role --}}
                    <div>
                        <label for="role" class="block text-sm font-bold text-slate-800 dark:text-slate-200 mb-2">Peran (Role) <span class="text-rose-500">*</span></label>
                        <select id="role" name="role" x-model="role" required
                                class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-300 dark:border-slate-600 rounded-xl text-sm 
                                       focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors
                                       @error('role') border-rose-300 focus:ring-rose-400 @enderror">
                            <option value="staff" {{ old('role') == 'staff' ? 'selected' : '' }}>Staff</option>
                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="super_admin" {{ old('role') == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                        </select>
                        @error('role')
                            <p class="mt-2 text-xs text-rose-600 font-bold flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Admin Level (Hanya tampil jika role == admin) --}}
                    <div x-show="role === 'admin'" x-transition class="bg-indigo-50/50 p-4 rounded-xl border border-indigo-100">
                        <label for="admin_level" class="block text-sm font-bold text-slate-800 dark:text-slate-200 mb-2">Tingkatan Admin <span class="text-rose-500">*</span></label>
                        <select id="admin_level" name="admin_level"
                                class="w-full px-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-xl text-sm 
                                       focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors
                                       @error('admin_level') border-rose-300 focus:ring-rose-400 @enderror">
                            <option value="admin_1" {{ old('admin_level') == 'admin_1' ? 'selected' : '' }}>PROGAR</option>
                            <option value="admin_2" {{ old('admin_level') == 'admin_2' ? 'selected' : '' }}>PEKAS</option>
                            <option value="admin_3" {{ old('admin_level') == 'admin_3' ? 'selected' : '' }}>SETUM</option>
                        </select>
                        <p class="text-[10px] uppercase tracking-wider text-indigo-500 font-bold mt-2">Pilih level admin untuk menentukan urutan review surat.</p>
                        @error('admin_level')
                            <p class="mt-2 text-xs text-rose-600 font-bold flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Divisi --}}
                    <div x-show="role === 'staff'" x-transition class="bg-slate-50/50 p-4 rounded-xl border border-slate-100">
                        <label for="division_id" class="block text-sm font-bold text-slate-800 dark:text-slate-200 mb-2">Divisi (Asal Staff) <span class="text-rose-500">*</span></label>
                        <select id="division_id" name="division_id"
                                class="w-full px-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-xl text-sm 
                                       focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors
                                       @error('division_id') border-rose-300 focus:ring-rose-400 @enderror">
                            @foreach($divisions as $division)
                                <option value="{{ $division->id }}" {{ old('division_id') == $division->id ? 'selected' : '' }}>
                                    {{ $division->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('division_id')
                            <p class="mt-2 text-xs text-rose-600 font-bold flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="bg-indigo-50/50 dark:bg-indigo-900/20 border border-indigo-100 dark:border-indigo-800 p-4 rounded-xl flex items-start gap-3">
                        <div class="mt-0.5 text-indigo-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-800 dark:text-slate-200">Kata Sandi Otomatis</p>
                            <p class="text-xs text-slate-600 dark:text-slate-400 mt-1">Pengguna baru akan menggunakan kata sandi bawaan sistem: <span class="font-mono bg-white dark:bg-slate-800 px-1.5 py-0.5 rounded font-bold text-indigo-600">@Password1_</span></p>
                            <p class="text-xs text-slate-500 mt-0.5">Harap sampaikan kata sandi ini kepada pengguna agar mereka dapat *login* dan menggantinya di menu Profil.</p>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex items-center justify-end gap-3 pt-6 border-t border-slate-100">
                    <a href="{{ route('super_admin.users.index') }}" 
                       class="px-6 py-2.5 text-sm font-bold text-slate-600 dark:text-slate-400 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                        Batal
                    </a>
                    <button type="submit" 
                            class="px-6 py-2.5 text-sm font-bold text-white bg-slate-900 rounded-xl hover:bg-slate-800 transition-colors shadow-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        Simpan Pengguna
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
