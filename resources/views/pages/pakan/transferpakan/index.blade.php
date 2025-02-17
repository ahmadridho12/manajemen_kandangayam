@extends('layout.main')

{{-- @push('script')
    <script>
        $(document).on('click', '.btn-edit', function () {
            const id = $(this).data('id');
            $('#editModal form').attr('action', '{{ route('lainnya.kandang.update', '') }}/' + id);
            $('#editModal input:hidden#id').val(id);
            $('#editModal input#nama_kandang').val($(this).data('nama_kandang'));
            $('#editModal input#tanggal_mulai').val($(this).data('tanggal_mulai'));
            $('#editModal input#tanggal_selesai').val($(this).data('tanggal_selesai'));
        });
    </script>
@endpush --}}

@section('content')
<x-breadcrumb
:values="[__('Pakan'), __('Pakan Transfer')]">
<a href="{{ route('pakan.transferpakan.create') }}" class="btn btn-primary">
    {{ __('Tambah Trasfer Pakan') }}
</a>
         {{-- <a href="{{ route('create.permission.add') }}" class="btn btn-primary">{{ __('') }}</a>  --}}
</x-breadcrumb>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('pakan.transferpakan.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="populasi">{{ __('Filter Periode') }}</label>
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
                            <option value="{{ $kandang->id_kandang }}" {{ request('id_kandang') == $kandang->id ? 'selected' : '' }}>
                                {{ $kandang->nama_kandang }}
                            </option>
                        @endforeach
                    </select>
                </div>
               
                <div class="col-md-4 align-self-end">
                    <button type="submit" class="btn btn-primary">{{ __('Filter') }}</button>
                    <a href="{{ route('pakan.transferpakan.index') }}" class="btn btn-secondary">{{ __('Reset') }}</a>
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
                    {{-- <th>{{ __('Periode') }}</th> --}}
                    <th>{{ __('Kandang Asal') }}</th>
                    <th>{{ __('Kandang Tujuan') }}</th>
                    <th>{{ __('Periode Asal') }}</th>
                    <th>{{ __('Periode Tujuan') }}</th>
                    <th>{{ __('Nama Pakan') }}</th>
                    <th>{{ __('tanggal') }}</th>
                    <th>{{ __('Quantity') }}</th>
                    <th>{{ __('Berat') }}</th>
                    <th>{{ __(' total') }}</th>
                    <th>{{ __(' Keterangan') }}</th>
                </tr>
                </thead>
                <tbody>
                @if($data && $data->count())
                    @foreach($data as $pt)
                        <tr>
                            <td>{{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}</td>
                            {{-- <td>{{ $pt->ayam->periode ?? 'Tidak Ada' }}</td> --}}
                            <td>{{ $pt->kandangAsal->nama_kandang }}</td>
                            <td>{{ $pt->kandangTujuan->nama_kandang }}</td>
                            <td>{{ $pt->ayamAsal->periode }}</td>
                            <td>{{ $pt->ayamTujuan->periode }}</td>
                            <td>{{ $pt->pakan->nama_pakan ?? 'Tidak Ada'}}</td>
                           
                            <td>{{ $pt->tanggal }}</td>
                            <td>{{ $pt->qty }}</td>
                            <td>{{ $pt->berat_zak }}</td>
                            <td>{{ $pt->total_berat }}</td>
                            <td>{{ $pt->keterangan }}</td>
                            
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
  