@extends('layout.main')

@push('script')
    <script>
        $(document).on('click', '.btn-toggle', function () {
            const target = $(this).data('target');
            $(target).collapse('toggle');
        });
    </script>
@endpush

@section('content')
    <x-breadcrumb :values="[__('Stok')]"></x-breadcrumb> 

    <div class="card mb-5">
        <div class="table-responsive" style="max-height: 400px; overflow-y: scroll;">
            <table class="table">
                <thead>
                <tr>
                    <th>No</th>
                    <th>{{ __('Kode Barang') }}</th>
                    <th>{{ __('Nama Barang') }}</th>
                    <th>{{ __('Kode Jenis') }}</th>
                    <th>{{ __('Nama Jenis') }}</th>
                    <th>{{ __('Jumlah') }}</th>
                    <th>{{ __('Total') }}</th>
                    <th>{{ __('Aksi') }}</th>
                </tr>
                </thead>
                <tbody>
                    @php
                    $grandTotal = 0; // Inisialisasi variabel untuk menyimpan total keseluruhan
                @endphp
                @foreach($data as $stok)
                    <tr>
                        <td>{{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}</td>
                        <td>{{ $stok->barangg->kode_barang ?? '-' }}</td>
                        <td>{{ $stok->barangg->deskripsi ?? '-' }}</td>
                        <td>{{ $stok->barangg->jenis->kode ?? '-' }}</td>
                        <td>{{ $stok->barangg->jenis->nama ?? '-' }}</td>
                        <td>{{ $stok->detailStok->sum('qty_stok') }}</td> <!-- Total qty_stok -->
                        {{-- <td>Rp. {{ number_format($stok->detailStok->harga * $stok->detailStok->qty_stok, 0, ',', '.') }}</td> --}}
                         @php
                            // Hitung total untuk stok ini
                            $totalStok = $stok->detailStok->sum(function($detail) {
                                return $detail->detailMasuk->harga_setelah_ppn * $detail->qty_stok;
                            });
                            $grandTotal += $totalStok; // Tambahkan ke grand total
                        @endphp

                        <td>
                            Rp. {{ number_format($totalStok, 2, ',', '.') }} <!-- Tampilkan total untuk stok ini -->
                        </td>


                        <td>
                            <button class="btn btn-primary btn-toggle" data-target="#collapse{{ $stok->id_stok }}">
                                Detail
                            </button>
                        </td>
                    </tr>
                    <!-- Row for collapse (Detail Stok) -->
                    <tr id="collapse{{ $stok->id_stok }}" class="collapse">
                        <td colspan="7">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th style="font-size: 14px">No</th>
                                        <th style="font-size: 14px">{{ __('Nama Barang') }}</th>
                                        <th style="font-size: 14px">{{ __('Kode Barang') }}</th>
                                        <th style="font-size: 14px">{{ __('Harga Satuan') }}</th>
                                        <th style="font-size: 14px">{{ __('QTY') }}</th>
                                        <th style="font-size: 14px">{{ __('Total') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($stok->detailStok as $detail)
                                        <tr>
                                            <td style="font-size: 14px">{{ chr(96 + $loop->iteration) }}</td>
                                            <td style="font-size: 14px">{{ $detail->barangg->deskripsi }}</td>
                                            <td style="font-size: 14px">{{ $detail->barangg->kode_barang }}</td>
                                            <td style="font-size: 14px">Rp.{{ number_format($detail->detailMasuk->harga_setelah_ppn, 2, ',', '.') }}</td>
                                            <td style="font-size: 14px">{{ $detail->qty_stok }}</td>
                                            <td style="font-size: 14px">
                                                Rp.{{ number_format($detail->detailMasuk->harga_setelah_ppn * $detail->qty_stok, 2, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {!! $data->appends(['search' => $search])->links() !!}
@endsection
