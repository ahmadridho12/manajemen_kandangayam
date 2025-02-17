@extends('layout.main')

@section('content')
    <x-breadcrumb
        :values="[__('menu.create.menu'), __('menu.create.permission_letter'), __('menu.create.add_letter')]">
    </x-breadcrumb>

    <div class="card mb-4">
        <form action="{{ route('create.permission.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-body row">
                <!-- Existing fields -->
                <input type="hidden" name="type" value="outgoing">

                <!-- New fields -->
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <x-input-form name="nama" :label="__('Nama')"/>
                </div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <x-input-form name="nik" :label="__('NIK')"/>
                </div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <x-input-form name="golongan" :label="__('Golongan')"/>
                </div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <label for="unit_kerja" class="form-label">{{ __('Unit Kerja') }}</label>
                    <select name="unit_kerja" id="unit_kerja" class="form-control">
                        @foreach($units as $unit) <!-- Menggunakan $units dan $unit sesuai dengan yang dikirimkan -->
                            <option value="{{ $unit->unit_kerja }}">{{ $unit->unit_kerja }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <label for="nama_jabatan" class="form-label">{{ __('Jabatan') }}</label>
                    <select name="nama_jabatan" id="nama_jabatan" class="form-control">
                        @foreach($jabatans as $jabatan) <!-- Menggunakan $units dan $unit sesuai dengan yang dikirimkan -->
                            <option value="{{ $jabatan->nama_jabatan }}">{{ $jabatan->nama_jabatan }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <x-input-form name="tgl_buat" :label="__('Tanggal Dibuat')" type="date"/>
                </div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <x-input-form name="tgl_mulai" :label="__('Tanggal Mulai')" type="date"/>
                </div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <x-input-form name="tgl_selesai" :label="__('Tanggal Selesai')" type="date"/>
                </div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <x-input-form name="perihal" :label="__('Perihal')"/>
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
