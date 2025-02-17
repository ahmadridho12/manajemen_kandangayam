@extends('layout.main')

@push('script')
<script>
    $(document).ready(function() {
        $('.btn-hitung-ip').on('click', function() {
            const kandangId = $(this).data('kandang-id');
            const periode = $('#id_ayam').val();
            
            $.ajax({
                url: `/performa/hitung-ip/${kandangId}`,
                method: 'POST',
                data: {
                    periode: periode,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        alert('IP berhasil dihitung: ' + response.data.ip);
                        location.reload();
                    } else {
                        alert('Gagal menghitung IP: ' + response.message);
                    }
                },
                error: function(xhr) {
                    alert('Terjadi kesalahan: ' + xhr.responseJSON.message);
                }
            });
        });
    });
</script>
@endpush

@section('content')
    <x-breadcrumb :values="[__('Index Performance')]">
    </x-breadcrumb>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('ip.index') }}" class="row g-3">
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
                    <a href="{{ route('performa.index') }}" class="btn btn-secondary">{{ __('Reset') }}</a>
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

    {!! $data->appends(['search' => $search])->links() !!}
@endsection