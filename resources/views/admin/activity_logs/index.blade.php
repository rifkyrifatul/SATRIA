@extends('layouts.app')

@section('title', 'LOG AKTIVITAS SISTEM')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white">Log Aktivitas Sistem</h2>
            <p class="text-sm text-slate-500 mt-1">Merekam semua aktivitas penting (Audit Trail) yang terjadi di dalam sistem.</p>
        </div>
    </div>

    {{-- ── Tabel ── --}}
    <div class="bg-white dark:bg-slate-800 rounded-xl border-2 border-slate-300 dark:border-slate-600 shadow-sm overflow-hidden">
        @if($logs->isEmpty())
            <div class="flex flex-col items-center justify-center py-20">
                <div class="w-16 h-16 bg-slate-50 dark:bg-slate-900/50 border border-slate-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <p class="text-sm font-bold text-slate-700 dark:text-slate-300">Tidak ada log aktivitas ditemukan</p>
                <p class="text-xs text-slate-500 mt-1">Belum ada aktivitas yang direkam oleh sistem.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-900/50 border-b border-slate-100 text-slate-500 font-semibold text-xs uppercase tracking-wider">
                            <th class="text-left px-6 py-4">Waktu</th>
                            <th class="text-left px-6 py-4">Pelaku (User)</th>
                            <th class="text-left px-6 py-4">Deskripsi Aktivitas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                        @foreach($logs as $log)
                            <tr class="border-b border-slate-200 dark:border-slate-700 hover:bg-slate-50/50 transition-colors last:border-0">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <p class="text-xs font-bold text-slate-800 dark:text-slate-200">{{ $log->created_at->format('d M Y') }}</p>
                                    <p class="text-[10px] text-slate-500">{{ $log->created_at->format('H:i:s') }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        @if($log->causer)
                                            <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center flex-shrink-0 text-indigo-700 font-bold uppercase text-[10px] border border-indigo-200">
                                                {{ substr($log->causer->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="font-bold text-slate-900 dark:text-white text-xs">{{ $log->causer->name }}</p>
                                                <p class="text-[10px] text-slate-500">{{ $log->causer->role }}</p>
                                            </div>
                                        @else
                                            <span class="text-slate-400 italic text-xs font-semibold">Sistem / Guest</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $subject = $log->subject_type ? class_basename($log->subject_type) : '';
                                        $subjectName = match($subject) {
                                            'User'           => 'Pengguna',
                                            'Category'       => 'Kategori Surat',
                                            'Letter'         => 'Surat',
                                            'LetterTemplate' => 'Template Surat',
                                            'StaffDivision'  => 'Divisi Staff',
                                            'LetterLog'      => 'Log Surat',
                                            default          => $subject
                                        };

                                        $action = '';
                                        if ($log->event === 'created') {
                                            $action = "Menambahkan $subjectName baru";
                                        } elseif ($log->event === 'updated') {
                                            if (isset($log->properties['attributes']['status'])) {
                                                $action = "Mengupdate status $subjectName";
                                            } elseif (isset($log->properties['attributes']['password'])) {
                                                $action = "Mengupdate kata sandi (password) $subjectName";
                                            } else {
                                                $action = "Memperbarui data $subjectName";
                                            }
                                        } elseif ($log->event === 'deleted') {
                                            $action = "Menghapus $subjectName";
                                        } elseif ($log->event === 'login') {
                                            $action = "Melakukan login ke sistem";
                                        } elseif ($log->event === 'logout') {
                                            $action = "Melakukan logout dari sistem";
                                        } else {
                                            $action = "Melakukan aksi " . $log->event . ($subjectName ? " pada $subjectName" : "");
                                        }
                                        
                                        // Highlight the action text color based on event type
                                        $colorClass = 'text-slate-600 dark:text-slate-400';
                                        if ($log->event === 'created' || $log->event === 'login') $colorClass = 'text-emerald-600 font-medium';
                                        elseif ($log->event === 'updated') $colorClass = 'text-blue-600 font-medium';
                                        elseif ($log->event === 'deleted' || $log->event === 'logout') $colorClass = 'text-rose-600 font-medium';
                                    @endphp
                                    <div class="flex items-center gap-2">
                                        <span class="{{ $colorClass }} text-sm">{{ $action }}</span>
                                        @if($log->subject_id && !in_array($log->event, ['login', 'logout']))
                                            <span class="bg-slate-100 dark:bg-slate-700/50 text-slate-500 text-[10px] px-2 py-0.5 rounded-full font-mono border border-slate-200 dark:border-slate-700">#{{ $log->subject_id }}</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($logs->hasPages())
                <div class="px-6 py-4 border-t border-slate-100">
                    {{ $logs->links() }}
                </div>
            @endif
        @endif
    </div>

</div>
@endsection
