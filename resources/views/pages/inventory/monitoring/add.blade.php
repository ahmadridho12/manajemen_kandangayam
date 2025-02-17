@extends('layout.main')

@section('content')
    <x-breadcrumb
        :values="[__('Monitoring Ayam'), __('Tambah Laporan')]">
    </x-breadcrumb>

    <div class="card mb-4">
        @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

        <form action="{{ route('inventory.monitoring.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-body row">
                <input type="hidden" name="type" value="outgoing">
                <div class="col-sm-12 col-12 col-md-6 col-lg-6">
                    <label for="ayam_id" class="form-label">{{ __('Periode') }}</label>
                    <select name="ayam_id" id="id_yam" class="form-control">
                        @foreach($ayams as $ayam)
                        <option value="{{ $ayam->id_ayam }}">{{ $ayam->periode }}</option>
                    @endforeach
                    </select>
                </div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-6">
                    <x-input-form name="tanggal_monitoring" :label="__('Tanggal')" type="date" />
                </div>
                
                <div class="col-sm-12 col-12 col-md-6 col-lg-3">
                    <x-input-form name="skat_1_bw" :label="__('Berat Skat 1')" type="number" />
                </div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-3">
                    <x-input-form name="skat_2_bw" :label="__('Berat Skat 2')" type="number" />
                </div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-3">
                    <x-input-form name="skat_3_bw" :label="__('Berat Skat 3')" type="number" />
                </div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-3">
                    <x-input-form name="skat_4_bw" :label="__('Berat Skat 4')" type="number" />
                </div>
              

               
                <button type="submit" class="btn btn-success mt-3">Kirim</button>
            </div>
        </form>
    </div>
    
    {{-- <script>
        // Data stok dari backend
        const stokData = @json($stokData);
    
        console.log('Stok Data:', stokData); // Tambahkan log untuk debugging
    
        let barangIndex = 1;
    
        // Fungsi untuk mengupdate stok
        function updateStok(selectElement) {
            const barangId = selectElement.value;
            const stokInput = selectElement.closest('.row').querySelector('[id^=stok-]');
            
            // Debug: Cek nilai barangId dan stokData
            console.log('Barang ID:', barangId);
            console.log('Stok Data:', stokData);
            console.log('Total Stok:', stokData[barangId]);
            
            const totalStok = stokData[barangId] || 0;
            
            stokInput.value = totalStok;
        }
    
        // Event listener untuk select barang awal
        document.addEventListener('DOMContentLoaded', function() {
            const initialSelect = document.querySelector('#barang');
            
            if (initialSelect) {
                initialSelect.addEventListener('change', function() {
                    updateStok(this);
                });
    
                // Inisialisasi stok awal
                updateStok(initialSelect);
            }
        });
    
        // Tambah barang dinamis
        document.getElementById('add-barang-btn').addEventListener('click', function() {
            const wrapper = document.getElementById('barang-input-wrapper');
            const newBarang = document.createElement('div');
            newBarang.classList.add('row', 'barang-input', 'mt-3');
    
            newBarang.innerHTML = `
                <div class="col-sm-12 col-12 col-md-4">
                    <label for="barang-${barangIndex}" class="form-label">{{ __('Nama Barang') }}</label>
                    <select name="barang[${barangIndex}][id_barang]" id="barang-${barangIndex}" class="form-control barang-select" required>
                        @foreach($barangs as $barangg)
                            <option value="{{ $barangg->id_barang }}">{{ $barangg->deskripsi }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-sm-12 col-12 col-md-4">
                    <label for="jumlah-${barangIndex}" class="form-label">{{ __('Qty') }}</label>
                    <input type="number" name="barang[${barangIndex}][qty]" id="jumlah-${barangIndex}" class="form-control" required>
                </div>
    
                <div class="col-sm-12 col-12 col-md-4">
                    <label for="stok-${barangIndex}" class="form-label">{{ __('Stok Tersedia') }}</label>
                    <input type="text" id="stok-${barangIndex}" class="form-control" readonly>
                </div>
            `;
    
            wrapper.appendChild(newBarang);
    
            // Tambahkan event listener untuk select barang baru
            const newSelect = newBarang.querySelector(`#barang-${barangIndex}`);
            newSelect.addEventListener('change', function() {
                updateStok(this);
            });
    
            // Inisialisasi stok untuk barang baru
            updateStok(newSelect);
    
            barangIndex++;
        });
    </script> --}}
@endsection
