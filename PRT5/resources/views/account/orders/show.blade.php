@extends('layouts.app')

@section('title', 'Order ' . $order->order_number)

@section('content')
<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('account.index') }}">My Account</a></li>
            <li class="breadcrumb-item"><a href="{{ route('account.orders.index') }}">Orders</a></li>
            <li class="breadcrumb-item active">{{ $order->order_number }}</li>
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
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 style="color: var(--prt-brown);">Order {{ $order->order_number }}</h1>
                    <p class="text-muted mb-0">Placed on {{ $order->order_date->format('F j, Y g:i A') }}</p>
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
                <span class="badge bg-{{ $statusColors[$order->status] ?? 'secondary' }} fs-6 py-2 px-3">
                    {{ ucfirst($order->status) }}
                </span>
            </div>

            {{-- Order Details --}}
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="bi bi-truck"></i> Shipping Address</h5>
                        </div>
                        <div class="card-body">
                            <address class="mb-0">
                                <strong>{{ $order->customer_first_name }} {{ $order->customer_last_name }}</strong><br>
                                {{ $order->shipping_address1 }}<br>
                                @if($order->shipping_address2)
                                    {{ $order->shipping_address2 }}<br>
                                @endif
                                {{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_zip }}<br>
                                @if($order->customer_phone)
                                    <i class="bi bi-telephone"></i> {{ $order->customer_phone }}
                                @endif
                            </address>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="bi bi-credit-card"></i> Payment Information</h5>
                        </div>
                        <div class="card-body">
                            @if($order->payment_card_type && $order->payment_last4)
                                <p class="mb-2">
                                    <strong>Payment:</strong>
                                    <i class="bi bi-credit-card"></i> {{ $order->payment_card_type }} ending in {{ $order->payment_last4 }}
                                </p>
                            @else
                                <p class="mb-2">
                                    <strong>Payment:</strong> Credit/Debit Card
                                </p>
                            @endif
                            <p class="mb-0">
                                <strong>Status:</strong>
                                <span class="badge bg-{{ $order->status === 'delivered' ? 'success' : ($order->status === 'cancelled' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Order Items --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-box"></i> Order Items</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th colspan="2">Product</th>
                                    <th>Price</th>
                                    <th>Qty</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                    <tr>
                                        <td style="width: 80px;">
                                            @if($item->product)
                                                <a href="{{ route('products.show', $item->product_id) }}">
                                                    <img src="{{ $item->product->primaryImage }}"
                                                         alt="{{ $item->product_name }}"
                                                         style="width: 70px; height: 70px; object-fit: contain;"
                                                         onerror="this.src='{{ asset('assets/images/no-image.svg') }}'">
                                                </a>
                                            @else
                                                <img src="{{ asset('assets/images/no-image.svg') }}"
                                                     alt="Product"
                                                     style="width: 70px; height: 70px; object-fit: contain;">
                                            @endif
                                        </td>
                                        <td>
                                            @if($item->product)
                                                <a href="{{ route('products.show', $item->product_id) }}"
                                                   class="text-decoration-none" style="color: var(--prt-brown);">
                                                    <strong>{{ $item->product_name }}</strong>
                                                </a>
                                            @else
                                                <strong>{{ $item->product_name }}</strong>
                                            @endif
                                        </td>
                                        <td>${{ number_format($item->unit_price, 2) }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td class="text-end fw-bold">${{ number_format($item->total_price, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Order Summary --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-calculator"></i> Order Summary</h5>
                </div>
                <div class="card-body">
                    <div class="row justify-content-end">
                        <div class="col-md-6">
                            <table class="table table-sm mb-0">
                                <tr>
                                    <td>Subtotal</td>
                                    <td class="text-end">${{ number_format($order->subtotal ?? 0, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>Shipping</td>
                                    <td class="text-end">
                                        {{ ($order->shipping_cost ?? 0) > 0 ? '$' . number_format($order->shipping_cost, 2) : 'Free' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>Tax</td>
                                    <td class="text-end">${{ number_format($order->tax_amount ?? 0, 2) }}</td>
                                </tr>
                                <tr class="fw-bold fs-5 border-top">
                                    <td>Total</td>
                                    <td class="text-end">${{ number_format($order->total_amount, 2) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            @if($order->order_notes)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-chat-text"></i> Order Notes</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">{{ $order->order_notes }}</p>
                    </div>
                </div>
            @endif

            <div class="d-flex justify-content-between">
                <a href="{{ route('account.orders.index') }}" class="btn btn-outline-secondary" data-bs-toggle="tooltip" title="Return to orders list">
                    <i class="bi bi-arrow-left"></i> Back to Orders
                </a>
                <a href="{{ route('products.index') }}" class="btn btn-primary" data-bs-toggle="tooltip" title="Browse more products">
                    <i class="bi bi-shop"></i> Continue Shopping
                </a>
            </div>
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
