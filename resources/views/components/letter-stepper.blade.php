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

<div class="bg-white dark:bg-slate-800 rounded-2xl border-2 border-slate-300 dark:border-slate-600 shadow-sm p-5 sm:p-6 mb-6">
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
                                <form action="{{ route('admin.letters.manual_progress', $letter) }}" method="POST" class="mt-3">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 text-[10px] sm:text-[11px] font-bold rounded-lg text-white shadow-sm transition-colors whitespace-nowrap {{ $step === 'KABAGUM' ? 'bg-indigo-600 hover:bg-indigo-700' : 'bg-emerald-600 hover:bg-emerald-700' }}">
                                        {{ $step === 'KABAGUM' ? 'Lanjut ke KASEK' : 'Tandatangan Selesai' }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
</div>
