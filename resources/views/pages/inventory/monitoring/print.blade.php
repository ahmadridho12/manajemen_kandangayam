<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Populasi</title>
    <script>
        window.onload = function() {
            window.print();
        };
    </script>
    <style>
        @media print {
            @page {
                size: A4; /* Ukuran kertas A4 */
                margin: 0; /* Menghapus margin default */
            }
            body {
                font-size: 12px; /* Ukuran font lebih kecil */
                margin: 10mm; /* Memberikan margin pada body */
                font-family: 'Calibri', sans-serif; /* Menerapkan font Calibri */
            }
        }

        body {
            font-family: 'Calibri', sans-serif;
            margin: 10mm;
            font-size: 12px;
        }
        .header {
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            padding: 0px;
        }
        .logo {
            width: 48px;
            height: auto;
            margin-right: 0px;
        }
        .company-info {
            flex-grow: 1;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            margin: 0;
            text-align: left;
        }
        .report-info {
            margin: 20px 0;
        }
        .report {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
        }
        .text-green-500 {
        color: #10B981 !important;
    }
    .text-red-500 {
        color: #EF4444 !important;
    }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ asset('ayam.png') }}" alt="" class="logo">
        <div class="company-info">
            <div class="company-name">NC FARM</div>
        </div>
    </div>
    <div class="report">
        <h2>Laporan Berat Ayam</h2>
    </div>
    <div class="report-info">
        <p>Periode: {{ $periode }}</p>
        <p>Kandang: {{ $kandang }}</p>
        <p>Tanggal Cetak: {{ date('d/m/Y') }}</p>
    </div>

    <table class="table">
        <thead>
            
        <tr>
            
            {{-- <th>{{ __('menu.general.action') }}</th> --}}

            <th rowspan="2" width="2%" style="font-size: 14px;  text-align: center; vertical-align: middle; background-color: #10b93d; color:white">No</th>
            <th rowspan="2" width="15%" style="font-size: 14px; text-align: center; vertical-align: middle;  background-color: #10b93d; color:white">{{ __('Periode') }}</th>
            <th rowspan="2" width="25%" style="font-size: 14px;  text-align: center; vertical-align: middle; background-color: #10b93d; color:white">{{ __('Kandang') }}</th>
            <th rowspan="2" width="4%" style="font-size: 14px;  text-align: center; vertical-align: middle; background-color: #10b93d; color:white">{{ __('tanggal') }}</th>
            <th rowspan="2" width="8%" style="font-size: 14px; text-align: center; vertical-align: middle; background-color: #10b93d; color:white">
                {{ __('Hari') }}
            </th>                    <th colspan="2" width="30%" style="font-size: 14px; text-align: center;  background-color: #10b93d; color:white" >{{ __('Skat 1') }}</th>
            <th colspan="2" width="30%" style="font-size: 14px; text-align: center;  background-color: #10b93d; color:white">{{ __('Skat 2') }}</th>
            <th colspan="2" width="30%" style="font-size: 14px; text-align: center;  background-color: #10b93d; color:white">{{ __('Skat 3') }}</th>
            <th colspan="2" width="30%" style="font-size: 14px; text-align: center;  background-color: #10b93d; color:white">{{ __('Skat 4') }}</th>
            
            <th rowspan="2" width="15%" style="font-size: 14px; text-align: center; vertical-align: middle; background-color: #10b93d; color:white">{{ __('Body Weight') }}</th>
            <th rowspan="2" width="15%" style="font-size: 14px;  text-align: center; vertical-align: middle; background-color: #10b93d; color:white">{{ __('Daily Again') }}</th>
        </tr>
        <tr>
            <th width="5%" style="font-size: 14px; background-color: #10B981; color:white">BW</th>
            <th width="7%" style="font-size: 14px; background-color: #106db9; color:white">DG</th>
            <th width="5%" style="font-size: 14px; background-color: #10B981; color:white">BW</th>
            <th width="7%" style="font-size: 14px; background-color: #106db9; color:white">DG</th>
            <th width="5%" style="font-size: 14px; background-color: #10B981; color:white">BW</th>
            <th width="7%" style="font-size: 14px; background-color: #106db9; color:white">DG</th>
            <th width="5%" style="font-size: 14px; background-color: #10B981; color:white">BW</th>
            <th width="7%" style="font-size: 14px; background-color: #106db9; color:white">DG</th>
      
            
        </tr>
        </thead>
        @if($data && $data->count())
            <tbody>
                
                @foreach($data as $mt)
                
                @if ($data instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <td>{{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}</td>
            @else
                <td>{{ $loop->iteration }}</td>
            @endif
                            <td>{{ $mt->ayam->periode }}</td>
                <td>{{ $mt->kandang->nama_kandang }}</td>
                <td>{{ $mt->tanggal }}</td>
                <td>{{ $mt->age_day }}</td>
                
                <td class="{{ $mt->skat_1_bw_status == 'above' ? 'text-green-500' : 'text-red-500' }}">
                    {{ $mt->skat_1_bw }}
                </td>
                <td class="{{ $mt->skat_1_dg_status == 'above' ? 'text-green-500' : 'text-red-500' }}">
                    {{ $mt->skat_1_dg }}
                </td>
                
                <td class="{{ $mt->skat_2_bw_status == 'above' ? 'text-green-500' : 'text-red-500' }}">
                    {{ $mt->skat_2_bw }}
                </td>
                <td class="{{ $mt->skat_2_dg_status == 'above' ? 'text-green-500' : 'text-red-500' }}">
                    {{ $mt->skat_2_dg }}
                </td>
                
                <td class="{{ $mt->skat_3_bw_status == 'above' ? 'text-green-500' : 'text-red-500' }}">
                    {{ $mt->skat_3_bw }}
                </td>
                <td class="{{ $mt->skat_3_dg_status == 'above' ? 'text-green-500' : 'text-red-500' }}">
                    {{ $mt->skat_3_dg }}
                </td>
                
                <td class="{{ $mt->skat_4_bw_status == 'above' ? 'text-green-500' : 'text-red-500' }}">
                    {{ $mt->skat_4_bw }}
                </td>
                <td class="{{ $mt->skat_4_dg_status == 'above' ? 'text-green-500' : 'text-red-500' }}">
                    {{ $mt->skat_4_dg }}
                </td>
                
                <td>{{ $mt->body_weight }}</td>
                <td>{{ $mt->daily_gain }}</td>
                
           
                    
                    <td>
                        {{-- <button class="btn btn-info btn-sm btn-edit"
                                data-id="{{ $p->id_populasi }}"
                                data-tanggal="{{ $p->tanggal }}"
                                data-day="{{ $p->day }}"
                                data-qty_now="{{ $p->qty_now }}"
                                data-qty_mati="{{ $p->qty_mati }}"
                                data-qty_panen="{{ $p->qty_panen }}"
                                data-total="{{ $p->total }}"
                                >
                            {{ __('menu.general.edit') }}
                        </button> --}}
                        <form action="" class="d-inline" method="post">
                            @csrf
                            @method('DELETE')
                            {{-- <button class="btn btn-danger btn-sm btn-delete"
                                    type="submit">{{ __('menu.general.delete') }}</button> --}}
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        @else
            <tbody>
            <tr>
                <td colspan="4" class="text-center">
                    {{ __('menu.general.empty') }}
                </td>
            </tr>
            </tbody>
        @endif
    </table>

    <div class="footer">
        <p>Dicetak oleh: {{ auth()->user()->name ?? 'Admin' }}</p>
        <p>Tanggal: {{ date('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>