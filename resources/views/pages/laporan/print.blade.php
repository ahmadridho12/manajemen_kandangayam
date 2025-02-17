<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BPB Print View</title>
    <style>
        @media print {
            @page {
                size: 21cm 33cm landscape; /* Mengatur ukuran F4 dan orientasi landscape */
                margin: 0;
            }

        }
        
        body {
            margin: 24mm;
            font-family: Arial, sans-serif;
            font-size: 14px; /* Ukuran font lebih kecil */
            font-family: 'Calibri', sans-serif; /* Menerapkan font Calibri */
            
        }
        .header {
            text-align: center;
            margin-top: 80px; /* Mengurangi margin */
        }
        .header img {
            width: 100%; /* Membuat gambar memenuhi lebar kontainer */
            height: auto; /* Mempertahankan aspek rasio gambar */
        }
        .header h1, .header h3 {
            margin: 0; /* Menghapus margin untuk header */
        }
        .content-table {
            width: 100%;
            border-collapse: collapse; /* Menggabungkan border */
            margin-bottom: 10px; /* Mengurangi margin bawah */
        }
        .content-table th, .content-table td {
            border: 1px solid black; /* Border untuk setiap sel */
            padding: 2px; /* Mengurangi padding */
            text-align: center;
            font-size: 10px; /* Ukuran font tabel lebih kecil */
        }
        .signature-section {
            width: 100%;
            margin-top: 10px; /* Mengurangi margin atas */
        }
        .signature-section td {
            width: 33%;
            text-align: center;
            vertical-align: top;
        }
        .footer hr {
            border: 1px solid black;
            margin: 5px 0; /* Mengurangi margin */
        }
    </style>
