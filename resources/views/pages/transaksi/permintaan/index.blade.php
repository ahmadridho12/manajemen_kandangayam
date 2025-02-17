@extends('layout.main')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
.no-trans {
    font-weight: bolder;
}

.dropdown-content {
    display: none;
    position: absolute;
    background-color: #E0E0E0;
    min-width: 160px;
    box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
    z-index: 1;
}

.dropdown-content a {
    color: #404040;
    padding: 12px 16px;
    text-decoration: none;
    display: flex;
    align-items: center;
}

.dropdown-content a i {
    margin-right: 8px;
}

.dropdown-trigger:hover .dropdown-content {
    display: block;
}

.dropdown-trigger {
    position: relative;
    cursor: pointer;
}
.status-badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.8em;
}
.status-pending {
    background-color: #FFC107;
    color: #000;
}
.btn-success:hover {
    background-color: #28a745; /* Warna hijau lebih gelap */
    border-color: #218838; /* Border hijau lebih gelap */
    transform: scale(1.05); /* Efek zoom */
    transition: transform 0.2s; /* Transisi halus */
}
</style>
@push('script')
    <script>
    $(document).on('click', '.btn-edit', function () {
    const id = $(this).data('id'); // id_permintaan
    const keterangan = $(this).data('keterangan'); // keterangan dari tabel permintaan
    const tgl_permintaan = $(this).data('tgl_permintaan'); // tgl_permintaan dari tabel permintaan
    const bagian = $(this).data('bagian'); // bagian dari tabel permintaan
    const tipe_id = $(this).data('tipe_id'); // tipe_id dari tabel permintaan

    // Set form action dengan ID permintaan
    $('#editModal form').attr('action', '{{ route("transaksi.permintaan.update", ":id") }}'.replace(':id', id));

    // Isi field-field di modal
    $('#edit-keterangan').val(keterangan);
    $('#edit-tgl_permintaan').val(tgl_permintaan);

    // Set selected option untuk bagian
    $('#edit-bagian').val(bagian);

    // Set selected option untuk tipe
    $('#edit-tipe').val(tipe_id);
});
    </script>
@endpush

