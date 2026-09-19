<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Rekap Arsip Surat - {{ $monthName }} {{ $yearNum }}</title>
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
        <h2>REKAPITULASI ARSIP SURAT INTERNAL — PERIODE {{ strtoupper($monthName) }} {{ $yearNum }}</h2>
        <p>Laporan Resmi Rekapitulasi Dokumen Surat yang telah Disetujui (Approved)</p>
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
                <td>{{ count($letters) }} Surat Disetujui</td>
                <td><strong>Dicetak Oleh:</strong></td>
                <td>{{ auth()->user()->name ?? 'Administrator' }}</td>
            </tr>
        </table>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th width="4%" class="text-center">No</th>
                <th width="16%">Nomor Surat</th>
                <th width="28%">Judul Surat</th>
                <th width="16%">Alur Pengajuan</th>
                <th width="16%">Pengaju / Divisi</th>
                <th width="10%" class="text-center">Tgl. Pengajuan</th>
                <th width="10%" class="text-center">Tgl. Selesai</th>
            </tr>
        </thead>
        <tbody>
            @forelse($letters as $index => $letter)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $letter->letter_number ?? 'Belum bernomor' }}</strong>
                    </td>
                    <td>{{ $letter->title }}</td>
                    <td>{{ $letter->category->name ?? '-' }}</td>
                    <td>
                        <strong>{{ $letter->creator->name ?? '-' }}</strong><br>
                        <span style="color: #64748b; font-size: 9px;">{{ $letter->creator->division->name ?? 'Staff' }}</span>
                    </td>
                    <td class="text-center">{{ $letter->created_at->format('d/m/Y') }}</td>
                    <td class="text-center">{{ $letter->updated_at->format('d/m/Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 20px; color: #64748b;">
                        Tidak ada dokumen arsip surat pada periode {{ $monthName }} {{ $yearNum }}.
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
