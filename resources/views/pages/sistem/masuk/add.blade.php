@extends('layout.main')

@section('content')
    <x-breadcrumb
        :values="[__('Masuk'), __('Tambah Masuk')]">
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

        <form action="{{ route('sistem.masuk.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-body row">
                <input type="hidden" name="type" value="outgoing">

                <div class="col-sm-12 col-12 col-md-6 col-lg-3">
                    <x-input-form name="periode" :label="__('Periode')"  />
                </div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-3">
                    <x-input-form name="tanggal_masuk" :label="__('Tanggal Masuk')" type="date" />
                </div>
                {{-- <div class="col-sm-12 col-12 col-md-6 col-lg-3">
                    <x-input-form name="tanggal_selesai" :label="__('Tanggal Selesai')" type="date" />
                </div> --}}
                <div class="col-sm-12 col-12 col-md-6 col-lg-3">
                    <x-input-form name="rentang_hari" :label="__('Rentang Hari')" type="number" />
                </div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-3">
                    <x-input-form name="qty_ayam" :label="__('Populasi')" type="number" />
                </div>
                {{-- <div class="col-sm-12 col-12 col-md-6 col-lg-3">
                    <x-input-form name="harga" :label="__('Harga')" type="number" step="0.01" />
                </div> --}}
                
                <!-- Bagian yang Meminta -->
                <div class="col-sm-12 col-12 col-md-6 col-lg-3">
                    <label for="doc_id" class="form-label">{{ __('Harga DOC') }}</label>
                    <select name="doc_id" id="id_doc" class="form-control">
                        @foreach($docs as $doc)
                            <option value="{{ $doc->id_doc}}">{{ $doc->harga }}</option>
                        @endforeach
                    </select>
                    
                </div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-3">
                    <label for="kandang_id" class="form-label">{{ __('Nama Kandang') }}</label>
                    <select name="kandang_id" id="id_kandang" class="form-control">
                        @foreach($kandangs as $kandang)
                            <option value="{{ $kandang->id_kandang}}">{{ $kandang->nama_kandang }}</option>
                        @endforeach
                    </select>
                    
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
