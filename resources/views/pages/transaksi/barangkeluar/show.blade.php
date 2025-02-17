@extends('layout.main')

@section('content')
    <x-breadcrumb
        :values="[__('Permintaan'), __('Detail Permintaan'), __('menu.general.view')]">
        <a href="{{ route('transaksi.barangkeluar.print', $barangkeluar->id_keluar) }}" class="btn btn-primary">
            Print
        </a>
    </x-breadcrumb>

        <div class="mt-2">
            <h5 style="font-weight: bolder">{{ $barangkeluar->no_transaksi }}</h5>
            <dl class="row mt-3">

                <dt class="col-sm-3">{{ __('Nomor Transaksi') }}</dt>
                <dd class="col-sm-9">{{ $barangkeluar->no_transaksi }}</dd>

                <dt class="col-sm-3">{{ __('Bagian yang meminta') }}</dt>
                <dd class="col-sm-9">{{ $barangkeluar->permintaan->bagiann->nama_bagian }}</dd>

                <dt class="col-sm-3">{{ __('Kegunaan') }}</dt>
                <dd class="col-sm-9">{{ $barangkeluar->permintaan->tipe->nama_tipe }}</dd>

                <dt class="col-sm-3">{{ __('Keterangan') }}</dt>
                <dd class="col-sm-9">{{ $barangkeluar->permintaan->keterangan }}</dd>

                <dt class="col-sm-3">{{ __('Tanggal Keluar') }}</dt>
                <dd class="col-sm-9">{{ $barangkeluar->tanggal_keluar }}</dd>

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
                        <th>{{ __('harga Satuan ') }}</th>
                        <th>{{ __('jumlah ') }}</th>
                        <th>{{ __('Catatan ') }}</th>
                   
                    </tr>
                    </thead>
                    @if($barangkeluar)
                    <tbody>
                        @php $counter = 1 @endphp
                        @foreach($barangkeluar->detailBarangKeluar as $detail)
                            @if($detail->jumlah > 0)
                                <tr>
                                    <td>{{ $counter }}</td>
                                    <td>{{ $detail->barang->kode_barang }}</td>
                                    <td>{{ $detail->barang->deskripsi }}</td>
                                    <td>{{ $detail->barang->satuan->nama_satuan }}</td>
                                    <td>{{ $detail->jumlah }}</td>
                                    <td>{{ number_format($detail->harga, 2) }}</td>
                                    <td>{{ number_format($detail->total, 2) }}</td>
                                    <td>{{ $barangkeluar->permintaan->keterangan }}</td>
                                </tr>
                                @php $counter++ @endphp
                            @endif
                        @endforeach
                    </tbody>
                    @else
                        <p>Data tidak ditemukan.</p>
                    @endif

                               

                </table>
        </div>
    </div>

@endsection
