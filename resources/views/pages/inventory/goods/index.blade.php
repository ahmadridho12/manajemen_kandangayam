@extends('layout.main')

@push('script')

<script>
    $(document).on('click', '.btn-edit', function () {
        const id = $(this).data('id');
        const deskripsi = $(this).data('deskripsi');
        const idSatuan = $(this).data('id_satuan');

        // Set action URL
        $('#editModal form').attr('action', '{{ route('inventory.goods.update', '') }}/' + id);
        $('#editModal input:hidden#id_barang').val(id);
        $('#editModal input#deskripsi').val(deskripsi);

        // Ambil data satuan via AJAX
        $.ajax({
            url: '{{ route('inventory.getSatuans') }}', // Tambahkan prefix 'inventory.'
            method: 'GET',
            success: function(response) {
                let satuanOptions = '';
                $.each(response.satuans, function(key, satuan) {
                    satuanOptions += `<option value="${satuan.id_satuan}" ${satuan.id_satuan == idSatuan ? 'selected' : ''}>${satuan.nama_satuan}</option>`;
                });
                $('#editModal select#id_satuan').html(satuanOptions);
            }
        });
    });
</script>

@endpush

@section('content')
    <x-breadcrumb
    :values="[__('Barang'), __('Barang')]">
    <a href="{{ route('inventory.goods.create') }}" class="btn btn-primary">
        {{ __('Tambah Barang') }}
    </a>
            {{-- <a href="{{ route('create.permission.add') }}" class="btn btn-primary">{{ __('') }}</a>  --}}
    </x-breadcrumb>


    <div class="card mb-5">
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                <tr>
                    <th>No</th> <!-- Kolom Nomor -->
                    <th>{{ __('Kode Barang') }}</th> <!-- Kolom Kode Barang -->
                    <th>{{ __('Nama Barang') }}</th> <!-- Kolom Deskripsi -->
                    <th>{{ __('Satuan') }}</th> <!-- Kolom Unit -->
                    <th>{{ __('Aksi') }}</th>
                  
                </tr>
                </thead>
                @if($data && $data->count())
                    <tbody>
                    @foreach($data as $barangg)
                        <tr>
                            <td>{{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}</td>
                            <td>{{ $barangg->kode_barang }}</td> <!-- Deskripsi -->
                            <td>{{ $barangg->deskripsi }}</td> <!-- Deskripsi -->
                            <td>{{ $barangg->satuan->nama_satuan ?? '-' }}</td>

                            
                            {{-- <td><span
                                    class="badge bg-label-primary me-1">{{  __('model.user.' . ($user->is_active ? 'active' : 'nonactive')) }}</span>
                            </td> --}}
                             <td>
                                <button class="btn btn-info btn-sm btn-edit"
                                        data-id="{{ $barangg->id_barang }}"
                                        data-deskripsi="{{ $barangg->deskripsi }}"
                                        data-id_satuan="{{ $barangg->id_satuan }}"
                                        {{-- data-nama_satuan="{{ $barangg->satuan->nama_satuan ?? '-' }}" --}}
                                        data-bs-toggle="modal"
                                        data-bs-target="#editModal">
                                    {{ __('menu.general.edit') }}
                                </button>
                                <form action="{{ route('inventory.goods.destroy', $barangg->id_barang) }}" class="d-inline" method="post">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm btn-delete"
                                            type="button">{{ __('menu.general.delete') }}</button>
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

     
    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" data-bs-backdrop="static" tabindex="-1">
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
                    <input type="hidden" name="deskripsi" id="id_barang" value="">
                    <x-input-form name="deskripsi" :label="__('Nama Barang')"/>
                     <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                        <label for="id_satuan" class="form-label">{{ __('Satuan') }}</label>
                        <select name="id_satuan" id="id_satuan" class="form-control">
                            @foreach($satuans as $satuan)
                                <option value="{{ $satuan->id_satuan }}">{{ $satuan->nama_satuan }}</option>
                            @endforeach
                        </select>
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
    </div>


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
