@extends('layout.main')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

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
    
    
    </style>
    
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
        :values="[__('Barang Keluar')]">
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
                    <th>{{ __('Bagian ') }}</th>
                    <th>{{ __('Nomor DPPB') }}</th>
                    <th>{{ __('Tanggal') }}</th>
                    <th>{{ __('User') }}</th>
                    <th>{{ __('Keterangan') }}</th>
                    {{-- <th>{{ __('Aksi') }}</th> --}}
                  
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
                    @endphp
                    @foreach($data as $barangkeluar)
                        <tr>
                            <td>{{ $loop->iteration + $offset }}</td> <!-- Menampilkan nomor urut -->
                            <td class="dropdown-trigger">
                                <span class="no-trans">{{ $barangkeluar->no_transaksi }}</span>
                                <div class="dropdown-content">
                                    <a href="{{ route('transaksi.barangkeluar.show', $barangkeluar->id_keluar) }}">
                                        <i class="fa fa-search"></i> Lihat Detail
                                    </a>
                                </div>
                            </td>
                            {{-- <td>{{ $barangkeluar->no_transaksi }}</td> --}}
                            <td>{{ $barangkeluar->permintaan->bagiann->nama_bagian }}</td>
                            <td>{{ $barangkeluar->permintaan->no_trans }}</td>
                            <td>{{ $barangkeluar->tanggal_keluar }}</td>
                            <td>{{ $barangkeluar->permintaan->user->name }}</td>
                            <td>{{ $barangkeluar->permintaan->keterangan }}</td>
                            <td>{{ $barangkeluar->kategori }}</td>
                            
                            {{-- <td><span
                                    class="badge bg-label-primary me-1">{{  __('model.user.' . ($user->is_active ? 'active' : 'nonactive')) }}</span>
                            </td> --}}
                             {{-- <td>
                                <button class="btn btn-info btn-sm btn-edit"
                                        data-Notrans="{{ $barangkeluar->Notrans }}"
                                        data-kodekaryawan="{{ $barangkeluar->kodekaryawan }}"
                                        data-tanggal="{{ $barangkeluar->tanggal }}"
                                        data-totalamount="{{ $barangkeluar->totalamount }}"
                                        data-keterangan="{{ $barangkeluar->keterangan }}"
                                        data-noregis="{{ $barangkeluar->noregis }}"
                                        data-kategori="{{ $barangkeluar->kategori }}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editModal">
                                    {{ __('menu.general.edit') }}
                                </button>
                                <form action="" class="d-inline" method="post">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm btn-delete"
                                            type="button">{{ __('menu.general.delete') }}</button>
                                </form>
                            </td> 
                        </tr> --}}
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


@endsection
