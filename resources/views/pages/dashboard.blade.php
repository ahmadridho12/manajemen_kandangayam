@extends('layout.main')

@push('style')
<link rel="stylesheet" href="{{ asset('sneat/vendor/libs/apex-charts/apex-charts.css') }}" />
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.bootstrap5.min.css">
<link rel="stylesheet" href="{{ asset('sneat/vendor/libs/apex-charts/apex-charts.css') }}" />
@endpush

@push('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
  // Chart "Hari ini"
  const todayOptions = {
  chart: { type: 'bar' },
  colors: ['#008000', '#E0115F', '#4e73df'], // Warna per batang
  plotOptions: {
    bar: {
      distributed: true,  // <-- ini penting biar tiap data dapat warna dari colors
      borderRadius: 4,
      horizontal: false
    }
  },
  dataLabels: { enabled: true },
  series: [{
    name: '{{ __('Transaksi Ayam') }}',
    data: [{{ $todayayammasuk }}, {{ $todayayammati }}, {{ $todaypanen }}]
  }],
  xaxis: {
    categories: [
      '{{ __('Ayam Masuk') }}',
      '{{ __('Ayam Mati') }}',
      '{{ __('Panen') }}'
    ]
  }
};

const todayChart = new ApexCharts(document.querySelector("#today-graphic"), todayOptions);
todayChart.render();

  // DataTables
  $(document).ready(function() {
    const table = $('#dataTable').DataTable({
      dom: 'Bfrtip',
      buttons: [
        { extend: 'print', text: 'Print', exportOptions: { columns: ':visible' } },
        { extend: 'excel', text: 'Export Excel', className: 'btn-success', exportOptions: { columns: ':visible' } },
        { extend: 'pdf', text: 'PDF', exportOptions: { columns: ':visible' } }
      ],
      language: { paginate: { previous: '<i class="bx bx-chevron-left"></i>', next: '<i class="bx bx-chevron-right"></i>' } }
    });
    table.buttons().container().appendTo('#exportDropdown');
    $('.dt-buttons').hide();
    $('#printTable').on('click', () => table.button('.buttons-print').trigger());
    $('#exportExcel').on('click', e => { e.preventDefault(); table.button('.buttons-excel').trigger(); });
  });
</script>
@endpush

@push('script')
<script>
  // Chart terfilter (periode + kandang)
  let filteredChart;

  const initFiltered = () => {
    const opts = {
      chart: { type: 'bar', height: 450 },
      colors: ['#E0115F','#008000','#4e73df'],
      series: [
        { name: 'Ayam Mati', data: [] },
        { name: 'Ayam Panen', data: [] },
        { name: 'Populasi', type: 'line', data: [] }
      ],
      xaxis: { categories: [] },
      yaxis: [
        { title: { text: 'Jumlah Ayam (Mati/Panen)' } },
        { opposite: true, title: { text: 'Populasi Ayam' } }
      ],
      title: { text: 'Grafik Ayam Mati dan Panen Berdasarkan Periode', align: 'center' },
      tooltip: { shared: true, intersect: false }
    };

    filteredChart = new ApexCharts(document.querySelector('#filtered-chart'), opts);
    filteredChart.render();
  };

  // Jalankan saat DOM selesai dimuat
  document.addEventListener('DOMContentLoaded', function() {
    initFiltered();

    const chartForm = document.getElementById('chartFilterForm');
    if (chartForm) {
      chartForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const btn = this.querySelector('button[type="submit"]');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Loading...';

        const id_ayam = document.getElementById('id_ayam').value;
        const id_kandang = document.getElementById('id_kandang').value;

        fetch(`{{ route('dashboard.chart-data') }}?id_ayam=${id_ayam}&id_kandang=${id_kandang}`)
          .then(res => res.json())
          .then(data => {
            if (!data.success) throw new Error(data.message || 'Gagal memuat data');

            if (data.labels.length === 0) {
              document.getElementById('chart-info').innerHTML = `<div class="alert alert-warning mt-3">Tidak ada data untuk filter yang dipilih.</div>`;
              filteredChart.updateOptions({ xaxis: { categories: ['Tidak ada data'] } });
              filteredChart.updateSeries([
                { name: 'Ayam Mati', data: [0] },
                { name: 'Ayam Panen', data: [0] },
                { name: 'Populasi', data: [0] }
              ]);
            } else {
              filteredChart.updateOptions({ xaxis: { categories: data.labels } });
              filteredChart.updateSeries([
                { name: 'Ayam Mati', data: data.qty_mati_series },
                { name: 'Ayam Panen', data: data.qty_panen_series },
                { name: 'Populasi', type: 'line', data: data.populasi_series }
              ]);

              document.getElementById('chart-info').innerHTML = `
                <div class="row mt-3">
                  <div class="col-md-6">
                    <div class="card bg-light text-center p-3">
                      <h5>Total Ayam Mati</h5>
                      <h3 class="text-danger">${data.total_mati}</h3>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="card bg-light text-center p-3">
                      <h5>Total Ayam Panen</h5>
                      <h3 class="text-success">${data.total_panen}</h3>
                    </div>
                  </div>
                </div>`;
            }

            filteredChart.updateOptions({
              title: {
                text: `Grafik Ayam Mati dan Panen - ${id_ayam ? document.getElementById('id_ayam').selectedOptions[0].text : 'Semua Periode'}`
              }
            });

            btn.disabled = false;
            btn.innerText = 'Filter';
          })
          .catch(err => {
            document.getElementById('chart-info').innerHTML = `<div class="alert alert-danger mt-3">Terjadi kesalahan: ${err.message}</div>`;
            btn.disabled = false;
            btn.innerText = 'Filter';
          });
      });
    }
  });
