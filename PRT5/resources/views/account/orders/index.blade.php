@extends('layouts.app')

@section('title', 'My Orders')

@section('content')
<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('account.index') }}">My Account</a></li>
            <li class="breadcrumb-item active">Orders</li>
        </ol>
    </nav>
</div>

<div class="container my-4">
    <div class="row">
        {{-- Sidebar --}}
        <div class="col-md-3 mb-4">
            @include('account.partials.sidebar', ['active' => 'orders'])
        </div>

        {{-- Main Content --}}
        <div class="col-md-9">
            <h1 style="color: var(--prt-brown);"><i class="bi bi-box-seam"></i> My Orders</h1>

            {{-- Filters --}}
            <div class="card mb-4">
                <div class="card-body">
                    <form action="{{ route('account.orders.index') }}" method="GET" class="row g-3">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control"
                                   placeholder="Search order number..."
                                   value="{{ request('search') }}">
                        </div>
                        <div class="col-md-4">
                            <select name="status" class="form-select">
                                <option value="all">All Status ({{ $statusCounts['all'] }})</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>
                                    Pending ({{ $statusCounts['pending'] }})
                                </option>
                                <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>
                                    Processing ({{ $statusCounts['processing'] }})
                                </option>
                                <option value="shipped" {{ request('status') === 'shipped' ? 'selected' : '' }}>
                                    Shipped ({{ $statusCounts['shipped'] }})
                                </option>
                                <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>
                                    Delivered ({{ $statusCounts['delivered'] }})
                                </option>
                                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>
                                    Cancelled ({{ $statusCounts['cancelled'] }})
                                </option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary" data-bs-toggle="tooltip" title="Search your orders">
                                <i class="bi bi-search"></i> Search
                            </button>
                            <a href="{{ route('account.orders.index') }}" class="btn btn-outline-secondary" data-bs-toggle="tooltip" title="Clear search filters">Clear</a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Orders List --}}
            @if($orders->count() > 0)
                @foreach($orders as $order)
                    <div class="card mb-3">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div>
                                <strong>{{ $order->order_number }}</strong>
                                <span class="text-muted ms-2">{{ $order->order_date->format('M j, Y g:i A') }}</span>
                            </div>
                            @php
                                $statusColors = [
                                    'pending' => 'warning',
                                    'processing' => 'info',
                                    'shipped' => 'primary',
                                    'delivered' => 'success',
                                    'cancelled' => 'danger',
                                ];
                            @endphp
                            <span class="badge bg-{{ $statusColors[$order->status] ?? 'secondary' }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($order->items->take(3) as $item)
                                            @if($item->product)
                                                <img src="{{ $item->product->primaryImage }}"
                                                     alt="{{ $item->product_name }}"
                                                     style="width: 60px; height: 60px; object-fit: contain; border: 1px solid #ddd; border-radius: 4px;"
                                                     onerror="this.src='{{ asset('assets/images/no-image.svg') }}'">
                                            @endif
                                        @endforeach
                                        @if($order->items->count() > 3)
                                            <div class="d-flex align-items-center justify-content-center"
                                                 style="width: 60px; height: 60px; background: #f8f9fa; border: 1px solid #ddd; border-radius: 4px;">
                                                <small class="text-muted">+{{ $order->items->count() - 3 }}</small>
                                            </div>
                                        @endif
                                    </div>
                                    <p class="mt-2 mb-0 text-muted">
                                        {{ $order->items->sum('quantity') }} item(s)
                                    </p>
                                </div>
                                <div class="col-md-3">
                                    <p class="mb-1"><strong>Ship To:</strong></p>
                                    <p class="mb-0 small text-muted">
                                        {{ $order->customer_first_name }} {{ $order->customer_last_name }}<br>
                                        {{ $order->shipping_city }}, {{ $order->shipping_state }}
                                    </p>
                                </div>
                                <div class="col-md-3 text-end">
                                    <p class="fs-5 fw-bold mb-2">${{ number_format($order->total_amount, 2) }}</p>
                                    <a href="{{ route('account.orders.show', $order) }}" class="btn btn-outline-primary btn-sm" data-bs-toggle="tooltip" title="View order details and tracking">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="d-flex justify-content-center mt-4">
                    {{ $orders->links() }}
                </div>
            @else
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-box-seam text-muted" style="font-size: 4rem;"></i>
                        <h3 class="mt-3">No orders found</h3>
                        <p class="text-muted">You haven't placed any orders yet.</p>
                        <a href="{{ route('products.index') }}" class="btn btn-primary" data-bs-toggle="tooltip" title="Browse our product catalog">
                            <i class="bi bi-shop"></i> Start Shopping
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endpush
