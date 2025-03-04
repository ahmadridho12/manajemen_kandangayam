@extends('layout.main')

@push('script')
    <script>
        $(document).on('click', '.btn-edit', function () {
            const id = $(this).data('id');
            const status = $(this).data('status'); // Ambil status dari data atribut

            $('#editModal form').attr('action', '{{ route('lainnya.hargaayam.update', '') }}/' + id);
            $('#editModal input:hidden#id').val(id);
            $('#editModal input#min_berat').val($(this).data('min_berat'));
            $('#editModal input#max_berat').val($(this).data('max_berat'));
            $('#editModal input#harga').val($(this).data('harga'));
            // $('#editModal select#status').val(status);
        });
        
    </script>
@endpush

@section('content')
    <x-breadcrumb
        :values="[__('Harga Ayam')]">
        <button
            type="button"
            class="btn btn-primary btn-create"
            data-bs-toggle="modal"
            data-bs-target="#createModal">
            {{ __('menu.general.create') }}
        </button>
    </x-breadcrumb>

    <div class="card mb-5">
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                <tr><th>No</th>
                    <th>{{ __('Min Berat ') }}</th>
                    <th>{{ __('Max Berat') }}</th>
                    <th>{{ __('Harga') }}</th>
                    {{-- <th>{{ __('Status') }}</th> <!-- Tambahkan kolom status --> --}}

                    <th>{{ __('menu.general.action') }}</th>
                </tr>
                </thead>
                @if($data && $data->count())
                    <tbody>
                    @foreach($data as $hap)
                        <tr>
                            <td>{{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}</td>

                            <td>{{ $hap->min_berat }}</td>
                            <td>{{ $hap->max_berat }}</td>
                            <td>{{ number_format($hap->harga, 0, ',', '.') ?? 'Tidak Ada' }}</td>
                            
                            
                            <td>
                                <button class="btn btn-info btn-sm btn-edit"
                                        data-id="{{ $hap->id_harga }}"
                                        data-min_berat="{{ $hap->min_berat }}"
                                        data-max_berat="{{ $hap->max_berat }}"
                                        data-harga="{{ $hap->harga }}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editModal">
                                    {{ __('menu.general.edit') }}
                                </button>
                                <form action="{{ route('lainnya.hargaayam.destroy', $hap->id_harga) }}" class="d-inline" method="post">
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
            </table>
        </div>
    </div>

    {!! $data->appends(['search' => $search])->links() !!} 

    <!-- Create Modal -->
    <div class="modal fade" id="createModal" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" method="post" action="{{ route('lainnya.hargaayam.store') }}">
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
                    
                    <x-input-form name="min_berat" :label="__('Min Berat')" type="decimal"/>
                    <x-input-form name="max_berat" :label="__('Max Berat')" type="decimal"/>
                    <x-input-form name="harga" :label="__('Harga Jual')" type="number"/>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        {{ __('menu.general.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-primary">{{ __('menu.general.save') }}</button>
                </div>
            </form>
        </div>
    </div>

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
                    <input type="hidden" name="id" id="id" value="">
                                   
                    <x-input-form name="min_berat" :label="__('Min Berat')" type="decimal"/>
                    <x-input-form name="max_berat" :label="__('Max Berat')" type="decimal"/>
                    <x-input-form name="harga" :label="__('Harga Jual')" type="number"/>                    
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
@endsection