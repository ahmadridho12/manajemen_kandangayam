@extends('layout.main')

@section('content')
    <x-breadcrumb
        :values="[__('Tamabah Menu'), __('Tambah Pakan Keluar'), __('Tambah Pakan Keluar')]">
    </x-breadcrumb>

    <div class="card mb-4">
        <form action="{{ route('pakan.pakankeluar.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-body row">
                <!-- Field lainnya -->
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
                        {{-- Hapus opsi kosong agar selalu ada pilihan --}}
                        @foreach($pakans as $pakan)
                            <option value="{{ $pakan->id_pakan }}">{{ $pakan->nama_pakan }}</option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Field tanggal, qty, berat -->
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <x-input-form name="tanggal" :label="__('Tanggal_Masuk')" type="date"/>
                </div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <x-input-form name="qty" :label="__('Jumlah')"/>
                </div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <x-input-form name="berat_zak" :label="__('Berat')"/>
                </div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <label for="masuk" class="form-label">Quantity Pakan</label>
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
<script>
    var stokData = @json($stokData);
    console.log('stokData:', stokData);

    function updateStok() {
        var ayamId = document.getElementById('id_ayam').value;
        var pakanId = document.getElementById('pakan_id').value;

        // Debug
        console.log("Ayam ID:", ayamId, "Pakan ID:", pakanId);

        var stokTersedia = 0;
        if (stokData[ayamId] && stokData[ayamId][pakanId]) {
            stokTersedia = stokData[ayamId][pakanId];
        }

        document.getElementById('masuk').value = stokTersedia;
    }

    // Event listener pada kedua select
    document.getElementById('pakan_id').addEventListener('change', updateStok);
    document.getElementById('id_ayam').addEventListener('change', updateStok);

    // Panggil updateStok saat halaman pertama kali dimuat
    updateStok();
</script>

@endsection
