@extends('layout.main')

@push('script')

<script>
    $(document).on('click', '.btn-edit', function () {
        const id = $(this).data('id');
        const periode = $(this).data('periode');
        const tanggal_masuk = $(this).data('tanggal_masuk');
        const tanggal_selesai = $(this).data('tanggal_selesai');
        const qty_ayam = $(this).data('qty_ayam');
        const status = $(this).data('status');
        const kandang_id = $(this).data('kandang_id');
        
        // Perbaikan di sini - menggunakan ID form langsung
        $('#editForm').attr('action', '{{ route("sistem.masuk.update", ":id") }}'.replace(':id', id));

        // Isi field-field di modal
        $('#periode').val(periode);
        $('#tanggal_masuk').val(tanggal_masuk);
        $('#tanggal_selesai').val(tanggal_selesai);
        $('#qty_ayam').val(qty_ayam);
        $('#status').val(status);

        // Perbaikan di sini - menggunakan kandang_id bukan kandang
        $('#edit-kandang').val(kandang_id);
    });
</script>
@endpush

@section('content')
    <x-breadcrumb :values="[__('Ayam'), __('Ayam')]">
        <a href="{{ route('sistem.masuk.create') }}" class="btn btn-primary">
            {{ __('Tambah Ayam') }}
        </a>
    </x-breadcrumb>

    <div class="card mb-5">
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>{{ __('Periode') }}</th>
                        <th>{{ __('Tanggal Masuk') }}</th>
                        <th>{{ __('Tanggal Selesai') }}</th>
                        {{-- <th>{{ __('Berat Awal') }}</th> --}}
                        <th>{{ __('Populasi') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Kandang ID') }}</th>
                        <th>{{ __('Aksi') }}</th>
                    </tr>
                </thead>
                @if($data && $data->count())
                    <tbody>
                        @foreach($data as $ayam)
                            <tr>
                                <td>{{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}</td>
                                <td>{{ $ayam->periode }}</td>
                                <td>{{ $ayam->tanggal_masuk }}</td>
                                <td>{{ $ayam->tanggal_selesai }}</td>
                                {{-- <td>{{ $ayam->berat_awal }}</td> --}}
                                <td>{{ $ayam->qty_ayam }}</td>
                                <td>{{ $ayam->status }}</td>
                                <td>{{ $ayam->kandang->nama_kandang }}</td>
                                <td>
                                    <button class="btn btn-info btn-sm btn-edit"
                                            data-id="{{ $ayam->id_ayam }}"
                                            data-periode="{{ $ayam->periode }}"
                                            data-tanggal_masuk="{{ $ayam->tanggal_masuk }}"
                                            data-tanggal_selesai="{{ $ayam->tanggal_selesai }}"
                                            data-qty_ayam="{{ $ayam->qty_ayam }}"
                                            data-status="{{ $ayam->status }}"
                                            data-kandang_id="{{ $ayam->kandang_id }}"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editModal">
                                        {{ __('menu.general.edit') }}
                                    </button>
                                    <form action="{{ route('sistem.masuk.destroy', $ayam->id_ayam) }}" method="post" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm btn-delete" type="submit">Hapus</button>
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

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" method="post" id="editForm">
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
                        <label for="tanggal_selesai" class="form-label">{{ __('Tanggal Selesai') }}</label>
                        <input type="date" class="form-control" id="tanggal_selesai" name="tanggal_selesai" required>
                    </div>
                   
                    <div class="mb-3">
                        <label for="qty_ayam" class="form-label">{{ __('Jumlah') }}</label>
                        <input type="number" class="form-control" id="qty_ayam" name="qty_ayam" required>
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
                                <option value="{{ $kandang->id_kandang }}">{{ $kandang->nama_kandang }}</option>
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
    </div>
@endsection