<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Resi Registrasi - {{ $letter->letter_number ?? $letter->id }}</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Vite CSS -->
    @vite(['resources/css/app.css'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #fff;
            color: #000;
        }
        
        /* Print Styles */
        @page {
            size: A4 portrait;
            margin: 20mm;
        }
        
        @media print {
            body {
                background-color: #ffffff;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .no-print {
                display: none !important;
            }
            .print-border {
                border: 1px solid #000 !important;
            }
        }
    </style>
</head>
<body class="bg-slate-100 dark:bg-slate-700/50 min-h-screen py-10 print:py-0 print:bg-white flex justify-center">

    <div class="w-full max-w-4xl bg-white dark:bg-slate-800 p-8 md:p-12 shadow-2xl print:shadow-none print:p-0 mx-auto print:max-w-none print:w-full print:m-0 print:border-none print:bg-white print:text-black">
        
        {{-- TOMBOL PRINT (Sembunyi saat dicetak) --}}
        <div class="flex justify-end mb-8 no-print border-b border-slate-200 dark:border-slate-700 pb-4">
            <button onclick="window.print()" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg shadow-sm transition-colors flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Cetak Lembar Registrasi
            </button>
        </div>

        {{-- HEADER RESI --}}
        <div class="flex items-center justify-between border-b-2 border-black pb-6 mb-8 print-border" style="border-bottom-width: 3px;">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-black text-white flex items-center justify-center font-extrabold text-3xl">
                    S
                </div>
                <div>
                    <h1 class="text-2xl font-extrabold uppercase tracking-tight text-black">Sistem Informasi Administrasi Persuratan</h1>
                    <p class="text-sm font-semibold text-gray-700 mt-1 uppercase tracking-widest">LEMBAR TANDA BUKTI REGISTRASI SURAT - SISTEM SIMSURAT</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-xs text-gray-500 uppercase tracking-widest">Dicetak Pada</p>
                <p class="text-sm font-bold text-black">{{ now()->format('d M Y - H:i') }}</p>
            </div>
        </div>

        {{-- ISI DATA --}}
        <div class="mb-10">
            <h2 class="text-sm font-bold uppercase tracking-wider text-gray-500 mb-4">Metadata Dokumen Resmi</h2>
            
            <table class="w-full text-left text-sm border-collapse">
                <tbody>
                    <tr class="border-b border-gray-200 print-border">
                        <td class="py-3 font-semibold text-gray-600 w-1/3">ID Registrasi Sistem</td>
                        <td class="py-3 font-bold text-black text-base">{{ $letter->letter_number ?? 'REG-'.str_pad($letter->id, 5, '0', STR_PAD_LEFT) }}</td>
                    </tr>
                    <tr class="border-b border-gray-200 print-border">
                        <td class="py-3 font-semibold text-gray-600">Perihal / Judul Surat</td>
                        <td class="py-3 font-bold text-black text-base uppercase">{{ $letter->title }}</td>
                    </tr>
                    <tr class="border-b border-gray-200 print-border">
                        <td class="py-3 font-semibold text-gray-600">Alur Pengajuan</td>
                        <td class="py-3 font-bold text-black">{{ $letter->category->name ?? 'Surat Umum' }}</td>
                    </tr>
                    <tr class="border-b border-gray-200 print-border">
                        <td class="py-3 font-semibold text-gray-600">Diajukan Oleh</td>
                        <td class="py-3 font-bold text-black">{{ $letter->creator->name ?? 'Unknown' }}</td>
                    </tr>
                    <tr class="border-b border-gray-200 print-border">
                        <td class="py-3 font-semibold text-gray-600">Divisi Pengaju</td>
                        <td class="py-3 font-bold text-black">{{ $letter->creator->division->name ?? 'Staf Umum' }}</td>
                    </tr>
                    <tr class="border-b border-gray-200 print-border">
                        <td class="py-3 font-semibold text-gray-600">Tanggal Pengajuan</td>
                        <td class="py-3 font-bold text-black">{{ $letter->created_at->format('l, d F Y') }}</td>
                    </tr>
                    <tr class="border-b border-gray-200 print-border">
                        <td class="py-3 font-semibold text-gray-600">Status Akhir Dokumen</td>
                        <td class="py-3 font-extrabold uppercase {{ $letter->is_force_approved ? 'text-red-600' : ($letter->status === \App\Models\Letter::STATUS_APPROVED ? 'text-black' : 'text-gray-500') }}">
                            @if($letter->is_force_approved)
                                DISETUJUI VIA BYPASS DARURAT (SUPER ADMIN)
                            @else
                                {{ str_replace('_', ' ', $letter->status) }}
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- AREA TANDA TANGAN & QR CODE --}}
        <div class="flex justify-between items-end mt-16 pt-8 border-t border-dashed border-gray-400 print:break-inside-avoid">
            
            <div class="w-1/2">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-widest mb-12">Diserahkan Oleh,</p>
                <div class="w-48 border-b border-black"></div>
                <p class="text-sm font-bold text-black mt-2">{{ $letter->creator->name ?? 'Staf Terkait' }}</p>
                <p class="text-xs text-gray-600">NIP/NRP: .......................................</p>
            </div>

            <div class="w-1/2 flex flex-col items-end text-center">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-widest mb-2">Validasi Sistem Digital</p>
                
                {{-- KOTAK QR CODE --}}
                <div class="w-32 h-32 border-4 border-black p-1 flex items-center justify-center bg-white dark:bg-slate-800 mb-2 print:border-black print:contrast-125 print:grayscale">
                    @if(isset($qrCodeUrl))
                        <img src="{{ $qrCodeUrl }}" alt="QR Code Validasi" class="w-full h-full object-contain print:brightness-0 print:contrast-200">
                    @else
                        <!-- Fallback jika library QR belum diinstall, menggunakan SVG statis dummy -->
                        <svg class="w-full h-full text-black" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M3 3h8v8H3V3zm2 2v4h4V5H5zm8-2h8v8h-8V3zm2 2v4h4V5h-4zM3 13h8v8H3v-8zm2 2v4h4v-4H5zm13-2h3v2h-3v-2zm-3 0h2v2h-2v-2zm3 3h3v2h-3v-2zm-3 0h2v2h-2v-2zm3 3h3v2h-3v-2zm-3 0h2v2h-2v-2zm0-3h-3v-2h3v2z"/>
                        </svg>
                    @endif
                </div>
                <p class="text-[10px] font-semibold text-gray-500 uppercase">Scan untuk verifikasi</p>
            </div>
            
        </div>
        
        <div class="mt-16 text-center">
            <p class="text-[10px] text-gray-400 font-medium">Dokumen ini dicetak otomatis oleh sistem SIMSURAT. Dokumen ini sah dan tidak memerlukan cap basah tambahan selama tervalidasi oleh QR Code.</p>
        </div>

    </div>

</body>
</html>