</script>
@endpush

<style>
  .dt-buttons { display: none !important; }
  .dataTables_filter, .dataTables_length { display: none; }
</style>

@section('content')
    {{-- Filter Form untuk periode & kandang --}}
    <div class="container mb-4">
        <form method="GET" action="{{ route('home') }}" class="row g-3" id="topFilterForm">
            <div class="col-md-4">
                <label for="id_ayam" class="form-label">Periode</label>
                <select name="id_ayam" id="id_ayam" class="form-select">
                    <option value="">{{ __('Pilih Periode') }}</option>
                    @foreach($ayams as $ayam)
                        <option value="{{ $ayam->id_ayam }}" {{ request('id_ayam') == $ayam->id_ayam ? 'selected' : '' }}>
                            {{ $ayam->periode }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label for="id_kandang" class="form-label">Kandang</label>
                <select name="id_kandang" id="id_kandang" class="form-select">
                    <option value="">{{ __('Semua Kandang') }}</option>
                    @foreach($kandangs as $kandang)
                        <option value="{{ $kandang->id_kandang }}" {{ request('id_kandang') == $kandang->id_kandang ? 'selected' : '' }}>
                            {{ $kandang->nama_kandang }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
        </form>
    </div>

    {{-- Konten Dashboard --}}
    <div class="row">
        <div class="col-lg-8 mb-4 order-0">
            <!-- Greeting Card -->
            <div class="card mb-4">
                <div class="d-flex align-items-end row">
                    <div class="col-sm-7">
                        <div class="card-body">
                            <h4 class="card-title text-primary">{{ $greeting }}</h4>
                            <p class="mb-4">{{ $currentDate }}</p>
                            <p class="text-gray" style="font-size: smaller">*) {{ __('dashboard.today_report') }}</p>
                        </div>
                    </div>
                    <div class="col-sm-5 text-center text-sm-left">
                        <div class="card-body pb-0 px-0 px-md-4">
                            <img src="{{ asset('sneat/img/mail.png') }}" height="200" alt="Illustration">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grafik Transaksi Ayam Hari Ini -->
            <div class="mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between flex-sm-row flex-column gap-3">
                            <div>
                                <div class="card-title mb-2">
                                    <h5>{{ __('Grafik Transaksi Ayam') }}</h5>
                                    <span class="badge bg-label-warning rounded-pill">{{ __('dashboard.today') }}</span>
                                </div>
                                <div class="mt-sm-auto">
                                    <div class="mb-3">
                                        <h5>Ayam Masuk</h5>
                                        <h3 class="display-4">{{ $todayayammasuk }}</h3>
                                        @if($percentageChange > 0)
                                            <small class="text-success fw-semibold"><i class="bx bx-chevron-up"></i> {{ abs($percentageChange) }}%</small>
                                        @elseif($percentageChange < 0)
                                            <small class="text-danger fw-semibold"><i class="bx bx-chevron-down"></i> {{ abs($percentageChange) }}%</small>
                                        @endif
                                    </div>
                                    <div class="mb-3">
                                        <h5>Ayam Mati</h5>
                                        <h3 class="display-4">{{ $todayayammati }}</h3>
                                        @if($percentageChangekeluar > 0)
                                            <small class="text-success fw-semibold"><i class="bx bx-chevron-up"></i> {{ abs($percentageChangekeluar) }}%</small>
                                        @elseif($percentageChangekeluar < 0)
                                            <small class="text-danger fw-semibold"><i class="bx bx-chevron-down"></i> {{ abs($percentageChangekeluar) }}%</small>
                                        @endif
                                    </div>
                                    <div>
                                        <h5>Panen</h5>
                                        <h3 class="display-4">{{ $todaypanen }}</h3>
                                    </div>
                                </div>
                            </div>
                            <div id="profileReportChart" style="width: 80%; min-height: 80px;"><div id="today-graphic"></div></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kartu Metrik Harian -->
        <div class="col-lg-4 col-md-4 order-1">
            <div class="row">
                <div class="col-lg-6 col-md-12 col-6 mb-4">
                    <x-dashboard-card-simple 
                    :label="__('Total Ayam Mati')"
                    :value="$mati" 
                    :daily="true" 
                    color="warning" 
                    icon="bx bx-ghost"
                    :percentage="$percentageIncomingLetter"  
                    :route="'sistem.keluar.index'" />

                </div>
                <div class="col-lg-6 col-md-12 col-6 mb-4">
                    <x-dashboard-card-simple 
                    :label="__('Total DOC')"
                    :value="$doc" 
                    :daily="true" 
                    color="primary" 
                    icon="bx bx-log-in"
                    :percentage="$percentageIncomingLetter"  
                    :route="'sistem.masuk.index'" />

                </div>
                <div class="col-lg-6 col-md-12 col-6 mb-4">
                    <x-dashboard-card-simple 
                    :label="__('Pakan Tersisa')"
                    :value="$stokpakan" 
                    :daily="true" 
                    color="primary" 
                    icon="bx-category"
                    :percentage="$percentageIncomingLetter"  
                    :route="'pakan.stokpakan.index'" />
                    
                </div>
                
                <div class="col-lg-6 col-md-12 col-6 mb-4">
                    <x-dashboard-card-simple 
                    :label="__('Jenis Pakan')"
                    :value="$pakan" 
                    :daily="true" 
                    color="primary" 
                    icon="bx-category"
                    :percentage="$percentageIncomingLetter"  
                    :route="'lainnya.pakan.index'" />
                    
                </div>
                <div class="col-lg-6 col-md-12 col-6 mb-4">
                    <x-dashboard-card-simple 
                    :label="__('Abk')"
                    :value="$abk" 
                    :daily="true" 
                    color="primary" 
                    icon="bx bx-user"
                    :percentage="$percentageIncomingLetter"  
                    :route="'lainnya.abk.index'" />

                </div>
                <div class="col-lg-6 col-md-12 col-6 mb-4">
                    <x-dashboard-card-simple 
                    :label="__('Kandang')"
                    :value="$jumlahKandang" 
                    :daily="true" 
                    color="primary" 
                    icon="bx bx-home"
                    :percentage="$percentageIncomingLetter"  
                    :route="'lainnya.kandang.index'" />
                </div>
            </div>
        </div>
    </div>

    <!-- Grafik & Info Periode -->
    <div class="container mt-4">
        {{-- <h2>Dashboard Transaksi Ayam</h2> --}}
        <div id="filtered-chart"></div>
        <div id="chart-info"></div>
    </div>
    @push('script')
    <script src="{{ asset('sneat/vendor/libs/apex-charts/apexcharts.js') }}"></script>
    <script>
        // Tunggu hingga DOM selesai dimuat sebelum menjalankan kode
        document.addEventListener('DOMContentLoaded', function() {
            // Inisialisasi chart dengan data default
            const filteredOptions = {
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

            // Pastikan elemen filtered-chart ada sebelum membuat chart
            const chartElement = document.querySelector("#filtered-chart");
            if (!chartElement) {
                console.warn('Elemen #filtered-chart tidak ditemukan');
                return;
            }

            // Buat instance chart
            const filteredChart = new ApexCharts(chartElement, filteredOptions);
            filteredChart.render();

            // Fungsi untuk memuat data dan update chart
            function loadChartData(id_ayam, id_kandang) {
                const chartInfoElement = document.getElementById('chart-info');
                
                // Pastikan elemen chart-info ada
                if (!chartInfoElement) {
                    console.warn('Elemen #chart-info tidak ditemukan');
                    return;
                }
                
                // Tampilkan loading indicator
                chartInfoElement.innerHTML = `
                    <div class="text-center my-3">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Memuat data grafik...</p>
                    </div>
                `;
                
                // Log untuk debugging
                console.log('Memuat data untuk ayam_id:', id_ayam, 'kandang_id:', id_kandang);
                
                // Panggil endpoint untuk mengambil data
                fetch(`{{ route('dashboard.chart-data') }}?id_ayam=${id_ayam}&id_kandang=${id_kandang}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok: ' + response.statusText);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Data diterima:', data);
                        
                        if (!data.labels || data.labels.length === 0) {
                            // Tampilkan pesan jika tidak ada data
                            chartInfoElement.innerHTML = `
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
                            chartInfoElement.innerHTML = `
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
                        
                        // Tambahkan info periode ke judul jika ada elemen dropdown periode
                        const idAyamSelect = document.getElementById('id_ayam');
                        let periodeText = 'Semua Periode';
                        
                        if (idAyamSelect && id_ayam) {
                            const selectedOption = idAyamSelect.options[idAyamSelect.selectedIndex];
                            if (selectedOption) {
                                periodeText = selectedOption.text;
                            }
                        }
                            
                        // Update judul chart
                        filteredChart.updateOptions({
                            title: {
                                text: `Grafik Ayam Mati dan Panen - ${periodeText}`
                            }
                        });
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        
                        // Tampilkan pesan error jika elemen chart-info masih ada
                        if (chartInfoElement) {
                            chartInfoElement.innerHTML = `
                                <div class="alert alert-danger mt-3">
                                    Terjadi kesalahan saat memuat data: ${error.message}
                                </div>
                            `;
                        }
                    });
            }

            // Tambahkan event listener untuk form filter jika ada
            const topFilterForm = document.getElementById('topFilterForm');
            if (topFilterForm) {
                topFilterForm.addEventListener('submit', function() {
                    // Ini akan dijalankan saat form di-submit
                    // Tidak perlu preventDefault() karena halaman akan dimuat ulang
                    
                    // Pengiriman data sudah ditangani oleh form submit biasa
                });
            } else {
                console.warn('Form filter dengan ID topFilterForm tidak ditemukan');
            }

            // Muat data awal ketika halaman sudah dimuat
            const id_ayam = document.getElementById('id_ayam')?.value || '';
            const id_kandang = document.getElementById('id_kandang')?.value || '';
            loadChartData(id_ayam, id_kandang);
        });
    </script>
@endpush
@endsection