@extends('layout.main')

@push('script')
    <script>
        $(document).on('click', '.btn-edit', function () {
            const id = $(this).data('id');
            const status = $(this).data('status'); // Ambil status dari data atribut

            $('#editModal form').attr('action', '{{ route('lainnya.doc.update', '') }}/' + id);
            $('#editModal input:hidden#id').val(id);
            $('#editModal input#tahun').val($(this).data('tahun'));
            $('#editModal input#harga').val($(this).data('harga'));
            // $('#editModal select#status').val(status);
        });
        
    </script>
@endpush

@section('content')
    <x-breadcrumb
        :values="[__('DOC')]">
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
                    <th>{{ __('Tahun ') }}</th>
                    <th>{{ __('Harga') }}</th>
                    {{-- <th>{{ __('Status') }}</th> <!-- Tambahkan kolom status --> --}}

                    <th>{{ __('menu.general.action') }}</th>
                </tr>
                </thead>
                @if($data && $data->count())
                    <tbody>
                    @foreach($data as $doc)
                        <tr>
                            <td>{{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}</td>

                            <td>{{ $doc->tahun }}</td>
                            <td>{{ $doc->harga }}</td>
                            
                            
                            <td>
                                <button class="btn btn-info btn-sm btn-edit"
                                        data-id="{{ $doc->id_doc }}"
                                        data-tahun="{{ $doc->tahun }}"
                                        data-harga="{{ $doc->harga }}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editModal">
                                    {{ __('menu.general.edit') }}
                                </button>
                                <form action="{{ route('lainnya.doc.destroy', $doc->id_doc) }}" class="d-inline" method="post">
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
            <form class="modal-content" method="post" action="{{ route('lainnya.doc.store') }}">
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
                    <div class="form-group">
                        <label>Tahun</label>
                        <select name="tahun" class="form-control">
                            @for ($i = date('Y'); $i >= 1900; $i--)
                            <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <x-input-form name="harga" :label="__('Harga Doc')" type="number"/>
                    
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
                    <div class="form-group">
                        <label>Tahun</label>
                        <select name="tahun" class="form-control">
                            @for ($i = date('Y'); $i >= 1900; $i--)
                            <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div>                    
                    <x-input-form name="harga" :label="__('Harga DOC')"  id="harga" type="number"/>
                    
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