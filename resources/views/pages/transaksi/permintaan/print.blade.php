<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    
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
            img {
                width: 100%; /* Memastikan gambar memenuhi lebar kertas */
                height: auto; /* Mengatur tinggi otomatis untuk menjaga rasio aspek */
                position: relative; /* Posisi relatif untuk gambar */
            }
            .bbw {
                margin-top: 0px; /* Memberikan jarak atas */
                text-align: center; /* Pusatkan teks */
            }
        }
    </style>

</head>
<body>
    
    <img src="{{ asset('sneat/img/kopsurat.png') }}" alt="Kop Surat">
    
    <div class="bbw"> <!-- Tambahkan kelas untuk pengaturan -->
        <p style="font-size: 16px; font-weight: bold;">DAFTAR PENGAJUAN PERMINTAAN BARANG</p>
        <p style="margin-top: -20px; font-size: 16px;font-weight: bold">( DPPB )</p> <!-- Gunakan margin-top negatif untuk mendekatkan -->
    </div>

    <div style="display: flex; justify-content: space-between; margin: 20px 0;">
        <div>
            <strong>
                <small>Bagian yang Meminta: {{ $permintaan->bagiann->nama_bagian ?? '-' }}</small>
            </strong>
                    </div>
        <div>
            <strong><small>No:0{{ $permintaan->no_trans }}</small></strong>
        </div>
    </div>

    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
        <thead>
            <tr>
                <th width="5%" style="border: 1px solid #000; padding: 2px;">No</th>
                <th width="15%" style="border: 1px solid #000; padding: 2px;">Kode Barang</th>
                <th style="border: 1px solid #000; padding: 8px;">Nama Barang</th>
                <th width="10%" style="border: 1px solid #000; padding: 8px;">Satuan</th>
                <th width="10%" style="border: 1px solid #000; padding: 0px;">Banyak</th>
                <th style="border: 1px solid #000; padding: 8px;">Catatan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($permintaan->detailpermintaan as $index => $detail)
                <tr>
                    <td style="border: 1px solid #000; padding: 2px; font-size: 10px; text-align: center">{{ $index + 1 }}</td>
                    <td style="border: 1px solid #000; padding: 2px; font-size: 10px">{{ $detail->barang->kode_barang ?? '-' }}</td>
                    <td style="border: 1px solid #000; padding: 2px;font-size: 10px">{{ $detail->barang->deskripsi ?? '-' }}</td>
                    <td style="border: 1px solid #000; padding: 2px;font-size: 10px">{{ $detail->barang->satuan->nama_satuan ?? '-' }}</td>
                    <td style="border: 1px solid #000; padding: 2px; text-align: center;font-size: 10px">{{ $detail->qty }}</td>
                    <td style="border: 1px solid #000; padding: 2px;font-size: 10px">{{ $detail->keterangan }}</td>
                </tr>
                @endforeach
            </tbody>
            <tr>
               <td colspan="6" style="text-align: left; font-size: 14px; border: 1px solid #000; padding: 8px">
                Di Gunakan Untuk:{{ $permintaan->tipe->nama_tipe  }} {{ $permintaan->keterangan }}</td> 
            </tr>
    </table>

    <div style="text-align: right;">
        <p>Lubuk Basung, {{ $currentDate }}</p>
    </div>

    
    

    <div style="margin-top: 20px;margin-right: 30px; display: flex; justify-content: space-between; align-items: center;">
        <div style=" text-align: center;"> <!-- Tambahkan text-align: center -->
            <p style="margin: 0;">Di Periksa Oleh</p>
            <p style="margin: 0;">Kasubag Umum dan Gudang</p>
            <br><br><br><br>
            <p style="margin-top: 60px; margin: 0;font-weight: bold;">Ramli</p> <!-- Tambahkan nama Ramli di sini -->
            <hr style="margin: 0; border: 1px solid black;"> <!-- Garis di bawah Ramli -->
        </div>
        <div style=" text-align: center;">
            <p style="margin: 0;">Dikeluarkan Oleh</p>
            <p style="margin: 0;">Pelaksana Gudang</p>
            <br><br><br><br>
            <p style="margin-top: 60px; margin: 0;font-weight: bold;">Adi Duano</p> <!-- Tambahkan nama Ramli di sini -->
            <hr style="margin: 0; border: 1px solid black;"> <!-- Garis di bawah Ramli -->
        </div>
        
    </div>
    <div style="text-align: center; margin-top: 20px;">
        <p>Diketahui Oleh</p>
        <p style="margin-top: -10px;">Kabag Adm dan Keuangan</p>
        <br><br><br><br>
        <p style="margin-top: 60px; margin: 0; font-weight: bold;">Destaman, S.Ap</p>
        <hr style="width: 25%; margin: 0 auto; border: 1px solid black;"> <!-- Garis horizontal untuk tanda tangan -->
    </div>
    
    

    <script>
        window.onload = function() {
            window.print();
        };
    </script>

</body>
</html>
