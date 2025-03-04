@extends('layout.main')

@push('script')
<script>
    $(document).on('click', '.btn-edit', function () {
        const id = $(this).data('id');
        $('#editModal form').attr('action', '{{ route('pakan.pakankeluar.update', '') }}/' + id);
        $('#editModal input:hidden#id').val(id);
        $('#editModal input#ayam_id').val($(this).data('ayam_id'));
        $('#editModal input#pakan_id').val($(this).data('pakan_id'));
        $('#editModal input#tanggal').val($(this).data('tanggal'));
        $('#editModal input#qty').val($(this).data('qty'));
        $('#editModal input#berat_zak').val($(this).data('berat_zak'));
        $('#editModal input#total_berat').val($(this).data('total_berat'));
    });
</script>
@endpush

@section('content')
<x-breadcrumb
:values="[__('Pakan'), __('Pakan Keluar')]">
<a href="{{ route('pakan.pakankeluar.create') }}" class="btn btn-primary">
    {{ __('Tambah Pakan Keluar') }}
</a>
         {{-- <a href="{{ route('create.permission.add') }}" class="btn btn-primary">{{ __('') }}</a>  --}}
</x-breadcrumb>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('pakan.pakankeluar.index') }}" class="row g-3">
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
                <tr>
                    <th>No</th>
                    <th>{{ __('Periode') }}</th>
                    <th>{{ __('Nama Pakan') }}</th>
                    <th>{{ __('tanggal') }}</th>
                    <th>{{ __('Quantity') }}</th>
                    <th>{{ __('Berat') }}</th>
                    <th>{{ __(' Total Berat') }}</th>
                </tr>
                </thead>
                <tbody>
                @if($data && $data->count())
                    @foreach($data as $pk)
                        <tr>
                            <td>{{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}</td>
                            <td>{{ $pk->ayam->periode ?? 'Tidak Ada' }}</td>
                            <td>{{ $pk->pakan->nama_pakan }}</td>
                            <td>{{ $pk->tanggal }}</td>
                            {{-- <td>{{ $pk->masuk }}</td> --}}
                            <td>{{ $pk->qty }}</td>
                            <td>{{ $pk->berat_zak }}</td>
                            <td>{{ $pk->total_berat }}</td>
                            <td>
                                <button class="btn btn-info btn-sm btn-edit"
                                        data-id="{{ $pk->id }}"
                                        data-ayam_id="{{ $pk->ayam_id }}"
                                        data-pakan_id="{{ $pk->pakan_id }}"
                                        data-tanggal="{{ $pk->tanggal }}"
                                        data-qty="{{ $pk->qty }}"
                                        data-berat_zak="{{ $pk->berat_zak }}"
                                        data-total_berat="{{ $pk->total_berat }}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editModal">
                                    {{ __('menu.general.edit') }}
                                </button>
                                <form action="{{ route('pakan.pakankeluar.destroy', $pk->id) }}" method="post" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm btn-delete" type="button">Hapus</button>
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
                        <label for="id_ayam">Periode</label>
                        <select name="ayam_id" id="id_ayam" class="form-control">
                            @foreach($ayams as $ayam)
                                <option value="{{ $ayam->id_ayam }}">{{ $ayam->periode }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="id_pakan">Nama Pakan</label>
                        <select name="pakan_id" id="id_pakan" class="form-control">
                            @foreach($pakans as $pakan)
                                <option value="{{ $pakan->id_pakan }}">{{ $pakan->nama_pakan }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    {{-- <x-input-form name="nama_kandang" :label="__('Nama Kandang')" id="nama_kandang"/> --}}
                    <x-input-form name="tanggal" :label="__('Tanggal ')" type="date" id="tanggal"/>
                    <x-input-form name="qty" :label="__('Jumlah ')" type="number" id="qty"/>
                    <x-input-form name="berat_zak" :label="__('Berat Per Zak ')" type="number" id="berat_zak"/>
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
  