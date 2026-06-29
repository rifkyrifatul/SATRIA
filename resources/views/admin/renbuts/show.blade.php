@extends('layouts.app')

@section('title', 'Tinjau Pengajuan Renbut')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <a href="{{ route('admin.renbuts.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-slate-500 hover:text-indigo-600 transition-colors mb-4">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali ke Daftar
            </a>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">{{ $renbut->title }}</h1>
            <div class="flex flex-wrap items-center gap-2 mt-2">
                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-md text-[11px] font-medium border border-slate-200 dark:border-slate-700">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Diajukan oleh <span class="font-bold text-slate-800 dark:text-white uppercase">{{ $renbut->user->name }}</span>
                </div>
                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-md text-[11px] font-medium border border-slate-200 dark:border-slate-700">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>{{ $renbut->created_at->format('d M Y, H:i') }}</span>
                </div>
            </div>
        </div>
        <div>
            @if($renbut->status === 'pending')
                <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold bg-amber-100 text-amber-700 uppercase tracking-wider border border-amber-200">Menunggu Persetujuan</span>
            @elseif($renbut->status === 'approved')
                <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold bg-emerald-100 text-emerald-700 uppercase tracking-wider border border-emerald-200">Disetujui</span>
            @else
                <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold bg-rose-100 text-rose-700 uppercase tracking-wider border border-rose-200">Ditolak</span>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Left Column: Details & Items --}}
        <div class="lg:col-span-2 space-y-6">
            @if($renbut->description)
            <div class="bg-white dark:bg-slate-800 rounded-xl border-2 border-slate-300 dark:border-slate-600 shadow-sm p-6">
                <h3 class="text-xs font-extrabold text-slate-500 uppercase tracking-wider mb-2">Deskripsi / Keterangan Pemohon</h3>
                <p class="text-sm text-slate-700 dark:text-slate-300 whitespace-pre-wrap">{{ $renbut->description }}</p>
            </div>
            @endif

            {{-- Attachment & Budget --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white dark:bg-slate-800 rounded-xl border-2 border-slate-300 dark:border-slate-600 shadow-sm p-6 flex flex-col justify-center">
                    <h3 class="text-xs font-extrabold text-slate-500 uppercase tracking-wider mb-2">Estimasi Total Anggaran</h3>
                    <p class="text-2xl font-extrabold font-mono text-indigo-600 dark:text-indigo-400">Rp {{ number_format($renbut->total_estimated_budget, 0, ',', '.') }}</p>
                </div>

                <div class="bg-white dark:bg-slate-800 rounded-xl border-2 border-slate-300 dark:border-slate-600 shadow-sm p-6">
                    <h3 class="text-xs font-extrabold text-slate-500 uppercase tracking-wider mb-2">Lampiran Rincian</h3>
                    @if($renbut->file_path)
                        <a href="{{ route('admin.renbuts.download_attachment', $renbut) }}" class="inline-flex items-center gap-3 p-3 bg-slate-50 hover:bg-slate-100 dark:bg-slate-900/50 dark:hover:bg-slate-900/80 border border-slate-200 dark:border-slate-700 rounded-xl transition-colors group w-full">
                            <div class="w-10 h-10 bg-white dark:bg-slate-800 text-slate-500 rounded-lg flex items-center justify-center shadow-sm border border-slate-200 dark:border-slate-700 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            </div>
                            <div>
                                <p class="font-bold text-slate-800 dark:text-slate-200 text-sm group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">Unduh File Lampiran</p>
                                <p class="text-[11px] text-slate-500 mt-0.5">Berisi daftar rinci pengajuan</p>
                            </div>
                        </a>
                    @else
                        <p class="text-sm text-slate-500 italic">Tidak ada file lampiran.</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Right Column: Action / Response Form --}}
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-slate-800 rounded-xl border-2 border-indigo-200 dark:border-indigo-800 shadow-sm overflow-hidden sticky top-6">
                <div class="p-4 bg-indigo-50 dark:bg-indigo-900/30 border-b border-indigo-100 dark:border-indigo-800">
                    <h3 class="font-bold text-indigo-900 dark:text-indigo-300">Tindakan PROGAR</h3>
                </div>
                
                <div class="p-5">
                    <form action="{{ route('admin.renbuts.update', $renbut) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                        @csrf
                        @method('PUT')

                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Keputusan <span class="text-rose-500">*</span></label>
                            <select name="status" required class="w-full px-4 py-2.5 text-sm border border-slate-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-indigo-500 bg-slate-50 dark:bg-slate-900/50">
                                <option value="approved" {{ $renbut->status === 'approved' ? 'selected' : '' }}>Disetujui (ACC)</option>
                                <option value="rejected" {{ $renbut->status === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                                @if($renbut->status === 'pending')
                                    <option value="pending" selected disabled>Pilih Keputusan...</option>
                                @endif
                            </select>
                            @error('status') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Catatan / Arahan (Opsional)</label>
                            <textarea name="response_note" rows="3" placeholder="Berikan alasan penolakan atau instruksi selanjutnya..." class="w-full px-4 py-2.5 text-sm border border-slate-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-indigo-500 bg-slate-50 dark:bg-slate-900/50">{{ old('response_note', $renbut->response_note) }}</textarea>
                            @error('response_note') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Unggah File Balasan (Opsional)</label>
                            <p class="text-[11px] text-slate-500 mb-2">Jika ada SPK, Nota, atau file Excel realisasi. Format: .pdf, .docx, .xlsx</p>
                            
                            @if($renbut->response_file_path)
                                <div class="mb-3 p-3 bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-100 dark:border-indigo-800 rounded-lg flex items-center justify-between">
                                    <span class="text-xs font-semibold text-indigo-700 dark:text-indigo-300 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                        File terlampir
                                    </span>
                                </div>
                            @endif

                            <input type="file" name="response_file" accept=".pdf,.doc,.docx,.xls,.xlsx" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 border border-slate-300 dark:border-slate-600 rounded-xl bg-slate-50 dark:bg-slate-900/50 transition-colors">
                            @error('response_file') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="pt-4 border-t border-slate-200 dark:border-slate-700">
                            <button type="submit" class="w-full py-3 text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-sm hover:shadow-md transition-all flex justify-center items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Simpan Keputusan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
    </div>
</div>
@endsection
