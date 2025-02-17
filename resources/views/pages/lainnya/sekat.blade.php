@extends('layout.main')

@push('script')
    <script>
        $(document).on('click', '.btn-edit', function () {
            const id = $(this).data('id');
            $('#editModal form').attr('action', '{{ route('lainnya.sekat.index') }}/' + id);
            $('#editModal input:hidden#id').val(id);
            $('#editModal input#nama_sekat').val($(this).data('nama_sekat'));
            $('#editModal select#kandang_id').val($(this).data('kandang_id'));
        });
    </script>
@endpush

@section('content')
    <x-breadcrumb
        :values="[__('Sekat')]">
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
                <tr>
                    <th>No</th>
                    <th>{{ __('Nama Sekat') }}</th>
                    <th>{{ __('Kandang') }}</th>
                    <th>{{ __('Quantity') }}</th>
                    <th>{{ __('menu.general.action') }}</th>
                </tr>
                </thead>
                @if($data && $data->count())
                    <tbody>
                    @foreach($data as $s)
                        <tr>
                            <td>{{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}</td>

                            <td>{{ $s->nama_sekat }}</td>
                            <td>{{ $s->kandang->nama_kandang }}</td>
                            <td>{{ $s->qty }}</td>
                            <td>
                                <button class="btn btn-info btn-sm btn-edit"
                                        data-id="{{ $s->id_sekat }}"
                                        data-nama_sekat="{{ $s->nama_sekat }}"
                                        data-kandang_id="{{ $s->kandang_id }}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editModal">
                                    {{ __('menu.general.edit') }}
                                </button>
                                <form action="{{ route('lainnya.sekat.destroy', $s->id_sekat) }}" class="d-inline" method="post">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm btn-delete"
                                            type="submit">{{ __('menu.general.delete') }}</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                @else
                    <tbody>
                    <tr>
                        <td colspan="3" class="text-center">{{ __('Tidak ada data sekat.') }}</td>
                    </tr>
                    </tbody>
                @endif
            </table>
        </div>
        <div class="card-footer">
            {!! $data->appends(['search' => $search])->links() !!} 
        </div>
    </div>

    <!-- Modal Create -->
    <div class="modal fade" id="createModal" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" method="post" action="{{ route('lainnya.sekat.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('menu.general.create') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <x-input-form name="nama_sekat" :label="__('Nama Sekat')" />
                    <div class="col-sm-12 col-12 col-md-6 col-lg-12">
                        <label for="id_kandang" class="form-label">{{ __('Kandang') }}</label>
                        <select name="kandang_id" id="id_kandang" class="form-control">
                            @foreach($kandangs as $kandang)
                                <option value="{{ $kandang->id_kandang }}">{{ $kandang->nama_kandang }}</option>
                            @endforeach
                        </select>
                    </div>  
                 </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('menu.general.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('menu.general.create') }}</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit -->
    <div class="modal fade" id="editModal" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" method="post" action="">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('menu.general.edit') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="id" value="">
                    <x-input-form name="nama_sekat" :label="__('Nama Sekat')" id="nama_sekat" />
                    <div class="col-sm-12 col-12 col-md-6 col-lg-12">
                        <label for="id_kandang" class="form-label">{{ __('Kandang') }}</label>
                        <select name="kandang_id" id="id_kandang" class="form-control">
                            @foreach($kandangs as $kandang)
                                <option value="{{ $kandang->id_kandang }}">{{ $kandang->nama_kandang }}</option>
                            @endforeach
                        </select>
                    </div>        
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('menu.general.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('menu.general.update') }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection