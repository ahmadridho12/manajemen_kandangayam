@extends('layout.main')

@section('content')
    <x-breadcrumb
        :values="[__('Panen'), __('Panen Ayam')]">
        <a href="{{ route('sistem.panen.create') }}" class="btn btn-primary">
            {{ __('Tambah Panen') }}
        </a>
                 {{-- <a href="{{ route('create.permission.add') }}" class="btn btn-primary">{{ __('') }}</a>  --}}
    </x-breadcrumb>
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('sistem.panen.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="populasi">{{ __('Filter Periode') }}</label>
                    <select name="id_ayam" id="id_ayam" class="form-control">
                        <option value="">{{ __('Pilih Periode') }}</option>
                        @foreach($ayams as $ayam)
                            <option value="{{ $ayam->id_ayam }}" {{ request('id_ayam') == $ayam->id_ayam ? 'selected' : '' }}>
                                {{ $ayam->periode }}
                            </option>
                        @endforeach
                    </select>
                    
                </div>
               
                <div class="col-md-4">
                    <label for="id_kandang">{{ __('Filter Kandang') }}</label>
                    <select name="id_kandang" id="id_kandang" class="form-control">
                        <option value="">{{ __('Pilih Kandang') }}</option>
                        @foreach($kandangs as $kandang)
                            <option value="{{ $kandang->id_kandang }}" {{ request('id_kandang') == $kandang->id ? 'selected' : '' }}>
                                {{ $kandang->nama_kandang }}
                            </option>
                        @endforeach
                    </select>
                </div>
               
                <div class="col-md-4 align-self-end">
                    <button type="submit" class="btn btn-primary">{{ __('Filter') }}</button>
                    <a href="{{ route('pakan.pakanmasuk.index') }}" class="btn btn-secondary">{{ __('Reset') }}</a>
                </div>
            </form>
        </div>
    </div>
  



    @foreach($data as $panen)
    <x-panen-card
        :panen="$panen"
    />
@endforeach

    {!! $data->appends(['search' => $search])->links() !!}
@endsection
