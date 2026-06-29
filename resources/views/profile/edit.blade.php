@extends('layouts.app')

@section('title', 'PROFIL SAYA')

@section('content')
<div class="max-w-4xl mx-auto space-y-6 pb-10">

    {{-- ⚠️ Banner Paksa Ganti Password --}}
    @if(auth()->user()->must_change_password)
    <div class="relative overflow-hidden bg-gradient-to-r from-amber-500 to-orange-500 rounded-xl p-4 sm:p-5 shadow-lg border border-amber-400/50">
        <div class="absolute inset-0 bg-white/5 animate-pulse rounded-xl pointer-events-none"></div>
        <div class="relative flex items-start gap-4">
            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-white/20 flex items-center justify-center mt-0.5">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-white font-extrabold text-sm tracking-wide">⚠️ Keamanan Akun — Wajib Ganti Password!</p>
                <p class="text-amber-50 text-xs mt-1 leading-relaxed">
                    Anda saat ini menggunakan <strong>kata sandi bawaan sistem</strong> yang tidak aman. Silakan ganti kata sandi sekarang di tab <strong>Ubah Password</strong> di bawah ini sebelum dapat melanjutkan aktivitas.
                </p>
            </div>
        </div>
    </div>
    @endif

    {{-- Profile Header Card (Horizontal) --}}
    <div class="bg-white dark:bg-slate-800 rounded-xl border-2 border-slate-300 dark:border-slate-600 shadow-sm p-6 sm:p-8 flex flex-col sm:flex-row items-center sm:items-start gap-6">
        @if(auth()->user()->profile_photo)
            <img src="{{ asset('storage/' . auth()->user()->profile_photo) }}" alt="Avatar" class="flex-shrink-0 w-24 h-24 rounded-full object-cover shadow-sm ring-4 ring-slate-50">
        @else
            <div class="flex-shrink-0 w-24 h-24 rounded-full bg-gradient-to-br from-indigo-500 to-indigo-600 flex items-center justify-center text-3xl font-extrabold text-white uppercase shadow-sm ring-4 ring-slate-50">
                {{ substr(auth()->user()->name, 0, 1) }}
            </div>
        @endif
        <div class="flex-1 text-center sm:text-left mt-2 sm:mt-0">
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">{{ auth()->user()->name }}</h1>
            <p class="text-slate-500 text-sm font-medium mt-1">{{ auth()->user()->email }}</p>
            
            <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2 mt-4">
                @if(auth()->user()->role === 'super_admin')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-slate-900 text-white border border-slate-800 shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                        Super Admin
                    </span>
                @elseif(auth()->user()->role === 'admin')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-slate-900 text-white border border-slate-800 shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                        Administrator
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[10px] uppercase tracking-wider font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                        Level: <span class="font-extrabold">{{ auth()->user()->admin_level_label ?? str_replace('admin_', '', auth()->user()->admin_level) }}</span>
                    </span>
                @elseif(auth()->user()->role === 'staff')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-slate-900 text-white border border-slate-800 shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        Staff
                    </span>
                    @if(auth()->user()->division)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[10px] uppercase tracking-wider font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            Divisi: {{ auth()->user()->division->name }}
                        </span>
                    @endif
                @endif
            </div>
        </div>
    </div>

    {{-- Tabs Section --}}
    <div x-data="{ 
            tab: '{{ (auth()->user()->must_change_password || session('status') == 'password-updated' || $errors->hasBag('updatePassword')) ? 'password' : 'profil' }}' 
         }" 
         class="bg-white dark:bg-slate-800 rounded-xl border-2 border-slate-300 dark:border-slate-600 shadow-sm overflow-hidden">
        
        {{-- Tabs Navigation --}}
        <div class="flex border-b border-slate-200 dark:border-slate-700 bg-slate-50/50">
            <button @click="tab = 'profil'" 
                    :class="{ 'bg-indigo-600 text-white border-indigo-600 dark:bg-indigo-500 dark:border-indigo-500': tab === 'profil', 'bg-transparent text-slate-500 border-transparent hover:bg-indigo-50 hover:text-indigo-600 dark:text-slate-400 dark:hover:bg-slate-700 dark:hover:text-slate-200': tab !== 'profil' }"
                    class="flex-1 py-4 px-6 text-sm font-extrabold tracking-wide uppercase border-b-2 transition-all focus:outline-none flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                Informasi Pribadi
            </button>
            <button @click="tab = 'password'" 
                    :class="{ 'bg-indigo-600 text-white border-indigo-600 dark:bg-indigo-500 dark:border-indigo-500': tab === 'password', 'bg-transparent text-slate-500 border-transparent hover:bg-indigo-50 hover:text-indigo-600 dark:text-slate-400 dark:hover:bg-slate-700 dark:hover:text-slate-200': tab !== 'password' }"
                    class="flex-1 py-4 px-6 text-sm font-extrabold tracking-wide uppercase border-b-2 transition-all focus:outline-none flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                Ubah Password
            </button>
        </div>

        {{-- Tab Content 1: Edit Profil --}}
        <div x-show="tab === 'profil'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="p-6 sm:p-8">
                @csrf
                @method('PATCH')

                <div class="mb-8" x-data="{ photoName: null, photoPreview: null }">
                    <label class="block text-sm font-bold text-slate-800 dark:text-slate-200 mb-3">Foto Profil</label>
                    <div class="flex items-center gap-5">
                        
                        <!-- Current Photo / Initials -->
                        <div x-show="!photoPreview" class="flex-shrink-0">
                            @if(auth()->user()->profile_photo)
                                <img src="{{ asset('storage/' . auth()->user()->profile_photo) }}" alt="Avatar" class="w-20 h-20 rounded-full object-cover border-2 border-slate-100 shadow-sm">
                            @else
                                <div class="w-20 h-20 rounded-full bg-slate-100 dark:bg-slate-700/50 flex items-center justify-center text-2xl font-extrabold text-slate-400 shadow-sm border border-slate-200 dark:border-slate-700">
                                    {{ substr(auth()->user()->name, 0, 1) }}
                                </div>
                            @endif
                        </div>

                        <!-- Live Preview -->
                        <div x-show="photoPreview" style="display: none;" class="flex-shrink-0">
                            <span class="block w-20 h-20 rounded-full bg-cover bg-no-repeat bg-center border border-indigo-200 shadow-sm ring-2 ring-indigo-50"
                                  x-bind:style="'background-image: url(\'' + photoPreview + '\');'">
                            </span>
                        </div>

                        <div class="flex-1">
                            <input type="file" name="profile_photo" id="profile_photo" accept="image/jpeg,image/png,image/jpg" class="hidden"
                                   x-ref="photo"
                                   x-on:change="
                                        photoName = $refs.photo.files[0].name;
                                        const reader = new FileReader();
                                        reader.onload = (e) => {
                                            photoPreview = e.target.result;
                                        };
                                        reader.readAsDataURL($refs.photo.files[0]);
                                   ">
                            <button type="button" x-on:click.prevent="$refs.photo.click()" class="inline-flex items-center px-4 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-xs text-slate-600 dark:text-slate-400 hover:text-indigo-600 shadow-sm hover:bg-slate-50 dark:hover:bg-slate-700/50 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors">
                                Pilih Foto Baru
                            </button>
                            <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 mt-2">Maksimal 2MB. Format: JPG, PNG, JPEG.</p>
                        </div>
                    </div>
                    @error('profile_photo') <p class="mt-2 text-xs text-rose-600 font-bold flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-8">
                    <div>
                        <label class="block text-sm font-bold text-slate-800 dark:text-slate-200 mb-2">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" required
                               class="w-full rounded-xl border-slate-300 dark:border-slate-600 focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5 px-4 bg-slate-50 dark:bg-slate-900/50 focus:bg-white transition-colors">
                        @error('name') <p class="mt-2 text-xs text-rose-600 font-bold flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>{{ $message }}</p> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-bold text-slate-800 dark:text-slate-200 mb-2">Alamat Email</label>
                        <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required
                               class="w-full rounded-xl border-slate-300 dark:border-slate-600 focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5 px-4 bg-slate-50 dark:bg-slate-900/50 focus:bg-white transition-colors">
                        @error('email') <p class="mt-2 text-xs text-rose-600 font-bold flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex justify-end pt-6 border-t border-slate-100">
                    <button type="submit" class="px-6 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-sm font-bold shadow-sm transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

        {{-- Tab Content 2: Ubah Password --}}
        <div x-show="tab === 'password'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
            <form action="{{ route('password.update') }}" method="POST" class="p-6 sm:p-8">
                @csrf
                @method('PUT')
                    
                <div class="space-y-6 mb-8 max-w-xl mx-auto">
                    <div>
                        <label class="block text-sm font-bold text-slate-800 dark:text-slate-200 mb-2">Password Saat Ini</label>
                        <input type="password" name="current_password" placeholder="Masukkan password lama"
                               class="w-full rounded-xl border-slate-300 dark:border-slate-600 focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5 px-4 bg-slate-50 dark:bg-slate-900/50 focus:bg-white transition-colors">
                        @error('current_password', 'updatePassword') <p class="mt-2 text-xs text-rose-600 font-bold flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-800 dark:text-slate-200 mb-2">Password Baru</label>
                        <input type="password" name="password" placeholder="Masukkan password baru"
                               class="w-full rounded-xl border-slate-300 dark:border-slate-600 focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5 px-4 bg-slate-50 dark:bg-slate-900/50 focus:bg-white transition-colors">
                        @error('password', 'updatePassword') <p class="mt-2 text-xs text-rose-600 font-bold flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-800 dark:text-slate-200 mb-2">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" placeholder="Ulangi password baru"
                               class="w-full rounded-xl border-slate-300 dark:border-slate-600 focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5 px-4 bg-slate-50 dark:bg-slate-900/50 focus:bg-white transition-colors">
                    </div>
                </div>

                <div class="flex justify-end pt-6 border-t border-slate-100">
                    <button type="submit" class="px-6 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-sm font-bold shadow-sm transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        Perbarui Password
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection
