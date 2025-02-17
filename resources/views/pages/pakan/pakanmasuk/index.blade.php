@extends('layout.main')

@push('script')
    <script>
        $(document).on('click', '.btn-edit', function () {
            const id = $(this).data('id');
            $('#editModal form').attr('action', '{{ route('pakan.pakanmasuk.update', '') }}/' + id);
            $('#editModal input:hidden#id').val(id);
            $('#editModal input#ayam_id').val($(this).data('ayam_id'));
            $('#editModal input#pakan_id').val($(this).data('pakan_id'));
            $('#editModal input#tanggal').val($(this).data('tanggal'));
            $('#editModal input#masuk').val($(this).data('masuk'));
            $('#editModal input#berat_zak').val($(this).data('berat_zak'));
            $('#editModal input#total_berat').val($(this).data('total_berat'));
        });
    </script>
@endpush

@section('content')
<x-breadcrumb
:values="[__('Pakan'), __('Pakan Masuk')]">
<a href="{{ route('pakan.pakanmasuk.create') }}" class="btn btn-primary">
    {{ __('Tambah Pakan Masuk') }}
</a>
         {{-- <a href="{{ route('create.permission.add') }}" class="btn btn-primary">{{ __('') }}</a>  --}}
</x-breadcrumb>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('pakan.pakanmasuk.index') }}" class="row g-3">
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
                    <a href="{{ route('pakan.pakanmasuk.index') }}" class="btn btn-secondary">{{ __('Reset') }}</a>
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
                    <th>{{ __('Nama Pakan') }}</th>
                    <th>{{ __('tanggal') }}</th>
                    <th>{{ __('Quantity') }}</th>
                    <th>{{ __('Berat') }}</th>
                    <th>{{ __(' total') }}</th>
                    <th>{{ __('Aksi') }}</th>
                </tr>
                </thead>
                <tbody>
                @if($data && $data->count())
                    @foreach($data as $pm)
                        <tr>
                            <td>{{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}</td>
                            <td>{{ $pm->ayam->periode ?? 'Tidak Ada' }}</td>
                            <td>{{ $pm->pakan->nama_pakan }}</td>
                            <td>{{ $pm->tanggal }}</td>
                            <td>{{ $pm->masuk }}</td>
                            <td>{{ $pm->berat_zak }}</td>
                            <td>{{ $pm->total_berat }}</td>
                            <td>
                                <button class="btn btn-info btn-sm btn-edit"
                                        data-id="{{ $pm->id }}"
                                        data-ayam_id="{{ $pm->ayam_id }}"
                                        data-pakan_id="{{ $pm->pakan_id }}"
                                        data-tanggal="{{ $pm->tanggal }}"
                                        data-masuk="{{ $pm->masuk }}"
                                        data-berat_zak="{{ $pm->berat_zak }}"
                                        data-total_berat="{{ $pm->total_berat }}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editModal">
                                    {{ __('menu.general.edit') }}
                                </button>
                                <form action="{{ route('pakan.pakanmasuk.destroy', $pm->id) }}" method="post" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm btn-delete" type="submit">Hapus</button>
                                </form>
                                
                                
                            </td>
                            
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
                    <select name="ayam_id" id="id_ayam" class="form-control">
                        @foreach($ayams as $ayam)
                            <option value="{{ $ayam->id_ayam }}">{{ $ayam->periode }}</option>
                        @endforeach
                    </select>
                    <select name="pakan_id" id="id_pakan" class="form-control">
                        @foreach($pakans as $pakan)
                            <option value="{{ $pakan->id_pakan }}">{{ $pakan->nama_pakan }}</option>
                        @endforeach
                    </select>
                    {{-- <x-input-form name="nama_kandang" :label="__('Nama Kandang')" id="nama_kandang"/> --}}
                    <x-input-form name="tanggal" :label="__('Tanggal ')" type="date" id="tanggal"/>
                    <x-input-form name="masuk" :label="__('Jumlah ')" type="number" id="masuk"/>
                    <x-input-form name="berat_zak" :label="__('Berat Per Zak ')" type="number" id="berat_zak"/>
                    {{-- <x-input-form name="tanggal_selesai" :label="__('Tanggal Selesai')" type="date" id="tanggal_selesai"/> --}}
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