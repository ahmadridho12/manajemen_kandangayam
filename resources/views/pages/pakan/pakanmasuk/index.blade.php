@extends('layout.main')

@push('script')
    <script>
        $(document).on('click', '.btn-edit', function () {
            const id = $(this).data('id');
            $('#editModal form').attr('action', '{{ route('pakan.pakanmasuk.update', '') }}/' + id);
            $('#editModal input:hidden#id').val(id);
            $('#editModal select#id_ayam').val($(this).data('ayam_id'));
            $('#editModal select#id_pakan').val($(this).data('pakan_id'));
            $('#editModal input#tanggal').val($(this).data('tanggal'));
            $('#editModal input#masuk').val($(this).data('masuk'));
            $('#editModal input#berat_zak').val($(this).data('berat_zak'));
            $('#editModal input#total_berat').val($(this).data('total_berat'));
            $('#editModal input#total_harga_pakan').val($(this).data('total_harga_pakan'));
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
                    <th>{{ __(' Total') }}</th>
                    <th>{{ __(' Total Harga') }}</th>
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
                            <td>{{number_format( $pm->total_harga_pakan, 0 , '.','.' )}} 
                            <td>
                                <button class="btn btn-info btn-sm btn-edit"
                                        data-id="{{ $pm->id }}"
                                        data-ayam_id="{{ $pm->ayam_id }}"
                                        data-pakan_id="{{ $pm->pakan_id }}"
                                        data-tanggal="{{ $pm->tanggal }}"
                                        data-masuk="{{ $pm->masuk }}"
                                        data-berat_zak="{{ $pm->berat_zak }}"
                                        data-total_berat="{{ $pm->total_berat }}"
                                        data-total_berat="{{ $pm->total_berat }}"
                                        data-total_harga_pakan="{{ $pm->total_harga_pakan }}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editModal">
                                    {{ __('menu.general.edit') }}
                                </button>
                                <form action="{{ route('pakan.pakanmasuk.destroy', $pm->id) }}" method="post" class="d-inline">
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
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body">
                    <input type="hidden" name="id" id="id" value="">
                    
                    <!-- Periode -->
                    <div class="mb-3">
                        <label for="id_ayam" class="form-label">{{ __('Periode') }}</label>
                        <select name="ayam_id" id="id_ayam" class="form-control">
                            @foreach($ayams as $ayam)
                                <option value="{{ $ayam->id_ayam }}">{{ $ayam->periode }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Nama Pakan -->
                    <div class="mb-3">
                        <label for="id_pakan" class="form-label">{{ __('Nama Pakan') }}</label>
                        <select name="pakan_id" id="id_pakan" class="form-control">
                            @foreach($pakans as $pakan)
                                <option value="{{ $pakan->id_pakan }}">{{ $pakan->nama_pakan }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Tanggal -->
                    <div class="mb-3">
                        <x-input-form name="tanggal" :label="__('Tanggal')" type="date" id="tanggal"/>
                    </div>
                    
                    <!-- Harga (readonly) -->
                    {{-- <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                        <label for="harga" class="form-label">Harga</label>
                        <input type="number" class="form-control" id="harga" name="harga" readonly>
                    </div> --}}
                    
                    <!-- Jumlah -->
                    <div class="mb-3">
                        <x-input-form name="masuk" :label="__('Jumlah')" id="masuk" type="number"/>
                    </div>
                    
                    <!-- Berat Per Zak -->
                    <div class="mb-3">
                        <x-input-form name="berat_zak" :label="__('Berat Per Zak')" id="berat_zak" type="number" step="0.01"/>
                    </div>
                    
                    <!-- Total Berat (readonly) -->
                    <div class="mb-3">
                        <x-readonly-input name="total_berat" :label="'Total Berat'" :value="old('total_berat')" type="text"/>
                    </div>
                    
                    <!-- Total Harga (readonly) -->
                    <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                        <x-readonly-input name="total_harga_pakan" id="total_harga_pakan" :label="'Total Harga'" ... />
                    </div>
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
    
    @section('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Gunakan selector untuk modal edit; misalnya "id_pakan" pada modal edit
            const pakanSelect = document.getElementById("id_pakan");
            const hargaInput = document.getElementById("harga");
    
            // Update harga saat pakan dipilih
            if (pakanSelect) {
                pakanSelect.addEventListener("change", function () {
                    const pakans = @json($pakans);
                    const selectedPakan = pakans.find(pakan => pakan.id_pakan == this.value);
        
                    if (selectedPakan) {
                        hargaInput.value = formatNumber(selectedPakan.harga);
                    } else {
                        hargaInput.value = '';
                    }
        
                    hitungTotal();
                });
            }
        
            // Event hitung total saat input jumlah atau berat per zak berubah
            $('#masuk, #berat_zak').on('keyup change', function () {
                hitungTotal();
            });
        
            // Fungsi Format Number (misalnya untuk ribuan)
            function formatNumber(number) {
                return new Intl.NumberFormat('id-ID', { maximumFractionDigits: 0 }).format(number);
            }
        
            // Fungsi Hitung Total Berat & Harga
            function hitungTotal() {
                let masuk = parseFloat($('#masuk').val().replace(/\./g, '')) || 0;
                let berat_zak = parseFloat($('#berat_zak').val().replace(/\./g, '')) || 0;
                let harga = parseFloat($('#harga').val().replace(/\./g, '')) || 0;
        
                let total_berat = masuk * berat_zak;
                let total_harga_pakan = total_berat * harga;
        
                $('#total_berat').val(formatNumber(total_berat));
                $('#total_harga_pakan').val(formatNumber(total_harga_pakan));
            }
        
            // Trigger perubahan select pakan saat modal edit dibuka
            $(pakanSelect).trigger('change');
        });
    </script>
    
    
    @endsection
    
@endsection