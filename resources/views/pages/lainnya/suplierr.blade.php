@extends('layout.main')

@push('script')
    <script>
        $(document).on('click', '.btn-edit', function () {
            const id = $(this).data('id');
            $('#editModal form').attr('action', '{{ route('lainnya.suplierr.update', '') }}/' + id);
            $('#editModal input:hidden#id').val(id);
            $('#editModal input#nama_suplier').val($(this).data('nama_suplier'));
            $('#editModal input#alamat').val($(this).data('alamat'));
            $('#editModal input#npwp').val($(this).data('npwp'));
            $('#editModal input#note').val($(this).data('note'));

        });
    </script>
@endpush

@section('content')
    <x-breadcrumb
        :values="[__('Suplier')]">
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
                    <th>No</th> <!-- Kolom Nomor -->
                    <th>{{ __('Nama Suplier') }}</th>
                    <th>{{ __('Alamat') }}</th>
                    <th>{{ __('NPWP') }}</th>
                    <th>{{ __('Keterangan') }}</th>
                    <th>{{ __('Aksi') }}</th>
                  
                </tr>
                </thead>
                @if($data && $data->count())
                    <tbody>
                    @foreach($data as $suplierr)
                        <tr>
                            <td>{{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}</td>

                            <td>{{ $suplierr->nama_suplier }}</td>
                            <td>{{ $suplierr->alamat }}</td>
                            <td>{{ $suplierr->npwp }}</td>
                            <td>{{ $suplierr->note }}</td>
                            {{-- <td><span
                                    class="badge bg-label-primary me-1">{{  __('model.user.' . ($user->is_active ? 'active' : 'nonactive')) }}</span>
                            </td> --}}
                             <td>
                                <button class="btn btn-info btn-sm btn-edit"
                                        data-id="{{ $suplierr->id_suplier }}"
                                        data-nama_suplier="{{ $suplierr->nama_suplier }}"
                                        data-alamat="{{ $suplierr->alamat }}"
                                        data-npwp="{{ $suplierr->npwp }}"
                                        data-note="{{ $suplierr->note }}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editModal">
                                    {{ __('menu.general.edit') }}
                                </button>
                                <form action="{{ route('lainnya.suplierr.destroy', $suplierr->id_suplier) }}" class="d-inline" method="post">
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
                {{-- <tfoot class="table-border-bottom-0">
                <tr>
                    <th>{{ __('model.user.name') }}</th>
                    <th>{{ __('model.user.email') }}</th>
                    <th>{{ __('model.user.phone') }}</th>
                    <th>{{ __('model.user.is_active') }}</th>
                    <th>{{ __('menu.general.action') }}</th>
                </tr>
                </tfoot> --}}
            </table>
        </div>
    </div>

     {!! $data->appends(['search' => $search])->links() !!} 

    <!-- Create Modal -->
    <div class="modal fade" id="createModal" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" method="post" action="{{ route('lainnya.suplierr.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="createModalTitle">{{ __('Tambah Suplier') }}</h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    ></button>
                </div>
                <div class="modal-body">
                    <x-input-form name="nama_suplier" :label="__('Nama Suplier')"/>
                    <x-input-form name="alamat" :label="__('Alamat Suplier')"/>
                    <x-input-form name="npwp" :label="__('Npwp')"/>
                    <x-input-form name="note" :label="__('Keterangan')"/>
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
                    <x-input-form name="nama_suplier" :label="__('Nama Suplier')"/>
                    <x-input-form name="alamat" :label="__('Alamat Suplier')"/>
                    <x-input-form name="npwp" :label="__('Npwp')"/>
                    <x-input-form name="note" :label="__('Keterangan')"/>
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
