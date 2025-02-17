@extends('layout.main')

@section('content')
    <x-breadcrumb
        :values="[__('menu.create.menu'), __('Surat Cuti')]">
        <a href="" class="btn btn-primary">
            {{ __('menu.create.add_letter') }}
        </a>
                 {{-- <a href="{{ route('create.cuti.add') }}" class="btn btn-primary">{{ __('') }}</a>   --}}
    </x-breadcrumb>



     @foreach($data as $cuti)
    <x-cuti-card
        :cuti="$cuti"
    />
    @endforeach 

    {{-- {!! $data->appends(['search' => $search])->links() !!} --}}
@endsection
