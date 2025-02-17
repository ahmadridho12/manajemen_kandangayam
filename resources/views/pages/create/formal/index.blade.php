@extends('layout.main')

@section('content')
    <x-breadcrumb
        :values="[__('menu.create.menu'), __('Surat Tugas')]">
        <a href="" class="btn btn-primary">
            {{ __('menu.create.add_letter') }}
        </a>
                 {{-- <a href="{{ route('create.permission.add') }}" class="btn btn-primary">{{ __('') }}</a>  --}}
    </x-breadcrumb>



    @foreach($data as $permission)
    <x-permission-card
        :permission="$permission"
    />
@endforeach

    {!! $data->appends(['search' => $search])->links() !!}
@endsection
