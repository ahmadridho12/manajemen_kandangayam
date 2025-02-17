@extends('layout.main')

@section('content')
    <x-breadcrumb
        :values="[__('Tamabah Menu'), __('Tambah Pakan Keluar'), __('Tambah Pakan Keluar')]">
    </x-breadcrumb>

    <div class="card mb-4">
        <form action="{{ route('pakan.pakankeluar.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-body row">
                <!-- Existing fields -->
                <input type="hidden" name="type" value="outgoing">

                <!-- New fields -->
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <label for="ayam_id" class="form-label">{{ __('Periode') }}</label>
                    <select name="ayam_id" id="id_ayam" class="form-control">
                        @foreach($ayams as $ayam)
                        <option value="{{ $ayam->id_ayam }}">{{ $ayam->periode }}</option>
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
                    <x-input-form name="tanggal" :label="__('Tanggal_Masuk')" type="date"/>
                </div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <x-input-form name="qty" :label="__('Jumlah')"/>
                </div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <x-input-form name="berat_zak" :label="__('Berat')"/>
                </div>
                
            
               
                
            </div>
            <div class="card-footer pt-0">
                <button class="btn btn-primary" type="submit">{{ __('menu.general.save') }}</button>
            </div>
        </form>
    </div>
@endsection
