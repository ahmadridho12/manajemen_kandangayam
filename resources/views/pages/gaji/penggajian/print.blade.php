<!DOCTYPE html>
<html>
<head>
    <style>
        @media print {
            @page {
                size: A4;
                margin: 0;
            }
        }
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px double #000;
            padding-bottom: 10px;
        }
        .logo {
            max-width: 100px;
            margin-bottom: 10px;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 5px;
        }
        .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .detail-table th, .detail-table td {
            border: 1px solid #000;
            padding: 8px;
        }
        .detail-table th {
            background-color: #f0f0f0;
        }
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .summary-table td {
            padding: 5px;
        }
        .total-row {
            font-weight: bold;
            background-color: #f0f0f0;
        }
        .signature {
            margin-top: 50px;
            text-align: right;
        }
        @media print {
            .no-print {
                display: none;
            }
            body {
                margin: 0;
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ asset('ayam.png') }}" alt="" class="logo" style="width: 200px; height: auto;">
        <h2 style="margin: 5px 0">NC FARM</h2>

    </div>

    <table class="info-table">
        <tr>
            <td width="200">Nama Kandang</td>
            <td>: {{ $perhitunganGaji->kandang->nama_kandang }}</td>
        </tr>
        <tr>
            <td>Periode</td>
            <td>: {{ $perhitunganGaji->ayam->periode }}</td>
        </tr>
        <tr>
            <td>Hasil Pemeliharaan</td>
            <td>: Rp {{ number_format($perhitunganGaji->hasil_pemeliharaan, 0, ',', '.') }}</td>
        </tr>
    </table>

    <h4>Rincian Potongan Operasional</h4>
    <table class="detail-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Potongan</th>
                <th>Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @foreach($operasional as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->nama_potongan }}</td>
                <td>Rp {{ number_format($item->jumlah, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tbody>
            @foreach($perhitunganGaji->operasional as $index => $operasional)
            <tr>
                <td style="text-align: center">{{ $index + 1 }}</td>
                <td>{{ $operasional->nama_potongan }}</td>
                <td style="text-align: right">Rp {{ number_format($operasional->jumlah, 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="2" style="text-align: right">Total Potongan:</td>
                <td style="text-align: right">Rp {{ number_format($perhitunganGaji->total_potongan, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <h4>Perhitungan Gaji</h4>
    <table class="summary-table">
        <tr>
            <td width="300">Hasil Setelah Potongan</td>
            <td>: Rp {{ number_format($perhitunganGaji->hasil_setelah_potongan, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Total Gaji Pokok (Sum)</td>
            <td>: Rp {{ number_format($perhitunganGaji->rincianGaji->sum('gaji_pokok'), 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Total Bonus (Sum)</td>
            <td>: Rp {{ number_format($perhitunganGaji->rincianGaji->sum('bonus'), 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Total Gaji Pokok + Bonus</td>
            <td>: Rp {{ number_format($perhitunganGaji->rincianGaji->sum('gaji_pokok') + $perhitunganGaji->rincianGaji->sum('bonus'), 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Total Pinjaman</td>
            <td>: Rp {{ number_format($perhitunganGaji->rincianGaji->sum('jumlah_pinjaman'), 0, ',', '.') }}</Rp></td>

        </tr>
        <tr >
            <td>Total Gaji Bersih</td>
            <td>: Rp {{ number_format(
                $perhitunganGaji->rincianGaji->sum('gaji_pokok') + 
                $perhitunganGaji->rincianGaji->sum('bonus') - 
                $perhitunganGaji->rincianGaji->sum(function($rincian) {
                    return $rincian->pinjaman ? $rincian->pinjaman->jumlah_pinjaman : 0;
                }), 0, ',', '.') }}</td>
        </tr>
        <tr class="table-success">
            <td>Laba Bersih Kandang</td>
            <td>: 
                <strong>Rp {{ number_format(
                    $perhitunganGaji->hasil_pemeliharaan - 
                    $perhitunganGaji->total_potongan - 
                    ($perhitunganGaji->rincianGaji->sum('gaji_pokok') + $perhitunganGaji->rincianGaji->sum('bonus')) +
                    $perhitunganGaji->rincianGaji->sum('jumlah_pinjaman')
                , 0, ',', '.') }}</strong>
            </td>
        </tr>
    </table>

    <div style="margin-top: 20px;">
        <strong>Keterangan:</strong><br>
        <p>{{$perhitunganGaji->keterangan}}</p>
    </div>

    <div class="signature">
        <p>........................., ...............................</p>
        <br><br><br>
        <p>( .......................................................... )</p>
        <p>Penanggung Jawab</p>
    </div>

    {{-- <button onclick="window.print()" class="no-print" style="margin-top: 20px;">Cetak</button> --}}
    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>