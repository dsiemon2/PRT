@extends('layouts.app')

@section('title', 'Shopping Cart')

@section('content')
<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item active">Shopping Cart</li>
        </ol>
    </nav>
</div>

<div class="container my-4">
    <h1 style="color: var(--prt-brown);"><i class="bi bi-cart3"></i> Shopping Cart</h1>

    @if(count($cartItems) > 0)
        <div class="row">
            {{-- Cart Items --}}
            <div class="col-lg-8">
                <div class="card">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th colspan="2">Product</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Total</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cartItems as $item)
                                    <tr data-index="{{ $item['index'] }}">
                                        <td style="width: 100px;">
                                            <img src="{{ $item['product']->primaryImage }}"
                                                 alt="{{ $item['product']->ShortDescription }}"
                                                 style="width: 80px; height: 80px; object-fit: contain;"
                                                 onerror="this.src='{{ asset('assets/images/no-image.svg') }}'">
                                        </td>
                                        <td>
                                            <a href="{{ route('products.show', $item['product']->ID) }}"
                                               class="text-decoration-none" style="color: var(--prt-brown);">
                                                <strong>{{ $item['product']->ShortDescription }}</strong>
                                            </a>
                                            @if($item['size'])
                                                <br><small class="text-muted">Size: {{ $item['size'] }}</small>
                                            @endif
                                            <br><small class="text-muted">Item #: {{ $item['product']->ItemNumber }}</small>
                                        </td>
                                        <td>${{ number_format($item['product']->UnitPrice, 2) }}</td>
                                        <td>
                                            <div class="input-group" style="width: 120px;">
                                                <button class="btn btn-outline-secondary btn-sm" type="button"
                                                        onclick="updateQuantity({{ $item['index'] }}, -1)" data-bs-toggle="tooltip" title="Decrease quantity">
                                                    <i class="bi bi-dash"></i>
                                                </button>
                                                <input type="number" class="form-control form-control-sm text-center"
                                                       id="qty_{{ $item['index'] }}"
                                                       value="{{ $item['quantity'] }}"
                                                       min="1" max="99"
                                                       onchange="updateQuantity({{ $item['index'] }}, 0, this.value)">
                                                <button class="btn btn-outline-secondary btn-sm" type="button"
                                                        onclick="updateQuantity({{ $item['index'] }}, 1)" data-bs-toggle="tooltip" title="Increase quantity">
                                                    <i class="bi bi-plus"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td class="fw-bold">${{ number_format($item['total'], 2) }}</td>
                                        <td>
                                            <button class="btn btn-outline-danger btn-sm"
                                                    onclick="removeItem({{ $item['index'] }})" data-bs-toggle="tooltip" title="Remove this item from cart">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer d-flex justify-content-between">
                        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary" data-bs-toggle="tooltip" title="Return to browse more products">
                            <i class="bi bi-arrow-left"></i> Continue Shopping
                        </a>
                        <button class="btn btn-outline-danger" onclick="clearCart()" data-bs-toggle="tooltip" title="Remove all items from your cart">
                            <i class="bi bi-trash"></i> Clear Cart
                        </button>
                    </div>
                </div>
            </div>

            {{-- Order Summary --}}
            <div class="col-lg-4">
                {{-- Coupon --}}
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-tag"></i> Coupon Code</h5>
                    </div>
                    <div class="card-body">
                        @if($coupon)
                            <div class="alert alert-success mb-0 d-flex justify-content-between align-items-center">
                                <span>
                                    <i class="bi bi-check-circle"></i> {{ $coupon['code'] }}
                                    @if($coupon['type'] === 'percentage')
                                        ({{ $coupon['value'] }}% off)
                                    @else
                                        (${{ number_format($coupon['value'], 2) }} off)
                                    @endif
                                </span>
                                <form action="{{ route('cart.coupon.remove') }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-link text-danger p-0" data-bs-toggle="tooltip" title="Remove this coupon">
                                        Remove
                                    </button>
                                </form>
                            </div>
                        @else
                            <form action="{{ route('cart.coupon') }}" method="POST">
                                @csrf
                                <div class="input-group">
                                    <input type="text" name="coupon_code" class="form-control"
                                           placeholder="Enter coupon code">
                                    <button type="submit" class="btn btn-primary" data-bs-toggle="tooltip" title="Apply coupon code for discount">Apply</button>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>

                {{-- Summary --}}
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-receipt"></i> Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm mb-0">
                            <tr>
                                <td>Subtotal</td>
                                <td class="text-end">${{ number_format($subtotal, 2) }}</td>
                            </tr>
                            @if($discount > 0)
                                <tr class="text-success">
                                    <td>Discount</td>
                                    <td class="text-end">-${{ number_format($discount, 2) }}</td>
                                </tr>
                            @endif
                            <tr>
                                <td>Tax (8.25%)</td>
                                <td class="text-end">${{ number_format($tax, 2) }}</td>
                            </tr>
                            <tr class="fw-bold fs-5">
                                <td>Total</td>
                                <td class="text-end">${{ number_format($total, 2) }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="card-footer d-grid">
                        @auth
                            <a href="{{ route('checkout.index') }}" class="btn btn-primary btn-lg" data-bs-toggle="tooltip" title="Continue to complete your purchase">
                                <i class="bi bi-credit-card"></i> Proceed to Checkout
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-primary btn-lg" data-bs-toggle="tooltip" title="Sign in to complete your purchase">
                                <i class="bi bi-person"></i> Login to Checkout
                            </a>
                            <small class="text-muted text-center mt-2">
                                Don't have an account? <a href="{{ route('register') }}">Register</a>
                            </small>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    @else
        {{-- Empty Cart --}}
        <div class="text-center py-5">
            <i class="bi bi-cart-x display-1 text-muted"></i>
            <h3 class="mt-3">Your cart is empty</h3>
            <p class="text-muted">Looks like you haven't added any items to your cart yet.</p>
            <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg" data-bs-toggle="tooltip" title="Browse our product catalog">
                <i class="bi bi-shop"></i> Start Shopping
            </a>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
// Initialize Bootstrap tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

function updateQuantity(index, change, value = null) {
    const input = document.getElementById('qty_' + index);
    let qty = value !== null ? parseInt(value) : parseInt(input.value) + change;
    qty = Math.max(1, Math.min(99, qty));
    input.value = qty;

    fetch('{{ route("cart.update") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ index: index, quantity: qty })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}

function removeItem(index) {
    if (!confirm('Remove this item from cart?')) return;

    fetch('{{ url("cart/remove") }}/' + index, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}

function clearCart() {
    if (!confirm('Clear all items from cart?')) return;
    // Would need a clear cart route
    location.reload();
}
</script>
@endsection
