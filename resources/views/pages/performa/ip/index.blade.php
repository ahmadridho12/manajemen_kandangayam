@extends('layout.main')

@push('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
<!-- Di bagian head atau sebelum closing body -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script>
$(document).ready(function() {
    $('.btn-hitung-ip').on('click', function() {
        const kandangId = $(this).data('kandang-id');
        const periode = $(this).data('periode');
        
        $.ajax({
            url: `/performa/ip/hitung-ip/${kandangId}`,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                periode: periode
            },
            success: function(response) {
                if (response.success) {
                    console.log('Response lengkap:', response); // Tambahkan log

                    const data = response.data;
                    
                    // Update IP dan komponen
                    $('#performanceDashboard').removeClass('d-none');
                    $('#avgIP').text(data.ip);
                    $('#dayaHidup').text(data.komponen.daya_hidup);
                    $('#bobotBadan').text(data.komponen.bobot_badan);
                    $('#fcr').text(data.komponen.fcr);
                    
                    // Update chart
                    // new Chart(document.getElementById('performanceChart').getContext('2d'), {
                    //     type: 'line',
                    //     data: {
                    //         labels: ['Current'],
                    //         datasets: [
                    //             {
                    //                 label: 'IP',
                    //                 data: [data.ip],
                    //                 borderColor: 'rgb(75, 192, 192)'
                    //             },
                    //             {
                    //                 label: 'Daya Hidup',
                    //                 data: [parseFloat(data.komponen.daya_hidup)],
                    //                 borderColor: 'rgb(255, 99, 132)'
                    //             },
                    //             {
                    //                 label: 'FCR',
                    //                 data: [parseFloat(data.komponen.fcr)],
                    //                 borderColor: 'rgb(255, 205, 86)'
                    //             }
                    //         ]
                    //     },
                    //     options: {
                    //         responsive: true,
                    //         scales: { y: { beginAtZero: true } }
                    //     }
                    // });

                    // Update Populasi Data jika ada
                    if (response.populasiData && response.populasiData.status) {
            console.log('Populasi data:', response.populasiData.data);
            $('#populasiCard').removeClass('d-none');

            const populasiData = response.populasiData.data;

            $('#populasiAwal').text(number_format(populasiData.populasi_awal) + ' Ekor');
            $('#ayamMati').text(number_format(populasiData.ayam_mati) + ' Ekor');
            $('#persentaseMati').text(number_format(populasiData.persentase_mati, 2) + ' %');
            $('#ayamSisa').text(number_format(populasiData.ayam_sisa) + ' Ekor');
            $('#ayamPanen').text(number_format(populasiData.ayam_panen) + ' Ekor');
        } else {
            console.warn('Populasi data tidak tersedia atau status false.');
        }

        if (response.dataPanen && response.dataPanen.success) {
    const totalDaging = response.dataPanen.data.total.total_bb;
    const totalPakan = response.dataPanen.data.ringkasan.total_pakan_terpakai;
    const rasioKonversi = (totalDaging / totalPakan) * 100;

    // Update tabel
    $('#totalPakan').text(number_format(totalPakan, 2) + ' Kg');
    $('#totalDaging').text(number_format(totalDaging, 2) + ' Kg');
    $('#rasioKonversi').text(number_format(rasioKonversi, 2) + '%');

    // Destroy chart lama jika ada
    if (konversiChart instanceof Chart) {
        konversiChart.destroy();
    }

    try {
        // Buat pie chart baru
        const ctx = document.getElementById('konversiChart');
        if (ctx) {
            konversiChart = new Chart(ctx.getContext('2d'), {
                type: 'pie',
                data: {
                    labels: ['Berat Daging', 'Sisa Pakan'],
                    datasets: [{
                        data: [totalDaging, totalPakan - totalDaging],
                        backgroundColor: [
                            'rgba(54, 162, 235, 0.8)',
                            'rgba(255, 99, 132, 0.8)'
                        ],
                        borderColor: [
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 99, 132, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        title: {
                            display: true,
                            text: 'Konversi Pakan ke Daging'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.raw;
                                    const percentage = ((value / totalPakan) * 100).toFixed(2);
                                    return `${label}: ${number_format(value, 2)} Kg (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        } else {
            console.error('Element konversiChart tidak ditemukan');
        }
    } catch (error) {
        console.error('Error saat membuat chart:', error);
    }
            $('#dataPanenTable').removeClass('d-none');
            const records = response.dataPanen.data.records;
            const tbody = $('#dataPanenTable tbody');
            tbody.empty(); // Hapus semua baris yang lama sebelum menambahkan yang baru

            records.forEach(panen => {
                tbody.append(`
                    <tr>
                        <td>${moment(panen.tanggal_panen).format('DD/MM/YYYY')}</td>
                        <td>${panen.umur}</td>
                        <td>${number_format(panen.persen_panen, 3)}%</td>
                        <td>${number_format(panen.jumlah_panen)}</td>
                        <td>${number_format(panen.bb_rata, 3)}</td>
                        <td>${number_format(panen.total_bb_panen, 2)}</td>
                        <td>${number_format(panen.age_quantity)}</td>
                    </tr>
                `);
            });
        } else {
            $('#dataPanenTable').addClass('d-none');
        }
    } else {
        alert('Gagal menghitung IP: ' + response.message);
    }
},
            error: function(xhr) {
                alert('Terjadi kesalahan: ' + (xhr.responseJSON?.message || 'Unknown error'));
            }
        });
    });
});

function number_format(number, decimals = 0) {
    return new Intl.NumberFormat('id-ID', {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals
    }).format(number);
}
</script>
@endpush

@section('content')

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('performa.ip.index') }}" class="row g-3">
            <div class="col-md-4">
                <label for="id_ayam">{{ __('Filter Periode') }}</label>
                <select name="id_ayam" id="id_ayam" class="form-control">
                    <option value="">{{ __('Pilih Periode') }}</option>
                    @foreach($ayams as $ayam)
                        <option value="{{ $ayam->id_ayam }}" {{ request('id_ayam') == $ayam->id_ayam ? 'selected' : '' }}>
                            {{ $ayam->periode }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label for="id_kandang">{{ __('Filter Kandang') }}</label>
                <select name="id_kandang" id="id_kandang" class="form-control">
                    <option value="">{{ __('Pilih Kandang') }}</option>
                    @foreach($kandangs as $kandang)
                        <option value="{{ $kandang->id_kandang }}" {{ request('id_kandang') == $kandang->id_kandang ? 'selected' : '' }}>
                            {{ $kandang->nama_kandang }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4 align-self-end">
                <button type="submit" class="btn btn-primary">{{ __('Filter') }}</button>
                <a href="{{ route('performa.ip.index') }}" class="btn btn-secondary">{{ __('Reset') }}</a>
            </div>
        </form>
    </div>
</div>

<div class="card mb-5">
    <div class="table-responsive text-nowrap">
        <table class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>{{ __('Periode') }}</th>
                    <th>{{ __('Kandang') }}</th>
                    <th>{{ __('Umur') }}</th>
                    <th>{{ __('Deplesi') }}</th>
                    <th>{{ __('FCR') }}</th>
                    <th>{{ __('IP') }}</th>
                    <th>{{ __('Aksi') }}</th>
                </tr>
            </thead>
            <tbody>
                @if($data && $data->count())
                    @foreach($data as $item)
                        <tr>
                            <td>{{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}</td>
                            <td>{{ $item->periode }}</td>
                            <td>{{ $item->kandang->nama_kandang ?? 'N/A' }}</td>
                            <td>{{ $item->umur ?? 'N/A' }}</td>
                            <td>{{ $item->deplesi ?? 'N/A' }}%</td>
                            <td>{{ $item->fcr ?? 'N/A' }}</td>
                            <td>{{ $item->ip ?? 'N/A' }}</td>
                            <td>
                                <button 
                                    class="btn btn-primary btn-sm btn-hitung-ip" 
                                    data-kandang-id="{{ $item->kandang_id }}"
                                    data-periode="{{ $item->periode }}"
                                    {{ !$item->kandang_id ? 'disabled' : '' }}
                                >
                                    Hitung IP
                                </button>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="8" class="text-center">
                            {{ __('menu.general.empty') }}
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>


<div id="performanceDashboard" class="d-none">
    
    {{-- <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">IP</h5>
                    <h2 class="text-primary" id="avgIP">-</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Daya Hidup</h5>
                    <h2 class="text-success" id="dayaHidup">-</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Bobot Badan</h5>
                    <h2 class="text-warning" id="bobotBadan">-</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">FCR</h5>
                    <h2 class="text-info" id="fcr">-</h2>
                </div>
            </div>
        </div>
    </div> --}}

    @if(isset($ringkasan))
        <div class="mt-4">
            <h5>Hasil Perhitungan IP</h5>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tr>
                        <th colspan="2" class="text-center bg-light">Komponen Perhitungan</th>
                    </tr>
                    <tr>
                        <td>Daya Hidup (Survival Rate)</td>
                        <td>{{ $ringkasan['komponen']['daya_hidup'] }}</td>
                    </tr>
                    <tr>
                        <td>Bobot Badan Rata-rata</td>
                        <td>{{ $ringkasan['komponen']['bobot_badan'] }}</td>
                    </tr>
                    <tr>
                        <td>Umur Panen Rata-rata</td>
                        <td>{{ $ringkasan['komponen']['umur'] }}</td>
                    </tr>
                    <tr>
                        <td>FCR (Feed Conversion Ratio)</td>
                        <th>{{ $ringkasan['komponen']['fcr'] ?? 'N/A' }}</th>
                    </tr>
                    <tr class="table-primary">
                        <th>Index Performance (IP/EEF)</th>
                        <th>{{ $ringkasan['data']['ringkasan']['ip'] ?? 'N/A' }}</th>
                    </tr>
                </table>
            </div>
        </div>
    @endif
    
    <div id="populasiCard" class="card mb-3 d-none">

        <div class="card-header">
            <h5 class="card-title">Data Populasi Kandang</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h6 style="color: white" class="card-title">Populasi Awal</h6>
                            <h3 style="color: white" class="card-text" id="populasiAwal">0 Ekor</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <div style="display: flex; align-items: center; justify-content: space-between;">
                                <h6 style="color: white" class="card-title">Ayam Mati</h6>
                                <h5 style="color: white; font-size: 14px;" class="card-text" id="persentaseMati">(0%)</h5>
                            </div>
                            <h3 style="color: white" class="card-text" id="ayamMati">0 Ekor</h3>
                        </div>
                    </div>
                </div>
                
                
                
               
                <div class="col-md-3">
                    <div class="card bg-warning text-dark">
                        <div class="card-body">
                            <h6 style="color: white" class="card-title">Ayam Sisa</h6>
                            <h3 style="color: white" class="card-text" id="ayamSisa">0 Ekor</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h6 style="color: white" class="card-title">Ayam Terpanen</h6>
                            <h3 style="color: white" class="card-text" id="ayamPanen">0 Ekor</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title">Konversi Pakan ke Daging</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <canvas id="konversiChart" height="20"></canvas>
                </div>
                <div class="col-md-6">
                    <div class="mt-3">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Total Berat Pakan</strong></td>
                                <td id="totalPakan">0 Kg</td>
                            </tr>
                            <tr>
                                <td><strong>Total Berat Daging</strong></td>
                                <td id="totalDaging">0 Kg</td>
                            </tr>
                            <tr>
                                <td><strong>Rasio Konversi</strong></td>
                                <td id="rasioKonversi">0%</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if(isset($dataPanen) && $dataPanen['success'])
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title">Ringkasan Data Panen</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Kolom Kiri -->
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td style="width: 40%"><strong>KANDANG</strong></td>
                            <td>{{ $data->first()->kandang->nama_kandang ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>UMUR RATA-RATA</strong></td>
                            <td>{{ number_format($dataPanen['data']['ringkasan']['umur_rata_rata'], 2) }} Hari</td>
                        </tr>
                        <tr>
                            <td><strong>TOTAL PAKAN TERPAKAI</strong></td>
                            <td>
                                @foreach($dataPanen['data']['ringkasan']['data_pakan'] as $pakan)
                                    {{ $pakan->nama_pakan }} {{ number_format($pakan->total_qty) }} Bag<br>
                                @endforeach
                                <strong>Total: {{ number_format($dataPanen['data']['ringkasan']['total_pakan_terpakai']) }} Kg</strong>
                            </td>
                        </tr>
                    </table>
                </div>
                <!-- Kolom Kanan -->
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td style="width: 40%"><strong>BOBOT PANEN RATA-RATA</strong></td>
                            <td>{{ number_format($dataPanen['data']['ringkasan']['bobot_panen_rata_rata'], 3) }} Kg</td>
                        </tr>
                        <tr>
                            <td><strong>DAYA HIDUP</strong></td>
                            <td>{{ number_format($dataPanen['data']['ringkasan']['daya_hidup'], 2) }} %</td>
                        </tr>
                        <tr>
                            <td><strong>FCR</strong></td>
                            <td>{{ number_format($dataPanen['data']['ringkasan']['fcr'], 3) }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Detail Panen -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title">Detail Data Panen</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Tanggal Panen</th>
                        <th>Umur (Hari)</th>
                        <th>Persentase Panen</th>
                        <th>Jumlah Panen</th>
                        <th>Total BB Panen (Kg)</th>
                        <th>BB Rata-rata (Kg)</th>
                        <th>Age Quantity</th>
                        <th>Harga Per Ekor</th>
                        <th>Total Terjual</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dataPanen['data']['records'] as $record)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($record->tanggal_panen)->format('d/m/Y') }}</td>
                            <td>{{ $record->umur }}</td>
                            <td>{{ number_format($record->persen_panen, 3) }}%</td>
                            <td>{{ number_format($record->jumlah_panen) }}</td>
                            <td>{{ number_format($record->total_bb_panen, 2) }}</td>
                            <td>{{ number_format($record->total_bb_panen / $record->jumlah_panen, 3) }}</td>
                            <td>{{ number_format($record->age_quantity) }}</td>
                            <td>{{ number_format($record->harga, 2, ',', '.') }}</td>
                            <td>{{ number_format($record->total_panen, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="table-active">
                        <td colspan="2"><strong>Total</strong></td>
                        <td><strong>{{ number_format($dataPanen['data']['total']['total_persen'] ?? 0, 3) }}%</strong></td>
                        <td><strong>{{ number_format($dataPanen['data']['total']['total_jumlah'] ?? 0) }}</strong></td>
                        <td><strong>{{ number_format($dataPanen['data']['total']['total_bb'] ?? 0, 0) }}</strong></td>
                        <td>-</td>
                        <td><strong>{{ number_format($dataPanen['data']['total']['total_age_quantity'] ?? 0) }}</strong></td>
                        <td>
                            <strong>{{ number_format($dataPanen['data']['total']['average_harga'] ?? 0, 0, ',', '.') }}</strong>
                        </td>
                        <td>
                            <strong>{{ number_format($dataPanen['data']['total']['total_panen'] ?? 0, 0, ',', '.') }}</strong>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    @if(!empty($estimasiPembelian))
    <h4>Estimasi Pembelian</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Uraian</th>
                <th>QTY</th>
                <th>Satuan</th>
                <th>Harga Satuan</th>
                <th>Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <!-- DOC -->
            <tr>
                <td>DOC</td>
                <td>{{ number_format($ayam->qty_ayam) ?? 0 }}</td>
                <td>Ekor</td>
                <td>{{ number_format($ayam->doc->harga, 0, ',', '.') }}</td>
                <td>Rp {{ number_format($estimasiPembelian['doc']['total_harga'] ?? 0) }}</td>
            </tr>
            <!-- Pakan -->
            @foreach($estimasiPembelian['pakan'] as $pakan)
                <tr>
                    <td>{{ $pakan->nama_pakan }}</td>
                    <td>{{ number_format($pakan->total_qty) ?? 0 }}</td>
                    <td>Kg</td>
                    <td>{{ number_format($pakan->harga, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($pakan->total_harga) }}</td>
                </tr>
            @endforeach
            <!-- Obat -->
            <tr>
                <td>Obat & Vitamin</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>Rp {{ number_format($estimasiPembelian['obat'] ?? 0) }}</td>
            </tr>
            <!-- Pakan Tersisa -->
            @if(!empty($estimasiPembelian['pakan_tersisa_detail']))
                <tr class="table-active">
                    <td colspan="5" class="text-center"><strong>Pakan Tersisa</strong></td>
                </tr>
                @foreach($estimasiPembelian['pakan_tersisa_detail'] as $tersisa)
                    <tr>
                        <td>{{ $tersisa->nama_pakan }}</td>
                        <td>{{ number_format($tersisa->total_berat, 0, ',', '.') }}</td>
                        <td>Kg</td>
                        <td>{{ number_format($tersisa->harga, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($tersisa->total_cost, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            @endif
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4"><strong>Total Pembelian</strong></td>
                <td><strong>Rp {{ number_format($estimasiPembelian['total_pembelian'] ?? 0) }}</strong></td>
            </tr>
        </tfoot>
    </table>
@endif


@if(!empty($penjualan))
    <h4>PENJUALAN</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>Uraian</th>
                <th>Rata-Rata Berat</th>
                <th>Satuan</th>
                <th>Harga Satuan</th>
                <th>Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>Penjualan Daging</td>
                <td>{{ number_format($penjualan['penjualan_daging']['qty'] ?? 0) }}</td>
                <td>Kg</td>
                <td>Rp {{ number_format($penjualan['penjualan_daging']['harga_satuan'] ?? 0) }}</td>
                <td>Rp {{ number_format($penjualan['total_penjualan'] ?? 0) }}</td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5"><strong>TOTAL PENJUALAN</strong></td>
                <td>Rp {{ number_format($penjualan['total_penjualan'] ?? 0) }}</td>
            </tr>
            <tr>
                <td colspan="5">BONUS FCR</td>
                <td>Rp {{ number_format($penjualan['bonus_fcr'] ?? 0) }}</td>
            </tr>
            <tr>
                <td colspan="5">BONUS KEMATIAN</td>
                <td>Rp {{ number_format($penjualan['bonus_kematian'] ?? 0) }}</td>
            </tr>
            <tr class="table-active">
                <td colspan="5"><strong>LABA (PENJUALAN - PEMBELIAN)</strong></td>
                <td><strong>Rp {{ number_format($penjualan['laba'] ?? 0) }}</strong></td>
            </tr>
        </tfoot>
    </table>
@endif

@else
    <div class="alert alert-info">
        Pilih periode untuk melihat data panen
    </div>
@endif
    
    
    <div class="card mb-4">
        {{-- <div class="card-body">
            <h5 class="card-title">Performance Trends</h5>
            <canvas id="performanceChart" height="100"></canvas>
        </div> --}}
    </div>
</div>
@endsection