@section('content')
    <x-breadcrumb
    :values="[__('Permintaan'), __('Permintaan')]">
    <a href="{{ route('transaksi.permintaan.create') }}" class="btn btn-primary">
        {{ __('Tambah Permintaan') }}
    </a>
            {{-- <a href="{{ route('create.permission.add') }}" class="btn btn-primary">{{ __('') }}</a>  --}}
    </x-breadcrumb>


    <div class="card mb-5">
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                <tr>
                    <th>No</th>
                    <th>{{ __('No Trans') }}</th>
                    <th>{{ __('Bagian Meminta') }}</th>
                    <th>{{ __('Jenis') }}</th>
                    <th>{{ __('Tanggal') }}</th>
                    <th>{{ __('Keterangan') }}</th>
                    <th>{{ __('Nama') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th>{{ __('Tanggal Disetujui') }}</th>
                    <th>{{ __(' Disetujui') }}</th>
                    <th>{{ __('Aksi') }}</th>
                </tr>
                </thead>
                @if($data && $data->count())
                    <tbody>
                    @foreach($data as $permintaan)
                        <tr>
                            <td>{{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}</td>
                            <td>
                                <span class="no-trans">
                                    {{ $permintaan->no_trans ?? 'Belum diproses' }}
                                </span>
                            </td>
                            
                            <td>{{ $permintaan->bagiann->nama_bagian ?? '-' }}</td>
                            <td>{{ $permintaan->tipe->nama_tipe ?? '-' }}</td>
                            <td>{{ $permintaan->tgl_permintaan }}</td>
                            <td>{{ $permintaan->keterangan }}</td>
                            <td>{{ $permintaan->user->name ?? '-' }}</td>
                            <td>
                                @if($permintaan->status_persetujuan === 'pending')
                                    <span class="badge status-badge status-pending">
                                        Pending
                                    </span>
                                @else
                                    <span class="badge bg-success">Approved</span>
                                @endif
                            </td>
                            <td>{{ $permintaan->tanggal_persetujuan ?? '-' }}</td> <!-- Tampilkan tanggal disetujui -->
                            <td>{{ $permintaan->userPersetujuan->name ?? '-' }}</td> <!-- Tampilkan nama pengguna yang menyetujui -->

                            <td>
                                @if($permintaan->status_persetujuan === 'pending' && 
                                (auth()->user()->role === 'admin' || 
                                 auth()->user()->role === 'kasubagumumdangudang' || 
                                 auth()->user()->role === 'staff'))
                                <button class="btn btn-success btn-sm" type="button" title="Setujui Permintaan"
                                    onclick="confirmApproval('{{ route('transaksi.permintaan.approve', $permintaan->id_permintaan) }}')">
                                    <i class="fas fa-check-circle"></i> Setujui
                                </button>
                            @endif

                                <a href="{{ route('transaksi.permintaan.show', $permintaan->id_permintaan) }}" 
                                   class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i> Lihat
                                </a>

                                @if($permintaan->status_persetujuan !== 'pending')
                                    <button class="btn btn-warning btn-sm btn-edit"
                                            data-id="{{ $permintaan->id_permintaan }}"
                                            data-keterangan="{{ $permintaan->keterangan }}"
                                            data-tgl_permintaan="{{ $permintaan->tgl_permintaan }}"
                                            data-bagian="{{ $permintaan->bagian }}"
                                            data-tipe_id="{{ $permintaan->tipe_id }}"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editModal">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                @else
                    <tbody>
                    <tr>
                        <td colspan="9" class="text-center">
                            {{ __('Tidak ada data permintaan') }}
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
            <form class="modal-content" method="POST" action="{{ route('transaksi.permintaan.update', $permintaan->id_permintaan) }}">
                @csrf
                @method('PUT')
                
                <div class="modal-header">
                    <h5 class="modal-title">Edit Permintaan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Bagian</label>
                        <select class="form-control" name="bagian" id="edit-bagian">
                            @foreach($bagians as $bagian)
                                <option value="{{ $bagian->id_bagian }}" {{ $bagian->id_bagian == $permintaan->bagian ? 'selected' : '' }}>{{ $bagian->nama_bagian }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Tipe</label>
                        <select class="form-control" name="tipe_id" id="edit-tipe">
                            @foreach($kategoris as $kategori)
                                <option value="{{ $kategori->id_tipe }}" {{ $kategori->id_tipe == $permintaan->tipe_id ? 'selected' : '' }}>{{ $kategori->nama_tipe }}</option>
                            @endforeach
                        </select>
                    </div>
    
                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <input type="text" class="form-control" name="keterangan" id="edit-keterangan" value="{{ $permintaan->keterangan }}">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Tanggal Permintaan</label>
                        <input type="date" class="form-control" name="tgl_permintaan" id="edit-tgl_permintaan" value="{{ $permintaan->tgl_permintaan }}">
                    </div>
    
                    <div class="mb-3">
                        <label class="form-label">Barang yang DiMinta</label>
                        <table class="table" id="editBarangTable">
                            <thead>
                                <tr>
                                    <th>Barang</th>
                                    <th>Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($permintaan->detailPermintaan as $detail)
                                    <tr>
                                        <td>
                                            <select class="form-control" name="barang[{{ $detail->id_barang }}][id_barang]">
                                                <option value="{{ $detail->barang->id_barang }}" selected>{{ $detail->barang->deskripsi }}</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control" name="barang[{{ $detail->id_barang }}][qty]" value="{{ $detail->qty }}" min="1">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
    

    <!-- Edit Modal -->
@endsection
<script>
    function confirmApproval(url) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Anda akan menyetujui permintaan ini!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Setujui!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Submit the form dynamically
                let form = document.createElement('form');
                form.action = url;
                form.method = 'POST';
                form.style.display = 'none';

                // Tambahkan CSRF token
                let csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = '{{ csrf_token() }}';
                form.appendChild(csrfInput);

                document.body.appendChild(form);
                form.submit();
            }
        });
    }
</script>
