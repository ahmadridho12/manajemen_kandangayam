<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
<!-- Di bagian head atau sebelum closing body -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <title>Performance Dashboard Print</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
        }
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
            /* .page-break {
                page-break-after: always;
            } */
            .no-print {
                display: none;
            }
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .section-title {
            background-color: #f4f4f4;
            padding: 10px;
            margin-top: 20px;
            border-bottom: 1px solid #ddd;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        th {
            background-color: #f4f4f4;
            text-align: left;
        }
        .strong {
            font-weight: bold;
        }
        .summary-row {
            background-color: #f9f9f9;
        }
        .total-row {
            font-weight: bold;
            background-color: #e9e9e9;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Performance Dashboard Report</h1>
        <p>Generated on: {{ date('d/m/Y H:i:s') }}</p>
    </div>

    <div class="section-title">
        <h2>Performance Calculation Components</h2>
    </div>
    <table>
        <tr>
            <td>Survival Rate (Daya Hidup)</td>
            <td class="strong">{{ isset($ringkasan['komponen']['daya_hidup']) ? number_format($ringkasan['komponen']['daya_hidup'], 2) . '%' : 'N/A' }}</td>
        </tr>
        <tr>
            <td>Average Body Weight</td>
            <td class="strong">{{ isset($ringkasan['komponen']['bobot_badan']) ? number_format($ringkasan['komponen']['bobot_badan'], 2) . ' Kg' : 'N/A' }}</td>
        </tr>
        <tr>
            <td>Average Harvest Age</td>
            <td class="strong">{{ isset($ringkasan['komponen']['umur']) ? number_format($ringkasan['komponen']['umur'], 0) . ' Days' : 'N/A' }}</td>
        </tr>
        <tr>
            <td>Feed Conversion Ratio (FCR)</td>
            <td class="strong">{{ isset($ringkasan['komponen']['fcr']) ? number_format($ringkasan['komponen']['fcr'], 3) : 'N/A' }}</td>
        </tr>
        <tr class="summary-row">
            <td class="strong">Performance Index (IP/EEF)</td>
            <td class="strong">{{ isset($ringkasan['data']['ringkasan']['ip']) ? number_format($ringkasan['data']['ringkasan']['ip'], 2) : 'N/A' }}</td>
        </tr>
    </table>

    <div class="page-break"></div>

    <div class="section-title">
        <h2>Data Populasi Kandang</h2>
    </div>
    <table class="population-table">
        <tr>
            <td>Populasi Awal</td>
            <td class="strong">{{ number_format($populasiData['populasi_awal'] ?? 0) }} Ekor</td>
        </tr>
        <tr>
            <td>Ayam Mati</td>
            <td class="strong">{{ number_format($populasiData['ayam_mati'] ?? 0) }} Ekor ({{ number_format($populasiData['persentase_mati'] ?? 0, 2) }}%)</td>
        </tr>
        <tr>
            <td>Ayam Tersisa</td>
            <td class="strong">{{ number_format($populasiData['ayam_sisa'] ?? 0) }} Ekor</td>
        </tr>
        <tr>
            <td>Ayam Terpanen</td>
            <td class="strong">{{ number_format($populasiData['ayam_panen'] ?? 0) }} Ekor```php
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Performance Dashboard Print</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
        }
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
            /* .page-break {
                page-break-after: always;
            } */
            .no-print {
                display: none;
            }
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .section-title {
            background-color: #f4f4f4;
            padding: 10px;
            margin-top: 20px;
            border-bottom: 1px solid #ddd;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        th {
            background-color: #f4f4f4;
            text-align: left;
        }
        .strong {
            font-weight: bold;
        }
        .summary-row {
            background-color: #f9f9f9;
        }
        .total-row {
            font-weight: bold;
            background-color: #e9e9e9;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Performance Dashboard Report</h1>
        <p>Generated on: {{ date('d/m/Y H:i:s') }}</p>
    </div>

    <div class="section-title">
        <h2>Performance Calculation Components</h2>
    </div>
    <table>
        <tr>
            <td>Survival Rate (Daya Hidup)</td>
            <td class="strong">{{ isset($ringkasan['komponen']['daya_hidup']) ? number_format($ringkasan['komponen']['daya_hidup'], 2) . '%' : 'N/A' }}</td>
        </tr>
        <tr>
            <td>Average Body Weight</td>
            <td class="strong">{{ isset($ringkasan['komponen']['bobot_badan']) ? number_format($ringkasan['komponen']['bobot_badan'], 2) . ' Kg' : 'N/A' }}</td>
        </tr>
        <tr>
            <td>Average Harvest Age</td>
            <td class="strong">{{ isset($ringkasan['komponen']['umur']) ? number_format($ringkasan['komponen']['umur'], 0) . ' Days' : 'N/A' }}</td>
        </tr>
        <tr>
            <td>Feed Conversion Ratio (FCR)</td>
            <td class="strong">{{ isset($ringkasan['komponen']['fcr']) ? number_format($ringkasan['komponen']['fcr'], 3) : 'N/A' }}</td>
        </tr>
        <tr class="summary-row">
            <td class="strong">Performance Index (IP/EEF)</td>
            <td class="strong">{{ isset($ringkasan['data']['ringkasan']['ip']) ? number_format($ringkasan['data']['ringkasan']['ip'], 2) : 'N/A' }}</td>
        </tr>
    </table>

    <div class="page-break"></div>

    <div class="section-title">
        <h2>Data Populasi Kandang</h2>
    </div>
    <table class="population-table">
        <tr>
            <td>Populasi Awal</td>
            <td class="strong">{{ number_format($populasiData['populasi_awal'] ?? 0) }} Ekor</td>
        </tr>
        <tr>
            <td>Ayam Mati</td>
            <td class="strong">{{ number_format($populasiData['ayam_mati'] ?? 0) }} Ekor ({{ number_format($populasiData['persentase_mati'] ?? 0, 2) }}%)</td>
        </tr>
        <tr>
            <td>Ayam Tersisa</td>
            <td class="strong">{{ number_format($populasiData['ayam_sisa'] ?? 0) }} Ekor</td>
        </tr>
        <tr>
            <td>Ayam Terpanen</td>
            <td class="strong">{{ number_format($populasiData['ayam_panen'] ?? 0) }} Ekor</td>
        </tr>
    </table>

    <div class="section-title">
        <h2>Konversi Pakan ke Daging</h2>
    </div>
    <table>
        <tr>
            <td>Total Berat Pakan</td>
            <td class="strong">{{ number_format($dataPanen['data']['ringkasan']['total_pakan_terpakai'] ?? 0, 2) }} Kg</td>
        </tr>
        <tr>
            <td>Total Berat Dagingt</td>
            <td class="strong">{{ number_format($dataPanen['data']['total']['total_bb'] ?? 0, 2) }} Kg</td>
        </tr>
        <tr>
            <td>Rasio Konversi</td>
            <td class="strong">{{ number_format((($dataPanen['data']['total']['total_bb'] ?? 0) / ($dataPanen['data']['ringkasan']['total_pakan_terpakai'] ?? 1)) * 100, 2) }}%</td>
        </tr>
    </table>
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title">Ringkasan Data Panen</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Kolom Kiri -->
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td style="width: 40%"><strong>KANDANG</strong></td>
                            <td>{{ $data->first()->kandang->nama_kandang ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>UMUR RATA-RATA</strong></td>
                            <td>{{ number_format($dataPanen['data']['ringkasan']['umur_rata_rata'], 2) }} Hari</td>
                        </tr>
                        <tr>
                            <td><strong>TOTAL PAKAN TERPAKAI</strong></td>
                            <td>
                                @foreach($dataPanen['data']['ringkasan']['data_pakan'] as $pakan)
                                    {{ $pakan->nama_pakan }} {{ number_format($pakan->total_qty) }} Kg<br>
                                @endforeach
                                <strong>Total: {{ number_format($dataPanen['data']['ringkasan']['total_pakan_terpakai']) }} Kg</strong>
                            </td>
                        </tr>
                    </table>
                </div>
                <!-- Kolom Kanan -->
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td style="width: 40%"><strong>BOBOT PANEN RATA-RATA</strong></td>
                            <td>{{ number_format($dataPanen['data']['ringkasan']['bobot_panen_rata_rata'], 3) }} Kg</td>
                        </tr>
                        <tr>
                            <td><strong>DAYA HIDUP</strong></td>
                            <td>{{ number_format($dataPanen['data']['ringkasan']['daya_hidup'], 2) }} %</td>
                        </tr>
                        <tr>
                            <td><strong>FCR</strong></td>
                            <td>{{ number_format($dataPanen['data']['ringkasan']['fcr'], 3) }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="page-break"></div>

    <div class="section-title">
        <h2>Detail Data Panen</h2>
    </div>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Age (Days)</th>
                <th>% Panen</th>
                <th>Quantity Panen</th>
</td>
        </tr>
    </table>

    <div class="section-title">
        <h2>Konversi Pakan ke Daging</h2>
    </div>
    <table>
        <tr>
            <td>Total Berat Pakan</td>
            <td class="strong">{{ number_format($dataPanen['data']['ringkasan']['total_pakan_terpakai'] ?? 0, 2) }} Kg</td>
        </tr>
        <tr>
            <td>Total Berat Dagingt</td>
            <td class="strong">{{ number_format($dataPanen['data']['total']['total_bb'] ?? 0, 2) }} Kg</td>
        </tr>
        <tr>
            <td>Rasio Konversi</td>
            <td class="strong">{{ number_format((($dataPanen['data']['total']['total_bb'] ?? 0) / ($dataPanen['data']['ringkasan']['total_pakan_terpakai'] ?? 1)) * 100, 2) }}%</td>
        </tr>
    </table>
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title">Ringkasan Data Panen</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Kolom Kiri -->
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td style="width: 40%"><strong>KANDANG</strong></td>
                            <td>{{ $data->first()->kandang->nama_kandang ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>UMUR RATA-RATA</strong></td>
                            <td>{{ number_format($dataPanen['data']['ringkasan']['umur_rata_rata'], 2) }} Hari</td>
                        </tr>
                        <tr>
                            <td><strong>TOTAL PAKAN TERPAKAI</strong></td>
                            <td>
                                @foreach($dataPanen['data']['ringkasan']['data_pakan'] as $pakan)
                                    {{ $pakan->nama_pakan }} {{ number_format($pakan->total_qty) }} Kg<br>
                                @endforeach
                                <strong>Total: {{ number_format($dataPanen['data']['ringkasan']['total_pakan_terpakai']) }} Kg</strong>
                            </td>
                        </tr>
                    </table>
                </div>
                <!-- Kolom Kanan -->
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td style="width: 40%"><strong>BOBOT PANEN RATA-RATA</strong></td>
                            <td>{{ number_format($dataPanen['data']['ringkasan']['bobot_panen_rata_rata'], 3) }} Kg</td>
                        </tr>
                        <tr>
                            <td><strong>DAYA HIDUP</strong></td>
                            <td>{{ number_format($dataPanen['data']['ringkasan']['daya_hidup'], 2) }} %</td>
                        </tr>
                        <tr>
                            <td><strong>FCR</strong></td>
                            <td>{{ number_format($dataPanen['data']['ringkasan']['fcr'], 3) }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="page-break"></div>

    <div class="section-title">
        <h2>Detail Data Panen</h2>
    </div>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Age (Days)</th>
                <th>% Panen</th>
                <th>Quantity Panen</th>
                <th>Total Weight (Kg)</th>
                <th>Avg Weight (Kg)</th>
                <th>Age Quantity</th>
                <th>Harga Per Ekor</th>
                <th>Total Terjual</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dataPanen['data']['records'] ?? [] as $record)
            <tr>
                <td>{{ \Carbon\Carbon::parse($record->tanggal_panen ?? now())->format('d/m/Y') }}</td>
                <td>{{ $record->umur ?? '-' }}</td>
                <td>{{ number_format($record->persen_panen ?? 0, 3) }}%</td>
                <td>{{ number_format($record->jumlah_panen ?? 0) }}</td>
                <td>{{ number_format($record->total_bb_panen ?? 0, 2) }}</td>
                <td>{{ number_format(($record->total_bb_panen ?? 0) / max(($record->jumlah_panen ?? 1), 1), 3) }}</td>
                <td>{{ number_format($record->age_quantity ?? 0) }}</td>
                <td>{{ number_format($record->harga ?? 0, 2, ',', '.') }}</td>
                <td>{{ number_format($record->total_panen ?? 0, 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="2">Total</td>
                <td>{{ number_format($dataPanen['data']['total']['total_persen'] ?? 0, 3) }}%</td>
                <td>{{ number_format($dataPanen['data']['total']['total_jumlah'] ?? 0) }}</td>
                <td>{{ number_format($dataPanen['data']['total']['total_bb'] ?? 0, 2) }}</td>
                <td>-</td>
                <td><strong>{{ number_format($dataPanen['data']['total']['total_age_quantity'] ?? 0) }}</strong></td>
                <td>
                    <strong>{{ number_format($dataPanen['data']['total']['average_harga'] ?? 0, 0, ',', '.') }}</strong>
                </td>
                <td>
                    <strong>{{ number_format($dataPanen['data']['total']['total_panen'] ?? 0, 0, ',', '.') }}</strong>
                </td>
            </tr>
        </tbody>
    </table>
    <br>
    <br>
    <br>
    @if(!empty($estimasiPembelian))
    <h4>Estimasi Pembelian</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Uraian</th>
                <th>QTY</th>
                <th>Satuan</th>
                <th>Harga Satuan</th>
                <th>Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <!-- DOC -->
            <tr>
                <td>DOC</td>
                <td>{{ number_format($ayam->qty_ayam) ?? 0 }}</td>
                <td>Ekor</td>
                <td>{{ number_format($ayam->doc->harga, 0, ',', '.') }}</td>
                <td>Rp {{ number_format($estimasiPembelian['doc']['total_harga'] ?? 0) }}</td>
            </tr>
            <!-- Pakan -->
            @foreach($estimasiPembelian['pakan'] as $pakan)
                <tr>
                    <td>{{ $pakan->nama_pakan }}</td>
                    <td>{{ number_format($pakan->total_qty) ?? 0 }}</td>
                    <td>Kg</td>
                    <td>{{ number_format($pakan->harga, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($pakan->total_harga) }}</td>
                </tr>
            @endforeach
            <!-- Obat -->
            <tr>
                <td>Obat & Vitamin</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>Rp {{ number_format($estimasiPembelian['obat'] ?? 0) }}</td>
            </tr>
            <!-- Pakan Tersisa -->
            @if(!empty($estimasiPembelian['pakan_tersisa_detail']))
                <tr class="table-active">
                    <td colspan="5" class="text-center"><strong>Pakan Tersisa</strong></td>
                </tr>
                @foreach($estimasiPembelian['pakan_tersisa_detail'] as $tersisa)
                    <tr>
                        <td>{{ $tersisa->nama_pakan }}</td>
                        <td>{{ number_format($tersisa->total_berat, 0, ',', '.') }}</td>
                        <td>Kg</td>
                        <td>{{ number_format($tersisa->harga, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($tersisa->total_cost, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            @endif
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4"><strong>Total Pembelian</strong></td>
                <td><strong>Rp {{ number_format($estimasiPembelian['total_pembelian'] ?? 0) }}</strong></td>
            </tr>
        </tfoot>
    </table>
@endif

   
    @if(!empty($penjualan))
    <h4>PENJUALAN</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>Uraian</th>
                <th>Rata-Rata Berat</th>
                <th>Satuan</th>
                <th>Harga Satuan</th>
                <th>Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>Penjualan Daging</td>
                <td>{{ number_format($penjualan['penjualan_daging']['qty'] ?? 0) }}</td>
                <td>Kg</td>
                <td>Rp {{ number_format($penjualan['penjualan_daging']['harga_satuan'] ?? 0) }}</td>
                <td>Rp {{ number_format($penjualan['total_penjualan'] ?? 0) }}</td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5"><strong>TOTAL PENJUALAN</strong></td>
                <td>Rp {{ number_format($penjualan['total_penjualan'] ?? 0) }}</td>
            </tr>
            <tr>
                <td colspan="5">BONUS FCR</td>
                <td>Rp {{ number_format($penjualan['bonus_fcr'] ?? 0) }}</td>
            </tr>
            <tr>
                <td colspan="5">BONUS KEMATIAN</td>
                <td>Rp {{ number_format($penjualan['bonus_kematian'] ?? 0) }}</td>
            </tr>
            <tr class="table-active">
                <td colspan="5"><strong>LABA (PENJUALAN - PEMBELIAN)</strong></td>
                <td><strong>Rp {{ number_format($penjualan['laba'] ?? 0) }}</strong></td>
            </tr>
        </tfoot>
    </table>
@endif
    <script>
        // Automatically trigger print when page loads
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>