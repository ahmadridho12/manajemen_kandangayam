@extends('layout.main')

@section('content')
    <x-breadcrumb
        :values="[__('menu.create.menu'), __('menu.create.permission_letter'), __('menu.general.view')]">
        <a href="{{ route('create.permission.print', $permission->id_permission) }}" class="btn btn-primary">
            Print
        </a>
        
        
    </x-breadcrumb>

    <div class="card mb-4">
        <div class="card-body">
            <x-permission-card :permission="$permission"/>
            <div class="mt-2">
                <div class="divider">
                    <div class="divider-text">{{ __('menu.general.view') }}</div>
                </div>
                <dl class="row mt-3">

                    <dt class="col-sm-3">{{ __('Nama') }}</dt>
                    <dd class="col-sm-9">:{{ $permission->nama }}</dd>
                
                    <dt class="col-sm-3">{{ __('NIP') }}</dt>
                    <dd class="col-sm-9">:{{ $permission->nik }}</dd>
                
                    <dt class="col-sm-3">{{ __('Golongan') }}</dt>
                    <dd class="col-sm-9">:{{ $permission->golongan }}</dd>
                
                    <dt class="col-sm-3">{{ __('Kantor') }}</dt>
                    <dd class="col-sm-9">:{{ $permission->unit_kerja}}</dd>

                    <dt class="col-sm-3">{{ __('Jabatan') }}</dt>
                    <dd class="col-sm-9">:{{ $permission->nama_jabatan}}</dd>
                
                    <dt class="col-sm-3">{{ __('Tanggal Buat') }}</dt>
                    <dd class="col-sm-9">:{{ $permission->tgl_buat }}</dd>
                
                    <dt class="col-sm-3">{{ __('Tanggal Mulai') }}</dt>
                    <dd class="col-sm-9">:{{ $permission->tgl_mulai }}</dd>
                
                    <dt class="col-sm-3">{{ __('Tanggal Selesai') }}</dt>
                    <dd class="col-sm-9">:{{ $permission->tgl_selesai }}</dd>
                
                    <dt class="col-sm-3">{{ __('Perihal') }}</dt>
                    <dd class="col-sm-9">:{{ $permission->perihal }}</dd>
                
                    @if($permission->foto)
                        <dt class="col-sm-3">{{ __('Foto') }}</dt>
                        <dd class="col-sm-9">
                            <a href="{{ asset('storage/' . $permission->foto) }}" target="_blank" class="text-primary">
                                <img src="{{ asset('storage/' . $permission->foto) }}" alt="Foto Permission" style="max-width: 100px; height: auto;">
                            </a>
                        </dd>
                    @endif
                </dl>
            </div>
        </div>
    </div>
@endsection
