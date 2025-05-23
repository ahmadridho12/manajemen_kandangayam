@extends('layout.main')

@push('script')
    <script>
        $(document).on('click', '.btn-edit', function () {
            const id = $(this).data('id');
$('#editModal form').attr('action', '/lainnya/beratstandard/' + id);
            $('#editModal input:hidden#id').val(id);
            $('#editModal input#hari_ke').val($(this).data('hari_ke'));
            $('#editModal input#bw').val($(this).data('bw'));
            $('#editModal input#dg').val($(this).data('dg'));
            // $('#editModal input#tanggal_mulai').val($(this).data('tanggal_mulai'));
            // $('#editModal input#tanggal_selesai').val($(this).data('tanggal_selesai'));
        });
    </script>
@endpush

@section('content')
    <x-breadcrumb
        :values="[__('Standard Berat Ayam')]">
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
                    <th>{{ __('Hari') }}</th>
                    <th>{{ __('Body Weight') }}</th>
                    <th>{{ __('Daily Again') }}</th>
                    {{-- <th>{{ __('Tanggal Mulai') }}</th>
                    <th>{{ __('Tanggal Selesai') }}</th> --}}
                    <th>{{ __('menu.general.action') }}</th>
                </tr>
                </thead>
                @if($data && $data->count())
                    <tbody>
                    @foreach($data as $bs)
                        <tr>
                            <td>{{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}</td>

                            <td>{{ $bs->hari_ke }}</td>
                            <td>{{ $bs->bw }}</td>
                            <td>{{ $bs->dg }}</td>
                            {{-- <td>{{ $k->tanggal_mulai }}</td>
                            <td>{{ $k->tanggal_selesai }}</td> --}}
                            <td>
                                <button class="btn btn-info btn-sm btn-edit"
                                        data-id="{{ $bs->id }}"
                                        data-hari_ke="{{ $bs->hari_ke }}"
                                        data-bw="{{ $bs->bw }}"
                                        data-dg="{{ $bs->dg }}"
                                        {{-- data-tanggal_mulai="{{ $k->tanggal_mulai }}"
                                        data-tanggal_selesai="{{ $k->tanggal_selesai }}" --}}
                                        data-bs-toggle="modal"
                                        data-bs-target="#editModal">
                                    {{ __('menu.general.edit') }}
                                </button>
                                <form action="{{ route('lainnya.beratstandard.destroy', $bs->id) }}" class="d-inline" method="post">
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
            <form class="modal-content" method="post" action="{{ route('lainnya.beratstandard.store') }}">
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
                    <x-input-form name="hari_ke" :label="__('Hari')" type="number"/>
                    <x-input-form name="bw" :label="__('Body Weight')" type="decimal"/>
                    <x-input-form name="dg" :label="__('Daily Again')" type="decimal"/>
                    {{-- <x-input-form name="tanggal_mulai" :label="__('Tanggal Mulai')" type="date"/>
                    <x-input-form name="tanggal_selesai" :label="__('Tanggal Selesai')" type="date"/> --}}
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
                    <x-input-form name="hari_ke" :label="__('Hari')" type="number"/>
                    <x-input-form name="bw" :label="__('Body Weight')" type="decimal"/>
                    <x-input-form name="dg" :label="__('Daily Again')" type="decimal"/>
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