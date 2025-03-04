@extends('layout.main')

@push('script')

<script>
    // $(document).on('click', '.btn-edit', function () {
    //     const id = $(this).data('id');
    //     const periode = $(this).data('periode'); // keterangan dari tabel permintaan
    //     const tanggal_masuk = $(this).data('tanggal_masuk'); // tgl_permintaan dari tabel permintaan
    //     const tanggal_selesai = $(this).data('tanggal_selesai'); // bagian dari tabel permintaan
    //     const qty_ayam = $(this).data('qty_ayam'); // tipe_id dari tabel permintaan
    //     const status = $(this).data('status'); // tipe_id dari tabel permintaan
    //     const kandang_id = $(this).data('kandang_id'); // tipe_id dari tabel permintaan
        
    //     $('#editModal form').attr('action', '{{ route("sistem.masuk.update", ":id") }}'.replace(':id', id));

    //     // Isi field-field di modal
    //     $('#periode').val(periode);
    //     $('#tanggal_masuk').val(tanggal_masuk);
    //     $('#tanggal_selesai').val(tanggal_selesai);
    //     $('#qty_ayam').val(qty_ayam);
    //     $('#status').val(status);

    //     // Set selected option untuk bagian
    //     $('#edit-kandang').val(kandang);

    //     // Set selected option untuk tipe
    //     });
</script>
@endpush

@section('content')
    <x-breadcrumb :values="[__('Gaji'), __('Penggajian Petugas')]">
        <a href="{{ route('gaji.penggajian.create') }}" class="btn btn-primary">
            {{ __('Tambah Penggajian') }}
        </a>
    </x-breadcrumb>

    <div class="card mb-5">
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>{{ __('Kandang') }}</th>
                        <th>{{ __('Periode') }}</th>
                        <th>{{ __('Hasil Pemeliharaan') }}</th>
                        {{-- <th>{{ __('Berat Awal') }}</th> --}}
                        <th>{{ __('Total Potongan') }}</th>
                        <th>{{ __('Hasil Bersih') }}</th>
                        <th>{{ __('Keterangan') }}</th>
                        <th>{{ __('Tanggal') }}</th>
                        <th>{{ __('Aksi') }}</th>
                    </tr>
                </thead>
                @if($data && $data->count())
                    <tbody>
                        @foreach($data as $pgaji)
                            <tr>
                                <td>{{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}</td>
                                <td>{{ $pgaji->kandang->nama_kandang }}</td>
                                <td>
                                    <a style="color:black" href="{{ route('gaji.penggajian.show', $pgaji->id_perhitungan) }}">
                                        <strong>{{ $pgaji->ayam->periode }}</strong>
                                    </a>
                                </td>                               
                                <td>Rp. {{ number_format($pgaji->hasil_pemeliharaan, 2, ',', '.') }} </td>
                                {{-- <td>{{ $ayam->berat_awal }}</td> --}}
                                <td>Rp. {{ number_format($pgaji->total_potongan, 2, ',', '.') }}</td>                                <td>{{ $pgaji->hasil_setelah_potongan }}</td>
                                <td>{{ $pgaji->keterangan }}</td>
                                <td>{{ $pgaji->created_at }}</td>
                                <td>
                                    <button class="btn btn-info btn-sm btn-edit"
                                            data-id="{{ $pgaji->id_perhitungan }}"
                                            data-kandang_id="{{ $pgaji->kandang_id }}"
                                            data-ayam_id="{{ $pgaji->ayam_id }}"
                                            data-hasil_pemeliharaan="{{ $pgaji->hasil_pemeliharaan }}"
                                            data-keterangan="{{ $pgaji->keterangan }}"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editModal">
                                        {{ __('menu.general.edit') }}
                                    </button>
                                    <form action="" method="post" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm btn-delete" type="button">Hapus</button>
                                    </form>
                                    
                                    
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                @else
                    <tbody>
                        <tr>
                            <td colspan="7" class="text-center">
                                {{ __('menu.general.empty') }}
                            </td>
                        </tr>
                    </tbody>
                @endif
            </table>
        </div>
    </div>

    {!! $data->appends(['search' => $search])->links() !!} 

    {{-- <!-- Edit Modal -->
    <div class="modal fade" id="editModal" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" method="post" action="{{ route('sistem.masuk.update', $ayam->id_ayam) }}">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalTitle">{{ __('menu.general.edit') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="Periode" class="form-label">{{ __('Periode') }}</label>
                        <input type="string" class="form-control" id="periode" name="periode" required>
                    </div>
                    <div class="mb-3">
                        <label for="tanggal_masuk" class="form-label">{{ __('Tanggal Masuk') }}</label>
                        <input type="date" class="form-control" id="tanggal_masuk" name="tanggal_masuk" required>
                    </div>
                    <div class="mb-3">
                        <label for="tanggal_selesai" class="form-label">{{ __('Tanggal Masuk') }}</label>
                        <input type="date" class="form-control" id="tanggal_selesai" name="tanggal_selesai" required>
                    </div>
                   
                    <div class="mb-3">
                        <label for="qty_ayam" class="form-label">{{ __('Jumlah') }}</label>
                        <input type="number" class="form-control" id="qty_ayam" name="qty_ayam" value="0" required>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">{{ __('Status') }}</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="active">{{ __('Active') }}</option>
                            <option value="inactive">{{ __('Inactive') }}</option>
                        </select>
                    </div>
                    
                    
                    <div class="mb-3">
                        <label class="form-label">Kandang</label>
                        <select class="form-control" name="kandang_id" id="edit-kandang">
                            @foreach($kandangs as $kandang)
                                <option value="{{ $kandang->id_kandang }}" {{ $kandang->id_kandang == $ayam->kandang_id ? 'selected' : '' }}>{{ $kandang->nama_kandang }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Tutup') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Simpan Perubahan') }}</button>
                </div>
            </form>
        </div>
    </div> --}}
@endsection