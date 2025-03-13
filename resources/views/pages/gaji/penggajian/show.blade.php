@extends('layout.main')

@section('content')
<div class="container">
    <!-- Informasi Dasar -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Detail Perhitungan Gaji</h5>
            {{-- <a href="{{ route('gaji.penggajian.index') }}" class="btn btn-secondary">Kembali</a> --}}
        </div>
        <x-breadcrumb 
    :values="[__('Penggajian'), __('Detail Penggajian'), __('menu.general.view')]">
    <a href="{{ route('gaji.penggajian.print', $perhitunganGaji->id_perhitungan) }}" class="btn btn-primary">
        <i class="fas fa-print"></i> Print
    </a>
</x-breadcrumb>
        <div class="card-body">
            <div class="row">
                <!-- Informasi Kandang dan Pemeliharaan -->
                <div class="col-md-6">
                    <div class="mb-3">
                        <strong>Kandang:</strong> {{ $perhitunganGaji->kandang->nama_kandang }}
                    </div>
                    <div class="mb-3">
                        <strong>Periode Ayam:</strong> {{ $perhitunganGaji->ayam->periode }}
                    </div>
                    <div class="mb-3">
                        <strong>Hasil Pemeliharaan:</strong> Rp {{ number_format($perhitunganGaji->hasil_pemeliharaan, 0, ',', '.') }}
                    </div>
                    <div class="mb-3">
                        <strong>Total Operasional:</strong> Rp {{ number_format($perhitunganGaji->total_potongan, 0, ',', '.') }}
                    </div>
                    <div class="mb-3">
                        <strong>Hasil Setelah Potongan:</strong> Rp {{ number_format($perhitunganGaji->hasil_setelah_potongan, 0, ',', '.') }}
                    </div>
                </div>
                <!-- Ringkasan Potongan dan Bonus -->
                <div class="col-md-6">
                    <div class="mb-3">
                        <strong>Total Gaji (20%):</strong> Rp {{ number_format($perhitunganGaji->hasil_setelah_potongan * 0.20, 0, ',', '.') }}
                    </div>
                    <div class="mb-3">
                        <strong>Bonus:</strong> Rp {{ number_format($perhitunganGaji->rincianGaji->sum('bonus'), 0, ',', '.') }}
                    </div>
                    <div class="mb-3">
                        <strong>Total Gaji Pokok + Bonus:</strong> Rp {{ number_format($perhitunganGaji->rincianGaji->sum('gaji_pokok') + $perhitunganGaji->rincianGaji->sum('bonus'), 0, ',', '.') }}
                    </div>
                    <div class="mb-3">
                        <strong>Bonus Per Orang:</strong> Rp {{ number_format($perhitunganGaji->bonus_per_orang, 0, ',', '.') }}
                    </div>
                </div>
            </div>
            <div class="row">
                <!-- Informasi Kandang dan Pemeliharaan -->
                <div class="col-md-12">
                    <div class="mb-12">
                        <strong>Keterangan:</strong> {{ $perhitunganGaji->keterangan }}
                    </div>
                    
                </div>
                <!-- Ringkasan Potongan dan Bonus -->
                
            </div>
        </div>

        <br>
        <br>
    <!-- Rincian Operasional -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Rincian Potongan Operasional</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Potongan</th>
                            <th>Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($operasional as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->nama_potongan }}</td>
                            <td>Rp {{ number_format($item->jumlah, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Rincian Gaji ABK -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Rincian Gaji ABK</h5>
            {{-- <div>
                <button onclick="window.print()" class="btn btn-primary">
                    <i class="fas fa-print"></i> Print Semua
                </button>
            </div> --}}
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama ABK</th>
                            <th>Gaji Pokok</th>
                            <th>Bonus</th>
                            <th>Pinjaman</th>
                            <th>Gaji Bersih</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($perhitunganGaji->rincianGaji as $index => $rincian)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $rincian->abk->nama }}</td>
                            <td>Rp {{ number_format($rincian->gaji_pokok, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($rincian->bonus, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($rincian->jumlah_pinjaman, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($rincian->gaji_bersih, 0, ',', '.') }}</td>
                            <td>
                                <a href="{{ route('gaji.penggajian.print.slip', $rincian->id_rincian) }}" 
                                    class="btn btn-sm btn-info">
                                     <i class="fas fa-print"></i> Print Slip
                                 </a>
                            </td>
                        </tr>
                        @endforeach
                        <tr class="table-info">
                            <td colspan="2"><strong>Total</strong></td>
                            <td><strong>Rp {{ number_format($perhitunganGaji->rincianGaji->sum('gaji_pokok'), 0, ',', '.') }}</strong></td>
                            <td><strong>Rp {{ number_format($perhitunganGaji->rincianGaji->sum('bonus'), 0, ',', '.') }}</strong></td>
                            <td><strong>Rp {{ number_format($perhitunganGaji->rincianGaji->sum('jumlah_pinjaman'), 0, ',', '.') }}</strong></td>

                            <td><strong>Rp {{ number_format($perhitunganGaji->rincianGaji->sum('gaji_bersih'), 0, ',', '.') }}</strong></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Perhitungan Akhir -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Perhitungan Akhir</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tr>
                        <th width="40%">Total Hasil Pemeliharaan</th>
                        <td>Rp {{ number_format($perhitunganGaji->hasil_pemeliharaan, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th>Total Potongan Operasional</th>
                        <td>Rp {{ number_format($perhitunganGaji->total_potongan, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th>Total Gaji Pokok + Bonus</th>
                        <td>Rp {{ number_format($perhitunganGaji->rincianGaji->sum('gaji_pokok') + $perhitunganGaji->rincianGaji->sum('bonus'), 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th>Total Pinjaman</th>
                        <td><strong>Rp {{ number_format($perhitunganGaji->rincianGaji->sum('jumlah_pinjaman'), 0, ',', '.') }}</strong></td>

                    </tr>
                    <tr class="table-success">
                        <th>Keuntungan Bersih Kandang</th>
                        <td>
                            <strong>Rp {{ number_format(
                                $perhitunganGaji->hasil_pemeliharaan - 
                                $perhitunganGaji->total_potongan - 
                                ($perhitunganGaji->rincianGaji->sum('gaji_pokok') + $perhitunganGaji->rincianGaji->sum('bonus')) +
                                $perhitunganGaji->rincianGaji->sum('jumlah_pinjaman')
                            , 0, ',', '.') }}</strong>
                        </td>
                    </tr>
                    
                </table>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    @media print {
        .btn, .card-header { display: none !important; }
        .card { border: none !important; }
        .table td, .table th { padding: 0.5rem !important; }
    }
</style>
@endpush
@endsection