</head>
<body>

    

    <!-- Information Section -->
    <div class="header" style="display: flex; justify-content: space-between; align-items: flex-start;margin-top: -40px;">
        <div class="logo" style="width: 48px; height: 48px; padding-right: 10px;">
            <img src="{{ asset('sneat/img/logo.png') }}" alt="Kop Surat">
        </div>
        <div style="margin-bottom: 10px; margin-top: -5px;">
            <h3 style="text-align: left;">
                PERUMDA AIR MINUM TIRTA ANTOKAN
                <span style="display: block;">KABUPATEN AGAM</span>
            </h3>
            <p style="margin-top: 0px;">
                Jl. Dr. Mohd. Hatta No.531 Telp. (0752) 76057 LUBUK BASUNG - 26415 
                <span style="display: block; text-align: left;">Website: pdamagam.com email : agampdam@yahoo.co.id</span>
            </p>
        </div>
    
        <div style="text-align: right; margin-left: auto;">
            <h3 style="margin: 0;">LAPORAN REKAPITULASI STOCK BARANG BULANAN <span style="display: block;">PERIODE :{{ $bulan }}</span></h3>
        </div>
    </div>
    
   
    <table class="content-table">
        <tr>
            <th rowspan="2" width="2%" style="font-size: 14px;">No</th>
            <th rowspan="2" width="5%" style="font-size: 14px;">Kode Barang</th>
            <th rowspan="2" width="15%" style="font-size: 14px;">Nama Barang</th>
            <th rowspan="2" width="4%" style="font-size: 14px;">Satuan</th>
            <th rowspan="2" width="8%" style="font-size: 14px;">Harga</th>
            <th colspan="2" width="30%" style="font-size: 14px;">SALDO AWAL</th>
            <th colspan="2" width="30%" style="font-size: 14px;">PENERIMAAN</th>
            <th colspan="2" width="30%" style="font-size: 14px;">PENGELUARAN</th>
            <th colspan="2" width="30%" style="font-size: 14px;">PENYESUAIAN</th>
            <th colspan="2" width="30%" style="font-size: 14px;">SALDO AKHIR</th>
            <th rowspan="2" width="15%" style="font-size: 14px;">KET</th>
        </tr>
    
        <tr>
            <th width="5%" style="font-size: 14px;">Q</th>
            <th width="7%" style="font-size: 14px;">JUMLAH</th>
            <th width="5%" style="font-size: 14px;">Q</th>
            <th width="7%" style="font-size: 14px;">JUMLAH</th>
            <th width="5%" style="font-size: 14px;">Q</th>
            <th width="7%" style="font-size: 14px;">JUMLAH</th>
            <th width="5%" style="font-size: 14px;">Q</th>
            <th width="7%" style="font-size: 14px;">JUMLAH</th>
            <th width="5%" style="font-size: 14px;">Q</th>
            <th width="7%" style="font-size: 14px;">JUMLAH</th>
            
        </tr>
    
        @php
            $totalAwal = 0; // Inisialisasi variabel total jumlah
            $totalDiterima = 0; // Inisialisasi total diterima
            $totalKeluar = 0; // Inisialisasi total keluar
            $totalNilaiStoks = 0; // Inisialisasi total nilai stoks
            $totalSaldoAkhir = 0; // Inisialisasi total nilai stoks

        @endphp
    
    <tr>
        <td colspan="16" style="text-align: left; font-size: 14px; font-weight: bold">KELOMPOK Barang: {{ $jenisOptions[$id_jenis] ?? '-' }}</td>
    </tr>
    @foreach($data as $index => $detailstok)
    <tr>
        <td>{{ $index + 1 }}</td>
        
        <td>{{ $detailstok->kode_barang ?? '-' }}</td>
        <td style="text-align: left;padding-left: 5px">{{ $detailstok->deskripsi ?? '-' }}</td>
        
        <td>{{ $detailstok->nama_satuan ?? '-' }}</td>
        <td>{{ number_format($detailstok->harga ?? 0, 2, ',', '.') }}</td>
        {{-- <td>{{ number_format($detailstok->total_masuk ?? '.') }}</td> --}}
        <td>{{ $detailstok->qty_awal_periode ??  '-'}}</td> 
        <td>{{ number_format($detailstok->qty_awal_periode * $detailstok->harga ?? 0, 2, ',', '.') }}</td> <!-- Pastikan ini sesuai dengan data -->
        
        <td>{{ $detailstok->qty_masuk ?? '.' }}</td> <!-- Sesuaikan dengan data total diterima -->
        <td>{{ number_format($detailstok->qty_masuk * $detailstok->harga ?? 0, 2, ',', '.') }}</td> <!-- Sesuaikan dengan data total diterima -->
        
        <td>{{ $detailstok->qty_keluar ?? '.' }}</td> <!-- Ganti 'total_keluar' menjadi 'qty_keluar' -->
        <td>{{ number_format($detailstok->nilai_keluar ?? 0,2, ',', '.') }}</td>
       
        <td>{{ number_format ( $detailstok->qty_stoks ?? 0,0, ',', '.' )}}</td> <!-- Sesuaikan dengan data qty_akhir -->
        <td>{{ number_format($detailstok->total_nilai_stokds ?? 0, 1, ',', '.') }}</td>

        <td>{{ $detailstok->qty_akhir ?? '.' }}</td> <!-- Sesuaikan dengan data qty_akhir -->
        <td>{{ number_format($detailstok->saldo_akhir ?? 0, 2, ',', '.') }}</td>

        <td>{{ $detailstok->qty_stoks ?? '.' }}</td> <!-- Sesuaikan dengan data qty_akhir -->

    </tr>
    @php
    $totalAwal += $detailstok->qty_awal_periode * $detailstok->harga ?? 0; // Menambahkan nilai total ke variabel totalJumlah
    $totalDiterima += $detailstok->qty_masuk * $detailstok->harga ?? 0; // Menambahkan nilai total diterima
    $totalKeluar += $detailstok->nilai_keluar ?? 0; // Menambahkan nilai total keluar
    $totalNilaiStoks += $detailstok->total_nilai_stoks ?? 0; // Menambahkan nilai total nilai stoks
    $totalSaldoAkhir += $detailstok->saldo_akhir ?? 0; // Menambahkan nilai total nilai stok
    @endphp
    @endforeach

    <!-- Baris Total -->
    <tr>
        <td colspan="5" style="text-align: center;"><strong>SUB TOTAL PERKELOMPOK BARANG </strong></td>
        <td></td> <!-- Kosongkan kolom 6 -->
        <td><strong>{{ number_format($totalAwal, 2, ',', '.') }}</strong></td>
        <td></td> <!-- Kosongkan kolom 6 -->
        <td><strong>{{ number_format($totalDiterima, 2, ',', '.') }}</strong></td>
        <td></td> <!-- Kosongkan kolom 6 -->
        <td><strong>{{ number_format($totalKeluar, 2, ',', '.') }}</strong></td>
        <td></td> <!-- Kosongkan kolom 6 -->
        <td><strong>{{ number_format($totalNilaiStoks, 1, ',', '.') }}</strong></td>
        <td></td> <!-- Kosongkan kolom 6 -->
        <td><strong>{{ number_format($totalSaldoAkhir, 2, ',', '.') }}</strong></td>
        <td></td> <!-- Kolom catatan kosong -->
    </tr>
        </table>
    

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
