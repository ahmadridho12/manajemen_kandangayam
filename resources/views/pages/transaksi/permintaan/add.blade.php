@extends('layout.main')

@section('content')
    <x-breadcrumb
        :values="[__('Permintaan'), __('Tambah Permintaan')]">
    </x-breadcrumb>

    <div class="card mb-4">
        <form action="{{ route('transaksi.permintaan.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <d class="card-body row">
                <input type="hidden" name="type" value="outgoing">

                <!-- Bagian yang Meminta -->
                <div class="col-sm-12 col-12 col-md-6 col-lg-3">
                    <label for="nama_bagian" class="form-label">{{ __('Bagian yang Meminta') }}</label>
                    <select name="nama_bagian" id="id_bagian" class="form-control">
                        @foreach($bagians as $bagian)
                            <option value="{{ $bagian->nama_bagian }}">{{ $bagian->nama_bagian }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Type -->
                <div class="col-sm-12 col-12 col-md-6 col-lg-3">
                    <label for="nama_tipe" class="form-label">{{ __('Type') }}</label>
                    <select name="nama_tipe" id="id_tipe" class="form-control">
                        @foreach($kategoris as $kategori)
                            <option value="{{ $kategori->nama_tipe }}">{{ $kategori->nama_tipe }}</option>
                        @endforeach
                    </select>
                </div>
    
                <!-- Keterangan -->
                <div class="col-sm-12 col-12 col-md-6 col-lg-3">
                    <x-input-form name="keterangan" :label="__('Keterangan')" />
                </div>
    
                <!-- Tanggal Permintaan -->
                <div class="col-sm-12 col-12 col-md-6 col-lg-3">
                    <x-input-form name="tgl_permintaan" :label="__('Tanggal Permintaan')" type="date" />
                </div>
    
                
                
                
                <!-- Barang -->
                <div class="col-12" style="text-align: center; margin-top: 20px; margin-bottom: 20px; font-weight: bold; font-size: 20px;"> Barang</div>
                <!-- Barang -->
                <br>
                <div id="barang-input-wrapper" class="row">
                    <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                        <label for="barang" class="form-label">{{ __('Nama Barang') }}</label>
                        <select name="barang[0][id_barang]" id="barang" class="form-control select2" required>
                            <option value="">Pilih Barang</option>
                            @foreach($barangs as $barangg)
                                <option value="{{ $barangg->id_barang }}">{{ $barangg->deskripsi }}</option>
                            @endforeach
                        </select>
                    </div>
                   
                    <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                        <label for="jumlah" class="form-label">{{ __('Qty') }}</label>
                        <input type="number" name="barang[0][qty]" id="jumlah" class="form-control" min="1" required>
                    </div>
                    <div class="col-sm-12 col-12 col-md-4">
                        <label for="stok" class="form-label">{{ __('Stok Tersedia') }}</label>
                        <input type="text" id="stok-0" class="form-control" readonly>
                    </div>
                   
                </div>
                
                <!-- Tempat untuk menambahkan input barang dinamis -->
    
                <button type="button" id="add-barang-btn" class="btn btn-primary mt-3">Tambah Barang</button>
                <button type="submit" class="btn btn-success mt-3">Kirim</button>
            </div>
        </form>
    </div>
    
    <script>
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
    </script>
@endsection
