<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') - SIMSURAT</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Scripts (Vite) & Alpine.js -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Prevent FOUC (Flicker of Unstyled Content)
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        /* Custom scrollbar for sidebar */
        .sidebar-scroll::-webkit-scrollbar {
            width: 5px;
        }
        .sidebar-scroll::-webkit-scrollbar-track {
            background: transparent;
        }
        .sidebar-scroll::-webkit-scrollbar-thumb {
            background-color: #334155;
            border-radius: 10px;
        }
        /* Fix SweetAlert z-index to cover sidebar */
        .swal2-container {
            z-index: 99999 !important;
        }
    </style>
    <script>
        window.authUser = {
            id: {{ auth()->id() ?? 'null' }},
            role: '{{ auth()->user()->role ?? '' }}',
            admin_level: '{{ str_replace('admin_', '', auth()->user()->admin_level ?? '') }}'
        };
    </script>
</head>
<body class="bg-slate-50 dark:bg-slate-900 antialiased text-slate-600 dark:text-slate-300 flex h-screen overflow-hidden" 
    x-data="{ 
        sidebarOpen: true, 
        profileOpen: false, 
        notifOpen: false,
        isDark: document.documentElement.classList.contains('dark'),
        toggleTheme() {
            this.isDark = !this.isDark;
            if (this.isDark) {
                document.documentElement.classList.add('dark');
                localStorage.setItem('color-theme', 'dark');
            } else {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('color-theme', 'light');
            }
        }
    }">

    <!-- ================= KIRI: STICKY SIDEBAR ================= -->
    <aside 
        class="bg-slate-950 text-slate-400 w-64 flex-shrink-0 flex flex-col h-full transition-all duration-300 relative z-20"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full absolute sm:relative sm:w-20'">
        
        <!-- Logo Area -->
        <div class="h-16 flex items-center justify-between px-6 bg-slate-950 border-b border-slate-900/50">
            <div class="flex items-center gap-3 overflow-hidden mt-2" x-show="sidebarOpen">
                <div class="w-8 h-8 bg-indigo-500 rounded-lg flex items-center justify-center shadow-lg shadow-indigo-500/20">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 19v-8.93a2 2 0 01.89-1.664l7-4.666a2 2 0 012.22 0l7 4.666A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-1.14.76a2 2 0 01-2.22 0l-1.14-.76"></path>
                    </svg>
                </div>
                <div class="flex flex-col justify-center">
                    <span class="text-white font-extrabold text-lg tracking-tight leading-none">SIMSURAT</span>
                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-0.5 leading-none">Sistem Persuratan</span>
                </div>
                
            </div>
            <!-- Mobile Close Button -->
            <button @click="sidebarOpen = false" class="sm:hidden text-slate-400 hover:text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="my-2 border-t border-slate-800 mx-1"></div>

        <!-- User Profile (Interactive Widget) -->
        <div class="px-4 py-2">
            <a href="{{ route('profile.edit') }}" 
               class="flex items-center gap-3 p-2 -mx-2 rounded-xl hover:bg-slate-800/50 transition-colors group cursor-pointer relative overflow-hidden">
                <!-- Avatar with Status Indicator -->
                <div class="relative flex-shrink-0">
                    @if(auth()->user()->profile_photo)
                        <img src="{{ asset('storage/' . auth()->user()->profile_photo) }}" alt="Avatar" class="w-10 h-10 rounded-full object-cover border-2 border-slate-700 group-hover:border-indigo-500 transition-colors">
                    @else
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=6366f1&color=fff&bold=true" alt="Avatar" class="w-10 h-10 rounded-full object-cover border-2 border-slate-700 group-hover:border-indigo-500 transition-colors">
                    @endif
                    <div class="absolute bottom-0 right-0 w-3 h-3 bg-emerald-500 border-2 border-slate-950 rounded-full"></div>
                </div>
                
                <!-- User Details -->
                <div class="text-left flex-1 min-w-0" x-show="sidebarOpen">
                    <p class="text-sm font-bold text-white truncate group-hover:text-indigo-300 transition-colors">{{ auth()->user()->name }}</p>
                    @if(auth()->user()->role === 'super_admin')
                        <p class="text-[11px] font-medium text-indigo-400">Super Admin</p>
                    @elseif(auth()->user()->role === 'admin')
                        @php
                            $roleLabel = match(auth()->user()->admin_level) {
                                'admin_1' => 'PROGAR',
                                'admin_2' => 'PEKAS',
                                'admin_3' => 'SETUM',
                                default => 'Admin'
                            };
                        @endphp
                        <p class="text-[10px] font-bold text-blue-400 tracking-wider uppercase">{{ $roleLabel }}</p>
                    @else
                        <p class="text-[11px] font-medium text-slate-400 truncate">{{ auth()->user()->division->name ?? 'Staff' }}</p>
                    @endif
                </div>

                <!-- Chevron Icon -->
                <div class="text-slate-500 group-hover:text-indigo-400 transition-colors flex-shrink-0" x-show="sidebarOpen">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </div>
            </a>
        </div>
        
        <div class="my-2 border-t border-slate-800 mx-1"></div>

        <!-- Menu Sidebar -->
        <nav class="flex-1 overflow-y-auto sidebar-scroll py-6 px-3 space-y-1">
            
            @php
                $role = auth()->user()->role ?? 'guest';
                $currentRoute = Route::currentRouteName();
                
                // Helper function to check active route
                $isActive = function($routePatterns) use ($currentRoute) {
                    foreach((array)$routePatterns as $pattern) {
                        if(Str::is($pattern, $currentRoute)) return true;
                    }
                    return false;
                };

                // Helper for generating menu classes
                $menuClass = function($active) {
                    $base = "flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 group relative ";
                    if ($active) {
                        return $base . "bg-indigo-500 text-white shadow-lg shadow-indigo-500/30";
                    }
                    return $base . "text-slate-400 hover:text-white hover:bg-slate-800/50";
                };
            @endphp

            {{-- 1. SUPER ADMIN MENU --}}
            @if($role === 'super_admin')
                <div class="px-3 mb-2 mt-0 text-xs font-semibold uppercase tracking-wider text-slate-500">Menu Utama</div>
                <div class="my-4 border-t border-slate-800 mx-3"></div>
                <a href="{{ route('super_admin.dashboard') }}" class="{{ $menuClass($isActive('super_admin.dashboard')) }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    <span x-show="sidebarOpen">Dashboard</span>
                </a>
                <a href="{{ route('super_admin.letters.index') }}" class="{{ $menuClass($isActive('super_admin.letters.*')) }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <span x-show="sidebarOpen">Surat Masuk</span>
                </a>
                <a href="{{ route('super_admin.archives.index') }}" class="{{ $menuClass($isActive('super_admin.archives.*')) }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                    <span x-show="sidebarOpen">Arsip Surat</span>
                </a>
                <a href="{{ route('super_admin.mail_registries.index') }}" class="{{ $menuClass($isActive('super_admin.mail_registries.*')) }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    <span x-show="sidebarOpen">Surat Masuk & Keluar</span>
                </a><br>
                
                <div class="px-3 mb-2 text-xs font-semibold uppercase tracking-wider text-slate-500">Master Data</div>
                <div class="my-4 border-t border-slate-800 mx-3"></div>
                <a href="{{ route('super_admin.users.index') }}" class="{{ $menuClass($isActive('super_admin.users.*')) }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <span x-show="sidebarOpen">Kelola Pengguna</span>
                </a>
                <a href="{{ route('super_admin.categories.index') }}" class="{{ $menuClass($isActive('super_admin.categories.*')) }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                    <span x-show="sidebarOpen">Kelola Kategori</span>
                </a>
                <a href="{{ route('super_admin.letter_templates.index') }}" class="{{ $menuClass($isActive('super_admin.letter_templates.*')) }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/></svg>
                    <span x-show="sidebarOpen">Template Surat</span>
                </a><br>

                <div class="px-3 mb-2 text-xs font-semibold uppercase tracking-wider text-slate-500">Sistem</div>
                <div class="my-4 border-t border-slate-800 mx-3"></div>
                <a href="{{ route('super_admin.trash.index') }}" class="{{ $menuClass($isActive('super_admin.trash.*')) }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    <span x-show="sidebarOpen">Tong Sampah</span>
                </a>
                <a href="{{ route('super_admin.reports.index') }}" class="{{ $menuClass($isActive('super_admin.reports.*')) }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    <span x-show="sidebarOpen">Laporan & Statistik</span>
                </a>
                <a href="{{ route('super_admin.activity_logs.index') }}" class="{{ $menuClass($isActive('super_admin.activity_logs.*')) }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span x-show="sidebarOpen">Log Aktivitas</span>
                </a>



            @endif

            {{-- 2. ADMIN (REVIEWER) MENU --}}
            @if($role === 'admin')
                <div class="px-3 mb-2 mt-0 text-xs font-semibold uppercase tracking-wider text-slate-500">Utama</div>
                <div class="my-4 border-t border-slate-800 mx-3"></div>
                <a href="{{ route('admin.dashboard') }}" class="{{ $menuClass($isActive('admin.dashboard')) }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    <span x-show="sidebarOpen">Dashboard</span>
                </a><br>

                <div class="px-3 mb-2 text-xs font-semibold uppercase tracking-wider text-slate-500">Lalu Lintas Surat</div>
                <div class="my-4 border-t border-slate-800 mx-3"></div>
                <a href="{{ route('admin.reviews.index') }}" class="{{ $menuClass($isActive('admin.reviews.index')) }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span x-show="sidebarOpen">Surat Masuk</span>
                </a>
                <a href="{{ route('admin.reviews.history') }}" class="{{ $menuClass($isActive('admin.reviews.history')) }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span x-show="sidebarOpen">Riwayat Review</span>
                </a>
                <a href="{{ route('admin.mail_registries.index') }}" class="{{ $menuClass($isActive('admin.mail_registries.*')) }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    <span x-show="sidebarOpen">Surat Masuk & Keluar</span>
                </a><br>

                <div class="px-3 mb-2 text-xs font-semibold uppercase tracking-wider text-slate-500">Penyimpanan & Utilitas</div>
                <div class="my-4 border-t border-slate-800 mx-3"></div>
                <a href="{{ route('admin.archives.index') }}" class="{{ $menuClass($isActive('admin.archives.*')) }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                    <span x-show="sidebarOpen">Arsip Surat</span>
                </a>
                @if(Auth::user()->admin_level === 'admin_1')
                <a href="{{ route('admin.renbuts.index') }}" class="{{ $menuClass($isActive('admin.renbuts.*')) }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    <span x-show="sidebarOpen">Rencana Kebutuhan</span>
                </a>
                @endif
                <a href="{{ route('admin.letter_templates.index') }}" class="{{ $menuClass($isActive('admin.letter_templates.*')) }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/></svg>
                    <span x-show="sidebarOpen">Template Surat</span>
                </a>
                @if(Auth::user()->admin_level === 'admin_3')
                <a href="{{ route('admin.categories.index') }}" class="{{ $menuClass($isActive('admin.categories.*')) }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                    <span x-show="sidebarOpen">Kelola Kategori</span>
                </a>
                @endif
            @endif

            {{-- 3. STAFF MENU --}}
            @if($role === 'staff')
                <div class="px-3 mb-2 mt-0 text-xs font-semibold uppercase tracking-wider text-slate-500">Dashboard</div>
                <div class="my-4 border-t border-slate-800 mx-3"></div>
                <a href="{{ route('staff.dashboard') }}" class="{{ $menuClass($isActive('staff.dashboard')) }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <span x-show="sidebarOpen">Dashboard</span>
                </a><br>

                <div class="px-3 mb-2 text-xs font-semibold uppercase tracking-wider text-slate-500">Menu Utama</div>
                <div class="my-4 border-t border-slate-800 mx-3"></div>
                <a href="{{ route('staff.letters.index') }}" class="{{ $menuClass($isActive('staff.letters.index') || $isActive('staff.letters.show')) }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <span x-show="sidebarOpen">Daftar Surat</span>
                </a>
                <a href="{{ route('staff.mail_registries.index') }}" class="{{ $menuClass($isActive('staff.mail_registries.*')) }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    <span x-show="sidebarOpen">Surat Masuk & Keluar</span>
                </a>
                <a href="{{ route('staff.archives.index') }}" class="{{ $menuClass($isActive('staff.archives.*')) }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                    <span x-show="sidebarOpen">Arsip Surat</span>
                </a><br>

                <div class="px-3 mb-2 text-xs font-semibold uppercase tracking-wider text-slate-500">Menu Lainnya</div>
                <div class="my-4 border-t border-slate-800 mx-3"></div>
                <a href="{{ route('staff.renbuts.index') }}" class="{{ $menuClass($isActive('staff.renbuts.*')) }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    <span x-show="sidebarOpen">Rencana Kebutuhan</span>
                </a>
                <a href="{{ route('staff.letter_templates.index') }}" class="{{ $menuClass($isActive('staff.letter_templates.*')) }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/></svg>
                    <span x-show="sidebarOpen">Template Surat</span>
                </a>
                <a href="{{ route('staff.trash.letters') }}" class="{{ $menuClass($isActive('staff.trash.*')) }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    <span x-show="sidebarOpen">Sampah Surat</span>
                </a>
            @endif

        </nav>

        <!-- Bottom Pinned Section -->
        <div class="p-4 border-t border-slate-800 flex flex-col gap-1">
            <!-- Logout Button -->
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" title="Keluar" class="w-full flex items-center gap-3 p-2 -mx-2 rounded-xl font-medium text-sm text-rose-400 hover:text-white hover:bg-rose-600 transition-colors group">
                    <div class="relative flex-shrink-0 flex items-center justify-center w-10">
                        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </div>
                    <span x-show="sidebarOpen">Keluar</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- ================= KANAN: HEADER & MAIN CONTENT ================= -->
    <div class="flex-1 flex flex-col min-w-0 bg-slate-50 relative">
        
        <!-- HEADER / NAVBAR -->
        <header class="h-16 bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between px-4 sm:px-8 z-10 sticky top-0">
            
            <div class="flex items-center gap-4">
                <!-- Hamburger Menu -->
                <button @click="sidebarOpen = !sidebarOpen" class="text-slate-400 hover:text-slate-600 focus:outline-none hidden sm:block">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
                </button>
                <button @click="sidebarOpen = !sidebarOpen" class="text-slate-400 hover:text-slate-600 focus:outline-none sm:hidden">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>

                <!-- Judul Halaman -->
                <h1 class="text-xl font-bold text-slate-900 dark:text-white hidden sm:block">@yield('title', 'Dashboard')</h1>
            </div>

            <div class="flex items-center gap-4 sm:gap-6">
                <!-- Notification Bell -->
                @php
                    $unreadCount = auth()->user()->unreadNotifications->count();
                    $initialNotifs = auth()->user()->unreadNotifications()->take(10)->get()->map(function($notif) {
                        return [
                            'id'      => $notif->id,
                            'title'   => $notif->data['title'] ?? 'Pemberitahuan',
                            'message' => $notif->data['message'] ?? '',
                            'url'     => $notif->data['url'] ?? '#',
                            'type'    => $notif->data['type'] ?? 'info',
                            'time'    => $notif->created_at->diffForHumans()
                        ];
                    });
                @endphp
                <div class="relative" x-data="{
                        unreadCount: {{ $unreadCount }},
                        notifications: {{ \Illuminate\Support\Js::from($initialNotifs) }},
                        init() {
                            // Cek notifikasi setiap 15 detik
                            setInterval(() => {
                                this.fetchNotifications();
                            }, 15000);
                        },
                        fetchNotifications() {
                            fetch('{{ route('notifications.unread') }}')
                                .then(response => response.json())
                                .then(data => {
                                    if (data.count > this.unreadCount) {
                                        this.$refs.bell.classList.add('animate-pulse', 'text-indigo-500');
                                        setTimeout(() => this.$refs.bell.classList.remove('animate-pulse', 'text-indigo-500'), 3000);
                                        
                                        const newNotifCount = data.count - this.unreadCount;
                                        if (newNotifCount > 0 && data.notifications.length > 0) {
                                            const latest = data.notifications[0];
                                            // Jangan gunakan Swal pop up besar agar tidak mengganggu (unintrusive)
                                            // Bisa gunakan Toast jika SweetAlert mendukung toast
                                        }
                                    }
                                    this.unreadCount = data.count;
                                    this.notifications = data.notifications;
                                })
                                .catch(error => console.error('Error fetching notifications:', error));
                        },
                        markAsRead(id, url) {
                            fetch(`/notifications/${id}/read`, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                    'Accept': 'application/json'
                                }
                            }).finally(() => {
                                window.location.href = url;
                            });
                        }
                    }">
                    <button x-ref="bell" @click="notifOpen = !notifOpen" @click.away="notifOpen = false" class="relative p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors rounded-full hover:bg-slate-100 dark:hover:bg-slate-700 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        
                        <span x-show="unreadCount > 0" x-cloak class="absolute top-1.5 right-1.5 w-2.5 h-2.5 bg-red-500 border-2 border-white dark:border-slate-800 rounded-full animate-pulse"></span>
                    </button>
                    @include('partials.notification-dropdown')
                </div>

                <!-- Dark Mode Toggle Button -->
                <button @click="toggleTheme()" class="relative p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors rounded-full hover:bg-slate-100 dark:hover:bg-slate-700 focus:outline-none" title="Toggle Dark/Light Mode">
                    <!-- Sun Icon -->
                    <svg x-show="isDark" x-cloak style="display: none;" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    <!-- Moon Icon -->
                    <svg x-show="!isDark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
                </button>

                <!-- Pemisah -->
                <div class="h-6 w-px bg-slate-200 dark:bg-slate-700 hidden sm:block"></div>

                <!-- Real-time Date and Time -->
                <div class="hidden sm:flex flex-col text-right" 
                     x-data="{ 
                        time: '', 
                        date: '', 
                        updateTime() {
                            const now = new Date();
                            this.time = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                            this.date = now.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
                        }
                     }" 
                     x-init="updateTime(); setInterval(() => updateTime(), 1000)">
                    <span class="text-sm font-bold text-slate-800 dark:text-slate-200" x-text="time"></span>
                    <span class="text-[11px] font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider" x-text="date"></span>
                </div>
            </div>
        </header>

        <!-- MAIN CONTENT -->
        <main class="flex-1 overflow-y-auto p-6 sm:p-8">
            
            {{-- Flash Messages --}}
            <script>
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                });
            </script>

            @if(session('success'))
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        Toast.fire({
                            icon: 'success',
                            title: @js(session('success'))
                        });
                    });
                </script>
            @endif

            @if(session('error'))
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        Toast.fire({
                            icon: 'error',
                            title: @js(session('error'))
                        });
                    });
                </script>
            @endif

            @if(session('warning'))
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        Toast.fire({
                            icon: 'warning',
                            title: @js(session('warning')),
                            timer: 6000,
                        });
                    });
                </script>
            @endif

            @if($errors->any())
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        Toast.fire({
                            icon: 'error',
                            title: 'Validasi Gagal!',
                            text: 'Terdapat kesalahan pada form. Mohon periksa kembali kolom input.'
                        });
                    });
                </script>
            @endif

            {{-- Global SweetAlert2 Confirm --}}
            <script>
                window.confirmAction = function(event, message, confirmText = 'Ya, Lanjutkan!', confirmColor = '#4f46e5', icon = 'warning') {
                    event.preventDefault();
                    const form = event.target;
                    Swal.fire({
                        title: 'Konfirmasi',
                        text: message,
                        icon: icon,
                        showCancelButton: true,
                        confirmButtonColor: confirmColor,
                        cancelButtonColor: '#94a3b8',
                        confirmButtonText: confirmText,
                        cancelButtonText: 'Batal',
                        reverseButtons: true,
                        heightAuto: false, 
                        scrollbarPadding: false,
                        customClass: {
                            confirmButton: 'font-bold rounded-xl px-5 py-2.5 shadow-sm',
                            cancelButton: 'font-bold rounded-xl px-5 py-2.5 shadow-sm',
                            popup: 'rounded-2xl shadow-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                };

                window.confirmDelete = function(event, message) {
                    window.confirmAction(event, message, 'Ya, Hapus!', '#ef4444', 'error');
                };
            </script>

            {{-- Dinamis Content --}}
            @yield('content')
            {{ $slot ?? '' }}

        </main>
    </div>

</body>
</html>
