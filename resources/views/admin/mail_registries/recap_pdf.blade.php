<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Rekap Arsip Surat Eksternal - {{ $monthName }} {{ $yearNum }}</title>
    <style>
        @page {
            margin: 1.5cm 1.5cm 1.5cm 1.5cm;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #1e293b;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #334155;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 16px;
            font-weight: bold;
            margin: 0 0 4px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #0f172a;
        }
        .header h2 {
            font-size: 13px;
            font-weight: bold;
            margin: 0 0 6px 0;
            color: #475569;
        }
        .header p {
            font-size: 10px;
            margin: 0;
            color: #64748b;
        }
        .meta-bar {
            margin-bottom: 16px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 10px 14px;
            border-radius: 6px;
        }
        .meta-table {
            width: 100%;
            border-collapse: collapse;
        }
        .meta-table td {
            font-size: 10px;
            padding: 2px 0;
            border: none;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table.data-table th {
            background-color: #4f46e5;
            color: #ffffff;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 8px 6px;
            border: 1px solid #4338ca;
            text-align: left;
        }
        table.data-table td {
            padding: 8px 6px;
            border: 1px solid #cbd5e1;
            font-size: 10px;
            vertical-align: top;
        }
        table.data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .badge-masuk {
            color: #047857;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-keluar {
            color: #b45309;
            font-weight: bold;
            text-transform: uppercase;
        }
        .footer {
            margin-top: 25px;
            font-size: 9px;
            color: #94a3b8;
            text-align: right;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>SISTEM INFORMASI PERSURATAN (SIMSURAT)</h1>
        <h2>REKAPITULASI BUKU AGENDA SURAT EKSTERNAL — PERIODE {{ strtoupper($monthName) }} {{ $yearNum }}</h2>
        <p>Laporan Rekapitulasi Data Surat Masuk dan Surat Keluar Terdaftar</p>
    </div>

    <div class="meta-bar">
        <table class="meta-table">
            <tr>
                <td width="15%"><strong>Periode Rekap:</strong></td>
                <td width="35%">{{ $monthName }} {{ $yearNum }}</td>
                <td width="15%"><strong>Tanggal Cetak:</strong></td>
                <td width="35%">{{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM YYYY, HH:mm') }} WIB</td>
            </tr>
            <tr>
                <td><strong>Total Dokumen:</strong></td>
                <td>{{ count($mailRegistries) }} Surat Terdaftar</td>
                <td><strong>Dicetak Oleh:</strong></td>
                <td>{{ auth()->user()->name ?? 'Administrator' }}</td>
            </tr>
        </table>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th width="4%" class="text-center">No</th>
                <th width="12%" class="text-center">Jenis Surat</th>
                <th width="20%">Nomor Surat</th>
                <th width="30%">Perihal</th>
                <th width="22%">Asal / Tujuan</th>
                <th width="12%" class="text-center">Tanggal Surat</th>
            </tr>
        </thead>
        <tbody>
            @forelse($mailRegistries as $index => $mail)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">
                        @if($mail->type === 'masuk')
                            <span class="badge-masuk">Surat Masuk</span>
                        @else
                            <span class="badge-keluar">Surat Keluar</span>
                        @endif
                    </td>
                    <td><strong>{{ $mail->reference_number }}</strong></td>
                    <td>{{ $mail->subject }}</td>
                    <td>{{ $mail->origin_destination }}</td>
                    <td class="text-center">{{ $mail->date ? $mail->date->format('d/m/Y') : '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 20px; color: #64748b;">
                        Tidak ada agenda surat eksternal pada periode {{ $monthName }} {{ $yearNum }}.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dokumen ini di-generate secara otomatis oleh SIMSURAT pada {{ date('d/m/Y H:i:s') }}
    </div>

</body>
</html>
