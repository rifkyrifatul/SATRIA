@props(['letter'])

@php
    $isDirect = $letter->category && $letter->category->review_type === 'langsung_admin_3';
    $status = $letter->status;

    // Define the sequence of states
    if ($isDirect) {
        $sequence = [
            'pending_admin_3', // SETUM
            'pending_kabagum', // KABAGUM
            'pending_kasek',   // KASEK
            'approved'         // Selesai
        ];
        $labels = [
            'Pengajuan' => 'Dikirim oleh Staff',
            'SETUM' => 'Verifikasi SETUM',
            'KABAGUM' => 'Menunggu KABAGUM',
            'KASEK' => 'Ditandatangani KASEK',
            'Selesai' => 'Surat Disetujui'
        ];
        $steps = ['Pengajuan', 'SETUM', 'KABAGUM', 'KASEK', 'Selesai'];
    } else {
        $sequence = [
            'pending_admin_1', // PROGAR
            'pending_admin_2', // PEKAS
            'pending_admin_3', // SETUM
            'pending_kabagum', // KABAGUM
            'pending_kasek',   // KASEK
            'approved'         // Selesai
        ];
        $labels = [
            'Pengajuan' => 'Dikirim oleh Staff',
            'PROGAR' => 'Verifikasi PROGAR',
            'PEKAS' => 'Verifikasi PEKAS',
            'SETUM' => 'Persetujuan SETUM',
            'KABAGUM' => 'Menunggu KABAGUM',
            'KASEK' => 'Ditandatangani KASEK',
            'Selesai' => 'Surat Disetujui'
        ];
        $steps = ['Pengajuan', 'PROGAR', 'PEKAS', 'SETUM', 'KABAGUM', 'KASEK', 'Selesai'];
    }

    $activeIndex = 0;
    $isRevision = $status === 'revision';
    $isRejected = $status === 'rejected';

    if ($isRevision || $isRejected) {
        // If it's revision, it is back to Pengajuan, but we want to show warning
        $activeIndex = 0;
    } else {
        if ($status === 'approved') {
            $activeIndex = count($steps) - 1; // Selesai
        } else {
            // Find current status in sequence
            $seqIndex = array_search($status, $sequence);
            if ($seqIndex !== false) {
                $activeIndex = $seqIndex + 1; // +1 because index 0 is Pengajuan
            }
        }
    }
@endphp

