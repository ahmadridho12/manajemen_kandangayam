@extends('layout.main')

@push('style')
<link rel="stylesheet" href="{{ asset('sneat/vendor/libs/apex-charts/apex-charts.css') }}" />
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.bootstrap5.min.css">
<link rel="stylesheet" href="{{ asset('sneat/vendor/libs/apex-charts/apex-charts.css') }}" />

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

@endpush

@push('script')
<script src="{{ asset('sneat/vendor/libs/apex-charts/apexcharts.js') }}"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>

    <script>
        const options = {
    chart: {
        type: 'bar',
        color: '#4e73df',
    },
    colors: [
        '#008000', // Warna untuk Barang Masuk
        '#E0115F', // Warna untuk Barang Keluar
        '#4e73df'  // Warna untuk Permintaan
    ],
    series: [{
        name: '{{ __('Transaksi Barang') }}',
        data: [
            {{ $todayayammasuk }},  // Sudah menggunakan sum dari controller
            {{ $todayayammati }}, // Sudah menggunakan sum dari controller
            {{ $todaypanen }}          // Tetap count untuk permintaan
        ]
    }],
    stroke: {
        curve: 'smooth',
    },
    xaxis: {
        categories: [
            '{{ __('Ayam Masuk') }}',
            '{{ __('Ayam Mati') }}',
            '{{ __('Panen') }}',
        ],
    }
}

        const chart = new ApexCharts(document.querySelector("#today-graphic"), options);

        chart.render();


        $(document).ready(function() {
            const table = $('#dataTable').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'print',
                        text: 'Print',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'excel',
                        text: 'Export Excel',
                        className: 'btn-success',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'pdf',
                        text: 'PDF',
                        exportOptions: {
                            columns: ':visible'
                        }
                    }
                ],
                language: {
                    paginate: {
                        previous: '<i class="bx bx-chevron-left"></i>',
                        next: '<i class="bx bx-chevron-right"></i>'
                    }
                }
            });

            // Sembunyikan tombol default
            table.buttons().container().appendTo('#exportDropdown');
            $('.dt-buttons').hide();

            // Event listener untuk print
            $('#printTable').on('click', function() {
                table.button('.buttons-print').trigger();
            });

            $('#exportExcel').on('click', function(e) {
            e.preventDefault();
            table.button('.buttons-excel').trigger();
        });
        });
    </script>
@endpush
@push('script')
    <script src="{{ asset('sneat/vendor/libs/apex-charts/apexcharts.js') }}"></script>
    <script>
        // Inisialisasi chart dengan data default
        // Inisialisasi chart dengan data default
// Inisialisasi chart dengan data default
let filteredOptions = {
    chart: {
        type: 'bar',
        height: 450,
        stacked: false
    },
    colors: ['#E0115F', '#008000', '#4e73df'], // Warna: [Ayam Mati, Ayam Panen, Populasi]
    series: [
        {
            name: 'Ayam Mati',
            data: []
        }, 
        {
            name: 'Ayam Panen',
            data: []
        },
        {
            name: 'Populasi',
            type: 'line',
            data: []
        }
    ],
    xaxis: {
        categories: []
    },
    yaxis: [
        {
            title: {
                text: 'Jumlah Ayam (Mati/Panen)'
            }
        },
        {
            opposite: true,
            title: {
                text: 'Populasi Ayam'
            }
        }
    ],
    title: {
        text: 'Grafik Ayam Mati dan Panen Berdasarkan Periode',
        align: 'center'
    },
    tooltip: {
        shared: true,
        intersect: false
    }
};

let filteredChart = new ApexCharts(document.querySelector("#filtered-chart"), filteredOptions);
filteredChart.render();

