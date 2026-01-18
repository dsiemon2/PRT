{{--
    Stats Card Component

    Usage:
    @include('components.stats-card', [
        'title' => 'Total Orders',
        'value' => 1234,
        'icon' => 'cart',
        'color' => 'primary',
        'id' => 'stat-total',  // optional, for AJAX updates
        'link' => route('admin.orders'),  // optional
        'trend' => '+5%',  // optional
        'trendUp' => true,  // optional
    ])
--}}

@props([
    'title',
    'value',
    'icon' => 'graph-up',
    'color' => 'primary',
    'id' => null,
    'link' => null,
    'trend' => null,
    'trendUp' => true,
])

<div class="col-md-3 col-sm-6 mb-3">
    @if($link)
        <a href="{{ $link }}" class="text-decoration-none">
    @endif

    <div class="card stat-card border-{{ $color }} h-100">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="text-muted mb-1">
                        <i class="bi bi-{{ $icon }} me-1"></i>
                        {{ $title }}
                    </h6>
                    <h3 class="mb-0 text-{{ $color }}" @if($id) id="{{ $id }}" @endif>
                        {{ is_numeric($value) ? number_format($value) : $value }}
                    </h3>
                    @if($trend)
                        <small class="text-{{ $trendUp ? 'success' : 'danger' }}">
                            <i class="bi bi-{{ $trendUp ? 'arrow-up' : 'arrow-down' }}"></i>
                            {{ $trend }}
                        </small>
                    @endif
                </div>
                <div class="text-{{ $color }} opacity-50">
                    <i class="bi bi-{{ $icon }}" style="font-size: 2.5rem;"></i>
                </div>
            </div>
        </div>
    </div>

    @if($link)
        </a>
    @endif
</div>
