@extends('layout.main')

{{-- @push('script')
    <script>
        $(document).on('click', '.btn-edit', function () {
            const id = $(this).data('id');
            $('#editModal form').attr('action', '{{ route('user.index') }}/' + id);
            $('#editModal input:hidden#id').val(id);
            $('#editModal input#name').val($(this).data('name'));
            $('#editModal input#phone').val($(this).data('phone'));
            $('#editModal input#email').val($(this).data('email'));
            $('#editModal select#role').val($(this).data('role')); // Mengisi nilai role

            if ($(this).data('active') == 1) {
                $('#editModal input#is_active').attr('checked', 1)
            } else {
                $('#editModal input#is_active').removeAttribute('checked');
            }
        });
    </script>
@endpush --}}

@section('content')
    <x-breadcrumb
        :values="[__('Detail Barang Keluar')]">
        {{-- <button
            type="button"
            class="btn btn-primary btn-create"
            data-bs-toggle="modal"
            data-bs-target="#createModal">
            {{ __('menu.general.create') }}
        </button> --}}
    </x-breadcrumb> 

    <div class="card mb-5">
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                <tr>
                    <th>No</th> <!-- Kolom Nomor -->
                    <th>{{ __('No Trans') }}</th>
                    <th>{{ __('Kode Barang') }}</th>
                    <th>{{ __('Nama Barang') }}</th>
                    <th>{{ __('Jumlah ') }}</th>
                    <th>{{ __('Harga ') }}</th>
                    <th>{{ __('Total') }}</th>
                    <th>{{ __('kategori') }}</th>
                    <th>{{ __('Tanggal Keluar') }}</th>
                    <th>{{ __('Keterangan') }}</th>
                  
                </tr>
                </thead>
                @if($data && $data->count())
                <tbody>
                    
                    @php
                    // Mendapatkan nomor halaman saat ini
                    $currentPage = request()->input('page', 1);
                    // Jumlah item per halaman
                    $perPage = $data->perPage();
                    // Menghitung offset
                    $offset = ($currentPage - 1) * $perPage;
                    // Inisialisasi counter dengan offset
                    $counter = $offset + 1;
                @endphp                    @foreach($data as $detail_barang_keluar)
                        @if($detail_barang_keluar->jumlah > 0)
                            <tr>
                                <td>{{ $counter }}</td>
                                <td>{{ $detail_barang_keluar->barangKeluar->no_transaksi }}</td>
                                <td>{{ $detail_barang_keluar->barang->kode_barang }}</td>
                                <td>{{ $detail_barang_keluar->barang->deskripsi }}</td>
                                <td>{{ $detail_barang_keluar->jumlah }}</td>
                                <td>{{ number_format($detail_barang_keluar->harga, 2, ',', '.') }}</td>
                                <td>Rp{{ number_format($detail_barang_keluar->total, 2, ',', '.') }}</td>
                                <td>{{ $detail_barang_keluar->barang->jenis->nama }}</td>
                                <td>{{ $detail_barang_keluar->tanggal_detailkeluar }}</td>
                                <td>{{ $detail_barang_keluar->barangkeluar->permintaan->keterangan }}</td>
                            </tr>
                            @php $counter++ @endphp
                        @endif
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
                {{-- <tfoot class="table-border-bottom-0">
                <tr>
                    <th>{{ __('model.user.name') }}</th>
                    <th>{{ __('model.user.email') }}</th>
                    <th>{{ __('model.user.phone') }}</th>
                    <th>{{ __('model.user.is_active') }}</th>
                    <th>{{ __('menu.general.action') }}</th>
                </tr>
                </tfoot> --}}
            </table>
        </div>
    </div>

     {!! $data->appends(['search' => $search])->links() !!} 

    <!-- Create Modal -->
    {{-- <div class="modal fade" id="createModal" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" method="post" action="{{ route('user.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="createModalTitle">{{ __('menu.general.create') }}</h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    ></button>
                </div>
                <div class="modal-body">
                    <x-input-form name="name" :label="__('model.user.name')"/>
                    <x-input-form name="email" :label="__('model.user.email')" type="email"/>
                    <x-input-form name="phone" :label="__('model.user.phone')"/>
                    <div class="role" :label="__('ROLE')">
                        <label for="role" class="form-label">Role</label>
                        <select id="role" name="role" class="form-select"> <!-- Tambahkan atribut name -->
                            <option value="admin">Admin</option>
                            <option value="staff">Staff</option>
                            <option value="kabaghublang">Kabag Hublang</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        {{ __('menu.general.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-primary">{{ __('menu.general.save') }}</button>
                </div>
            </form>
        </div>
    </div> --}}

    <!-- Edit Modal -->
    {{-- <div class="modal fade" id="editModal" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" method="post" action="">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalTitle">{{ __('menu.general.edit') }}</h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    ></button>
                </div>
                
                <div class="modal-body">
                    <input type="hidden" name="id" id="id" value="">
                    <x-input-form name="name" :label="__('model.user.name')"/>
                    <x-input-form name="email" :label="__('model.user.email')" type="email"/>
                    <x-input-form name="phone" :label="__('model.user.phone')"/>
                    <div class="role" :label="__('ROLE')">
                        <label for="role" class="form-label">Role</label>
                        <select id="role" name="role" class="form-select"> <!-- Tambahkan atribut name -->
                            <option value="admin">Admin</option>
                            <option value="staff">Staff</option>
                            <option value="kabaghublang">Kabag Hublang</option>
                        </select>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" value="true" id="is_active">
                        <label class="form-check-label" for="is_active"> {{ __('model.user.is_active') }} </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="reset_password" value="true" id="reset_password">
                        <label class="form-check-label" for="reset_password"> {{ __('model.user.reset_password') }} </label>
                    </div>
                    
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        {{ __('menu.general.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-primary">{{ __('menu.general.update') }}</button>
                </div>
            </form>
        </div>
    </div> --}}
@endsection
