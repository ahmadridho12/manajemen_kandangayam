<div class="card">
    <div class="card-body">
        <div class="card-title d-flex align-items-start justify-content-between">
            <div class="avatar flex-shrink-0">
                <span class="badge bg-label-{{ $color }} p-2">
                    @if(strpos($icon, 'img:') === 0)
                        <img src="{{ asset(substr($icon, 4)) }}" alt="Icon" class="menu-icon" style="width: 24px; height: 24px;">
                    @else
                        <i class="bx {{ $icon }} text-{{ $color }}"></i>
                    @endif
                </span>
            </div>

            <!-- Dropdown button to view more, only if route is provided -->
            @if($route)
            <div class="dropdown">
                <button class="btn p-0" type="button" id="cardOpt{{ Str::slug($label) }}" data-bs-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                    <i class="bx bx-dots-vertical-rounded"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt{{ Str::slug($label) }}">
                    <a class="dropdown-item" href="{{ route($route) }}">{{ __('dashboard.view_more') }}</a>
                </div>
            </div>
            @endif
        </div>

        <div class="card-content">
            <span class="fw-semibold d-block mb-1">{{ $label }} {{ $daily ? '*' : '' }}</span>
            <h3 class="card-title mb-2">{{ $value }}</h3>

            @if(isset($percentage))
                @if($percentage > 0)
                    <small class="text-success fw-semibold"><i class="bx bx-up-arrow-alt"></i> {{ $percentage }}%</small>
                @elseif($percentage < 0)
                    <small class="text-danger fw-semibold"><i class="bx bx-down-arrow-alt"></i> {{ $percentage }}%</small>
                @endif
            @endif
        </div>
    </div>
</div>
