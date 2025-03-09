<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    
</body>
</html>
<div class="card mb-4">
    <div class="card-header pb-0">
        <div class="d-flex justify-content-between flex-column flex-sm-row">
            <div class="card-title">
                <h5 class="text-nowrap mb-0 fw-bold">{{ $panen->ayam->periode }}</h5>
                <small class="text-black">
                    NO Panen: {{ $panen->no_panen }} |
                    DO Atas Nama: {{ $panen->atas_nama}} 
                </small>
                
            </div>
            <div class="card-title d-flex flex-row">
                <div class="d-inline-block mx-2 text-end text-black">
                    <small class="d-block text-secondary">Tanggal </small>
                    {{ $panen->tanggal_panen }}
                </div>
               
                <div class="dropdown d-inline-block">
                    <button class="btn p-0" type="button" id="dropdown-{{ $panen->id_panen }}"
                            data-bs-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">
                        <i class="bx bx-dots-vertical-rounded"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end"
                         aria-labelledby="dropdown-{{ $panen->id_panen }}">
                        <a class="dropdown-item" href="{{ route('sistem.panen.show', $panen->id_panen) }}">
                            {{ __('menu.general.view') }}
                        </a>
                        <a class="dropdown-item" href="{{ route('sistem.panen.edit', $panen->id_panen) }}">
                            {{ __('edit') }}
                        </a>
                        <form action="{{ route('sistem.panen.destroy', $panen->id_panen) }}" class="d-inline"
                            method="post">
                          @csrf
                          @method('DELETE')
                          <span
                              class="dropdown-item cursor-pointer btn-delete" >{{ __('menu.general.delete') }}</span>
                      </form>
                        
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <hr>
        <p>{{ $panen->berat_total }}Kg</p>
       
        {{ $slot }}
    </div>
</div>