<div class="bg-white dark:bg-slate-800 rounded-2xl border-2 border-slate-300 dark:border-slate-600 shadow-sm p-5 sm:p-6 mb-6" x-data="{ showDisposisiModal: false }">
    <h3 class="text-base font-extrabold text-slate-900 dark:text-white uppercase tracking-wider mb-6 flex items-center gap-2">
        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
        Tracking Posisi Surat
    </h3>
    
    <div class="relative">
        <!-- Base Background Line (Desktop) -->
        <div class="hidden sm:block absolute top-5 left-10 right-10 h-1 bg-slate-200 dark:bg-slate-700 rounded-full" aria-hidden="true"></div>

        <ul class="relative flex flex-col sm:flex-row justify-between gap-6 sm:gap-0">
            @foreach($steps as $index => $step)
                @php
                    $isCompleted = $index < $activeIndex;
                    $isActive = $index === $activeIndex;
                    $isFuture = $index > $activeIndex;
                    $hasError = $isActive && ($isRevision || $isRejected);

                    // Colors based on state
                    if ($hasError) {
                        $circleBg = 'bg-rose-500 border-4 border-rose-100 dark:border-rose-900/50 text-white ring-4 ring-rose-500/20';
                        $textColor = 'text-rose-600 dark:text-rose-400 font-bold';
                        $icon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />';
                    } elseif ($isCompleted) {
                        $circleBg = 'bg-emerald-500 border-4 border-emerald-100 dark:border-emerald-900/50 text-white';
                        $textColor = 'text-emerald-600 dark:text-emerald-400 font-bold';
                        $icon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />';
                    } elseif ($isActive) {
                        $circleBg = 'bg-indigo-600 border-4 border-indigo-100 dark:border-indigo-900/50 text-white ring-4 ring-indigo-500/20';
                        $textColor = 'text-indigo-700 dark:text-indigo-400 font-bold';
                        $icon = '<circle cx="12" cy="12" r="4" fill="currentColor"/>'; // Dot
                    } else {
                        $circleBg = 'bg-slate-100 dark:bg-slate-700 border-4 border-white dark:border-slate-800 text-slate-400 dark:text-slate-500';
                        $textColor = 'text-slate-400 dark:text-slate-500 font-medium';
                        $icon = ''; // Show number
                    }
                @endphp
                
                <li class="relative flex-1 group">
                    <!-- Progress Line (Mobile) -->
                    @if(!$loop->last)
                        <div class="sm:hidden absolute left-5 top-10 bottom-[-24px] w-1 {{ $isCompleted ? 'bg-emerald-500' : 'bg-slate-200 dark:bg-slate-700' }}"></div>
                    @endif
                    
                    <!-- Progress Line overlay (Desktop) -->
                    @if(!$loop->last)
                        <div class="hidden sm:block absolute top-5 left-[50%] w-full h-1 {{ $isCompleted ? 'bg-emerald-500' : 'bg-transparent' }} transition-all duration-500 z-0"></div>
                    @endif

                    <div class="flex sm:flex-col items-center sm:justify-center gap-4 sm:gap-3 relative z-10">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 transition-all duration-300 {{ $circleBg }}">
                            @if($icon)
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    {!! $icon !!}
                                </svg>
                            @else
                                <span class="text-sm font-bold">{{ $index + 1 }}</span>
                            @endif
                        </div>
                        
                        <div class="sm:text-center mt-0 sm:mt-1 flex-1 sm:px-2">
                            <h4 class="text-xs sm:text-sm uppercase tracking-wider {{ $textColor }}">
                                {{ $step }}
                            </h4>
                            <p class="text-[10px] sm:text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                {{ $labels[$step] }}
                            </p>
                            
                            @if($hasError && $isRevision)
                                <p class="text-[10px] font-bold text-rose-500 mt-1.5 px-2 py-0.5 bg-rose-50 dark:bg-rose-900/20 rounded-md inline-block">
                                    Butuh Revisi
                                </p>
                            @elseif($hasError && $isRejected)
                                <p class="text-[10px] font-bold text-rose-500 mt-1.5 px-2 py-0.5 bg-rose-50 dark:bg-rose-900/20 rounded-md inline-block">
                                    Ditolak
                                </p>
                            @elseif($isActive && !$isRevision && !$isRejected && $status !== 'approved')
                                <p class="text-[10px] font-bold text-indigo-500 mt-1.5 px-2 py-0.5 bg-indigo-50 dark:bg-indigo-900/20 rounded-md inline-block animate-pulse">
                                    Sedang Diproses
                                </p>
                            @endif

                            @if(Auth::check() && Auth::user()->isAdmin() && Auth::user()->admin_level === 'admin_3' && $isActive && in_array($step, ['KABAGUM', 'KASEK']) && $letter->isManualPending())
                                @if($step === 'KABAGUM')
                                    <form action="{{ route('admin.letters.manual_progress', $letter) }}" method="POST" class="mt-3">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 text-[10px] sm:text-[11px] font-bold rounded-lg text-white shadow-sm transition-colors whitespace-nowrap bg-indigo-600 hover:bg-indigo-700">
                                            Lanjut ke KASEK
                                        </button>
                                    </form>
                                @else
                                    <div class="mt-3">
                                        <button type="button" @click="showDisposisiModal = true" class="px-3 py-1.5 text-[10px] sm:text-[11px] font-bold rounded-lg text-white shadow-sm transition-colors whitespace-nowrap bg-emerald-600 hover:bg-emerald-700 flex items-center gap-1.5 mx-auto">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                            Tandatangan Kasek
                                        </button>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>

    {{-- TAMPILAN HASIL DISPOSISI KASEK (Jika sudah selesai & ada disposisi) --}}
    @if(!empty($letter->disposisi_kasek) || !empty($letter->catatan_disposisi))
        <div class="mt-6 pt-5 border-t border-slate-200 dark:border-slate-700">
            <div class="rounded-xl border-2 border-emerald-200 dark:border-emerald-800/80 bg-gradient-to-r from-emerald-50/70 to-teal-50/70 dark:from-emerald-950/30 dark:to-teal-950/30 p-4 sm:p-5">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-3 pb-3 border-b border-emerald-200/70 dark:border-emerald-800/60">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-emerald-600 text-white flex items-center justify-center shadow-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-black text-emerald-900 dark:text-emerald-300 uppercase tracking-wide">Lembar Disposisi Kepala Sekolah</h4>
                            <p class="text-[11px] text-emerald-700/80 dark:text-emerald-400">Instruksi resmi saat penandatanganan surat</p>
                        </div>
                    </div>
                    @if(!empty($letter->disposisi_kasek))
                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold bg-emerald-200/80 dark:bg-emerald-900/60 text-emerald-800 dark:text-emerald-300">
                            {{ count((array) $letter->disposisi_kasek) }} Pilihan Disposisi
                        </span>
                    @endif
                </div>

                @if(!empty($letter->disposisi_kasek))
                    <div class="mb-3">
                        <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500 block mb-2">Instruksi Disposisi:</span>
                        <div class="flex flex-wrap gap-2">
                            @foreach((array) $letter->disposisi_kasek as $dispItem)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-white dark:bg-slate-800 text-emerald-800 dark:text-emerald-300 rounded-lg text-xs font-bold border border-emerald-200 dark:border-emerald-800 shadow-2xs">
                                    <svg class="w-3.5 h-3.5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $dispItem }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if(!empty($letter->catatan_disposisi))
                    <div class="pt-2">
                        <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">Catatan Kasek:</span>
                        <div class="p-3 bg-white/80 dark:bg-slate-800/80 rounded-xl text-xs sm:text-sm text-slate-800 dark:text-slate-200 border border-emerald-100 dark:border-emerald-900/50 whitespace-pre-wrap font-medium">
                            {{ $letter->catatan_disposisi }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- MODAL DISPOSISI KASEK (Saat Admin SETUM mengklik Tandatangan Kasek) --}}
    @if(Auth::check() && Auth::user()->isAdmin() && Auth::user()->admin_level === 'admin_3' && $letter->isManualPending() && $letter->status === \App\Models\Letter::STATUS_PENDING_KASEK)
        <div 
            x-show="showDisposisiModal" 
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto"
            style="display: none;"
            @keydown.escape.window="showDisposisiModal = false"
        >
            <div class="fixed inset-0" @click="showDisposisiModal = false"></div>

            <div 
                x-show="showDisposisiModal"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                class="relative w-full max-w-2xl bg-white dark:bg-slate-800 rounded-2xl shadow-2xl border-2 border-slate-300 dark:border-slate-700 overflow-hidden my-8 z-10"
                @click.stop
            >
                {{-- Header --}}
                <div class="px-6 py-4 bg-gradient-to-r from-emerald-600 to-teal-700 text-white flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-white/20 backdrop-blur-xs flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold tracking-wide uppercase">Disposisi Kepala Sekolah</h3>
                            <p class="text-xs text-emerald-100 mt-0.5">Tandatangan & Selesaikan Surat: {{ Str::limit($letter->title, 45) }}</p>
                        </div>
                    </div>
                    <button type="button" @click="showDisposisiModal = false" class="text-white/80 hover:text-white p-1 rounded-lg hover:bg-white/10 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Form --}}
                <form action="{{ route('admin.letters.manual_progress', $letter) }}" method="POST" class="p-6 space-y-5">
                    @csrf

                    {{-- Kotak Disposisi Kasek (18 Checkboxes) --}}
                    <div class="border-2 border-emerald-200 dark:border-emerald-800/80 rounded-xl bg-slate-50/70 dark:bg-slate-900/40 p-4">
                        <div class="flex items-center justify-between mb-3 pb-2 border-b border-emerald-100 dark:border-slate-700">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                </svg>
                                <span class="text-xs font-black uppercase tracking-wider text-emerald-900 dark:text-emerald-300">Pilihan Disposisi</span>
                            </div>
                            <span class="text-[11px] text-slate-500 font-medium">Pilih checkbox yang sesuai</span>
                        </div>

                        @php
                            $disposisiGrid = [
                                ['no' => 1, 'label' => 'UDK'],
                                ['no' => 10, 'label' => 'MENGHADAP SAYA'],
                                ['no' => 2, 'label' => 'ACC'],
                                ['no' => 11, 'label' => 'COPY'],
                                ['no' => 3, 'label' => 'TINDAK LANJUTI'],
                                ['no' => 12, 'label' => 'EDARKAN'],
                                ['no' => 4, 'label' => 'AJUKAN SARAN'],
                                ['no' => 13, 'label' => 'ARSIPKAN'],
                                ['no' => 5, 'label' => 'PELAJARI'],
                                ['no' => 14, 'label' => 'JAWAB'],
                                ['no' => 6, 'label' => 'PEDOMANI'],
                                ['no' => 15, 'label' => 'WAKILI SAYA'],
                                ['no' => 7, 'label' => 'LAPORKAN'],
                                ['no' => 16, 'label' => 'DAMPINGI SAYA'],
                                ['no' => 8, 'label' => 'IKUTI PERKEMBANGAN'],
                                ['no' => 17, 'label' => 'SIAPKAN BAHAN'],
                                ['no' => 9, 'label' => 'KOORDINASIKAN'],
                                ['no' => 18, 'label' => 'BUAT NOTA DINAS'],
                            ];
                        @endphp

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-80 overflow-y-auto pr-1">
                            @foreach($disposisiGrid as $item)
                                <label class="flex items-center gap-2.5 p-2 bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700 hover:border-emerald-500 dark:hover:border-emerald-500 cursor-pointer transition-colors shadow-xs group select-none">
                                    <input type="checkbox" 
                                           name="disposisi_kasek[]" 
                                           value="{{ $item['label'] }}" 
                                           class="w-4 h-4 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500">
                                    <span class="text-xs font-semibold text-slate-700 dark:text-slate-200 group-hover:text-emerald-700 dark:group-hover:text-emerald-300 transition-colors">
                                        <span class="font-mono text-[11px] text-slate-400 group-hover:text-emerald-500">{{ $item['no'] }}.</span> {{ $item['label'] }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Catatan --}}
                    <div>
                        <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                            Catatan:
                        </label>
                        <textarea 
                            name="catatan_disposisi" 
                            rows="3" 
                            placeholder="Tuliskan catatan, arahan, atau instruksi khusus dari Kasek..." 
                            class="w-full px-4 py-2.5 text-sm border border-slate-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-emerald-500 bg-white dark:bg-slate-900/50"
                        ></textarea>
                    </div>

                    {{-- Action buttons --}}
                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-200 dark:border-slate-700">
                        <button type="button" @click="showDisposisiModal = false" class="px-4 py-2.5 text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-xl transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 text-xs font-extrabold uppercase tracking-wider text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-md hover:shadow-lg transition-all flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Simpan & Selesaikan TTD Kasek
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