// Event listener untuk form filter
document.getElementById('chartFilterForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...';
    
    // Ambil nilai filter dari form
    let id_ayam = document.getElementById('id_ayam').value;
    let id_kandang = document.getElementById('id_kandang').value;
    
    // Tampilkan pesan loading di console untuk debug
    console.log('Memuat data untuk ayam_id:', id_ayam, 'kandang_id:', id_kandang);
    
    // Panggil endpoint untuk mengambil data chart dengan parameter filter
    fetch(`{{ route('dashboard.chart-data') }}?id_ayam=${id_ayam}&id_kandang=${id_kandang}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            console.log('Data diterima:', data);
            
            if (!data.success) {
                throw new Error(data.message || 'Gagal memuat data');
            }
            
            if (data.labels.length === 0) {
                // Tampilkan pesan jika tidak ada data
                document.getElementById('chart-info').innerHTML = `
                    <div class="alert alert-warning mt-3">
                        Tidak ada data untuk filter yang dipilih.
                    </div>
                `;
                
                // Update chart kosong
                filteredChart.updateOptions({
                    xaxis: {
                        categories: ['Tidak ada data']
                    }
                });
                
                filteredChart.updateSeries([
                    {
                        name: 'Ayam Mati',
                        data: [0]
                    }, 
                    {
                        name: 'Ayam Panen',
                        data: [0]
                    },
                    {
                        name: 'Populasi',
                        data: [0]
                    }
                ]);
            } else {
                // Update chart dengan data
                filteredChart.updateOptions({
                    xaxis: {
                        categories: data.labels
                    }
                });
                
                filteredChart.updateSeries([
                    {
                        name: 'Ayam Mati',
                        data: data.qty_mati_series
                    }, 
                    {
                        name: 'Ayam Panen',
                        data: data.qty_panen_series
                    },
                    {
                        name: 'Populasi',
                        type: 'line',
                        data: data.populasi_series
                    }
                ]);
                
                // Tampilkan informasi total
                document.getElementById('chart-info').innerHTML = `
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h5>Total Ayam Mati</h5>
                                    <h3 class="text-danger">${data.total_mati}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h5>Total Ayam Panen</h5>
                                    <h3 class="text-success">${data.total_panen}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }
            
            // Tambahkan info periode ke judul
            const periodeText = id_ayam ? 
                document.getElementById('id_ayam').options[document.getElementById('id_ayam').selectedIndex].text :
                'Semua Periode';
                
            // Update judul chart
            filteredChart.updateOptions({
                title: {
                    text: `Grafik Ayam Mati dan Panen - ${periodeText}`
                }
            });
            
            // Aktifkan kembali tombol submit
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Filter';
        })
        .catch(error => {
            console.error('Error:', error);
            
            // Tampilkan pesan error
            document.getElementById('chart-info').innerHTML = `
                <div class="alert alert-danger mt-3">
                    Terjadi kesalahan saat memuat data: ${error.message}
                </div>
            `;
            
            // Aktifkan kembali tombol submit
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Filter';
        });
});
    </script>
@endpush

<style>
     .dt-buttons {
        display: none !important;
    }
    .dataTables_filter,
    .dataTables_length {
        display: none;
    }
    
