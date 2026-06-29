@props(['status'])

@php
    // Definisikan config berdasarkan status
    if (in_array($status, ['pending', 'pending_admin_1', 'pending_admin_2', 'pending_admin_3', 'pending_kabagum', 'pending_kasek'])) {
        $config = [
            'bg'   => 'bg-amber-50',
            'text' => 'text-amber-700',
            'ring' => 'ring-amber-200',
            'dot'  => 'bg-amber-500',
            'label'=> match($status) {
                'pending_admin_1' => 'Menunggu PROGAR',
                'pending_admin_2' => 'Menunggu PEKAS',
                'pending_admin_3' => 'Menunggu SETUM',
                'pending_kabagum' => 'Menunggu KABAGUM',
                'pending_kasek'   => 'Menunggu KASEK',
                default => 'Menunggu'
            },
        ];
    } elseif ($status === 'revision') {
        $config = [
            'bg'   => 'bg-rose-50',
            'text' => 'text-rose-700',
            'ring' => 'ring-rose-200',
            'dot'  => 'bg-rose-500',
            'label'=> 'Butuh Revisi',
        ];
    } elseif ($status === 'rejected') {
        $config = [
            'bg'   => 'bg-red-50',
            'text' => 'text-red-700',
            'ring' => 'ring-red-200',
            'dot'  => 'bg-red-500',
            'label'=> 'Ditolak',
        ];
    } elseif ($status === 'approved') {
        $config = [
            'bg'   => 'bg-emerald-50',
            'text' => 'text-emerald-700',
            'ring' => 'ring-emerald-200',
            'dot'  => 'bg-emerald-500',
            'label'=> 'Disetujui',
        ];
    } else {
        $config = [
            'bg'   => 'bg-slate-50',
            'text' => 'text-slate-700',
            'ring' => 'ring-slate-200',
            'dot'  => 'bg-slate-400',
            'label'=> 'Tidak Diketahui',
        ];
    }
@endphp

<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] uppercase tracking-wider font-bold border
             {{ $config['bg'] }} {{ $config['text'] }} {{ str_replace('ring-', 'border-', $config['ring']) }}">
    <span class="w-1.5 h-1.5 rounded-full {{ $config['dot'] }}"></span>
    {{ $config['label'] }}
</span>
