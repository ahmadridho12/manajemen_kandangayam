@extends('layout.main')

@section('content')
    <x-breadcrumb
        :values="[__('Permintaan'), __('Tambah Permintaan')]">
    </x-breadcrumb>

    <div class="card mb-4">
        <form action="{{ route('inventory.goods.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-body row">
                <input type="hidden" name="type" value="outgoing">

                <!-- Keterangan -->
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <x-input-form name="deskripsi" :label="__('Nama Barang')" />
                </div>

                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <label for="id_satuan" class="form-label">{{ __('Satuan') }}</label>
                    <select name="id_satuan" id="id_satuan" class="form-control">
                        @foreach($satuans as $satuan)
                            <option value="{{ $satuan->id_satuan }}">{{ $satuan->nama_satuan }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <label for="id_jenis" class="form-label">{{ __('Jenis') }}</label>
                    <select name="id_jenis" id="id_jenis" class="form-control">
                        @foreach($jenisa as $jenis)
                            <option value="{{ $jenis->id }}">{{ $jenis->nama }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <label for="kode_barang" class="form-label">{{ __('Kode Barang') }}</label>
                    <input type="text" name="kode_barang" id="kode_barang" class="form-control" readonly>
                </div> --}}

               

                <!-- Penamaan (No Transaksi) -->
                

             
                <button type="submit" class="btn btn-success mt-3">Submit</button>
            </div>
        </form>
    </div>
    <script>
        function generateKodeBarang() {
            const idJenis = document.getElementById('id_jenis').value;

            // Fetch Kode Barang dari server
            fetch(`/inventory/generate-kode-barang/${idJenis}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('kode_barang').value = data.kode_barang;
                })
                .catch(error => console.error('Error:', error));
        }
    </script>
   
@endsection
