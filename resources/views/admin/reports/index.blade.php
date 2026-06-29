@extends('layouts.app')

@section('title', 'LAPORAN & STATISTIK')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <div class="mb-6">
        <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">Pusat Laporan SIMSURAT</h1>
        <p class="text-sm text-slate-500 mt-1">Saring data persuratan dan ekspor laporan ke dalam format yang Anda butuhkan.</p>
    </div>

    {{-- ========================================================= --}}
    {{-- FORM FILTER & EKSPOR --}}
    {{-- ========================================================= --}}
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border-2 border-slate-300 dark:border-slate-600 overflow-hidden">
        
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200 flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                Filter Parameter Laporan
            </h3>
        </div>

        <form action="{{ route('super_admin.reports.index') }}" method="GET" class="p-6">
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                
                {{-- Tanggal Mulai --}}
                <div>
                    <label for="start_date" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">Tanggal Mulai</label>
                    <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}"
                           class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors">
                </div>

                {{-- Tanggal Selesai --}}
                <div>
                    <label for="end_date" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">Tanggal Selesai</label>
                    <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}"
                           class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors">
                </div>

                {{-- Filter Divisi --}}
                <div>
                    <label for="division_id" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">Filter Divisi</label>
                    <select id="division_id" name="division_id"
                            class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors">
                        <option value="">Semua Divisi</option>
                        @foreach($divisions as $division)
                            <option value="{{ $division->id }}" {{ request('division_id') == $division->id ? 'selected' : '' }}>
                                {{ $division->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Status Surat --}}
                <div>
                    <label for="status" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">Status Akhir Surat</label>
                    <select id="status" name="status"
                            class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors">
                        <option value="">Semua Status</option>
                        <option value="{{ \App\Models\Letter::STATUS_APPROVED }}" {{ request('status') == \App\Models\Letter::STATUS_APPROVED ? 'selected' : '' }}>Disetujui (Approved)</option>
                        <option value="{{ \App\Models\Letter::STATUS_REVISION }}" {{ request('status') == \App\Models\Letter::STATUS_REVISION ? 'selected' : '' }}>Ditolak (Revisi)</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Masih Dalam Proses</option>
                    </select>
                </div>

            </div>

            <hr class="border-slate-100 mb-6">

            <div class="flex flex-col sm:flex-row items-center gap-4 justify-end">
                <p class="text-xs text-slate-400 mr-auto hidden sm:block">Pilih format laporan yang ingin Anda unduh.</p>
                
                {{-- Tombol 0: Terapkan Filter --}}
                <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-lg shadow-sm transition-colors flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    Terapkan Filter
                </button>

                {{-- Tombol 1: Ekspor Excel / CSV --}}
                <button type="submit" formaction="{{ route('super_admin.reports.export.excel') }}" class="w-full sm:w-auto px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-lg shadow-sm transition-colors flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Ekspor ke Excel (CSV)
                </button>
                
                {{-- Tombol 2: Cetak Laporan PDF --}}
                <button type="submit" formaction="{{ route('super_admin.reports.export.pdf') }}" class="w-full sm:w-auto px-6 py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-sm font-bold rounded-lg shadow-sm transition-colors flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Cetak Laporan PDF
                </button>
            </div>
            </div>
            
        </form>
    </div>

    {{-- Info Card --}}
    <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-5 flex gap-4">
        <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center flex-shrink-0 text-indigo-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <h4 class="text-sm font-bold text-indigo-900">Informasi Ekspor</h4>
            <p class="text-xs text-indigo-700 mt-1 leading-relaxed">Format Excel (CSV) sangat cocok jika Anda ingin mengolah data angka atau melakukan pivot table. Gunakan format PDF untuk kebutuhan pelaporan cetak resmi ke manajemen.</p>
        </div>
    </div>

</div>
@endsection
