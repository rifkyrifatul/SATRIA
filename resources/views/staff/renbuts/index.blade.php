@extends('layouts.app')

@section('title', 'RENCANA KEBUTUHAN')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">Daftar Rencana Kebutuhan</h1>
            <p class="text-sm text-slate-500 mt-1">Kelola dan pantau status pengajuan kebutuhan barang / anggaran Anda.</p>
        </div>
        <div>
            <a href="{{ route('staff.renbuts.create') }}" class="px-5 py-2.5 text-sm font-bold bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-colors shadow-sm inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Buat Pengajuan Baru
            </a>
        </div>
    </div>



    <div class="bg-white dark:bg-slate-800 rounded-xl border-2 border-slate-300 dark:border-slate-600 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-indigo-600 dark:bg-indigo-800">
                        <th class="px-6 py-4 text-left font-extrabold text-white uppercase tracking-wider text-[11px] w-16">No</th>
                        <th class="px-6 py-4 text-left font-extrabold text-white uppercase tracking-wider text-[11px]">Judul Pengajuan</th>
                        <th class="px-6 py-4 text-left font-extrabold text-white uppercase tracking-wider text-[11px]">Estimasi Total</th>
                        <th class="px-6 py-4 text-center font-extrabold text-white uppercase tracking-wider text-[11px]">Status</th>
                        <th class="px-6 py-4 text-right font-extrabold text-white uppercase tracking-wider text-[11px]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                    @forelse($renbuts as $renbut)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group">
                            <td class="px-6 py-4 text-slate-400 font-mono text-xs font-semibold">
                                {{ $renbuts->firstItem() + $loop->index }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900 dark:text-white">{{ $renbut->title }}</div>
                                <div class="text-[11px] text-slate-500 mt-1">{{ $renbut->created_at->format('d M Y, H:i') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-mono text-xs font-bold text-slate-700 dark:text-slate-300">
                                    Rp {{ number_format($renbut->total_estimated_budget, 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($renbut->status === 'pending')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-bold bg-amber-100 text-amber-700 uppercase tracking-wider">Menunggu</span>
                                @elseif($renbut->status === 'approved')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-bold bg-emerald-100 text-emerald-700 uppercase tracking-wider">Disetujui</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-bold bg-rose-100 text-rose-700 uppercase tracking-wider">Ditolak</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('staff.renbuts.show', $renbut) }}"
                                       class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-slate-100 text-slate-600 hover:text-white hover:bg-slate-600 transition-all shadow-sm"
                                       title="Lihat Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-16 px-6 text-center">
                                <div class="w-16 h-16 bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700 rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm">
                                    <svg class="w-8 h-8 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <p class="text-slate-700 dark:text-slate-300 font-bold">Belum ada pengajuan RENBUT</p>
                                <p class="text-slate-500 font-medium text-sm mt-1 mb-6">Anda belum pernah membuat pengajuan Rencana Kebutuhan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($renbuts->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50">
                {{ $renbuts->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
