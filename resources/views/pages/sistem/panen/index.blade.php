@extends('layout.main')

@section('content')
    <x-breadcrumb
        :values="[__('Panen'), __('Panen Ayam')]">
        <a href="{{ route('sistem.panen.create') }}" class="btn btn-primary">
            {{ __('Tambah Panen') }}
        </a>
                 {{-- <a href="{{ route('create.permission.add') }}" class="btn btn-primary">{{ __('') }}</a>  --}}
    </x-breadcrumb>



    @foreach($data as $panen)
    <x-panen-card
        :panen="$panen"
    />
@endforeach

    {!! $data->appends(['search' => $search])->links() !!}
@endsection
