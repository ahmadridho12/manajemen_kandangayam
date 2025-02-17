@extends('layout.main')

@section('content')
    <x-breadcrumb
        :values="[__('menu.edit.menu'), __('menu.edit.permission_letter'), __('menu.edit.edit_letter')]">
    </x-breadcrumb>

    <div class="card mb-4">
        <form action="{{ route('sistem.panen.update', $panen->id_panen) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT') <!-- Untuk HTTP PUT request -->
            <div class="card-body row">
                <!-- Existing fields -->
                <input type="hidden" name="type" value="outgoing">

                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <label for="ayam_id" class="form-label">{{ __('Periode') }}</label>
                    <select name="ayam_id" id="ayam_id" class="form-control">
                        @foreach($ayams as $ayam)
                            <option value="{{ $ayam->id_ayam }}" {{ $panen->ayam_id == $ayam->id_ayam ? 'selected' : '' }}>
                                {{ $ayam->periode }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <x-input-form name="tanggal_panen" :label="__('Tanggal Panen')" type="date" :value="$panen->tanggal_panen" />
                    </div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <x-input-form name="quantity" :label="__('Jumlah')" :value="$panen->quantity" />
                    </div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <x-input-form name="berat_total" :label="__('Berat')" :value="$panen->berat_total" />
                </div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <x-input-form name="atas_nama" :label="__('DO. Atas Nama')" :value="$panen->atas_nama" />
                </div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <x-input-form name="no_panen" :label="__('No Panen')" :value="$panen->no_panen" />
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
                <button class="btn btn-primary" type="submit">{{ __('menu.general.update') }}</button>
            </div>
        </form>
    </div>
@endsection
