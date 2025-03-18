@extends('layout.main')

@push('script')
    <script>
        $(document).on('click', '.btn-edit', function () {
            const id = $(this).data('id');
            $('#editModal form').attr('action', '{{ route('gaji.operasional.update', '') }}/' + id);
            $('#editModal input:hidden#id').val(id);
            $('#editModal select#kandang_id').val($(this).data('kandang_id'));
            $('#editModal select#id_ayam').val($(this).data('ayam_id'));
            $('#editModal input#nama_potongan').val($(this).data('nama_potongan'));
            $('#editModal input#jumlah').val($(this).data('jumlah'));
            $('#editModal input#tanggal').val($(this).data('tanggal'));
        });
    </script>
@endpush

@section('content')
    <x-breadcrumb
    :values="[__('Gaji'), __('Operasional')]">
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
            <form method="GET" action="{{ route('gaji.operasional.index') }}" class="row g-3">
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
                    <a href="{{ route('gaji.operasional.index') }}" class="btn btn-secondary">{{ __('Reset') }}</a>
                </div>
            </form>
        </div>
    </div>
    <div class="card mb-5">
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                <tr><th>No</th>
                    <th>{{ __('Kandang') }}</th>
                    <th>{{ __('Periode') }}</th>
                    <th>{{ __('Nama Biaya') }}</th>
                    <th>{{ __('Total') }}</th>
                    <th>{{ __('Tanggal') }}</th>
                    <th>{{ __('menu.general.action') }}</th>
                </tr>
                </thead>
                @if($data && $data->count())
                    <tbody>
                    @foreach($data as $op)
                        <tr>
                            <td>{{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}</td>

                            <td>{{ $op->kandang->nama_kandang }}</td>
                            <td>{{ $op->ayam->periode }}</td>
                            <td>{{ $op->nama_potongan }}</td>
                            <td>Rp. {{number_format( $op->jumlah, 0.2 , '.','.' )}}</td>
                            <td>{{ $op->tanggal }}</td>
                            <td>
                                <button class="btn btn-info btn-sm btn-edit"
                                        data-id="{{ $op->id_potongan }}"
                                        data-kandang_id="{{ $op->kandang_id }}"
                                        data-ayam_id="{{ $op->ayam_id }}"
                                        data-nama_potongan="{{ $op->nama_potongan }}"
                                        data-jumlah="{{ $op->jumlah }}"
                                        data-tanggal="{{ $op->tanggal }}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editModal">
                                    {{ __('menu.general.edit') }}
                                </button>
                                <form action="{{ route('gaji.operasional.destroy', $op->id_potongan) }}" class="d-inline" method="post">
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
            <form class="modal-content" method="post" action="{{ route('gaji.operasional.store') }}">
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
                    <x-input-form name="nama_potongan" :label="__('Nama Biaya')" />
                    <x-input-form name="jumlah" :label="__('Total')" type="number"/>
                    <x-input-form name="tanggal" :label="__('Tanggal')" type="date"/>
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
                        <label for="id_ayam">Periode</label>
                        <select name="ayam_id" id="id_ayam" class="form-control">
                            @foreach($ayams as $ayam)
                                <option value="{{ $ayam->id_ayam }}">{{ $ayam->periode }}</option>
                            @endforeach
                        </select>
                    </div>  
                    <x-input-form name="nama_potongan" :label="__('Nama Biaya')" />
                    <x-input-form name="jumlah" :label="__('Total')" type="number"/>
                    <x-input-form name="tanggal" :label="__('Tanggal')" type="date"/>
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