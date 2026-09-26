@extends('layouts.app')

@section('title', 'KELOLA PENGGUNA')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">Daftar Akun</h1>
            <p class="text-sm text-slate-500 mt-1 font-medium">Kelola data super admin, admin (reviewer), dan staf (pengaju surat).</p>
        </div>
        
        <a href="{{ route('super_admin.users.create') }}" class="px-5 py-2.5 text-sm font-bold bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-colors flex items-center gap-2 whitespace-nowrap shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Pengguna
        </a>
    </div>

    {{-- ========================================================= --}}
    {{-- FILTER & PENCARIAN --}}
    {{-- ========================================================= --}}
    <form method="GET" action="{{ route('super_admin.users.index') }}" class="flex flex-col sm:flex-row gap-3 bg-white dark:bg-slate-800 p-4 rounded-xl border-2 border-slate-300 dark:border-slate-600 shadow-sm">
        <div class="relative flex-1">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau email..."
                   class="w-full pl-10 pr-4 py-2.5 text-sm border border-slate-300 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 dark:bg-slate-900/50 placeholder:text-slate-400 transition-colors">
        </div>
        
        <select name="role" onchange="this.form.submit()" class="w-full sm:w-48 py-2.5 px-4 text-sm border border-slate-300 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 dark:bg-slate-900/50 text-slate-600 dark:text-slate-400 transition-colors">
            <option value="">Semua Role</option>
            <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Hanya Admin</option>
            <option value="staff" {{ request('role') == 'staff' ? 'selected' : '' }}>Hanya Staff</option>
        </select>
        
        @if(request('search') || request('role'))
            <a href="{{ route('super_admin.users.index') }}" class="px-5 py-2.5 text-sm font-bold text-slate-600 dark:text-slate-400 hover:text-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 flex items-center shadow-sm transition-colors">Reset</a>
        @endif
        <button type="submit" class="px-5 py-2.5 text-sm font-bold bg-slate-900 text-white rounded-xl hover:bg-slate-800 transition-colors shadow-sm">Cari</button>
        
    </form>



    {{-- ========================================================= --}}
    {{-- TABEL KARYAWAN & ADMIN --}}
    {{-- ========================================================= --}}
    <div class="bg-white dark:bg-slate-800 rounded-xl border-2 border-slate-300 dark:border-slate-600 shadow-sm overflow-hidden" x-data="{ resetModalOpen: false, userToReset: null, userName: '' }">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr class="bg-indigo-600 dark:bg-indigo-800">
                        <th class="text-center px-4 py-4 text-[10px] font-extrabold text-white uppercase tracking-wider w-12 rounded-tl-lg">No</th>
                        <th class="px-6 py-4 text-xs font-extrabold text-white uppercase tracking-wider">Nama</th>
                        <th class="px-6 py-4 text-xs font-extrabold text-white uppercase tracking-wider">Role</th>
                        <th class="px-6 py-4 text-xs font-extrabold text-white uppercase tracking-wider">Divisi / Level</th>
                        <th class="px-6 py-4 text-xs font-extrabold text-white uppercase tracking-wider hidden md:table-cell">Bergabung</th>
                        <th class="px-6 py-4 text-xs font-extrabold text-white uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y-2 divide-slate-200 dark:divide-slate-700/50">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            <td class="px-4 py-4 text-center text-slate-400 font-mono text-xs font-semibold group-hover:text-indigo-500 transition-colors">
                                {{ $users->firstItem() + $loop->index }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-700/50 text-slate-600 dark:text-slate-400 flex items-center justify-center font-bold text-sm border border-slate-200 dark:border-slate-700">
                                        @if($user->profile_photo)
                                            <img src="{{ asset('storage/' . $user->profile_photo) }}" alt="Avatar" class="w-full h-full object-cover rounded-full">
                                        @else
                                            {{ substr($user->name, 0, 1) }}
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                            {{ $user->name }}
                                            @if(auth()->id() === $user->id)
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-900 text-white uppercase shadow-sm">Anda</span>
                                            @endif
                                        </p>
                                        <p class="text-xs text-slate-500 font-medium mt-0.5">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col items-start gap-1">
                                    @if($user->role === 'super_admin')
                                        <span class="px-2.5 py-1 rounded-md text-[10px] font-bold bg-rose-100 text-rose-700 uppercase tracking-wider border border-rose-200">Super Admin</span>
                                    @elseif($user->role === 'admin')
                                        <span class="px-2.5 py-1 rounded-md text-[10px] font-bold bg-indigo-100 text-indigo-700 uppercase tracking-wider border border-indigo-200">
                                            Admin
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-md text-[10px] font-bold bg-emerald-100 text-emerald-700 uppercase tracking-wider border border-emerald-200">
                                            Staff
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col items-start gap-1">
                                    @if($user->role === 'super_admin')
                                        <span class="text-xs font-bold text-slate-400">-</span>
                                    @elseif($user->role === 'admin')
                                        <span class="text-xs font-bold text-slate-700 dark:text-slate-300">{{ $user->admin_level_label }}</span>
                                    @else
                                        <span class="text-xs font-bold text-slate-700 dark:text-slate-300">{{ $user->division ? $user->division->name : '-' }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 hidden md:table-cell">
                                <span class="text-xs font-bold text-slate-600 dark:text-slate-400">{{ $user->created_at->format('d M Y') }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2 transition-opacity">
                                    {{-- Tombol Reset Password (Alpine.js) --}}
                                    @if(auth()->id() !== $user->id)
                                        <button @click="resetModalOpen = true; userToReset = {{ $user->id }}; userName = '{{ addslashes($user->name) }}'" 
                                                class="bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white border border-rose-200 text-xs px-3 py-1.5 rounded-lg font-bold transition-colors shadow-sm">
                                            Reset Sandi
                                        </button>
                                    @endif
                                    
                                    <a href="{{ route('super_admin.users.edit', $user) }}" class="p-2 rounded-lg text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    </a>

                                    @if(auth()->id() !== $user->id)
                                        <form method="POST" action="{{ route('super_admin.users.destroy', $user) }}" onsubmit="confirmDelete(event, 'Hapus pengguna ini secara permanen?');" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-2 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-16 px-6 text-center">
                                <div class="w-16 h-16 bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700 rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm">
                                    <svg class="w-8 h-8 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                </div>
                                <p class="text-slate-700 dark:text-slate-300 font-bold">Tidak ada pengguna ditemukan</p>
                                <p class="text-slate-500 font-medium text-sm mt-1 mb-6">Gunakan kata kunci pencarian yang lain.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $users->links() }}
            </div>
        @endif

        {{-- ========================================================= --}}
        {{-- MODAL RESET PASSWORD (ALPINE.JS) --}}
        {{-- ========================================================= --}}
        <div x-show="resetModalOpen" class="relative z-50" style="display: none;" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div x-show="resetModalOpen" x-transition.opacity class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div x-show="resetModalOpen" 
                         @click.away="resetModalOpen = false"
                         x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-slate-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-200 dark:border-slate-700">
                        
                        <div class="bg-white dark:bg-slate-800 px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-rose-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <svg class="h-6 w-6 text-rose-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                    </svg>
                                </div>
                                <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                                    <h3 class="text-base font-extrabold leading-6 text-slate-900 dark:text-white" id="modal-title">Reset Password Pengguna</h3>
                                    <div class="mt-2 text-sm text-slate-600 dark:text-slate-400 font-medium">
                                        <p>Anda yakin ingin mereset kata sandi untuk <span class="font-bold text-slate-900 dark:text-white" x-text="userName"></span>? Kata sandi akan diubah menjadi <span class="bg-rose-50 text-rose-600 border border-rose-200 px-1.5 py-0.5 rounded font-bold">@Password1_</span>.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-slate-50 dark:bg-slate-900/50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 border-t border-slate-100">
                            <!-- Form action dinamis di-generate melalui Alpine.js -->
                            <form method="POST" :action="`/super-admin/users/${userToReset}/reset-password`">
                                @csrf
                                <button type="submit" class="inline-flex w-full justify-center rounded-xl bg-rose-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-rose-700 sm:ml-3 sm:w-auto transition-colors">Reset Sandi</button>
                            </form>
                            <button type="button" @click="resetModalOpen = false" class="mt-3 inline-flex w-full justify-center rounded-xl bg-white dark:bg-slate-800 px-4 py-2.5 text-sm font-bold text-slate-700 dark:text-slate-300 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 sm:mt-0 sm:w-auto transition-colors">Batal</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection


