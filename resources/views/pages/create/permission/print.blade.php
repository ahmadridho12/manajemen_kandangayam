<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        @page {
            size: 176mm 250mm;
            margin: 10mm;
        }

        body {
            margin: 0;
            padding: 16px; /* Menambahkan padding ke seluruh body */
            font-family: Arial, sans-serif;
            line-height: 1.6;
            text-align: center;
        }

        h1, h4 {
            margin: 0;
        }

       

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            padding: 48px; /* Menambahkan padding ke tabel */
        }

        table, th, td {
            border: 1px solid black;
        }

        th, td {
            padding: 10px;
        }

        .header {
            text-align: right;
            margin-bottom: 20px;
        }

        .title {
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 20px;
        }

        .content {
            text-align: start;
        }

        .signature-section {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }

        .signature {
            text-align: center;
            width: 45%;
        }

        .signature p {
            margin-top: 50px;
        }

        .footer {
            margin-top: 20px;
            text-align: start;
        }

        .keterangan {
            margin-top: 10px;
            font-size: 10px;
        }
        .dt-dd-row {
        display: flex;
        justify-content: flex-start;
        width: 100%;
    }

    .label-col {
        width: 30%; /* Adjust this width as necessary */
        text-align: left;
        font-weight: normal;
    }

    .colon-col {
        width: 5%; /* Small width for the colon */
        text-align: left;
    }

    .value-col {
        width: 65%; /* Adjust this width as necessary */
        text-align: left;
    }
    </style>
</head>
<body onload="window.print()">

<div class="header">
    <p>Lubuk Basung, {{ \Carbon\Carbon::parse($permissions->tgl_buat)->format('d F Y') }}</p>
</div>

<div class="content">
    <p>Kepada Yth.</p>
    <p>Direktur PERUMDA Air Minum<br>Tirta Antokan Kabupaten Agam<br>di<br>Tempat</p>

    <p>Yang bertanda tangan di bawah ini:</p>
    <dl class="mt-12">
        <div class="dt-dd-row">
            <dt class="label-col">Nama</dt>
            <span class="colon-col">:</span>
            <dd class="value-col">{{ $permissions->nama }}</dd>
        </div>
        
        <div class="dt-dd-row">
            <dt class="label-col">NIK</dt>
            <span class="colon-col">:</span>
            <dd class="value-col">{{ $permissions->nik ?: '-' }}</dd>
        </div>
        
        <div class="dt-dd-row">
            <dt class="label-col">Pangkat/Gol</dt>
            <span class="colon-col">:</span>
            <dd class="value-col">{{ $permissions->golongan ?: '-' }}</dd>
        </div>
        
        <div class="dt-dd-row">
            <dt class="label-col">Jabatan</dt>
            <span class="colon-col">:</span>
            <dd class="value-col">{{ $permissions->nama_jabatan }}</dd>
        </div>
        
        <div class="dt-dd-row">
            <dt class="label-col">Unit Kerja</dt>
            <span class="colon-col">:</span>
            <dd class="value-col">{{ $permissions->unit_kerja }}</dd>
        </div>
    </dl>
    <p>Dengan ini mengajukan permohonan izin untuk tidak masuk bekerja selama 1 (satu) hari, pada hari 
       {{ \Carbon\Carbon::parse($permissions->tgl_mulai)->format('l, d F Y') }} dengan alasan {{ $permissions->perihal }}.</p>

    <p>Demikian surat izin ini saya buat dengan sebenar-benarnya, atas perhatian dan izin yang Bapak/Ibu berikan saya ucapkan terima kasih.</p>
</div>

<div class="signature-section">
    <div class="signature">
        <p>Mengetahui,</p>
        <p>
            @foreach($permissions as $permission)
            @if($permission->jabatan)
                {{ $permission->jabatan->nama_jabatan }}
                @if($permission->jabatan->pegawai)
                    {{ $permission->jabatan->pegawai->nama_pegawai }} ({{ $permission->jabatan->pegawai->nik }})
                @endif
            @endif
        @endforeach
        </p>
        <p>({{ $permissions->jabatan->nama_jabatan }})</p> <!-- Mengambil nama jabatan -->
    </div>


    <div class="signature">
        <p>Hormat Saya,</p>
        <p>{{ $permissions->nama }}<br>NIK. {{ $permissions->nik }}</p>
    </div>
</div>

<div class="footer">
    <p>Zaldi<br>NIK. 4773622</p>
    <p>(Kabag Hubungan Langganan)</p>
</div>

<div class="keterangan">
    <p><strong>Keterangan:</strong></p>
    <ol>
        <li>Jika sakit 3 hari mohon melampirkan surat keterangan sakit dari dokter.</li>
        <li>Mohon surat disampaikan melalui Sub Bagian SDM agar bisa diproses lebih lanjut.</li>
        <li>Untuk pelaksana di unit pelayanan mengetahui dan menyetujui Kepala Unit.</li>
    </ol>
</div>

</body>
</html>
