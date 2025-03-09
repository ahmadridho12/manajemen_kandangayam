@extends('layout.main')

@push('script')
    <script>
        $(document).on('click', '.btn-edit', function () {
            const id = $(this).data('id');
            $('#editModal form').attr('action', '{{ route('sistem.keluar.update', '') }}/' + id);
            $('#editModal input:hidden#id').val(id);
            $('#editModal input#ayam_id').val($(this).data('ayam_id'));
            $('#editModal input#tanggal_mati').val($(this).data('tanggal_mati'));
            $('#editModal input#quantity_mati').val($(this).data('quantity_mati'));
            $('#editModal input#alasan').val($(this).data('alasan'));
        });
    </script>
@endpush

@section('content')
    <x-breadcrumb
    :values="[__('Ayam'), __('Ayam Mati')]">
        <button
            type="button"
            class="btn btn-primary btn-create"
            data-bs-toggle="modal"
            data-bs-target="#createModal">
            {{ __('menu.general.create') }}
        </button>
    </x-breadcrumb>
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('sistem.keluar.index') }}" class="row g-3">
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
                    <a href="{{ route('pakan.pakankeluar.index') }}" class="btn btn-secondary">{{ __('Reset') }}</a>
                </div>
            </form>
        </div>
    </div>
    <div class="card mb-5">
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                <tr><th>No</th>
                    <th>{{ __('Periode') }}</th>
                    <th>{{ __('Tanggal_mati') }}</th>
                    <th>{{ __('Jumlah') }}</th>
                    <th>{{ __('Keterangan') }}</th>
                    <th>{{ __('menu.general.action') }}</th>
                </tr>
                </thead>
                @if($data && $data->count())
                    <tbody>
                    @foreach($data as $m)
                        <tr>
                            <td>{{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}</td>

                            <td>{{ $m->ayam->periode }}</td>
                            <td>{{ $m->tanggal_mati }}</td>
                            <td>{{ $m->quantity_mati }}</td>
                            <td>{{ $m->alasan }}</td>
                            <td>
                                <button class="btn btn-info btn-sm btn-edit"
                                        data-id="{{ $m->id_ayam_mati }}"
                                        data-ayam_id="{{ $m->ayam_id }}"
                                        data-tanggal_mati="{{ $m->tanggal_mati }}"
                                        data-quantity_mati="{{ $m->quantity_mati }}"
                                        data-alasan="{{ $m->alasan }}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editModal">
                                    {{ __('menu.general.edit') }}
                                </button>
                                <form action="{{ route('sistem.keluar.destroy', $m->id_ayam_mati) }}" class="d-inline" method="post">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm btn-delete"
                                            type="button">{{ __('menu.general.delete') }}</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                @else
                    <tbody>
                    <tr>
                        <td colspan="4" class="text-center">
                            {{ __('menu.general.empty') }}
                        </td>
                    </tr>
                    </tbody>
                @endif
            </table>
        </div>
    </div>

    {!! $data->appends(['search' => $search])->links() !!} 

    <!-- Create Modal -->
    <div class="modal fade" id="createModal" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" method="post" action="{{ route('sistem.keluar.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="createModalTitle">{{ __('menu.general.create') }}</h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    ></button>
                </div>
                <div class="modal-body">
                    <div class="col-sm-12 col-12 col-md-6 col-lg-12">
                        <label for="id_ayam" class="form-label">{{ __('Periode') }}</label>
                        <select name="ayam_id" id="id_ayam" class="form-control">
                            @foreach($ayams as $ayam)
                                <option value="{{ $ayam->id_ayam }}">{{ $ayam->periode }}</option>
                            @endforeach
                        </select>
                    </div>  
                    <x-input-form name="tanggal_mati" :label="__('Tanggal')" type="date"/>
                    <x-input-form name="quantity_mati" :label="__('Jumlah')" type="number"/>
                    <x-input-form name="alasan" :label="__('Keterangan')"/>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        {{ __('menu.general.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-primary">{{ __('menu.general.save') }}</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" method="post" action="">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalTitle">{{ __('menu.general.edit') }}</h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    ></button>
                </div>
                
                <div class="modal-body">
                    <input type="hidden" name="id" id="id" value="">
                    <div class="col-sm-12 col-12 col-md-6 col-lg-12">
                        <label for="id_ayam" class="form-label">{{ __('Periode') }}</label>
                        <select name="ayam_id" id="id_ayam" class="form-control">
                            @foreach($ayams as $ayam)
                                <option value="{{ $ayam->id_ayam }}">{{ $ayam->periode }}</option>
                            @endforeach
                        </select>
                    </div>  
                    <x-input-form name="tanggal_mati" :label="__('Tanggal')" type="date"/>
                    <x-input-form name="quantity_mati" :label="__('Jumlah')" type="number"/>
                    <x-input-form name="alasan" :label="__('Keterangan')"/>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        {{ __('menu.general.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-primary">{{ __('menu.general.update') }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection