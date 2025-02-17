@extends('layout.main')

@section('content')
    <x-breadcrumb
        :values="[__('menu.edit.menu'), __('menu.edit.permission_letter'), __('menu.edit.edit_letter')]">
    </x-breadcrumb>

    <div class="card mb-4">
        <form action="{{ route('create.permission.update', $permission->id_permission) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT') <!-- Untuk HTTP PUT request -->
            <div class="card-body row">
                <!-- Existing fields -->
                <input type="hidden" name="type" value="outgoing">

                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <x-input-form name="nama" :label="__('Nama')" :value="$permission->nama"/>
                </div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <x-input-form name="nik" :label="__('NIK')" :value="$permission->nik"/>
                </div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <x-input-form name="golongan" :label="__('Golongan')" :value="$permission->golongan"/>
                </div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <label for="unit_kerja" class="form-label">{{ __('Unit Kerja') }}</label>
                    <select name="unit_kerja" id="unit_kerja" class="form-control">
                        @foreach($units as $unit)
                            <option value="{{ $unit->id }}" {{ $permission->unit_kerja == $unit->unit_kerja ? 'selected' : '' }}>
                                {{ $unit->unit_kerja }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <label for="nama_jabatan" class="form-label">{{ __('Jabatan') }}</label>
                    <select name="nama_jabatan" id="nama_jabatan" class="form-control">
                        @foreach($jabatans as $jabatan)
                            <option value="{{ $jabatan->id_jabatan }}" {{ $permission->nama_jabatan == $jabatan->nama_jabatan ? 'selected' : '' }}>
                                {{ $jabatan->nama_jabatan }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <x-input-form name="tgl_buat" :label="__('Tanggal Dibuat')" type="date" :value="$permission->tgl_buat"/>
                </div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <x-input-form name="tgl_mulai" :label="__('Tanggal Mulai')" type="date" :value="$permission->tgl_mulai"/>
                </div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <x-input-form name="tgl_selesai" :label="__('Tanggal Selesai')" type="date" :value="$permission->tgl_selesai"/>
                </div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <x-input-form name="perihal" :label="__('Perihal')" :value="$permission->perihal"/>
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
