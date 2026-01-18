@extends('layouts.app')

@section('title', 'Order Confirmation')

@section('content')
<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item active">Order Confirmation</li>
        </ol>
    </nav>
</div>

<div class="container my-4">
    <div class="text-center mb-4">
        <i class="bi bi-check-circle text-success" style="font-size: 4rem;"></i>
        <h1 class="mt-3" style="color: var(--prt-brown);">Thank You for Your Order!</h1>
        <p class="lead text-muted">Your order has been placed successfully.</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            {{-- Order Info --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-receipt"></i> Order Details</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <td class="text-muted">Order Number:</td>
                                    <td class="fw-bold">{{ $order->order_number }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Order Date:</td>
                                    <td>{{ $order->order_date->format('F j, Y g:i A') }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Status:</td>
                                    <td>
                                        <span class="badge bg-warning text-dark">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Payment Method:</td>
                                    <td>
                                        @if($order->payment_method === 'card')
                                            <i class="bi bi-credit-card"></i> Credit/Debit Card
                                        @else
                                            <i class="bi bi-paypal"></i> PayPal
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Shipping Address</h6>
                            <address class="mb-0">
                                <strong>{{ $order->shipping_first_name }} {{ $order->shipping_last_name }}</strong><br>
                                {{ $order->shipping_address }}<br>
                                @if($order->shipping_address2)
                                    {{ $order->shipping_address2 }}<br>
                                @endif
                                {{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_zip }}<br>
                                @if($order->shipping_phone)
                                    <i class="bi bi-telephone"></i> {{ $order->shipping_phone }}
                                @endif
                            </address>
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
                                        <td style="width: 70px;">
                                            @if($item->product)
                                                <img src="{{ $item->product->primaryImage }}"
                                                     alt="{{ $item->product_name }}"
                                                     style="width: 60px; height: 60px; object-fit: contain;"
                                                     onerror="this.src='{{ asset('assets/images/no-image.svg') }}'">
                                            @else
                                                <img src="{{ asset('assets/images/no-image.svg') }}"
                                                     alt="Product Image"
                                                     style="width: 60px; height: 60px; object-fit: contain;">
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $item->product_name }}</strong><br>
                                            <small class="text-muted">SKU: {{ $item->product_sku }}</small>
                                            @if($item->size)
                                                <br><small class="text-muted">Size: {{ $item->size }}</small>
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
                    <table class="table table-sm mb-0">
                        <tr>
                            <td>Subtotal</td>
                            <td class="text-end">${{ number_format($order->subtotal, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Shipping</td>
                            <td class="text-end">{{ ($order->shipping_cost ?? 0) > 0 ? '$' . number_format($order->shipping_cost, 2) : 'Free' }}</td>
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

            {{-- Next Steps --}}
            <div class="card border-info mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-info-circle"></i> What's Next?</h5>
                </div>
                <div class="card-body">
                    <ul class="mb-0">
                        <li>You will receive an order confirmation email shortly.</li>
                        <li>We will notify you when your order ships.</li>
                        <li>You can track your order status in your account.</li>
                    </ul>
                </div>
            </div>

            {{-- Actions --}}
            <div class="d-flex justify-content-between">
                <a href="{{ route('products.index') }}" class="btn btn-outline-primary">
                    <i class="bi bi-shop"></i> Continue Shopping
                </a>
                <a href="{{ route('dashboard') }}" class="btn btn-primary">
                    <i class="bi bi-person"></i> View My Orders
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
