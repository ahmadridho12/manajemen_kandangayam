@extends('layout.main')

@push('script')

<script>
    $(document).on('click', '.btn-edit', function () {
    const id = $(this).data('id');
    const periode = $(this).data('periode');
    const tanggal_masuk = $(this).data('tanggal_masuk');
    const tanggal_selesai = $(this).data('tanggal_selesai');
    const rentang_hari = $(this).data('rentang_hari'); // gunakan nama berbeda
    const qty_ayam = $(this).data('qty_ayam');          // jumlah ayam
    const harga = $(this).data('harga');                // harga, jika diperlukan
    const doc_id = $(this).data('doc_id');              // doc_id
    const status = $(this).data('status');
    const kandang_id = $(this).data('kandang_id');
    
    // Ubah action form edit
    $('#editForm').attr('action', '{{ route("sistem.masuk.update", ":id") }}'.replace(':id', id));
    
    // Isi field-field di modal dengan variabel yang benar
    $('#periode').val(periode);
    $('#tanggal_masuk').val(tanggal_masuk);
    $('#tanggal_selesai').val(tanggal_selesai);
    $('#rentang_hari').val(rentang_hari);
    $('#qty_ayam').val(qty_ayam);
    $('#status').val(status);
    $('#edit-kandang').val(kandang_id);
    $('#edit-doc').val(doc_id);
});

</script>
@endpush

@section('content')
    <x-breadcrumb :values="[__('Ayam'), __('Ayam Masuk')]">
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
                        <th>{{ __('Rentang Hari') }}</th>
                        {{-- <th>{{ __('Berat Awal') }}</th> --}}
                        <th>{{ __('Populasi') }}</th>
                        <th>{{ __('Harga DOC') }}</th>
                        <th>{{ __('Total') }}</th>
                        {{-- <th>{{ __('Populasi') }}</th> --}}
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Kandang') }}</th>
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
                                <td>{{ $ayam->rentang_hari }}</td>
                                {{-- <td>{{ $ayam->berat_awal }}</td> --}}
                                <td>{{ $ayam->qty_ayam }}</td>
                                <td>{{number_format( $ayam->doc->harga, 0.2 , '.','.' )}} 
                                <td>Rp.{{number_format( $ayam->total_harga, 0.2 , '.','.' )}} </td>
                                <td>{{ $ayam->status }}</td>
                                <td>{{ $ayam->kandang->nama_kandang }}</td>
                                <td>
                                    <button class="btn btn-info btn-sm btn-edit"
                                            data-id="{{ $ayam->id_ayam }}"
                                            data-periode="{{ $ayam->periode }}"
                                            data-tanggal_masuk="{{ $ayam->tanggal_masuk }}"
                                            data-tanggal_selesai="{{ $ayam->tanggal_selesai }}"
                                            data-rentang_hari="{{ $ayam->rentang_hari }}"
                                            data-qty_ayam="{{ $ayam->qty_ayam }}"
                                            data-doc_id="{{ $ayam->doc_id }}"
                                            data-status="{{ $ayam->status }}"
                                            data-kandang_id="{{ $ayam->kandang_id }}"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editModal">
                                        {{ __('menu.general.edit') }}
                                    </button>
                                    <form action="{{ route('sistem.masuk.destroy', $ayam->id_ayam) }}" method="post" class="d-inline">
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
                        <label for="rentang_hari" class="form-label">Rentang Hari</label>
                        <input type="number" class="form-control" id="rentang_hari" name="rentang_hari" required>
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
                        <label class="form-label">Harga</label>
                        <select class="form-control" name="doc_id" id="edit-doc">
                            @foreach($docs as $doc)
                                <option value="{{ $doc->id_doc }}">{{ $doc->harga }}</option>
                            @endforeach
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