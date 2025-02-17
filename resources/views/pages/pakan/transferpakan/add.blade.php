@extends('layout.main')

@section('content')
    <x-breadcrumb
        :values="[__('Tamabah Menu'), __('Tambah Pakan Keluar'), __('Tambah Pakan Keluar')]">
    </x-breadcrumb>

    <div class="card mb-4">
        <form action="{{ route('pakan.transferpakan.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-body row">
                <!-- Existing fields -->
                <input type="hidden" name="type" value="outgoing">

                <!-- New fields -->
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <label for="kandang_asal_id" class="form-label">{{ __('Kandang Asal') }}</label>
                    <select name="kandang_asal_id" id="id_kandang" class="form-control">
                        @foreach($kandangs as $kandangasal)
                        <option value="{{ $kandangasal->id_kandang }}">{{ $kandangasal->nama_kandang }}</option>
                    @endforeach
                    </select>
                </div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <label for="kandang_tujuan_id" class="form-label">{{ __('Kandang Tujuan') }}</label>
                    <select name="kandang_tujuan_id" id="id_kandang" class="form-control">
                        @foreach($kandangs as $kandangtujuan)
                        <option value="{{ $kandangtujuan->id_kandang }}">{{ $kandangtujuan->nama_kandang }}</option>
                    @endforeach
                    </select>
                </div>
                
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <label for="ayam_asal_id" class="form-label">{{ __('Periode Asal') }}</label>
                    <select name="ayam_asal_id" id="id_ayam" class="form-control">
                        @foreach($ayams as $ayamasal)
                        <option value="{{ $ayamasal->id_ayam }}">{{ $ayamasal->periode }}</option>
                    @endforeach
                    </select>
                </div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <label for="ayam_tujuan_id" class="form-label">{{ __('Periode Tujuan') }}</label>
                    <select name="ayam_tujuan_id" id="id_ayam" class="form-control">
                        @foreach($ayams as $ayamtujuan)
                        <option value="{{ $ayamtujuan->id_ayam }}">{{ $ayamtujuan->periode }}</option>
                    @endforeach
                    </select>
                </div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <label for="pakan_id" class="form-label">{{ __('Nama Pakan') }}</label>
                    <select name="pakan_id" id="id_pakan" class="form-control">
                        @foreach($pakans as $pakan)
                        <option value="{{ $pakan->id_pakan }}">{{ $pakan->nama_pakan }}</option>
                    @endforeach
                    </select>
                </div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <x-input-form name="tanggal" :label="__('Tanggal Transfer')" type="date"/>
                </div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <x-input-form name="qty" :label="__('Jumlah')"/>
                </div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <x-input-form name="berat_zak" :label="__('Berat')"/>
                </div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <x-input-form name="keterangan" :label="__('Keterangan')"/>
                </div>
                
            
               
                
            </div>
            <div class="card-footer pt-0">
                <button class="btn btn-primary" type="submit">{{ __('menu.general.save') }}</button>
            </div>
        </form>
    </div>
@endsection
