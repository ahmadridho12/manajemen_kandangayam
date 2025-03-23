@extends('layout.main')

@push('script')
    <script src="{{ asset('sneat/vendor/libs/apex-charts/apexcharts.js') }}"></script>
    <script>
        // Inisialisasi chart dengan data default
        let filteredOptions = {
            chart: {
                type: 'bar',
                height: 450,
                stacked: false
            },
            colors: ['#E0115F', '#008000', '#4e73df'], // Warna: [Pakan Keluar, Pakan Masuk, Sisa]
            series: [
                {
                    name: 'Pakan Keluar',
                    data: []
                }, 
                {
                    name: 'Pakan Masuk',
                    data: []
                },
                {
                    name: 'Sisa',
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
                        text: 'Jumlah Pakan (Keluar/Masuk)'
                    }
                },
                {
                    opposite: true,
                    title: {
                        text: 'Sisa Pakan'
                    }
                }
            ],
            title: {
                text: 'Grafik Pakan Masuk dan Pakan Keluar Berdasarkan Periode',
                align: 'center'
            },
            tooltip: {
                shared: true,
                intersect: false
            }
        };

        // Tunggu DOM selesai dimuat sebelum menginisialisasi chart
        document.addEventListener('DOMContentLoaded', function() {
            // Pastikan elemen chart ada
            if (document.querySelector("#filtered-chart")) {
                // Buat instance chart
                let filteredChart = new ApexCharts(document.querySelector("#filtered-chart"), filteredOptions);
                filteredChart.render();
                
                // Load data default dengan nilai filter saat ini (jika ada)
                loadChartData();
                
                // Event listener untuk form filter
                if (document.getElementById('filterTerpadu')) {
                    document.getElementById('filterTerpadu').addEventListener('submit', function(e) {
                        e.preventDefault();
                        loadChartData();
                        
                        // Redirect untuk memperbarui tabel dengan filter yang sama
                        const id_ayam = document.getElementById('id_ayam').value;
                        const id_kandang = document.getElementById('id_kandang').value;
                        window.location.href = `{{ route('pakan.monitoringpakan.index') }}?id_ayam=${id_ayam}&id_kandang=${id_kandang}`;
                    });
                }
                
                // Fungsi untuk memuat data chart
                function loadChartData() {
                    const submitBtn = document.querySelector('#filterTerpadu button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memuat...';
                    }
                    
                    // Ambil nilai filter dari form
                    let id_ayam = document.getElementById('id_ayam')?.value || '';
                    let id_kandang = document.getElementById('id_kandang')?.value || '';
                    
                    // Debug
                    console.log('Memuat data untuk ayam_id:', id_ayam, 'kandang_id:', id_kandang);
                    
                    // Panggil endpoint untuk mengambil data chart dengan parameter filter
                    fetch(`{{ route('monitoringpakan.chart-data') }}?id_ayam=${id_ayam}&id_kandang=${id_kandang}`)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Respons jaringan tidak berhasil: ' + response.statusText);
                            }
                            return response.json();
                        })
                        .then(data => {
                            console.log('Data diterima:', data);
                            
                            if (!data.success) {
                                throw new Error(data.message || 'Gagal memuat data');
                            }
                            
                            // Jika data kosong
                            if (!data.labels || data.labels.length === 0) {
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
                                        name: 'Pakan Keluar',
                                        data: [0]
                                    }, 
                                    {
                                        name: 'Pakan Masuk',
                                        data: [0]
                                    },
                                    {
                                        name: 'Sisa',
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
                                        name: 'Pakan Keluar',
                                        data: data.qty_keluar_series
                                    }, 
                                    {
                                        name: 'Pakan Masuk',
                                        data: data.qty_masuk_series
                                    },
                                    {
                                        name: 'Sisa',
                                        type: 'line',
                                        data: data.pakan_series
                                    }
                                ]);
                                
                                // Tampilkan informasi total
                                document.getElementById('chart-info').innerHTML = `
                                    <div class="row mt-3">
                                        <div class="col-md-6">
                                            <div class="card bg-light">
                                                <div class="card-body text-center">
                                                    <h5>Total Pakan Keluar</h5>
                                                    <h3 class="text-danger">${data.total_keluar}</h3>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card bg-light">
                                                <div class="card-body text-center">
                                                    <h5>Total Pakan Masuk</h5>
                                                    <h3 class="text-success">${data.total_masuk}</h3>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `;
                            }
                            
                            // Tambahkan info periode ke judul
                            let periodeText = 'Semua Periode';
                            if (id_ayam && document.getElementById('id_ayam')) {
                                const selectElement = document.getElementById('id_ayam');
                                const selectedOption = selectElement.options[selectElement.selectedIndex];
                                if (selectedOption) {
                                    periodeText = selectedOption.text;
                                }
                            }
                                
                            // Update judul chart
                            filteredChart.updateOptions({
                                title: {
                                    text: `Grafik Monitoring Pakan - ${periodeText}`
                                }
                            });
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            
                            if (document.getElementById('chart-info')) {
                                document.getElementById('chart-info').innerHTML = `
                                    <div class="alert alert-danger mt-3">
                                        Terjadi kesalahan saat memuat data: ${error.message}
                                    </div>
                                `;
                            }
                        })
                        .finally(() => {
                            // Aktifkan kembali tombol submit
                            if (submitBtn) {
                                submitBtn.disabled = false;
                                submitBtn.innerHTML = 'Filter';
                            }
                        });
                }
            } else {
                console.error("Elemen dengan ID 'filtered-chart' tidak ditemukan");
            }
        });
    </script>
