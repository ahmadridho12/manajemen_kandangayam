@extends('layout.main')

@section('content')
    <x-breadcrumb
        :values="[__('Barang Masuk'), __('Detail Barang Masuk'), __('menu.general.view')]">
        <a href="" class="btn btn-primary">
            Print
        </a>
    </x-breadcrumb>

        <div class="mt-2">
            <h5 style="font-weight: bolder">{{ $barangMasuk->no_transaksi }}</h5>
            <dl class="row mt-3">

                <dt class="col-sm-3">{{ __('Nomor Transaksi') }}</dt>
                <dd class="col-sm-9">{{ $barangMasuk->no_transaksi }}</dd>

                <dt class="col-sm-3">{{ __('Suplier') }}</dt>
                <dd class="col-sm-9">{{ $barangMasuk->suplier->nama_suplier }}</dd>

                <dt class="col-sm-3">{{ __('Keterangan') }}</dt>
                <dd class="col-sm-9">{{ $barangMasuk->Keterangan }}</dd>

                <dt class="col-sm-3">{{ __('Tanggal Masuk') }}</dt>
                <dd class="col-sm-9">{{ $barangMasuk->tgl_masuk }}</dd>

                {{-- <dt class="col-sm-3">{{ __('model.classification.code') }}</dt>
                <dd class="col-sm-9">{{ $permintaan->classification_code }}</dd>

                <dt class="col-sm-3">{{ __('model.classification.type') }}</dt>
                <dd class="col-sm-9">{{ $permintaan->classification?->type }}</dd> --}}

                
            </dl>
            <div class="divider">
                <div class="divider-text">{{ __('menu.general.view') }}</div>
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>No</th> <!-- Kolom Nomor -->
                            <th>{{ __('Kode Barang') }}</th>
                            <th>{{ __('Nama Barang') }}</th>
                            <th>{{ __('Satuan ') }}</th>
                            <th>{{ __('Banyak ') }}</th>
                            <th>{{ __('harga Sebelum PPN ') }}</th>
                            <th>{{ __('harga Setelah PPN ') }}</th>
                            <th>{{ __('jumlah ') }}</th>
                            <th>{{ __('Keterangan ') }}</th>
                       
                        </tr>
                        </thead>
                        @if($barangMasuk->detail)
                            <tbody>
                                @foreach($barangMasuk->detail as $detail)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td> <!-- Menampilkan nomor urut -->
                                        <td>{{ $detail->barang->kode_barang }}</td> <!-- Mengakses kode barang dari relasi barang -->
                                        <td>{{ $detail->barang->deskripsi }}</td> <!-- Mengakses deskripsi barang -->
                                        <td>{{ $detail->barang->satuan->nama_satuan }}</td> <!-- Mengakses satuan dari relasi barang -->
                                        <td>{{ $detail->jumlah }}</td> <!-- Jumlah barang -->
                                        <td>{{ $detail->harga_sebelum_ppn }}</td> <!-- Harga barang -->
                                        <td>{{ $detail->harga_setelah_ppn }}</td> <!-- Harga barang -->
                                        <td>{{ $detail->total_setelah_ppn }}</td> <!-- Harga barang -->
                                        <td>{{ $barangMasuk->Keterangan }}</td> <!-- Keterangan permintaan -->
                                    </tr>
                                @endforeach
                            </tbody>
                        @else
                            <p>Data tidak ditemukan.</p>
                        @endif
    
                </table>
        </div>
    </div>

@endsection
