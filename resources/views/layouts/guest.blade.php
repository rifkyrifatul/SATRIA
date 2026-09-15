<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SIMSURAT') }} - Autentikasi</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
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
    </style>
</head>
<body class="antialiased text-slate-600 bg-slate-50 dark:bg-slate-900 dark:text-slate-300 overflow-x-hidden">
    <div class="min-h-screen flex flex-col justify-center items-center p-6 relative bg-gradient-to-br from-white via-indigo-50/50 to-slate-100 dark:from-slate-950 dark:via-slate-900 dark:to-indigo-950/30">
        
        <!-- Soft Abstract Glowing Orbs -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none z-0">
            <div class="absolute -top-[20%] -left-[10%] w-[50vw] h-[50vw] rounded-full bg-indigo-500/10 dark:bg-indigo-500/5 blur-[120px]"></div>
            <div class="absolute top-[60%] -right-[10%] w-[50vw] h-[50vw] rounded-full bg-emerald-500/10 dark:bg-emerald-500/5 blur-[120px]"></div>
        </div>

        <!-- Subtle Background Pattern -->
        <div class="absolute inset-0 z-0 opacity-50 bg-[linear-gradient(to_right,#80808012_1px,transparent_1px),linear-gradient(to_bottom,#80808012_1px,transparent_1px)] bg-[size:32px_32px] dark:opacity-20"></div>

        <div class="w-full max-w-md relative z-10">
            <!-- Logo Section -->
            <div class="text-center mb-8">
                <a href="/" class="inline-flex flex-col items-center gap-3 group">
                    <div class="w-14 h-14 bg-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-indigo-600/30 group-hover:scale-105 transition-transform duration-300">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 19v-8.93a2 2 0 01.89-1.664l7-4.666a2 2 0 012.22 0l7 4.666A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-1.14.76a2 2 0 01-2.22 0l-1.14-.76"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">SIMSURAT</h1>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-500 mt-1">Sistem Persuratan</p>
                    </div>
                </a>
            </div>

            <!-- Card -->
            <div class="bg-white dark:bg-slate-800 shadow-xl shadow-slate-200/40 dark:shadow-none rounded-2xl border-4 border-slate-300 dark:border-slate-600 overflow-hidden">
                <div class="p-8 sm:p-10">
                    {{ $slot }}
                </div>
            </div>
            
            <p class="mt-8 text-center text-[11px] font-bold uppercase tracking-widest text-slate-400">
                &copy; {{ date('Y') }} SIMSURAT. All rights reserved.
            </p>
        </div>
        
    </div>
</body>
</html>
