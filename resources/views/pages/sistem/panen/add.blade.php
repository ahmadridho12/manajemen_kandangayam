@extends('layout.main')

@section('content')
    <x-breadcrumb
        :values="[__('Tamabah Menu'), __('Tambah Panen'), __('Tambah Panen')]">
    </x-breadcrumb>

    <div class="card mb-4">
        <form action="{{ route('sistem.panen.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-body row">
                <!-- Existing fields -->
                <input type="hidden" name="type" value="outgoing">

                <!-- New fields -->
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <label for="ayam_id" class="form-label">{{ __('Periode') }}</label>
                    <select name="ayam_id" id="id_yam" class="form-control">
                        @foreach($ayams as $ayam)
                        <option value="{{ $ayam->id_ayam }}">{{ $ayam->periode }}</option>
                    @endforeach
                    </select>
                </div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <x-input-form name="tanggal_panen" :label="__('Tanggal Panen')" type="date"/>
                </div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <x-input-form name="quantity" :label="__('Jumlah')" type="number"/>
                </div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <label for="berat_total">{{ __('Berat Total') }}</label>
                    <input type="number" name="berat_total" id="berat_total" class="form-control" step="0.01" />
                                        {{-- <x-input-form name="berat_total" :label="__('Total Berat')" /> --}}

                </div>
                
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <x-input-form name="atas_nama" :label="__('DO. Atas Nama')" />
                </div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <x-input-form name="no_panen" :label="__('No Panen')" />
                </div>
               
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <div class="mb-3">
                        <label for="foto" class="form-label">Foto</label>
                        <input type="file" class="form-control @error('foto') is-invalid @enderror" id="foto" name="foto"/>
                        <span class="error invalid-feedback">{{ $errors->first('foto') }}</span>
                    </div>
                </div>
                
            </div>
            <div class="card-footer pt-0">
                <button class="btn btn-primary" type="submit">{{ __('menu.general.save') }}</button>
            </div>
        </form>
    </div>
@endsection