@endpush

@section('content')
    <x-breadcrumb :values="[__('Pakan'), __('Monitoring Pakan')]"></x-breadcrumb>
    <div class="container">
        <!-- Form Filter Terpadu -->
        <div class="card mb-4">
            <div class="card-body">
                <form id="filterTerpadu" method="GET" action="{{ route('pakan.monitoringpakan.index') }}" class="row g-3">
                    <div class="col-md-4 mb-3">
                        <label for="id_ayam">{{ __('Periode') }}</label>
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
                        <label for="id_kandang">{{ __('Kandang') }}</label>
                        <select id="id_kandang" name="id_kandang" class="form-control">
                            <option value="">{{ __('Semua Kandang') }}</option>
                            @foreach($kandangs as $kandang)
                                <option value="{{ $kandang->id_kandang }}" {{ request('id_kandang') == $kandang->id_kandang ? 'selected' : '' }}>
                                    {{ $kandang->nama_kandang }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3 align-self-end">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">{{ __('Filter') }}</button>
                            <a href="{{ route('pakan.monitoringpakan.index') }}" class="btn btn-secondary">{{ __('Reset') }}</a>
                            <a href="{{ route('pakan.monitoringpakan.print', ['id_ayam' => request('id_ayam'), 'id_kandang' => request('id_kandang')]) }}" target="_blank" class="btn btn-success">{{ __('Print') }}</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    
        <!-- Container untuk chart -->
        <div id="filtered-chart" class="mb-4"></div>
        
        <!-- Container untuk informasi tambahan -->
        <div id="chart-info"></div>
    </div>

    <div class="card mb-5">
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>{{ __('Periode') }}</th>
                        <th>{{ __('Tanggal') }}</th>
                        <th>{{ __('Hari') }}</th>
                        <th>{{ __('Total Masuk') }}</th>
                        <th>{{ __('Total Berat') }}</th>
                        <th>{{ __('Keluar') }}</th>
                        <th>{{ __('Transfer Masuk') }}</th>
                        <th>{{ __('Sisa') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @if($data && $data->count())
                        @foreach($data as $pa)
                            <tr>
                                <td>{{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}</td>
                                <td>{{ $pa->ayam->periode ?? 'Tidak Ada' }}</td>
                                <td>{{ $pa->tanggal }}</td>
                                <td>{{ $pa->day }}</td>
                                <td>{{ $pa->total_masuk }} </td>
                                <td>{{ $pa->total_berat }} Kg</td>
                                <td>{{ $pa->keluar }} </td>
                                <td>
                                    {{-- Tampilkan data transfer jika ada dan memenuhi syarat transfer masuk --}}
                                    @if($pa->transfer_id && $pa->transfer && $pa->transfer->kandang_tujuan_id == $pa->ayam->kandang_id)
                                        {{ $pa->total_transfer }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $pa->sisa }} </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="9" class="text-center">
                                {{ __('menu.general.empty') }}
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
    
    {!! $data->appends(['id_ayam' => request('id_ayam'), 'id_kandang' => request('id_kandang')])->links() !!} 
@endsection