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

        <form action="{{ route('gaji.penggajian.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-body row">
                <input type="hidden" name="type" value="outgoing">

                
                
                <!-- Bagian yang Meminta -->
                <div class="col-sm-12 col-12 col-md-6 col-lg-6">
                    <label for="id_kandang" class="form-label">{{ __('Nama Kandang') }}</label>
                    <select name="kandang_id" id="id_kandang" class="form-control">
                        @foreach($kandangs as $kandang)
                            <option value="{{ $kandang->id_kandang}}">{{ $kandang->nama_kandang }}</option>
                        @endforeach
                    </select>
                    
                </div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-6">
                    <label for="id_ayam" class="form-label">{{ __('Periode') }}</label>
                    <select name="ayam_id" id="id_ayam" class="form-control">
                        @foreach($ayams as $ayam)
                            <option value="{{ $ayam->id_ayam }}">{{ $ayam->periode }}</option>
                        @endforeach
                    </select>
                </div>  
                {{-- <div class="col-sm-12 col-12 col-md-6 col-lg-6">
                    <x-input-form name="hasil_pemeliharaan" :label="__('Hasil Pemeliharaan')" type="number" />
                </div> --}}
                <div class="col-sm-12 col-12 col-md-6 col-lg-6">
                    <x-input-form name="bonus_per_orang" :label="__('Bonus')" type="number" />
                </div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-6">
                    <x-input-form name="keterangan" :label="__('Keterangan')" />
                </div>
                <button type="submit" class="btn btn-success mt-3">Kirim</button>
            </div>
        </form>
    </div>
@endsection
