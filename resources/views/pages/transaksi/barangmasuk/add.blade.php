@extends('layout.main')

@section('content')
    <x-breadcrumb
        :values="[__('Barang Masuk'), __('Barang Masuk')]">
    </x-breadcrumb>

    <div class="card mb-4">
        <form action="{{ route('transaksi.barangmasuk.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-body row">
                <input type="hidden" name="type" value="outgoing">

                <!-- Penamaan (No Transaksi) -->
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <label for="suplier_id" class="form-label">{{ __('Suplier') }}</label>
                    <select name="suplier_id" id="suplier_id" class="form-control">
                        @foreach($suplierr  as $supplier)
                            <option value="{{ $supplier->id_suplier }}">{{ $supplier->nama_suplier }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Keterangan -->
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <x-input-form name="tgl_masuk" :label="__('Tanggal Masuk')" type="date"/>
                </div>

                <!-- Tanggal Permintaan -->
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <x-input-form name="keterangan" :label="__('Keterangan')" />
                </div>
                <h5 style="text-align: center; margin-top: 60px">Barang</h5>
<div id="barang-container">
    <div class="row mb-3 barang-item">
        <div class="col-md-3">
            <label for="barang[0][id]" class="form-label">Barang</label>
            <select name="barang[0][id]" class="form-select" required>
                <option value="">Pilih Barang</option>
                @foreach($barangg as $item)
                    <option value="{{ $item->id_barang }}">{{ $item->deskripsi }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label for="barang[0][jumlah]" class="form-label">Jumlah</label>
            <input type="number" name="barang[0][jumlah]" class="form-control" required>
        </div>

        <div class="col-md-3">
            <label for="barang[0][harga_sebelum_ppn]" class="form-label">Harga Sebelum PPN</label>
            <input type="number" name="barang[0][harga_sebelum_ppn]" class="form-control" required>
        </div>

        <div class="col-md-3">
            <label for="barang[0][kategori_ppn_id]" class="form-label">Kategori PPN</label>
            <select name="barang[0][kategori_ppn_id]" class="form-select" required>
                <option value="">Pilih Kategori PPN</option>
                @foreach($kategoribm as $kategori)
                    <option value="{{ $kategori->id_kategoribm }}">{{ $kategori->nama_ibm }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>


<button type="button" id="add-barang" class="btn btn-secondary mb-3">Tambah Barang</button>
<button type="submit" class="btn btn-primary">Simpan</button>
</div>
</form>
</div>


<script>
    document.getElementById('add-barang').addEventListener('click', function() {
        let container = document.getElementById('barang-container');
        let index = container.querySelectorAll('.barang-item').length;

        let newItem = `
            <div class="row mb-3 barang-item">
                <div class="col-md-3">
                    <label for="barang[${index}][id]" class="form-label">Barang</label>
                    <select name="barang[${index}][id]" class="form-select" required>
                        <option value="">Pilih Barang</option>
                        @foreach($barangg as $item)
                            <option value="{{ $item->id_barang }}">{{ $item->deskripsi }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="barang[${index}][jumlah]" class="form-label">Jumlah</label>
                    <input type="number" name="barang[${index}][jumlah]" class="form-control" required>
                </div>

                <div class="col-md-3">
                    <label for="barang[${index}][harga_sebelum_ppn]" class="form-label">Harga Sebelum PPN</label>
                    <input type="number" name="barang[${index}][harga_sebelum_ppn]" class="form-control" required>
                </div>

                <div class="col-md-3">
                    <label for="barang[${index}][kategori_ppn_id]" class="form-label">Kategori PPN</label>
                    <select name="barang[${index}][kategori_ppn_id]" class="form-select" required>
                        <option value="">Pilih Kategori PPN</option>
                        @foreach($kategoribm as $kategori)
                            <option value="{{ $kategori->id_kategoribm }}">{{ $kategori->nama_ibm }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        `;

        container.insertAdjacentHTML('beforeend', newItem);
    });
</script>

@endsection
