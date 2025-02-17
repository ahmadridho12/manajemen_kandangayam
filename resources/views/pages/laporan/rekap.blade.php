@extends('layout.main')

@section('content')
    <x-breadcrumb :values="[__('Laporan'), __('Laporan Stok Barang')]"></x-breadcrumb>

    <div class="card mb-5">
        <div class="card-header">
            <form action="{{ url()->current() }}">
                <input type="hidden" name="search" value="{{ $search ?? '' }}">
                <div class="row">
                    <div class="col">
                        <x-input-form name="since" :label="__('Tanggal Mulai')" type="date"
                                      :value="$since ? date('Y-m-d', strtotime($since)) : ''"/>
                    </div>
                    <div class="col">
                        <x-input-form name="until" :label="__('Tanggal Akhir')" type="date"
                                      :value="$until ? date('Y-m-d', strtotime($until)) : ''"/>
                    </div>
                    <div class="col">
                        <label for="id_jenis">{{ __('Kelompok Barang') }}</label>
                        <select name="id_jenis" id="id_jenis" class="form-control">
                            <option value="">{{ __('Pilih Kelompok Barang') }}</option>
                            @foreach($jenisOptions as $value => $label)
                                <option value="{{ $value }}" {{ (isset($id_jenis) && $id_jenis == $value) ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col">
                        <button class="btn btn-primary" type="submit">{{ __('menu.general.filter') }}</button>
                        <a href="{{ route('laporan.print', ['since' => $since, 'until' => $until, 'id_jenis' => $id_jenis]) }}" target="_blank" class="btn btn-primary">
                            {{ __('menu.general.print') }}
                        </a>                    
                    </div>
                </div>
            </form>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                <tr>
                    <th>No</th>
                    <th>{{ __('Kode Barang') }}</th>
                    <th>{{ __('Nama Barang') }}</th>
                    <th>{{ __('Satuan') }}</th>
                    <th>{{ __('Harga') }}</th>
                    <th>{{ __('Qty Awal') }}</th>
                    <th>{{ __('Saldo Awal') }}</th>
                    <th>{{ __('Qty Diterima') }}</th>
                    <th>{{ __('Total Diterima') }}</th>
                    <th>{{ __('Qty Keluar') }}</th>
                    <th>{{ __('Total Keluar') }}</th>
                    <th>{{ __('Qty Akhir') }}</th>
                    <th>{{ __('Saldo Akhir') }}</th>
                </tr>
                </thead>
                <tbody>
                @if($data && $data->count() > 0)
                    @foreach($data as $index => $detailstok)
                        <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $detailstok->kode_barang ?? '-' }}</td>
                        <td>{{ $detailstok->deskripsi ?? '-' }}</td>
                        <td>{{ $detailstok->nama_satuan ?? '-' }}</td>
                        <td>Rp{{ number_format($detailstok->harga ?? 0, 2, ',', '.') }}</td>
                        
                        {{-- Quantity Awal --}}
                        <td>{{ number_format($detailstok->qty_awal_periode ?? 0, 0, ',', '.') }}</td>
                        
                        {{-- Saldo Awal --}}
                        <td>Rp{{ number_format(($detailstok->qty_awal_periode * $detailstok->harga) ?? 0, 2, ',', '.') }}</td>
                        
                        {{-- Quantity Diterima --}}
                        <td>{{ $detailstok->qty_masuk ??  '.' }}</td>
                        
                        {{-- Nilai Masuk --}}
                        <td>Rp{{ number_format(($detailstok->qty_masuk * $detailstok->harga) ?? 0, 2, ',', '.') }}</td>
                        
                        {{-- Quantity Keluar --}}
                        <td>{{ number_format($detailstok->qty_keluar ?? 0, 0, ',', '.') }}</td>
                        
                        {{-- Nilai Keluar --}}
                        <td>Rp{{ number_format(($detailstok->nilai_keluar) ?? 0, 2, ',', '.') }}</td>
                        
                        {{-- Quantity Akhir --}}
                        <td>{{ number_format($detailstok->qty_akhir ?? 0, 0, ',', '.') }}</td>
                        
                        {{-- Saldo Akhir --}}
                        <td>Rp{{ number_format(($detailstok->saldo_akhir) ?? 0, 2, ',', '.') }}</td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="13" class="text-center">Data tidak ditemukan</td>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>
    </div>

    {!! $data->appends(['search' => $search, 'since' => $since, 'until' => $until, 'id_jenis' => $id_jenis])->links() !!}
@endsection