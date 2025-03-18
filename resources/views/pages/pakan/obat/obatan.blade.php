@extends('layout.main')

@push('script')
    <script>
        $(document).on('click', '.btn-edit', function () {
            const id = $(this).data('id');
            $('#editModal form').attr('action', '{{ route('pakan.obat.update', '') }}/' + id);
            $('#editModal input:hidden#id').val(id);
            $('#editModal select#id_kandang').val($(this).data('kandang_id'));
            $('#editModal select#id_ayam').val($(this).data('ayam_id'));
            $('#editModal input#nama_obat').val($(this).data('nama_obat'));
            $('#editModal input#total').val($(this).data('total'));
          
        });
    </script>
@endpush

@section('content')
    <x-breadcrumb
    :values="[__('Pakan'), __('Obat')]">
        <button
            type="button"
            class="btn btn-primary btn-create"
            data-bs-toggle="modal"
            data-bs-target="#createModal">
            {{ __('menu.general.create') }}
        </button>
    </x-breadcrumb>

    <div class="card mb-5">
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                <tr><th>No</th>
                    <th>{{ __('Kandang') }}</th>
                    <th>{{ __('Periode') }}</th>
                    <th>{{ __('Nama Obat') }}</th>
                    <th>{{ __('Total') }}</th>
                    <th>{{ __('menu.general.action') }}</th>
                </tr>
                </thead>
                @if($data && $data->count())
                    <tbody>
                    @foreach($data as $obt)
                        <tr>
                            <td>{{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}</td>

                            <td>{{ $obt->kandang->nama_kandang }}</td>
                            <td>{{ $obt->ayam->periode }}</td>
                            <td>{{ $obt->nama_obat }}</td>
                            <td>Rp. {{number_format( $obt->total, 0.2 , '.','.' )}}</td>
                            <td>
                                <button class="btn btn-info btn-sm btn-edit"
                                        data-id="{{ $obt->id_obat }}"
                                        data-kandang_id="{{ $obt->kandang_id }}"
                                        data-ayam_id="{{ $obt->ayam_id }}"
                                        data-nama_obat="{{ $obt->nama_obat }}"
                                        data-total="{{ $obt->total }}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editModal">
                                    {{ __('menu.general.edit') }}
                                </button>
                                <form action="{{ route('pakan.obat.destroy', $obt->id_obat) }}" class="d-inline" method="post">
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
            <form class="modal-content" method="post" action="{{ route('pakan.obat.store') }}">
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
                        <label for="id_kandang" class="form-label">{{ __('Nama Kandang') }}</label>
                        <select name="kandang_id" id="id_kandang" class="form-control">
                            @foreach($kandangs as $kandang)
                                <option value="{{ $kandang->id_kandang }}">{{ $kandang->nama_kandang }}</option>
                            @endforeach
                        </select>
                    </div>  
                    <div class="col-sm-12 col-12 col-md-6 col-lg-12">
                        <label for="id_ayam" class="form-label">{{ __('Periode') }}</label>
                        <select name="ayam_id" id="id_ayam" class="form-control">
                            @foreach($ayams as $ayam)
                                <option value="{{ $ayam->id_ayam }}">{{ $ayam->periode }}</option>
                            @endforeach
                        </select>
                    </div>  
                    <x-input-form name="nama_obat" :label="__('Nama Obat')" />
                    <x-input-form name="total" :label="__('Total')" type="number"/>
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
                        <label for="id_kandang" class="form-label">{{ __('Nama Kandang') }}</label>
                        <select name="kandang_id" id="id_kandang" class="form-control">
                            @foreach($kandangs as $kandang)
                                <option value="{{ $kandang->id_kandang }}">{{ $kandang->nama_kandang }}</option>
                            @endforeach
                        </select>
                    </div>  
                    <div class="col-sm-12 col-12 col-md-6 col-lg-12">
                        <label for="id_ayam" class="form-label">{{ __('Periode') }}</label>
                        <select name="ayam_id" id="id_ayam" class="form-control">
                            @foreach($ayams as $ayam)
                                <option value="{{ $ayam->id_ayam }}">{{ $ayam->periode }}</option>
                            @endforeach
                        </select>
                    </div>  
                    <x-input-form name="nama_obat" :label="__('Nama Obat')" />
                    <x-input-form name="total" :label="__('Total')" type="number"/>
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