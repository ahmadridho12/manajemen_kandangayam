@extends('layout.main')

@section('content')
    <x-breadcrumb :values="[__('Tambah Menu'), __('Tambah Pakan Keluar'), __('Tambah Pakan Keluar')]"></x-breadcrumb>

    <div class="card mb-4">
        <form action="{{ route('pakan.pakankeluar.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-body row">
                <!-- Pilihan Periode (Ayam) -->
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <label for="id_ayam" class="form-label">{{ __('Periode') }}</label>
                    <select name="ayam_id" id="id_ayam" class="form-control">
                        @foreach($ayams as $ayam)
                            <option value="{{ $ayam->id_ayam }}" {{ $loop->first ? 'selected' : '' }}>
                                {{ $ayam->periode }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                
                <!-- Pilihan Pakan -->
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <label for="pakan_id" class="form-label">Nama Pakan</label>
                    <select class="form-select" id="pakan_id" name="pakan_id">
                        @foreach($pakans as $pakan)
                            <option value="{{ $pakan->id_pakan }}">{{ $pakan->nama_pakan }}</option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Field Tanggal, Qty, Berat -->
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <x-input-form name="tanggal" :label="__('Tanggal_Masuk')" type="date"/>
                </div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <x-input-form name="qty" :label="__('Jumlah')"/>
                </div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <x-input-form name="berat_zak" :label="__('Berat')"/>
                </div>
                
                <!-- Field Stok Tersedia -->
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <label for="masuk" class="form-label">Stok Tersedia</label>
                    <input type="number" class="form-control" id="masuk" name="masuk" value="{{ old('masuk') }}" readonly/>
                </div>
            </div>
            
            <div class="card-footer pt-0">
                <button class="btn btn-primary" type="submit">{{ __('menu.general.save') }}</button>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
@push('script')
<script>
document.addEventListener("DOMContentLoaded", function () {
    console.log("Script updateStokAPI loaded");

    async function updateStokAPI() {
        var ayamId = document.getElementById('id_ayam').value;
        var pakanId = document.getElementById('pakan_id').value;
        
        console.log("Selected Ayam ID:", ayamId, "Selected Pakan ID:", pakanId);
        
        try {
            var response = await fetch(`/pakan/get-stok-pakan?ayam_id=${ayamId}&pakan_id=${pakanId}`);
            if (!response.ok) {
                throw new Error(`Network response was not ok: ${response.status}`);
            }
            var data = await response.json();
            console.log("API Response:", data);
            
            var stokTersedia = data.stok || 0;
            document.getElementById('masuk').value = stokTersedia;
            console.log("Field 'masuk' updated to:", stokTersedia);
        } catch (error) {
            console.error("Error fetching stok data:", error);
            document.getElementById('masuk').value = 0;
        }
    }
    
    // Pasang event listener
    var selectPakan = document.getElementById('pakan_id');
    var selectAyam = document.getElementById('id_ayam');
    
    if (selectPakan) {
        selectPakan.addEventListener('change', function() {
            console.log("pakan_id changed");
            updateStokAPI();
        });
    } else {
        console.error("Element with id 'pakan_id' not found");
    }
    
    if (selectAyam) {
        selectAyam.addEventListener('change', function() {
            console.log("id_ayam changed");
            updateStokAPI();
        });
    } else {
        console.error("Element with id 'id_ayam' not found");
    }
    
    // Panggil updateStokAPI saat DOM siap
    updateStokAPI();
});
</script>
@endpush

@endsection

