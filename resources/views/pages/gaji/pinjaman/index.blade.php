@extends('layout.main')

@push('script')
    <script>
        $(document).on('click', '.btn-edit', function () {
            const id = $(this).data('id');
            $('#editModal form').attr('action', '{{ route('gaji.pinjaman.update', '') }}/' + id);
            $('#editModal input:hidden#id').val(id);
            $('#editModal input#abk_id').val($(this).data('abk_id'));
            $('#editModal input#kandang_id').val($(this).data('kandang_id'));
            $('#editModal input#ayam_id').val($(this).data('ayam_id'));
            $('#editModal input#jumlah_pinjaman').val($(this).data('jumlah_pinjaman'));
            $('#editModal input#tanggal_pinjam').val($(this).data('tanggal_pinjam'));
        });
    </script>
@endpush

@section('content')
    <x-breadcrumb
    :values="[__('Gaji'), __('Pinjaman Petugas')]">
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
                    <th>{{ __('Nama Petugas') }}</th>
                    <th>{{ __('Kandang') }}</th>
                    <th>{{ __('Periode') }}</th>
                    <th>{{ __('Total') }}</th>
                    <th>{{ __('Tanggal') }}</th>
                    <th>{{ __('menu.general.action') }}</th>
                </tr>
                </thead>
                @if($data && $data->count())
                    <tbody>
                    @foreach($data as $pj)
                        <tr>
                            <td>{{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}</td>

                            <td>{{ $pj->abk->nama }}</td>
                            <td>{{ $pj->kandang->nama_kandang }}</td>
                            <td>{{ $pj->ayam->periode }}</td>
                            <td>Rp. {{number_format( $pj->jumlah_pinjaman, 0.2 , '.','.' )}}</td>
                            <td>{{ $pj->tanggal_pinjam }}</td>
                            <td>
                                <button class="btn btn-info btn-sm btn-edit"
                                        data-id="{{ $pj->id_pinjaman }}"
                                        data-abk_id="{{ $pj->abk }}"
                                        data-kandang_id="{{ $pj->kandang_id }}"
                                        data-ayam_id="{{ $pj->ayam_id }}"
                                        data-jumlah_pinjaman="{{ $pj->jumlah_pinjaman }}"
                                        data-tanggal_pinjam="{{ $pj->tanggal_pinjam }}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editModal">
                                    {{ __('menu.general.edit') }}
                                </button>
                                <form action="{{ route('gaji.pinjaman.destroy', $pj->id_pinjaman) }}" class="d-inline" method="post">
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
            <form class="modal-content" method="post" action="{{ route('gaji.pinjaman.store') }}">
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
                        <label for="id_abk" class="form-label">{{ __('Nama Petugas') }}</label>
                        <select name="abk_id" id="id_abk" class="form-control">
                            @foreach($abks as $abk)
                                <option value="{{ $abk->id_abk }}">{{ $abk->nama }}</option>
                            @endforeach
                        </select>
                    </div>  
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
                    <x-input-form name="jumlah_pinjaman" :label="__('Total Pinjaman')" type="number"/>
                    <x-input-form name="tanggal_pinjam" :label="__('Tanggal Pinjaman')" type="date"/>
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
                        <label for="id_abk" class="form-label">{{ __('Nama Petugas') }}</label>
                        <select name="abk_id" id="id_abk" class="form-control">
                            @foreach($abks as $abk)
                                <option value="{{ $abk->id_abk }}">{{ $abk->nama }}</option>
                            @endforeach
                        </select>
                    </div>
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
                    <x-input-form name="jumlah_pinjaman" :label="__('Total Pinjaman')" type="number"/>
                    <x-input-form name="tanggal_pinjam" :label="__('Tanggal Pinjaman')" type="date"/>
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