</style>
@section('content')
    <div class="row">
        <div class="col-lg-8 mb-4 order-0">
            <div class="card mb-4">
                <div class="d-flex align-items-end row">
                    <div class="col-sm-7">
                        <div class="card-body">
                            <h4 class="card-title text-primary">{{ $greeting }}</h4>
                            <p class="mb-4">
                                {{ $currentDate }}
                            </p>
                            <p style="font-size: smaller" class="text-gray">*) {{ __('dashboard.today_report') }}</p>
                        </div>
                    </div>
                    <div class="col-sm-5 text-center text-sm-left">
                        <div class="card-body pb-0 px-0 px-md-4">
                            <img src="{{asset('sneat/img/mail.png')}}" height="200"
                                 alt="View Badge User" data-app-dark-img="illustrations/man-with-laptop-dark.png"
                                 data-app-light-img="illustrations/mail.png">
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between flex-sm-row flex-column gap-3"
                             style="position: relative;">
                            <div class="">
                                <div class="card-title">
                                    <h5 class="text-nowrap mb-2">{{ __('Grafik Transaksi Barang') }}</h5>
                                    <span class="badge bg-label-warning rounded-pill">{{ __('dashboard.today') }}</span>
                                </div>
                                <div class="mt-sm-auto">
                                    <div>
                                        <h5>Ayam Masuk</h5>
                                        <h3 class="mb-0 display-4">{{ $todayayammasuk }}</h3>
                                        @if($percentageChange > 0)
                                            <small class="text-success text-nowrap fw-semibold">
                                                <i class="bx bx-chevron-up"></i> {{ abs($percentageChange) }}%
                                            </small>
                                        @elseif($percentageChange < 0)
                                            <small class="text-danger text-nowrap fw-semibold">
                                                <i class="bx bx-chevron-down"></i> {{ abs($percentageChange) }}%
                                            </small>
                                        @endif
                                    </div>
                                
                                    <div>
                                        <h5>Ayam Mati</h5>
                                        <h3 class="mb-0 display-4">{{ $todayayammati }}</h3>
                                        @if($percentageChangekeluar > 0)
                                            <small class="text-success text-nowrap fw-semibold">
                                                <i class="bx bx-chevron-up"></i> {{ abs($percentageChangekeluar) }}%
                                            </small>
                                        @elseif($percentageChangekeluar < 0)
                                            <small class="text-danger text-nowrap fw-semibold">
                                                <i class="bx bx-chevron-down"></i> {{ abs($percentageChangekeluar) }}%
                                            </small>
                                        @endif
                                    </div>
                                
                                    <div>
                                        <h5>Panen</h5>
                                        <h3 class="mb-0 display-4">{{ $todaypanen }}</h3>
                                    </div>
                                </div>
                            </div>
                            <div id="profileReportChart" style="min-height: 80px; width: 80%">
                                <div id="today-graphic"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-4 order-1">
            <div class="row">
                <div class="col-lg-6 col-md-12 col-6 mb-4">
                    <x-dashboard-card-simple
                        :label="__('Total Ayam Mati')"
                        :value="$todayayammati"
                        :daily="true"
                        color="primary"
                        icon="bx bx-ghost"
                        :percentage="$percentageIncomingLetter"
                    />
                </div>
                <div class="col-lg-6 col-md-12 col-6 mb-4">
                    <x-dashboard-card-simple
                        :label="__('Total Ayam Masuk')"
                        :value="$todayayammasuk"
                        :daily="true"
                        color="warning"
                        icon="bx bx-log-in"
                        :percentage="$percentageIncomingLetter"
                    />
                </div>
                <div class="col-lg-6 col-md-12 col-6 mb-4">
                    <x-dashboard-card-simple
                        :label="__('total Pakan/Zak')"
                        :value="$stokpakan" 
                        :daily="true"
                        color="danger"
                        icon="img:sack.png"
                        :percentage="$percentageIncomingLetter"
                    />

                </div>
                <div class="col-lg-6 col-md-12 col-6 mb-4">
                    <x-dashboard-card-simple
                        :label="__('Jenis Pakan')"
                        :value="$pakan"
                        :daily="true"
                        color="success"
                        icon="bx-category"
                        :percentage="$percentageIncomingLetter"
                    />
                </div>
                <div class="col-lg-6 col-md-12 col-6 mb-4">
                    <x-dashboard-card-simple
                        :label="__('Abk')"
                        :value="$abk"
                        :daily="true"
                        color="danger"
                        icon="bx bx-user"
                        :percentage="$percentageOutgoingLetter"
                    />
                </div>
                <div class="col-lg-6 col-md-12 col-6 mb-4">
                    <x-dashboard-card-simple
                        :label="__('Kandang')"
                        :value="$kandang"
                        :daily="true"
                        color="primary"
                        icon="bx bx-home"
                        :percentage="$percentageDispositionLetter"
                    />
                </div>
                
            </div>
            
        </div>
    </div>
    <div class="container">
        <h2>Dashboard Transaksi Ayam</h2>
    
        <!-- Form Filter -->
        <form id="chartFilterForm" class="mb-4">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="id_ayam">Periode</label>
                    <select name="id_ayam" id="id_ayam" class="form-control">
                        <option value="">{{ __('Pilih Periode') }}</option>
                        @foreach($ayams as $ayam)
                            <option value="{{ $ayam->id_ayam }}" {{ request('id_ayam') == $ayam->id_ayam ? 'selected' : '' }}>
                                {{ $ayam->periode }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="kandang">Kandang:</label>
                    <select id="id_kandang" name="id_kandang" class="form-control">
                        <option value="">Semua Kandang</option>
                        @foreach($kandangs as $kandang)
                            <option value="{{ $kandang->id_kandang }}"{{ request('id_kandang') == $kandang->id_kandang ? 'selected' : '' }}>
                                {{ $kandang->nama_kandang }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3 align-self-end">
                    <button type="submit" class="btn btn-primary">Filter</button>
                </div>
            </div>
        </form>
    
        <!-- Container untuk chart -->
        <div id="filtered-chart"></div>
        
        <!-- Container untuk informasi tambahan -->
        <div id="chart-info"></div>
    </div>
    
   

   
@endsection

