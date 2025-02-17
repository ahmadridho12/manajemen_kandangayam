@extends('layout.main')

{{-- @push('script')
    <script>
        $(document).on('click', '.btn-edit', function () {
            const id = $(this).data('id');
            $('#editModal form').attr('action', '{{ route('lainnya.kandang.update', '') }}/' + id);
            $('#editModal input:hidden#id').val(id);
            $('#editModal input#nama_kandang').val($(this).data('nama_kandang'));
            $('#editModal input#tanggal_mulai').val($(this).data('tanggal_mulai'));
            $('#editModal input#tanggal_selesai').val($(this).data('tanggal_selesai'));
        });
    </script>
@endpush --}}

@section('content')
    <x-breadcrumb
        :values="[__('Populasi')]">
        {{-- <button
            type="button"
            class="btn btn-primary btn-create"
            data-bs-toggle="modal"
            data-bs-target="#createModal">
            {{ __('menu.general.create') }}
        </button> --}}
    </x-breadcrumb>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('inventory.populasi.index') }}" class="row g-3">
                <div class="col-md-3">
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
               
                <div class="col-md-3">
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
                
                <div class="col-md-3 align-self-end">
                    <button type="submit" class="btn btn-primary">{{ __('Filter') }}</button>
                    <a href="{{ route('inventory.populasi.index') }}" class="btn btn-secondary">{{ __('Reset') }}</a>
                </div>
                <div class="col-md-3 align-self-end">
                    {{-- <button type="submit" class="btn btn-primary">{{ __('Filter') }}</button> --}}
                    <a href="{{ route('inventory.populasi.print', ['id_ayam' => request('id_ayam'), 'id_kandang' => request('id_kandang')]) }}" target="_blank" class="btn btn-success">{{ __('Print') }}</a>
                </div>
                
                
            </form>
        </div>
    </div>

    <div class="card mb-5">
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                <tr>
                    <th>No</th>
                    <th>{{ __('Periode') }}</th>
                    <th>{{ __('Tanggal') }}</th>
                    <th>{{ __('Hari') }}</th>
                    <th>{{ __('Populasi') }}</th>
                    <th>{{ __('Jumlah Mati') }}</th>
                    <th>{{ __('Jumlah Panen') }}</th>
                    <th>{{ __('Total') }}</th>
                </tr>
                </thead>
                <tbody>
                @if($data && $data->count())
                    @foreach($data as $p)
                        <tr>
                            <td>{{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}</td>
                            <td>{{ $p->ayam->periode ?? 'Tidak Ada' }}</td>
                            <td>{{ $p->tanggal }}</td>
                            <td>{{ $p->day }}</td>
                            <td>{{ $p->qty_now }} Ekor</td>
                            <td>{{ $p->qty_mati }} Ekor</td>
                            <td>{{ $p->qty_panen }} Ekor</td>
                            <td>{{ $p->total }} Ekor</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="8" class="text-center">
                            {{ __('menu.general.empty') }}
                        </td>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>
    </div>

    {!! $data->appends(['search' => $search])->links() !!} 

    <!-- Create Modal -->
    {{-- <div class="modal fade" id="createModal" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" method="post" action="{{ route('lainnya.kandang.store') }}">
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
                    <x-input-form name="nama_kandang" :label="__('Nama Kandang')"/>
                    <x-input-form name="tanggal_mulai" :label="__('Tanggal Mulai')" type="date"/>
                    <x-input-form name="tanggal_selesai" :label="__('Tanggal Selesai')" type="date"/>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        {{ __('menu.general.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-primary">{{ __('menu.general.save') }}</button>
                </div>
            </form>
        </div>
    </div> --}}

    <!-- Edit Modal -->
    {{-- <div class="modal fade" id="editModal" data-bs-backdrop="static" tabindex="-1">
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
                    <x-input-form name="nama_kandang" :label="__('Nama Kandang')" id="nama_kandang"/>
                    <x-input-form name="tanggal_mulai" :label="__('Tanggal Mulai')" type="date" id="tanggal_mulai"/>
                    <x-input-form name="tanggal_selesai" :label="__('Tanggal Selesai')" type="date" id="tanggal_selesai"/>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        {{ __('menu.general.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-primary">{{ __('menu.general.update') }}</button>
                </div>
            </form>
        </div>
    </div> --}}
@endsection