@extends('layouts.app')

@section('title', 'EDIT PENGGUNA')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('super_admin.users.index') }}" class="p-2 bg-white dark:bg-slate-800 text-slate-500 hover:text-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/50 rounded-xl border border-slate-200 dark:border-slate-700 transition-colors shadow-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">Pembaruan Data Akun</h1>
            <p class="text-sm text-slate-500 mt-1 font-medium">Perbarui informasi, peran, atau tingkatan admin milik {{ $user->name }}.</p>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl border-2 border-slate-300 dark:border-slate-600 shadow-sm overflow-hidden">
        <div class="p-6 sm:p-8">
            <form method="POST" action="{{ route('super_admin.users.update', $user) }}">
                @csrf
                @method('PUT')
                
                <div class="space-y-6" x-data="{ role: '{{ old('role', $user->role) }}' }">
                    {{-- Nama --}}
                    <div>
                        <label for="name" class="block text-sm font-bold text-slate-800 dark:text-slate-200 mb-2">Nama Lengkap <span class="text-rose-500">*</span></label>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required autofocus
                               class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-300 dark:border-slate-600 rounded-xl text-sm 
                                      focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors
                                      @error('name') border-rose-300 focus:ring-rose-400 @enderror">
                        @error('name')
                            <p class="mt-2 text-xs text-rose-600 font-bold flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-bold text-slate-800 dark:text-slate-200 mb-2">Alamat Email <span class="text-rose-500">*</span></label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                               class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-300 dark:border-slate-600 rounded-xl text-sm 
                                      focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors
                                      @error('email') border-rose-300 focus:ring-rose-400 @enderror">
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
                            <option value="staff" {{ old('role', $user->role) == 'staff' ? 'selected' : '' }}>Staff</option>
                            <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin (Reviewer)</option>
                            <option value="super_admin" {{ old('role', $user->role) == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
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
                            <option value="" selected disabled>Pilih Level Admin</option>
                            <option value="admin_1" {{ old('admin_level', $user->admin_level) == 'admin_1' ? 'selected' : '' }}>PROGAR (Program Anggaran) - Level 1</option>
                            <option value="admin_2" {{ old('admin_level', $user->admin_level) == 'admin_2' ? 'selected' : '' }}>PEKAS (Pemegang Kas) - Level 2</option>
                            <option value="admin_3" {{ old('admin_level', $user->admin_level) == 'admin_3' ? 'selected' : '' }}>SETUM (Sekretaris Umum) - Level 3</option>
                        </select>
                        <p class="text-[10px] uppercase tracking-wider text-indigo-500 font-bold mt-2">Anda dapat membuat beberapa akun admin pada level yang sama sebagai backup.</p>
                        @error('admin_level')
                            <p class="mt-2 text-xs text-rose-600 font-bold flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Divisi --}}
                    <div x-show="role === 'staff'" x-transition class="bg-slate-50/50 p-4 rounded-xl border border-slate-100">
                        <label for="division_id" class="block text-sm font-bold text-slate-800 dark:text-slate-200 mb-2">Divisi (Asal Staff) <span class="text-[10px] uppercase tracking-wider text-slate-400 font-bold ml-1">(Opsional)</span></label>
                        <select id="division_id" name="division_id"
                                class="w-full px-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-xl text-sm 
                                       focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors
                                       @error('division_id') border-rose-300 focus:ring-rose-400 @enderror">
                            <option value="">Tanpa Divisi</option>
                            @foreach($divisions as $division)
                                <option value="{{ $division->id }}" {{ old('division_id', $user->division_id) == $division->id ? 'selected' : '' }}>
                                    {{ $division->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('division_id')
                            <p class="mt-2 text-xs text-rose-600 font-bold flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-8 flex items-center justify-end gap-3 pt-6 border-t border-slate-100">
                    <a href="{{ route('super_admin.users.index') }}" 
                       class="px-6 py-2.5 text-sm font-bold text-slate-600 dark:text-slate-400 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                        Batal
                    </a>
                    <button type="submit" 
                            class="px-6 py-2.5 text-sm font-bold text-white bg-slate-900 rounded-xl hover:bg-slate-800 transition-colors shadow-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Reset Password Card --}}
    @if($user->id !== auth()->id())
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-rose-200 shadow-sm mt-6 overflow-hidden">
        <div class="p-6 sm:p-8 border-l-4 border-rose-500">
            <h3 class="text-lg font-extrabold text-slate-900 dark:text-white mb-2 flex items-center gap-2">
                <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                Reset Password (Tindakan Administratif)
            </h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-6 font-medium">Sebagai Super Admin, Anda tidak diizinkan membuat kata sandi manual untuk pengguna lain demi menjaga privasi. Gunakan tombol di bawah ini untuk mengatur ulang kata sandi pengguna ini ke default <span class="font-bold text-rose-600 bg-rose-50 px-2 py-0.5 rounded border border-rose-100">@Password1_</span>.</p>
            
            <form method="POST" action="{{ route('super_admin.users.reset_password', $user) }}" onsubmit="window.confirmAction(event, 'Peringatan: Kata sandi akan di-reset menjadi \'@Password1_\'. Anda yakin ingin melanjutkan?', 'Ya, Reset Sandi!', '#f59e0b', 'warning');">
                @csrf
                <button type="submit" 
                        class="px-5 py-2.5 text-sm font-bold text-white bg-rose-600 rounded-xl hover:bg-rose-700 transition-colors shadow-sm inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                    Reset Password ke Default
                </button>
            </form>
        </div>
    </div>
    @endif
</div>
@endsection
