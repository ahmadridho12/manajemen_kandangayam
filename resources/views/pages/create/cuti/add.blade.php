@extends('layout.main')

@section('content')
    <x-breadcrumb
        :values="[__('menu.create.menu'), __('Tambah Surat Cuti'), __('Tambah Surat Cuti')]">
    </x-breadcrumb>

    <div class="card mb-4">
        <form action="{{ route('create.cuti.store') }}" method="POST" enctype="multipart/form-data">
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
                    <x-input-form name="no_hp" :label="__('Nomor Hp')"/>
                </div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <x-input-form name="tgl_buat" :label="__('Tanggal Dibuat')" type="date"/>
                </div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <x-input-form name="mulai_cuti" :label="__('Tanggal Mulai Cuti')" type="date"/>
                </div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <x-input-form name="sampai_cuti" :label="__('Tanggal Selesai Cuti')" type="date"/>
                </div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <x-input-form name="nama_kuasa" :label="__('Nama Kuasa')"/>
                </div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <x-input-form name="nik_kuasa" :label="__('Nik Kuasa')"/>
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
                    <x-input-form name="nohp_kuasa" :label="__('Nomor Hp')"/>
                </div>
                
            </div>
            <div class="card-footer pt-0">
                <button class="btn btn-primary" type="submit">{{ __('menu.general.save') }}</button>
            </div>
        </form>
    </div>
@endsection
