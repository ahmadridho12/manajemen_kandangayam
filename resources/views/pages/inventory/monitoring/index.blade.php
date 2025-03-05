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
<style>
    .text-green-500 {
        color: #10B981 !important;
    }
    .text-red-500 {
        color: #EF4444 !important;
    }
</style>
@section('content')
<x-breadcrumb :values="[__('Monitoring '), __('Perkembangan Ayam')]">
    <a href="{{ route('inventory.monitoring.create') }}" class="btn btn-primary">
        {{ __('Tambah Ayam') }}
    </a>
</x-breadcrumb>
<div class="card-body">
    <form method="GET" action="{{ route('inventory.monitoring.index') }}" class="row g-3">
        <div class="col-md-4">
            <label for="ayam_id">{{ __('Filter Periode') }}</label>
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
            <a href="{{ route('inventory.monitoring.index') }}" class="btn btn-secondary">{{ __('Reset') }}</a>
        </div>
    </form>
</div>
</div>

    <div class="card mb-5">
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    
                <tr>
                    
                    {{-- <th>{{ __('menu.general.action') }}</th> --}}

                    <th rowspan="2" width="2%" style="font-size: 14px;  text-align: center; vertical-align: middle; background-color: #10b93d; color:white">No</th>
                    <th rowspan="2" width="15%" style="font-size: 14px; text-align: center; vertical-align: middle;  background-color: #10b93d; color:white">{{ __('Periode') }}</th>
                    <th rowspan="2" width="25%" style="font-size: 14px;  text-align: center; vertical-align: middle; background-color: #10b93d; color:white">{{ __('Kandang') }}</th>
                    <th rowspan="2" width="4%" style="font-size: 14px;  text-align: center; vertical-align: middle; background-color: #10b93d; color:white">{{ __('tanggal') }}</th>
                    <th rowspan="2" width="8%" style="font-size: 14px; text-align: center; vertical-align: middle; background-color: #10b93d; color:white">
                        {{ __('Hari') }}
                    </th>                    <th colspan="2" width="30%" style="font-size: 14px; text-align: center;  background-color: #10b93d; color:white" >{{ __('Skat 1') }}</th>
                    <th colspan="2" width="30%" style="font-size: 14px; text-align: center;  background-color: #10b93d; color:white">{{ __('Skat 2') }}</th>
                    <th colspan="2" width="30%" style="font-size: 14px; text-align: center;  background-color: #10b93d; color:white">{{ __('Skat 3') }}</th>
                    <th colspan="2" width="30%" style="font-size: 14px; text-align: center;  background-color: #10b93d; color:white">{{ __('Skat 4') }}</th>
                    
                    <th rowspan="2" width="15%" style="font-size: 14px; text-align: center; vertical-align: middle; background-color: #10b93d; color:white">{{ __('Body Weight') }}</th>
                    <th rowspan="2" width="15%" style="font-size: 14px;  text-align: center; vertical-align: middle; background-color: #10b93d; color:white">{{ __('Daily Again') }}</th>
                </tr>
                <tr>
                    <th width="5%" style="font-size: 14px; background-color: #10B981; color:white">BW</th>
                    <th width="7%" style="font-size: 14px; background-color: #106db9; color:white">DG</th>
                    <th width="5%" style="font-size: 14px; background-color: #10B981; color:white">BW</th>
                    <th width="7%" style="font-size: 14px; background-color: #106db9; color:white">DG</th>
                    <th width="5%" style="font-size: 14px; background-color: #10B981; color:white">BW</th>
                    <th width="7%" style="font-size: 14px; background-color: #106db9; color:white">DG</th>
                    <th width="5%" style="font-size: 14px; background-color: #10B981; color:white">BW</th>
                    <th width="7%" style="font-size: 14px; background-color: #106db9; color:white">DG</th>
              
                    
                </tr>
                </thead>
                @if($data && $data->count())
                    <tbody>
                        
                        @foreach($data as $mt)
                        
                        <td>{{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}</td>
                        <td>{{ $mt->ayam->periode }}</td>
                        <td>{{ $mt->kandang->nama_kandang }}</td>
                        <td>{{ $mt->tanggal }}</td>
                        <td>{{ $mt->age_day }}</td>
                        
                        <td class="{{ $mt->skat_1_bw_status == 'above' ? 'text-green-500' : 'text-red-500' }}">
                            {{ $mt->skat_1_bw }}
                        </td>
                        <td class="{{ $mt->skat_1_dg_status == 'above' ? 'text-green-500' : 'text-red-500' }}">
                            {{ $mt->skat_1_dg }}
                        </td>
                        
                        <td class="{{ $mt->skat_2_bw_status == 'above' ? 'text-green-500' : 'text-red-500' }}">
                            {{ $mt->skat_2_bw }}
                        </td>
                        <td class="{{ $mt->skat_2_dg_status == 'above' ? 'text-green-500' : 'text-red-500' }}">
                            {{ $mt->skat_2_dg }}
                        </td>
                        
                        <td class="{{ $mt->skat_3_bw_status == 'above' ? 'text-green-500' : 'text-red-500' }}">
                            {{ $mt->skat_3_bw }}
                        </td>
                        <td class="{{ $mt->skat_3_dg_status == 'above' ? 'text-green-500' : 'text-red-500' }}">
                            {{ $mt->skat_3_dg }}
                        </td>
                        
                        <td class="{{ $mt->skat_4_bw_status == 'above' ? 'text-green-500' : 'text-red-500' }}">
                            {{ $mt->skat_4_bw }}
                        </td>
                        <td class="{{ $mt->skat_4_dg_status == 'above' ? 'text-green-500' : 'text-red-500' }}">
                            {{ $mt->skat_4_dg }}
                        </td>
                        
                        <td>{{ $mt->body_weight }}</td>
                        <td>{{ $mt->daily_gain }}</td>
                        
                   
                            
                            <td>
                                {{-- <button class="btn btn-info btn-sm btn-edit"
                                        data-id="{{ $p->id_populasi }}"
                                        data-tanggal="{{ $p->tanggal }}"
                                        data-day="{{ $p->day }}"
                                        data-qty_now="{{ $p->qty_now }}"
                                        data-qty_mati="{{ $p->qty_mati }}"
                                        data-qty_panen="{{ $p->qty_panen }}"
                                        data-total="{{ $p->total }}"
                                        >
                                    {{ __('menu.general.edit') }}
                                </button> --}}
                                <form action="" class="d-inline" method="post">
                                    @csrf
                                    @method('DELETE')
                                    {{-- <button class="btn btn-danger btn-sm btn-delete"
                                            type="submit">{{ __('menu.general.delete') }}</button> --}}
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