@extends('layout.main')

@section('content')
    <x-breadcrumb
        :values="[__('Panen'), __('Panen'), __('detail Panen')]">
        {{-- <a href="{{ route('create.permission.print', $permission->id_permission) }}" class="btn btn-primary">
            Print
        </a> --}}
        
        
    </x-breadcrumb>

    <div class="card mb-4">
        <div class="card-body">
            <x-panen-card :panen="$panen"/>
            <div class="mt-2">
                <div class="divider">
                    <div class="divider-text">{{ __('menu.general.view') }}</div>
                </div>
                <dl class="row mt-3">

                    <dt class="col-sm-3">{{ __('Periode') }}</dt>
                    <dd class="col-sm-9">:{{ $panen->ayam->periode }}</dd>
                
                    <dt class="col-sm-3">{{ __('Tanggal Panen') }}</dt>
                    <dd class="col-sm-9">:{{ $panen->tanggal_panen }}</dd>
                
                    <dt class="col-sm-3">{{ __('Jumlah') }}</dt>
                    <dd class="col-sm-9">:{{ $panen->quantity }}</dd>
                
                    <dt class="col-sm-3">{{ __('Total Berat') }}</dt>
                    <dd class="col-sm-9">:{{ $panen->berat_total}}</dd>

                    <dt class="col-sm-3">{{ __('Do Atas Nama') }}</dt>
                    <dd class="col-sm-9">:{{ $panen->atas_nama}}</dd>
                
                    <dt class="col-sm-3">{{ __('No Panen') }}</dt>
                    <dd class="col-sm-9">:{{ $panen->no_panen }}</dd>
                
                   
                
                    @if($panen->foto)
                        <dt class="col-sm-3">{{ __('Foto') }}</dt>
                        <dd class="col-sm-9">
                            <a href="{{ asset('storage/' . $panen->foto) }}" target="_blank" class="text-primary">
                                <img src="{{ asset('storage/' . $panen->foto) }}" alt="Foto Permission" style="max-width: 100px; height: auto;">
                            </a>
                        </dd>
                    @endif
                </dl>
            </div>
        </div>
    </div>
@endsection
