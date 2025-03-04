@extends('layout.main')

@section('content')
    <x-breadcrumb
        :values="[__('Tamabah Menu'), __('Tambah Pakan Masuk'), __('Tambah Pakan Masuk')]">
    </x-breadcrumb>

    <div class="card mb-4">
        <form action="{{ route('pakan.pakanmasuk.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-body row">
                <!-- Existing fields -->
                <input type="hidden" name="type" value="outgoing">

                <!-- New fields -->
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <label for="ayam_id" class="form-label">{{ __('Periode') }}</label>
                    <select name="ayam_id" id="id_ayam" class="form-control">
                        @foreach($ayams as $ayam)
                        <option value="{{ $ayam->id_ayam }}">{{ $ayam->periode }}</option>
                    @endforeach
                    </select>
                </div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <label for="pakan_id" class="form-label">Nama Pakan</label>
                    <select class="form-select" id="pakan_id" name="pakan_id">
                        <option value="">-- Pilih Pakan --</option>
                        @foreach($pakans as $pakan)
                        <option value="{{ $pakan->id_pakan }}">{{ $pakan->nama_pakan }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <label for="harga" class="form-label">Harga</label>
                    <input type="number" class="form-control" id="harga" name="harga" value="{{ old('harga') }}" readonly/>
                </div>
                
                
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <x-input-form name="tanggal" :label="__('Tanggal_Masuk')" type="date"/>
                </div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <x-input-form name="masuk" :label="__('Jumlah')" id="masuk"/>
                </div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <x-input-form name="berat_zak" :label="__('Berat')" id="berat_zak"/>
                </div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <x-readonly-input name="total_berat" :label="'Total Berat'" :value="number_format($total_berat ?? 0, 0, ',', '.')" type="text"/>
                </div>
                
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <x-readonly-input name="total_harga_pakan" :label="'Total Harga '" :value="number_format($total_harga_pakan ?? 0, 0, ',', '.')" type="text"/>
                </div>
                
            
               
                
            </div>
            <div class="card-footer pt-0">
                <button class="btn btn-primary" type="submit">{{ __('menu.general.save') }}</button>
            </div>
        </form>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const pakanSelect = document.getElementById("pakan_id");
            const hargaInput = document.getElementById("harga");
    
            // Vanilla JS untuk Pilih Harga
            if (pakanSelect) {
                pakanSelect.addEventListener("change", function () {
                    const pakans = @json($pakans);
                    const selectedPakan = pakans.find(pakan => pakan.id_pakan == this.value);
    
                    if (selectedPakan) {
                        hargaInput.value = formatNumber(selectedPakan.harga);
                    } else {
                        hargaInput.value = '';
                    }
                    hitungTotal(); // Auto Hitung Total
                });
            }
    
            // Event Hitung Berat & Harga Saat Ketik
            $('#masuk, #berat_zak').on('keyup', function () {
                hitungTotal();
            });
    
            // Fungsi Format Number Ribuan
            function formatNumber(number) {
                return new Intl.NumberFormat('id-ID').format(number);
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
    
            // Trigger Change Saat Pertama Load
            $('#pakan_id').trigger('change');
        });
    </script>
    
    
    
@endsection
