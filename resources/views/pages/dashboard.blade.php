@extends('layout.main')

@push('style')
<link rel="stylesheet" href="{{ asset('sneat/vendor/libs/apex-charts/apex-charts.css') }}" />
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.bootstrap5.min.css">
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
    {{-- <div class="row">
        <!-- Kolom Tabel Stok Menipis -->
        <div class="col-md-7 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{ __('Stok Menipis') }}</h5>
                    <table class="table">
                        <thead style="background-color: #4e73df">
                            <tr>
                                <th style="color: white">{{ __('Kode Barang') }}</th>
                                <th style="color: white">{{ __('Nama Barang') }}</th>
                                <th style="color: white">{{ __('Total Stok') }}</th>
                                <th style="color: white">{{ __('Status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($lowStockItems->isNotEmpty())
                                @foreach($lowStockItems as $item)
                                    <tr>
                                        <td>{{ $item->kode_barang }}</td>
                                        <td>{{ $item->deskripsi }}</td>
                                        <td style="font-size: 14px">
                                            @if (floor($item->total_stok) == $item->total_stok)
                                                {{ intval($item->total_stok) }} <!-- Tampilkan sebagai integer jika tidak ada desimal -->
                                            @else
                                                {{ number_format($item->total_stok, 1, ',', '.') }} <!-- Tampilkan dengan satu desimal -->
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-danger">{{ __('Low') }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4" class="text-center">{{ __('Tidak ada barang dengan stok rendah') }}</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
    
                    <!-- Navigasi Paginasi -->
                    <div class="d-flex justify-content-center mt-3">
                        {{ $lowStockItems->links() }}
                    </div>
                </div>
            </div>
        </div>
    
        <!-- Kolom Chart Top Selling Items -->
        <div class="col-md-5 mb-4">
            <div class="card">
                <div class="card-body">
                    <canvas id="topSellingItemsChart"></canvas>
                </div>
            </div>
        </div>
    </div> --}}
    


    {{-- barnag perjenis --}}

    {{-- <div class="row">
        <div class="col-md-5 mb-4">
            <div class="card p-3">
                <div class="card-body">
                    <h5 class="card-title">{{ __('Barang Per Jenis') }}</h5>
                    <canvas id="barangPerJenisChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-7 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">{{ __('Barang Per Jenis') }}</h5>
                    <div class="dropdown">
                        <button class="btn btn-link" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bx bx-dots-vertical-rounded"></i>
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="exportDropdown">
                            <li>
                                <a class="dropdown-item" href="#" id="printTable">
                                    <i class="bx bx-printer me-2"></i> Cetak
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#" id="exportExcel">
                                    <i class="bx bx-file me-2"></i> Export Excel
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <table id="dataTable" class="table table-striped">
                        <thead style="background-color:#4e73df">
                            <tr>
                                <th style="color: white">{{ __('Nama Jenis') }}</th>
                                <th style="color: white">{{ __('Total Qty ') }}</th>
                                <th style="color: white">{{ __('Total Saldo') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(!empty($barangPerJenis))
                                @foreach($barangPerJenis as $id_jenis => $item)
                                    <tr>
                                        <td>{{ $item['nama_jenis'] ?? '-' }}</td>
                                        <td>{{ $item['total_qty'] ?? 0 }}</td>
                                        <td>{{ number_format($item['total_uang'] ?? 0, 2, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                                <tr style="font-weight: bold;">
                                    <td></td>
                                    <td>{{ $grandTotalQty }}</td>
                                    <td>{{ number_format($grandTotalUang, 2, ',', '.') }}</td>
                                </tr>
                            @else
                                <tr>
                                    <td colspan="4" class="text-center">{{ __('Tidak ada data barang per jenis') }}</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endsection --}}
    {{-- @push('script') --}}
        {{-- <script src="{{ asset('sneat/vendor/libs/apex-charts/apexcharts.js') }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> --}}
        {{-- <script>
            document.addEventListener('DOMContentLoaded', function() {
                
                // Inisialisasi Grafik Top Selling Items
                const ctx2 = document.getElementById('topSellingItemsChart').getContext('2d');
                const originalData = [
                    @foreach($topSellingItems as $item)
                        {{ $item->total_terjual }},
                    @endforeach
                ];

                new Chart(ctx2, {
                    type: 'polarArea',
                    
                    data: {
                        labels: [
                            @foreach($topSellingItems as $item)
                                "{{ $item->deskripsi }}",
                            @endforeach
                        ],
                        datasets: [{
                            label: '{{ __("Top Selling Items") }}',
                            data: originalData,
                            backgroundColor: [
                                '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796'
                            ],
                            borderColor: '#fff',
                            borderWidth: 1,
                        }]
                    },
                    options: {
                        responsive: true,
                        animation: {
                            duration: 5000,
                            easing: 'easeOutBounce',
                        },
                        plugins: {
                            legend: {
                                display: false,
                            },
                            title: {
                                display: true,
                                text: '{{ __("Barang Terlaris Bulan Lalu") }}'
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(tooltipItem) {
                                        return tooltipItem.label + ': ' + tooltipItem.raw;
                                    }
                                }
                            }
                        },
                        layout: {
                            padding: {
                                bottom: 10
                            }
                        },
                        scales: {
                            r: {
                                ticks: {
                                    display: false
                                },
                                grid: {
                                    color: '#eee'
                                }
                            }
                        }
                    }
                });

                // Inisialisasi Grafik Barang Per Jenis (Lingkaran)
                const ctx3 = document.getElementById('barangPerJenisChart').getContext('2d');
                const barangPerJenisData = [
                    @foreach($barangPerJenis as $item)
                        {{ $item['total_uang'] ?? 0 }},
                    @endforeach
                ];

                new Chart(ctx3, {
                    type: 'pie', // Ganti 'bar' menjadi 'pie' atau 'doughnut'
                    data: {
                        labels: [
                            @foreach($barangPerJenis as $item)
                                "{{ $item['nama_jenis'] ?? '-' }}",
                            @endforeach
                        ],
                        datasets: [{
                            label: '{{ __('Jumlah total per Jenis') }}',
                            data: barangPerJenisData,
                            backgroundColor: [
                            // Warna Utama
                            '#4e73df',  // Blue
                            '#1cc88a',  // Green
                            '#36b9cc',  // Cyan
                            '#f6c23e',  // Yellow
                            '#e74a3b',  // Red
                            '#858796',  // Gray
                            '#28a745',  // Dark Green
                            '#17a2b8',  // Teal

                            // Tambahan Warna
                            '#6a5acd',  // Slate Blue
                            '#ff6347',  // Tomato
                            '#3cb371',  // Medium Sea Green
                            '#1e90ff',  // Dodger Blue
                            '#ffa500',  // Orange
                            '#9370db',  // Medium Purple
                            '#20b2aa',  // Light Sea Green
                            '#cd5c5c',  // Indian Red
                            '#8b008b',  // Dark Magenta
                            '#00ced1'   // Dark Turquoise
                        ],
                            borderColor: '#fff',
                            borderWidth: 1,
                        }]
                    },
                    options: {
                        animation: {
                            duration: 1000,
                            easing: 'linear',
                        },
                        responsive: true,
                        plugins: {
                            legend: {
                                display: false,
                            },
                            title: {
                                display: true,
                                text: '{{ __('Barang Per Jenis') }}'
                            }
                        }
                    }
                });
            });
        </script> --}}
    {{-- @endpush --}}
@endsection

