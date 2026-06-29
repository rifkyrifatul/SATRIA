<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Statistik Surat</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #111;
        }
        .header-info {
            margin-bottom: 15px;
            font-size: 11px;
            color: #555;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th {
            background-color: #f4f4f4;
            color: #333;
            text-align: left;
            padding: 8px;
            font-weight: bold;
        }
        td {
            padding: 8px;
            vertical-align: top;
        }
        .text-center { text-align: center; }
        .badge {
            padding: 3px 6px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
        }
        .badge-pending { background: #fff3cd; color: #856404; }
        .badge-approved { background: #d4edda; color: #155724; }
        .badge-revision { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>

    <h2>Laporan Daftar Surat</h2>

    <div class="header-info">
        <strong>Tanggal Cetak:</strong> {{ \Carbon\Carbon::now()->format('d M Y, H:i') }}<br>
        <strong>Total Data:</strong> {{ count($letters) }} Surat
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">No. Surat</th>
                <th width="25%">Judul Surat</th>
                <th width="15%">Kategori</th>
                <th width="15%">Pembuat / Divisi</th>
                <th width="10%">Tanggal</th>
                <th width="15%">Status</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $mapStatus = [
                    'pending_admin_1' => ['label' => 'Menunggu PROGAR', 'class' => 'badge-pending'],
                    'pending_admin_2' => ['label' => 'Menunggu PEKAS', 'class' => 'badge-pending'],
                    'pending_admin_3' => ['label' => 'Menunggu Super Admin', 'class' => 'badge-pending'],
                    'approved'        => ['label' => 'Disetujui', 'class' => 'badge-approved'],
                    'revision'        => ['label' => 'Revisi', 'class' => 'badge-revision'],
                ];
            @endphp
            
            @forelse ($letters as $index => $letter)
                @php
                    $status = $mapStatus[$letter->status] ?? ['label' => $letter->status, 'class' => 'badge-pending'];
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $letter->letter_number ?? '-' }}</td>
                    <td>{{ $letter->title }}</td>
                    <td>{{ $letter->category->name ?? '-' }}</td>
                    <td>
                        {{ $letter->creator->name ?? '-' }}<br>
                        <small style="color: #666;">{{ $letter->creator->division->name ?? '-' }}</small>
                    </td>
                    <td>{{ $letter->created_at->format('d/m/Y') }}</td>
                    <td>
                        <span class="badge {{ $status['class'] }}">
                            {{ $status['label'] }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">Tidak ada data yang ditemukan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
