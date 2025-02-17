<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BPB Print View</title>
    <style>
        @media print {
            @page {
                size: A4;
                margin: 0;
            }
        }
        
        body {
            margin: 10mm;
            font-family: Arial, sans-serif;
            font-size: 14px; /* Ukuran font lebih kecil */
            font-family: 'Calibri', sans-serif; /* Menerapkan font Calibri */

        }
        .header {
            text-align: center;
            margin-bottom: 10px; /* Mengurangi margin */
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

    <!-- Header Section -->
    <div class="header">
        <img src="{{ asset('sneat/img/kopsurat.png') }}" alt="Kop Surat">
    </div>
    <!-- Title Section -->
    <div style="text-align: center;">
        <p style="margin: 0;font-size: 16px; font-weight: bold;">BUKTI PENERIMAAN DAN PENGELUARAN BARANG</p>
        <p style="margin: 4px 0 0 0; font-size: 16px; font-weight: bold;">( BPB )</p>
    </div>

    <!-- Information Section -->
    <div style="display: flex; justify-content: space-between;margin-top: 0px;">
        <div style="margin-bottom: 10px; margin-top: -5px;"> <!-- Menambahkan margin-top untuk menggeser jaraknya -->
            <p style="font-size: 14px;"><strong>Bagian Yang Meminta: {{ $barangkeluar->permintaan->bagiann->nama_bagian ?? '-' }}</strong></p>
        </div>

        <div>
            <p style="font-size: 14px; margin: 0 0 2px 0;"><strong>BPB No.0{{ $barangkeluar->no_transaksi }}</strong></p>
            <hr style="margin: 2px 0; border: 1px solid black;"> <!-- Mengatur margin 2px untuk garis -->
            <p style="font-size: 14px; margin: 2px 0 0 0;"><strong>DPPB No.0{{ $barangkeluar->permintaan->no_trans }}</strong></p>
        </div>
        
    </div>
    <div style="display: flex; justify-content: space-between; margin-top: -20px;">
        <div>
            <p style="font-size: 14px;">Tanggal disetujui: {{ $barangkeluar->created_at->format('d-m-Y') }}</p>
        </div>

        <div>
            <p style="font-size: 14px;">Tanggal Permintaan: {{ $barangkeluar->permintaan->tgl_permintaan }}</p>

        </div>

    </div>
    <table class="content-table">
        <tr>
            <th rowspan="2" width="2%" style="font-size: 14px;">No</th>
            <th colspan="3" width="30%" style="font-size: 14px;">Yang Meminta</th>
            <th rowspan="2" width="30%" style="font-size: 14px;" >Uraian</th>
            <th colspan="4" width="30%" style="font-size: 14px;">Dikeluarkan</th>
            <th rowspan="2" width="15%" style="font-size: 14px;">Catatan</th>
        </tr>
    
        <tr>
            <th width="5%" style="font-size: 14px;">BYK</th>
            <th width="7%" style="font-size: 14px;">SAT</th>
            <th width="7%" style="font-size: 14px;">Kode Barang</th>
            <th width="5%" style="font-size: 14px;">BYK</th>
            <th width="7%" style="font-size: 14px;">SAT</th>
            <th width="13%" style="font-size: 14px;">HRG. SAT</th>
            <th width="13%" style="font-size: 14px;">Jumlah</th>
        </tr>
    
        @php
            $totalJumlah = 0; // Inisialisasi variabel total jumlah
            $iteration = 1; // Inisialisasi nomor iterasi
        @endphp
    
        @foreach($barangkeluar->detailBarangKeluar as $detail)
            @if($detail->jumlah > 0) <!-- Hanya tampilkan jika jumlah lebih dari 0 -->
            <tr>
                <td style="font-size: 10px;">{{ $iteration }}</td>
                <td style="font-size: 10px;">{{ $detail->jumlah }}</td>
                <td style="font-size: 10px;">{{ $detail->barang->satuan->nama_satuan }}</td>
                <td style="font-size: 10px;">{{ $detail->barang->kode_barang }}</td>
                <td style="text-align: left">{{ $detail->barang->deskripsi }}</td>
                <td style="font-size: 10px;">{{ $detail->jumlah }}</td>
                <td style="font-size: 10px;">{{ $detail->barang->satuan->nama_satuan }}</td>
                <td style="font-size: 10px;">RP.{{number_format ($detail->harga, 0, ',', '.') }}</td>
                <td style="font-size: 10px;">Rp.{{number_format ($detail->total, 0, ',', '.') }}</td>
                <td style="font-size: 10px;">{{ $barangkeluar->permintaan->keterangang }}</td>
            </tr>
            @php
                $totalJumlah += $detail->total; // Menambahkan nilai total ke variabel totalJumlah
                $iteration++; // Increment nomor iterasi
            @endphp
            @endif
        @endforeach
    
        <!-- Baris Total -->
        <tr>
            <td colspan="8" style="text-align: right;"><strong>Total:</strong></td>
            <td><strong>{{ number_format($totalJumlah, 2) }}</strong></td>
            <td></td> <!-- Kolom catatan kosong -->
        </tr>
        <tr>
            <td colspan="10" style="text-align: Left; font-size: 12px">
                Digunakan untuk :{{ $barangkeluar->permintaan->keterangan }}
            </td>
        </tr>
    </table>
    <div style="text-align: right;">
        <p>Lubuk Basung, Tanggal: {{ $currentDate }}</p>
    </div>
    
    <!-- Footer Section -->
    <div style="margin-top: 20px;margin-right: 30px;margin-left: 30px; display: flex; justify-content: space-between; align-items: center;">
        <div > <!-- Tambahkan text-align: center -->
            <p style="margin: 0; text-align: center;">Diminta Oleh</p>
            <br><br><br><br><br>
            <div class="row" style="display: flex; justify-content: center; flex-direction: column; align-items: flex-start; width: 120px;"> <!-- Atur lebar sesuai kebutuhan -->
                <p style="margin: 0; ">Nama&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</p> <!-- Nama dengan margin 0 -->
                <p style="margin: 0;  margin-top: 10px;">Tanggal&nbsp;&nbsp;&nbsp;&nbsp;:</p> <!-- Tanggal dengan margin 0 dan jarak antar elemen -->
            </div>
        </div>
        <div style=" text-align: center;">
            <p style="margin: 0;">Diketahui Oleh</p>
            <p style="margin: 0;">Kasubag Umum</p>
            <br><br><br><br>
            <p style="margin-top: 60px; margin: 0;"><u>Ramli</u></p> <!-- Tambahkan nama Ramli di sini -->
        </div>
        <div>
            <p style="margin: 0; text-align: center;">Dikeluarkan Oleh</p>
            <p style="margin: 0; text-align: center;">Pelaksana Gudang</p>
            <br><br><br><br>
            <div class="row" style="display: flex; justify-content: center; flex-direction: column; align-items: flex-start; width: 150px; margin-right: 20px"> <!-- Atur lebar sesuai kebutuhan -->
                <p style="margin: 0; ">Nama&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: Adi Duano</p> <!-- Nama dengan margin 0 -->
                <p style="margin: 0;  margin-top: 10px;">Tanggal&nbsp;&nbsp;&nbsp;&nbsp;:</p> <!-- Tanggal dengan margin 0 dan jarak antar elemen -->
            </div>
        </div>
        
    </div>
    <br>
    <div style="margin-top: 20px;margin-right: 30px;margin-left: 30px; display: flex; justify-content: space-between; align-items: center;">
        <div > <!-- Tambahkan text-align: center -->
            <p style="margin: 0; text-align: center;">Barang yang diminta diterima</p>
            <br><br><br><br><br>
            <div class="row" style="display: flex; justify-content: center; flex-direction: column; align-items: flex-start; width: 120px;"> <!-- Atur lebar sesuai kebutuhan -->
                <p style="margin: 0; ">Nama&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</p> <!-- Nama dengan margin 0 -->
                <p style="margin: 0;  margin-top: 10px;">Tanggal&nbsp;&nbsp;&nbsp;&nbsp;:</p> <!-- Tanggal dengan margin 0 dan jarak antar elemen -->
            </div>
        </div>
        <div>
            <p style="margin: 0; text-align: center;">Dibukukan ke kantor Stock</p>
            <br><br><br><br>
            <div class="row" style="display: flex; justify-content: center; flex-direction: column; align-items: flex-start; width: 150px; margin-right: 20px"> <!-- Atur lebar sesuai kebutuhan -->
                <p style="margin: 0; ">Nama&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: Adi Duano</p> <!-- Nama dengan margin 0 -->
                <p style="margin: 0;  margin-top: 10px;">Tanggal&nbsp;&nbsp;&nbsp;&nbsp;:</p> <!-- Tanggal dengan margin 0 dan jarak antar elemen -->
            </div>
        </div>
        
    </div>
    {{-- <div class="signature-section">
        <table>
            <tr>
                <td>
                    Diminta Oleh: <br><br><br>
                    <strong>Nama</strong> <br><br>
                    <hr>
                </td>
                <td>
                    Diketahui Oleh: <br><br><br>
                    <strong>Risnal</strong> <br>
                    Kasubag Umum <br><br>
                    <hr>
                </td>
                <td>
                    Dikeluarkan Oleh: <br><br><br>
                    <strong>Syafrijon</strong> <br><br>
                    <hr>
                </td>
            </tr>
        </table>
    </div> --}}

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
