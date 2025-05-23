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
<style>
    .text-green-500 {
        color: #10B981 !important;
    }
    .text-red-500 {
        color: #EF4444 !important;
    }
</style>
@section('content')
<x-breadcrumb :values="[__('Monitoring '), __('Perkembangan Ayam')]">
    <a href="{{ route('inventory.monitoring.create') }}" class="btn btn-primary">
        {{ __('Tambah Ayam') }}
    </a>
</x-breadcrumb>
<div class="card-body">
    <form method="GET" action="{{ route('inventory.monitoring.index') }}" class="row g-3">
        <div class="col-md-4">
            <label for="ayam_id">{{ __('Filter Periode') }}</label>
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
            <a href="{{ route('inventory.monitoring.index') }}" class="btn btn-secondary">{{ __('Reset') }}</a>
            <a href="{{ route('inventory.monitoring.print', ['id_ayam' => request('id_ayam'), 'id_kandang' => request('id_kandang')]) }}" target="_blank" class="btn btn-success">{{ __('Print') }}</a>

        </div>
    </form>
</div>
</div>

    <div class="card mb-5">
        <div class="table-responsive text-nowrap">
            <table class="table">
              <thead>
                <tr>
                    <th rowspan="2" style="text-align: center; background-color: #10b93d; color: white;">No</th>
                    <th rowspan="2" style="text-align: center; background-color: #10b93d; color: white;">Periode</th>
                    <th rowspan="2" style="text-align: center; background-color: #10b93d; color: white;">Kandang</th>
                    <th rowspan="2" style="text-align: center; background-color: #10b93d; color: white;">Tanggal</th>
                    <th rowspan="2" style="text-align: center; background-color: #10b93d; color: white;">Hari</th>

                    @php
                         $maxSkat = $data->map(fn($item) => $item->kandang->jumlah_skat ?? 0)->max();
                    @endphp

                    @for ($i = 1; $i <= $maxSkat; $i++)
                        <th colspan="2" style="text-align: center; background-color: #10b93d; color: white;">Skat {{ $i }}</th>
                    @endfor

                    <th rowspan="2" style="text-align: center; background-color: #10b93d; color: white;">Body Weight</th>
                    <th rowspan="2" style="text-align: center; background-color: #10b93d; color: white;">Daily Gain</th>
                    <th rowspan="2" style="text-align: center; background-color: #10b93d; color: white;">Action</th>
                </tr>
                <tr>
                    @for ($i = 1; $i <= $maxSkat; $i++)
                        <th style="background-color: #10B981; color: white;">BW</th>
                        <th style="background-color: #106db9; color: white;">DG</th>
                    @endfor
                </tr>
                </thead>

                @if($data && $data->count())
                    <tbody>
                    @foreach ($data as $mt)
                    <tr>
                        <td>{{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}</td>
                        <td>{{ $mt->ayam->periode }}</td>
                        <td>{{ $mt->kandang->nama_kandang }}</td>
                        <td>{{ $mt->tanggal }}</td>
                        <td>{{ $mt->age_day }}</td>

                        @for ($i = 1; $i <= $mt->kandang->jumlah_skat; $i++)
                            @php
                                $bw = $mt->{"skat_{$i}_bw"};
                                $dg = $mt->{"skat_{$i}_dg"};
                                $standard = \App\Services\MonitoringGeneratorService::getStandard($mt->age_day);
                            @endphp
                            <td class="{{ $bw >= $standard['bw'] ? 'text-green-500' : 'text-red-500' }}">
                                {{ $bw }}
                            </td>
                            <td class="{{ $dg >= $standard['dg'] ? 'text-green-500' : 'text-red-500' }}">
                                {{ $dg }}
                            </td>

                        @endfor

                        <td>{{ $mt->body_weight }}</td>
                        <td>{{ $mt->daily_gain }}</td>

                        <td>
                            <button class="btn btn-info btn-sm btn-edit"
                                data-id="{{ $mt->id }}"
                                data-tanggal="{{ $mt->tanggal }}"
                                data-jumlah-skat="{{ $mt->kandang->jumlah_skat }}"
                                @for ($i = 1; $i <= $mt->kandang->jumlah_skat; $i++)
                                    data-skat_{{ $i }}_bw="{{ $mt->{'skat_'.$i.'_bw'} }}"
                                @endfor
                            >
                                Edit
                            </button>

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
        </div>
    </div>

    {!! $data->appends(['search' => $search])->links() !!} 

  <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" id="editForm">
      @csrf
      @method('PUT')
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editModalLabel">Edit Monitoring Ayam</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="tanggal_monitoring" id="editTanggal">
          <input type="hidden" name="jumlah_skat" id="jumlahSkat">

          <div class="row" id="edit-skat-inputs">
              {{-- Input akan dibuat via JavaScript --}}
          </div>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Simpan</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        </div>
      </div>
    </form>
  </div>
</div>

@push('script')
<script>
  function generateEditSkatInputs(jumlahSkat, data) {
    const container = document.getElementById('edit-skat-inputs');
    container.innerHTML = '';

    for (let i = 1; i <= jumlahSkat; i++) {
      const inputValue = data[`skat_${i}_bw`] || '';
      const div = document.createElement('div');
      div.classList.add('col-sm-12', 'col-12', 'col-md-6', 'col-lg-3');
      div.innerHTML = `
        <label for="skat_${i}_bw" class="form-label">Skat ${i} BW</label>
        <input type="number" name="skat_${i}_bw" id="skat_${i}_bw" class="form-control" step="0.01" value="${inputValue}" required>
      `;
      container.appendChild(div);
    }
  }

  $(document).on('click', '.btn-edit', function () {
    const id = $(this).data('id');
    const tanggal = $(this).data('tanggal');
    const jumlahSkat = parseInt($(this).data('jumlah-skat')) || 4;

    const data = {};
    for (let i = 1; i <= jumlahSkat; i++) {
      data[`skat_${i}_bw`] = $(this).data(`skat_${i}_bw`);
    }

    $('#editForm').attr('action', '/inventory/monitoring/' + id);
    $('#editTanggal').val(tanggal);
    $('#jumlahSkat').val(jumlahSkat);
    
    generateEditSkatInputs(jumlahSkat, data);
    $('#editModal').modal('show');
  });
</script>
@endpush


@endsection