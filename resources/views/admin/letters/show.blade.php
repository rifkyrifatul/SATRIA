@extends('layouts.app')

@section('title', 'DETAIL REVIEW SURAT')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pb-12" x-data="{ showPdfModal: false, pdfUrl: '', pdfTitle: '', action: null }">
<div class="max-w-[90rem] mx-auto space-y-6 pb-12" x-data="{ showPdfModal: false, pdfUrl: '', pdfTitle: '', action: null }">

    {{-- 1. STEPPER PROGRESS TRACKER --}}
    <div class="mb-4">
        <x-letter-stepper :letter="$letter" />
    </div>

    {{-- 2. RINCIAN DOKUMEN & TINDAKAN (HORIZONTAL TABLE LAYOUT) --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-300 dark:border-slate-700 shadow-sm overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-slate-700 uppercase bg-slate-50 border-b border-slate-300 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700">
                    <tr>
                        <th class="px-6 py-4 border-r border-slate-300 dark:border-slate-700">Informasi Surat</th>
                        <th class="px-6 py-4 border-r border-slate-300 dark:border-slate-700">Nomor & Tanggal</th>
                        <th class="px-6 py-4 border-r border-slate-300 dark:border-slate-700">Pengaju</th>
                        <th class="px-6 py-4 border-r border-slate-300 dark:border-slate-700">Dokumen</th>
                        @php
                            $isAuthorizedToReview = false;
                            if (Auth::user()->isAdmin()) {
                                $adminLevel = str_replace('admin_', '', Auth::user()->admin_level);
                                if ($letter->isPending()) {
                                    $isAuthorizedToReview = (
                                        ($adminLevel == '1' && $letter->status === \App\Models\Letter::STATUS_PENDING_ADMIN_1) ||
                                        ($adminLevel == '2' && $letter->status === \App\Models\Letter::STATUS_PENDING_ADMIN_2) ||
                                        ($adminLevel == '3' && $letter->status === \App\Models\Letter::STATUS_PENDING_ADMIN_3)
                                    );
                                }
                            }
                        @endphp
                        @if($isAuthorizedToReview)
                            <th class="px-6 py-4 bg-slate-100/50 dark:bg-slate-800/80">Tindakan Keputusan</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    <tr class="align-top hover:bg-slate-50/50 dark:hover:bg-slate-800/30">
                        {{-- 1. Informasi Surat --}}
                        <td class="px-6 py-5 whitespace-normal min-w-[250px] border-r border-slate-300 dark:border-slate-700">
                            <p class="font-bold text-slate-900 dark:text-white text-base mb-1.5">{{ $letter->title }}</p>
                            <span class="px-2 py-1 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 rounded text-xs font-medium border border-slate-200 dark:border-slate-600">{{ $letter->category->name ?? 'Surat Umum' }}</span>
                        </td>
                        
                        {{-- 2. Nomor & Tanggal --}}
                        <td class="px-6 py-5 whitespace-nowrap border-r border-slate-300 dark:border-slate-700">
                            <p class="font-mono font-bold text-slate-800 dark:text-slate-200 mb-1">{{ $letter->letter_number ?? '-' }}</p>
                            <p class="text-xs text-slate-500">{{ $letter->created_at->format('d M Y, H:i') }} WIB</p>
                        </td>
                        
                        {{-- 3. Pengaju --}}
                        <td class="px-6 py-5 whitespace-nowrap border-r border-slate-300 dark:border-slate-700">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-slate-200 dark:bg-slate-600 flex items-center justify-center text-sm font-bold text-slate-600 dark:text-slate-300">
                                    {{ substr($letter->creator->name ?? 'S', 0, 1) }}
                                </div>
                                <span class="font-medium text-slate-700 dark:text-slate-300">{{ $letter->creator->name ?? 'Staff' }}</span>
                            </div>
                        </td>
                        
                        {{-- 4. Dokumen --}}
                        <td class="px-6 py-5 min-w-[150px] border-r border-slate-300 dark:border-slate-700">
                            <div class="flex flex-col gap-2">
                                <div class="flex flex-wrap gap-2">
                                    @if(strtolower($letter->file_extension) === 'pdf')
                                        <button @click.prevent="pdfUrl = '{{ Auth::user()->isSuperAdmin() ? route('super_admin.letters.preview', $letter) : route('admin.letters.preview', $letter) }}'; pdfTitle = 'Draf Surat Aktif'; showPdfModal = true"
                                                class="px-3 py-1.5 text-xs font-bold bg-indigo-50 text-indigo-700 hover:bg-indigo-100 border border-indigo-200 rounded-lg transition-colors flex items-center gap-1.5 whitespace-nowrap shadow-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            Lihat
                                        </button>
                                    @endif
                                    @if($letter->final_file_path)
                                    <a href="{{ Auth::user()->isSuperAdmin() ? route('super_admin.letters.download_final', $letter) : route('admin.letters.download_final', $letter) }}" 
                                       class="px-3 py-1.5 text-xs font-bold bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 rounded-lg transition-colors flex items-center gap-1.5 whitespace-nowrap shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                        Unduh
                                    </a>
                                    @else
                                    <a href="{{ Auth::user()->isSuperAdmin() ? route('super_admin.letters.download', $letter) : route('admin.letters.download', $letter) }}" 
                                       class="px-3 py-1.5 text-xs font-bold bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 rounded-lg transition-colors flex items-center gap-1.5 whitespace-nowrap shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                        Unduh
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </td>

                        {{-- 5. Tindakan (Hanya muncul jika berhak) --}}
                        @if($isAuthorizedToReview)
                            <td class="px-6 py-5 whitespace-normal min-w-[280px] bg-slate-50/50 dark:bg-slate-800/40">
                                    <div class="flex flex-wrap gap-2 mb-2">
                                        <button @click="action = (action === 'acc' ? null : 'acc')" 
                                                :class="action === 'acc' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white border border-emerald-500 text-emerald-600 hover:bg-emerald-50'"
                                                class="px-3 py-1.5 text-xs font-bold rounded-lg transition-all flex items-center gap-1.5 flex-1 justify-center whitespace-nowrap">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            Setujui
                                        </button>

                                        <button @click="action = (action === 'upload' ? null : 'upload')" 
                                                :class="action === 'upload' ? 'bg-blue-600 text-white shadow-sm' : 'bg-white border border-blue-500 text-blue-600 hover:bg-blue-50'"
                                                class="px-3 py-1.5 text-xs font-bold rounded-lg transition-all flex items-center gap-1.5 flex-1 justify-center whitespace-nowrap">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            Koreksi
                                        </button>

                                        <button @click="action = (action === 'reject' ? null : 'reject')" 
                                                :class="action === 'reject' ? 'bg-rose-600 text-white shadow-sm' : 'bg-white border border-rose-500 text-rose-600 hover:bg-rose-50'"
                                                class="px-3 py-1.5 text-xs font-bold rounded-lg transition-all flex items-center gap-1.5 flex-1 justify-center whitespace-nowrap">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            Tolak
                                        </button>
                                    </div>

                                    {{-- Formulir yang muncul di bawah tombol tindakan di dalam sel tabel --}}
                                    <div x-show="action === 'acc'" x-collapse class="mt-3">
                                        <form action="{{ route('admin.letters.approve', $letter) }}" method="POST" class="bg-white border border-emerald-200 rounded-lg p-3 shadow-sm">
                                            @csrf
                                            <p class="text-[11px] font-medium text-slate-600 mb-3">Teruskan surat ini ke tahap selanjutnya?</p>
                                            @if($letter->status === \App\Models\Letter::STATUS_PENDING_ADMIN_3)
                                                <div class="mb-3">
                                                    <input type="text" name="letter_number" required class="w-full rounded border-slate-300 focus:ring-emerald-500 focus:border-emerald-500 text-xs" placeholder="No Surat (Wajib)">
                                                </div>
                                            @endif
                                            <button type="submit" class="w-full py-1.5 bg-emerald-600 text-white text-xs font-bold rounded hover:bg-emerald-700 shadow-sm">Ya, ACC Sekarang</button>
                                        </form>
                                    </div>

                                    <div x-show="action === 'upload'" x-collapse class="mt-3">
                                        <form action="{{ route('admin.letters.approve', $letter) }}" method="POST" enctype="multipart/form-data" class="bg-white border border-blue-200 rounded-lg p-3 shadow-sm">
                                            @csrf
                                            <div class="mb-3">
                                                <label class="block text-[11px] font-medium text-slate-600 mb-1">Upload File PDF/Word</label>
                                                <input type="file" name="final_file" accept=".pdf,.doc,.docx" required class="block w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 border border-slate-200 rounded bg-slate-50 cursor-pointer">
                                            </div>
                                            @if($letter->status === \App\Models\Letter::STATUS_PENDING_ADMIN_3)
                                                <div class="mb-3">
                                                    <input type="text" name="letter_number" required class="w-full rounded border-slate-300 focus:ring-blue-500 focus:border-blue-500 text-xs" placeholder="No Surat (Wajib)">
                                                </div>
                                            @endif
                                            <button type="submit" class="w-full py-1.5 bg-blue-600 text-white text-xs font-bold rounded hover:bg-blue-700 shadow-sm">Upload & ACC</button>
                                        </form>
                                    </div>

                                    <div x-show="action === 'reject' || {{ $errors->has('notes') ? 'true' : 'false' }}" x-init="if({{ $errors->has('notes') ? 'true' : 'false' }}) action = 'reject'" x-collapse class="mt-3">
                                        <form action="{{ route('admin.letters.revision', $letter) }}" method="POST" class="bg-white border border-rose-200 rounded-lg p-3 shadow-sm">
                                            @csrf
                                            <div class="mb-3">
                                                <textarea name="notes" rows="2" required minlength="10" 
                                                          class="w-full rounded border-slate-300 focus:ring-rose-500 focus:border-rose-500 text-xs" 
                                                          placeholder="Catatan revisi (Min. 10 karakter)...">{{ old('notes') }}</textarea>
                                                @error('notes')
                                                    <p class="mt-1 text-[10px] font-bold text-rose-600">{{ $message }}</p>
                                                @enderror
                                            </div>
                                            <button type="submit" class="w-full py-1.5 bg-rose-600 text-white text-xs font-bold rounded hover:bg-rose-700 shadow-sm">Kembalikan Dokumen</button>
                                        </form>
                                    </div>
                            </td>
                        @endif
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- 3. VERSI DOKUMEN --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-300 dark:border-slate-700 shadow-sm overflow-hidden mb-6">
        <div class="px-6 py-5 border-b border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50 flex items-center gap-2">
            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <h3 class="text-base font-bold text-slate-900 dark:text-white">Versi Dokumen</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-800/80">
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider border-r border-slate-300 dark:border-slate-600 w-32">Versi</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider border-r border-slate-300 dark:border-slate-600 w-48">Tanggal & Waktu</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider border-r border-slate-300 dark:border-slate-600">Keterangan Dokumen</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-32 text-center">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700 bg-white dark:bg-slate-800">
                    @forelse($letter->attachments as $index => $attachment)
                        @php
                            $typeLabel = match($attachment->file_type) {
                                'original' => 'Draf Awal',
                                'staff_revision' => 'Revisi Staff',
                                'progar_correction' => 'Koreksi PROGAR',
                                'pekas_correction' => 'Koreksi PEKAS',
                                'setum_final' => 'Final SETUM',
                                default => 'Pembaruan'
                            };
                            $isLatest = $index === $letter->attachments->count() - 1;
                            $downloadRoute = Auth::user()->isSuperAdmin() ? route('super_admin.letters.download', ['letter' => $letter->id, 'attachment_id' => $attachment->id]) : route('admin.letters.download', ['letter' => $letter->id, 'attachment_id' => $attachment->id]);
                        @endphp
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors {{ $isLatest ? 'bg-indigo-50/30' : '' }}">
                            <td class="px-6 py-4 border-r border-slate-200 dark:border-slate-700 align-middle">
                                <div class="flex flex-col items-start gap-1">
                                    <span class="font-bold text-sm text-slate-800 dark:text-slate-200">V{{ $index + 1 }}</span>
                                    @if($isLatest) 
                                        <span class="px-2 py-0.5 bg-indigo-500 text-white text-[9px] rounded-full uppercase font-bold tracking-wider shadow-sm">Terbaru</span> 
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 border-r border-slate-200 dark:border-slate-700 align-middle whitespace-nowrap">
                                <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">{{ $attachment->created_at->format('d M Y') }}</p>
                                <p class="text-xs text-slate-500">{{ $attachment->created_at->format('H:i') }} WIB</p>
                            </td>
                            <td class="px-6 py-4 border-r border-slate-200 dark:border-slate-700 align-middle">
                                <p class="text-sm font-bold text-slate-800 dark:text-slate-200 mb-0.5">{{ $typeLabel }}</p>
                                <p class="text-[11px] text-slate-500 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                    Dokumen PDF/Word
                                </p>
                            </td>
                            <td class="px-6 py-4 align-middle text-center">
                                <a href="{{ $downloadRoute }}" class="inline-flex items-center justify-center gap-2 px-3 py-1.5 rounded-lg bg-white border border-slate-300 text-slate-600 hover:text-indigo-600 hover:border-indigo-400 hover:bg-indigo-50 transition-colors shadow-sm text-xs font-bold" title="Unduh">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    Unduh
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-slate-500 italic">Riwayat versi tidak tersedia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- 4. JEJAK AUDIT --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-300 dark:border-slate-700 shadow-sm overflow-hidden mb-6">
        <div class="px-6 py-5 border-b border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50 flex items-center gap-2">
            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <h3 class="text-base font-bold text-slate-900 dark:text-white">Jejak Audit</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-800/80">
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider border-r border-slate-300 dark:border-slate-600 w-48">Tanggal & Waktu</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider border-r border-slate-300 dark:border-slate-600 w-48">Aktivitas</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider border-r border-slate-300 dark:border-slate-600 w-48">Pengguna</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Keterangan / Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700 bg-white dark:bg-slate-800">
                    @forelse($letter->logs->sortByDesc('created_at') as $index => $log)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="px-6 py-4 border-r border-slate-200 dark:border-slate-700 align-top whitespace-nowrap">
                                <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">{{ $log->created_at->format('d M Y') }}</p>
                                <p class="text-xs text-slate-500">{{ $log->created_at->format('H:i') }} WIB</p>
                            </td>
                            <td class="px-6 py-4 border-r border-slate-200 dark:border-slate-700 align-top">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold {{ $index === 0 ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'bg-slate-100 text-slate-700 border border-slate-200' }}">
                                    {{ $log->action_label ?? ucwords(str_replace('_', ' ', $log->action)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 border-r border-slate-200 dark:border-slate-700 align-top whitespace-nowrap">
                                <p class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ $log->user->name ?? 'Sistem' }}</p>
                            </td>
                            <td class="px-6 py-4 align-top">
                                @if($log->notes)
                                    <p class="text-sm text-slate-600 dark:text-slate-400 italic">"{{ $log->notes }}"</p>
                                @else
                                    <p class="text-sm text-slate-400 italic">-</p>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-slate-500 italic">Belum ada riwayat aktivitas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="pt-2">
        <a href="{{ Auth::user()->isSuperAdmin() ? route('super_admin.letters.index') : route('admin.reviews.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-slate-800 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Daftar Surat
        </a>
    </div>

    {{-- 5. PDF VIEWER MODAL --}}
    <div x-show="showPdfModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/80 backdrop-blur-sm" x-transition.opacity style="display: none;">
        <div class="bg-white dark:bg-slate-800 rounded-2xl w-full max-w-5xl h-[90vh] flex flex-col shadow-2xl overflow-hidden mx-4" @click.away="showPdfModal = false">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50">
                <div>
                    <h3 class="text-base font-bold text-slate-800 dark:text-white" x-text="pdfTitle">Preview Dokumen</h3>
                    <p class="text-xs text-slate-500">{{ $letter->title }}</p>
                </div>
                <button @click="showPdfModal = false" class="text-slate-400 hover:text-rose-500 p-2 rounded-xl transition-colors focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="flex-1 bg-slate-100 dark:bg-slate-900 relative">
                <iframe :src="pdfUrl" class="w-full h-full border-none relative z-10" title="PDF Viewer" x-show="pdfUrl"></iframe>
            </div>
        </div>
    </div>

</div>
</div>
@endsection
