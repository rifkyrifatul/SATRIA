@extends('layouts.app')

@section('title', 'TONG SAMPAH MASTER')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">Data Terhapus</h1>
            <p class="text-sm text-slate-500 mt-1">Kelola data yang dihapus sementara. Anda dapat memulihkan atau menghapus data secara permanen.</p>
        </div>
    </div>

    <!-- Tabs -->
    <div class="bg-white dark:bg-slate-800 p-2 rounded-xl border-2 border-slate-300 dark:border-slate-600 shadow-sm mb-6 flex flex-wrap gap-2">
        <a href="{{ route('super_admin.trash.index', ['tab' => 'letters']) }}" class="px-5 py-2.5 text-sm font-bold rounded-xl transition-colors {{ $tab == 'letters' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700' }}">Surat</a>
        <a href="{{ route('super_admin.trash.index', ['tab' => 'templates']) }}" class="px-5 py-2.5 text-sm font-bold rounded-xl transition-colors {{ $tab == 'templates' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700' }}">Template Surat</a>
        <a href="{{ route('super_admin.trash.index', ['tab' => 'users']) }}" class="px-5 py-2.5 text-sm font-bold rounded-xl transition-colors {{ $tab == 'users' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700' }}">Pengguna</a>
        <a href="{{ route('super_admin.trash.index', ['tab' => 'categories']) }}" class="px-5 py-2.5 text-sm font-bold rounded-xl transition-colors {{ $tab == 'categories' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700' }}">Kategori</a>
    </div>

    <!-- Content -->
    <div class="bg-white dark:bg-slate-800 rounded-xl border-2 border-slate-300 dark:border-slate-600 shadow-sm overflow-hidden">
        @if($tab == 'letters')
            @include('admin.trash.partials.letters')
        @elseif($tab == 'templates')
            @include('admin.trash.partials.templates')
        @elseif($tab == 'users')
            @include('admin.trash.partials.users')
        @elseif($tab == 'categories')
            @include('admin.trash.partials.categories')
        @endif
    </div>
</div>
@endsection
