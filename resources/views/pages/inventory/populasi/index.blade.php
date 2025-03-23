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
        document.getElementById('unifiedFilterForm').addEventListener('submit', function(e) {
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
            fetch(`{{ route('populasi.chart-data') }}?id_ayam=${id_ayam}&id_kandang=${id_kandang}`)
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
                    
                    // Redirect ke halaman tabel dengan parameter filter yang sama
                    window.location.href = `{{ route('inventory.populasi.index') }}?id_ayam=${id_ayam}&id_kandang=${id_kandang}`;
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

        // Load chart saat halaman dimuat dengan parameter yang ada di URL
        document.addEventListener('DOMContentLoaded', function() {
            // Ambil parameter dari URL
            const urlParams = new URLSearchParams(window.location.search);
            const id_ayam = urlParams.get('id_ayam') || '';
            const id_kandang = urlParams.get('id_kandang') || '';
            
            // Set nilai pada form
            document.getElementById('id_ayam').value = id_ayam;
            document.getElementById('id_kandang').value = id_kandang;
            
            // Jika ada parameter, load chart secara otomatis
            if (id_ayam || id_kandang) {
                // Tampilkan loading
                document.getElementById('chart-info').innerHTML = `
                    <div class="alert alert-info mt-3">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memuat data...
                    </div>
                `;
                
                // Panggil endpoint untuk data chart
                fetch(`{{ route('populasi.chart-data') }}?id_ayam=${id_ayam}&id_kandang=${id_kandang}`)
                    .then(response => response.json())
                    .then(data => {
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
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        
                        // Tampilkan pesan error
                        document.getElementById('chart-info').innerHTML = `
                            <div class="alert alert-danger mt-3">
                                Terjadi kesalahan saat memuat data: ${error.message}
                            </div>
                        `;
                    });
            }
        });
    </script>
@endpush

@section('content')
    <x-breadcrumb
    :values="[__('Monitoring'), __('Populasi Ayam')]">
    </x-breadcrumb>
    
    <div class="container">
        <!-- Form Filter Terpadu -->
        <div class="card mb-4">
            <div class="card-body">
                <form id="unifiedFilterForm" class="row g-3">
                    <div class="col-md-4 mb-3">
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
                    <div class="col-md-4 mb-3">
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
                    <div class="col-md-4 mb-3 align-self-end">
                        <button type="submit" class="btn btn-primary me-2">{{ __('Filter') }}</button>
                        <a href="{{ route('inventory.populasi.index') }}" class="btn btn-secondary me-2">{{ __('Reset') }}</a>
                        <a href="{{ route('inventory.populasi.print', ['id_ayam' => request('id_ayam'), 'id_kandang' => request('id_kandang')]) }}" target="_blank" class="btn btn-success">{{ __('Print') }}</a>
                    </div>
                </form>
            </div>
        </div>
    
        <!-- Container untuk chart -->
        <div id="filtered-chart"></div>
        
        <!-- Container untuk informasi tambahan -->
        <div id="chart-info"></div>
    </div>
    
    <div class="card mb-5 mt-4">
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                <tr>
                    <th>No</th>
                    <th>{{ __('Periode') }}</th>
                    <th>{{ __('Tanggal') }}</th>
                    <th>{{ __('Hari') }}</th>
                    <th>{{ __('Populasi') }}</th>
                    <th>{{ __('Jumlah Mati') }}</th>
                    <th>{{ __('Jumlah Panen') }}</th>
                    <th>{{ __('Total') }}</th>
                </tr>
                </thead>
                <tbody>
                @if($data && $data->count())
                    @foreach($data as $p)
                        <tr>
                            <td>{{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}</td>
                            <td>{{ $p->ayam->periode ?? 'Tidak Ada' }}</td>
                            <td>{{ $p->tanggal }}</td>
                            <td>{{ $p->day }}</td>
                            <td>{{ $p->qty_now }} Ekor</td>
                            <td>{{ $p->qty_mati }} Ekor</td>
                            <td>{{ $p->qty_panen }} Ekor</td>
                            <td>{{ $p->total }} Ekor</td>
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

    {!! $data->appends(['id_ayam' => request('id_ayam'), 'id_kandang' => request('id_kandang')])->links() !!}
@endsection