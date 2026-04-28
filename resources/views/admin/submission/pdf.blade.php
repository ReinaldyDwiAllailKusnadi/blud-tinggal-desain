<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pengajuan Sewa</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            text-transform: uppercase;
        }
        .header p {
            margin: 5px 0 0;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
            font-weight: bold;
        }
        .status-pending { color: #d97706; font-weight: bold; }
        .status-approved { color: #16a34a; font-weight: bold; }
        .status-rejected { color: #dc2626; font-weight: bold; }
        .footer {
            margin-top: 30px;
            text-align: right;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Laporan Penyewaan Lokasi Wisata</h1>
        <p>BLUD Pariwisata Banyumas</p>
        <p>Tanggal Cetak: {{ \Carbon\Carbon::now()->format('d F Y') }}</p>
    </div>

    <p><strong>Filter Status:</strong> {{ ucfirst($status) }}</p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal Pengajuan</th>
                <th>Nama Instansi / Vendor</th>
                <th>Lokasi</th>
                <th>Tgl Pelaksanaan</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($submissions as $index => $sub)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($sub->created_at)->format('d/m/Y') }}</td>
                <td>{{ $sub->vendor }}<br><small>PIC: {{ $sub->namePIC }}</small></td>
                <td>{{ $sub->location }}</td>
                <td>{{ \Carbon\Carbon::parse($sub->start_date)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($sub->end_date)->format('d/m/Y') }}</td>
                <td class="status-{{ $sub->status }}">
                    @if($sub->status == 'pending')
                        Menunggu
                    @elseif($sub->status == 'approved')
                        Disetujui
                    @else
                        Ditolak
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center;">Tidak ada data pengajuan.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Mengetahui,</p>
        <br><br><br>
        <p><strong>Admin BLUD Pariwisata</strong></p>
    </div>

</body>
</html>
