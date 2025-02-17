@extends('layout.main')

@section('content')
<div class="card mb-5">
    <div class="card-header">
        <h1>Hitung Tarif Air</h1>
    </div>
    <div class="card-body">
        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif
        <a href="{{ route('lainnya.tarif-air.index') }}" class="menu-link">Tarif</a>


        <form action="{{ route('lainnya.tarif.hitung') }}" method="POST">
            @csrf
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Pilih Golongan:</label>
                    <select name="type" class="form-select">
                        @foreach($types as $key => $value)
                            <option value="{{ $key }}" 
                                {{ (isset($type) && $type == $key) ? 'selected' : '' }}>
                                {{ $value }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Jumlah Kubik:</label>
                    <input type="number" name="kubik" 
                           class="form-control"
                           value="{{ $kubik ?? '' }}" 
                           required min="0">
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Hitung</button>
        </form>

        @if(isset($hasil))
            <div class="mt-4">
                <div class="card">
                    <div class="card-header">
                        <h2>Rincian Perhitungan Tarif Air</h2>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h4>Informasi Dasar</h4>
                                <table class="table">
                                    <tr>
                                        <td>Golongan</td>
                                        <td>: {{ $hasil['golongan'] }}</td>
                                    </tr>
                                    <tr>
                                        <td>Total Kubik</td>
                                        <td>: {{ $kubik }} m³</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <h4 class="mt-3">Rincian Perhitungan Kubik</h4>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Rentang Kubik</th>
                                    <th>Tarif/m³</th>
                                    <th>Jumlah m³</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $totalKubik = 0; @endphp
                                @foreach($hasil['detail_kubik'] as $detail)
                                <tr>
                                    <td>{{ $detail['min'] }} - {{ $detail['max'] }} m³</td>
                                    <td>Rp. {{ number_format($detail['tarif'], 0, ',', '.') }}</td>
                                    <td>{{ $detail['jumlah_kubik'] }} m³</td>
                                    <td>Rp. {{ number_format($detail['subtotal'], 0, ',', '.') }}</td>
                                </tr>
                                @php $totalKubik += $detail['jumlah_kubik']; @endphp
                                @endforeach
                            </tbody>
                        </table>

                        <h4 class="mt-3">Biaya Tambahan</h4>
                        <table class="table table-bordered">
                            <tbody>
                                @php $totalBiayaTambahan = 0; @endphp
                                @foreach($hasil['biaya_tambahan'] as $key => $value)
                                @if($value > 0)
                                <tr>
                                    <td>{{ ucwords(str_replace('_', ' ', $key)) }}</td>
                                    <td>Rp. {{ number_format($value, 0, ',', '.') }}</td>
                                </tr>
                                @php $totalBiayaTambahan += $value; @endphp
                                @endif
                                @endforeach
                            </tbody>
                        </table>

                        <div class="card mt-3">
                            <div class="card-body">
                                <h4>Ringkasan Tagihan</h4>
                                <table class="table">
                                    <tr>
                                        <td><strong>Total Biaya Kubik</strong></td>
                                        <td>: Rp. {{ number_format($hasil['biaya_kubik'], 0, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Total Biaya Tambahan</strong></td>
                                        <td>: Rp. {{ number_format($totalBiayaTambahan, 0, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>TOTAL TAGIHAN</strong></td>
                                        <td>: <span class="text-danger">Rp. {{ number_format($hasil['total_tagihan'], 0, ',', '.') }}</span></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection