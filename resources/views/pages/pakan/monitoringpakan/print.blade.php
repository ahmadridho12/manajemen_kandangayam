<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pakan</title>
    <script>
        window.onload = function() {
            window.print();
        };
    </script>
    <style>
        @media print {
            @page {
                size: A4; /* Ukuran kertas A4 */
                margin: 0; /* Menghapus margin default */
            }
            body {
                font-size: 12px; /* Ukuran font lebih kecil */
                margin: 10mm; /* Memberikan margin pada body */
                font-family: 'Calibri', sans-serif; /* Menerapkan font Calibri */
            }
        }

        body {
            font-family: 'Calibri', sans-serif;
            margin: 10mm;
            font-size: 12px;
        }
        .header {
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            padding: 0px;
        }
        .logo {
            width: 48px;
            height: auto;
            margin-right: 0px;
        }
        .company-info {
            flex-grow: 1;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            margin: 0;
            text-align: left;
        }
        .report-info {
            margin: 20px 0;
        }
        .report {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ asset('ayam.png') }}" alt="" class="logo">
        <div class="company-info">
            <div class="company-name">NC FARM</div>
        </div>
    </div>
    <div class="report">
        <h2>Laporan Pakan  Ayam</h2>
    </div>
    <div class="report-info">
        <p>Periode: {{ $periode }}</p>
        <p>Kandang: {{ $kandang }}</p>
        <p>Tanggal Cetak: {{ date('d/m/Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Periode</th>
                <th>Tanggal</th>
                <th>Hari</th>
                <th>Total Masuk</th>
                <th>Total Berat</th>
                <th>keluar</th>
                <th>Sisa</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $pa)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $pa->ayam->periode ?? 'Tidak Ada' }}</td>
                            <td>{{ $pa->tanggal }}</td>
                            <td>{{ $pa->day }}</td>
                            <td>{{ $pa->total_masuk }} </td>
                            <td>{{ $pa->total_berat }} Kg</td>
                            <td>{{ $pa->keluar }} </td>
                            <td>{{ $pa->sisa }} </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak oleh: {{ auth()->user()->name ?? 'Admin' }}</p>
        <p>Tanggal: {{ date('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>