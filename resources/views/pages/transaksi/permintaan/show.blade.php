@extends('layout.main')

@section('content')
    <x-breadcrumb
        :values="[__('Permintaan'), __('Detail Permintaan'), __('menu.general.view')]">
        <a href="{{ route('transaksi.permintaan.print', $permintaan->id_permintaan) }}" class="btn btn-primary">
            Print
        </a>
    </x-breadcrumb>

        <div class="mt-2">
            <h5 style="font-weight: bolder">{{ $permintaan->no_trans }}</h5>
            <dl class="row mt-3">

                <dt class="col-sm-3">{{ __('Nomor Permintaan') }}</dt>
                <dd class="col-sm-9">{{ $permintaan->no_trans }}</dd>

                <dt class="col-sm-3">{{ __('Bagian yang meminta') }}</dt>
                <dd class="col-sm-9">{{ $permintaan->bagiann->nama_bagian }}</dd>

                <dt class="col-sm-3">{{ __('Kegunaaan') }}</dt>
                <dd class="col-sm-9">{{ $permintaan->tipe->nama_tipe }}</dd>

                <dt class="col-sm-3">{{ __('Keterangan') }}</dt>
                <dd class="col-sm-9">{{ $permintaan->keterangan }}</dd>

                <dt class="col-sm-3">{{ __('Tanggal Permintaan') }}</dt>
                <dd class="col-sm-9">{{ $permintaan->tgl_permintaan }}</dd>

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
                        <th>{{ __('Jenis ') }}</th>
                        <th>{{ __('Catatan ') }}</th>
                   
                    </tr>
                    </thead>
                    @if($permintaan)
                        <tbody>
                            @foreach($permintaan->detailpermintaan as $detail)
                                <tr>
                                    <td>{{ $loop->iteration }}</td> <!-- Menampilkan nomor urut -->
                                    <td>{{ $detail->barang->kode_barang }}</td>
                                    <td>{{ $detail->barang->deskripsi }}</td>
                                    <td>{{ $detail->barang->satuan->nama_satuan }}</td>
                                    <td>{{ $detail->qty }}</td>
                                    <td>{{ $permintaan->tipe->nama_tipe }}</td>
                                    <td>{{ $permintaan->keterangan }}</td>
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
