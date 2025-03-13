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
        .slip-container {
            max-width: 800px;
            margin: 0 auto;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 8px;
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
        .signature {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            text-align: center;
            width: 200px;
        }
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="slip-container">
        <div class="header">
            <img src="{{ asset('ayam.png') }}" alt="" class="logo" style="width: 200px; height: auto;">
            <h2 style="margin: 5px 0">NC FARM</h2>

        </div>

        <h3 style="text-align: center">SLIP GAJI KARYAWAN</h3>

        <table class="info-table">
            <tr>
                <td width="150">Nama</td>
                <td width="10">:</td>
                <td>{{ $rincian->abk->nama }}</td>
            </tr>
            <tr>
                <td>Periode</td>
                <td>:</td>
                <td>{{ $rincian->perhitunganGaji->ayam->periode }}</td>
            </tr>
            <tr>
                <td>Kandang</td>
                <td>:</td>
                <td>{{ $rincian->perhitunganGaji->kandang->nama_kandang }}</td>
            </tr>
        </table>

        <table class="detail-table">
            <tr>
                <th colspan="2">RINCIAN GAJI</th>
            </tr>
            <tr>
                <td>Gaji Pokok</td>
                <td style="text-align: right">Rp {{ number_format($rincian->gaji_pokok, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Bonus</td>
                <td style="text-align: right">Rp {{ number_format($rincian->bonus, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td><strong>Total Gaji</strong></td>
                <td style="text-align: right"><strong>Rp {{ number_format($rincian->gaji_pokok + $rincian->bonus, 0, ',', '.') }}</strong></td>
            </tr>
            <tr>
                <td>Potongan Pinjaman</td>
                <td style="text-align: right">Rp {{ number_format($rincian->jumlah_pinjaman ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <th>TOTAL DITERIMA</th>
                <th style="text-align: right">Rp {{ number_format($rincian->gaji_bersih, 0, ',', '.') }}</th>
            </tr>
        </table>

        <div class="signature">
            <div class="signature-box">
                <p>Diterima oleh,</p>
                <br><br><br>
                <p>( {{ $rincian->abk->nama }} )</p>
                <p>Karyawan</p>
            </div>
            <div class="signature-box">
                <p>{{ date('d F Y') }}</p>
                <br><br><br>
                <p>( ........................... )</p>
                <p>Penanggung Jawab</p>
            </div>
        </div>

        {{-- <button onclick="window.print()" class="no-print" style="margin-top: 20px;">Cetak</button> --}}
    </div>
    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>