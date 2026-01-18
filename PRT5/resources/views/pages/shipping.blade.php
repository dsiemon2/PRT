@extends('layouts.app')

@section('title', 'Shipping Information')

@section('content')
<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item active">Shipping Information</li>
        </ol>
    </nav>
</div>

<div class="container my-4">
    <h1 style="color: var(--prt-brown);"><i class="bi bi-truck"></i> Shipping Information</h1>

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Shipping Options</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Method</th>
                                    <th>Estimated Delivery</th>
                                    <th>Cost</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Standard Shipping</strong></td>
                                    <td>5-7 business days</td>
                                    <td>$5.99 (Free over $50)</td>
                                </tr>
                                <tr>
                                    <td><strong>Expedited Shipping</strong></td>
                                    <td>2-3 business days</td>
                                    <td>$12.99</td>
                                </tr>
                                <tr>
                                    <td><strong>Express Shipping</strong></td>
                                    <td>1-2 business days</td>
                                    <td>$24.99</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Processing Time</h5>
                </div>
                <div class="card-body">
                    <p>
                        Orders are typically processed within 1-2 business days. Orders placed on
                        weekends or holidays will be processed on the next business day.
                    </p>
                    <p class="mb-0">
                        During peak seasons, processing times may be slightly longer. We'll notify you
                        if there are any delays with your order.
                    </p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Tracking Your Order</h5>
                </div>
                <div class="card-body">
                    <p>
                        Once your order ships, you'll receive an email with tracking information.
                        You can also track your order status in your account.
                    </p>
                    <a href="{{ route('account.orders.index') }}" class="btn btn-outline-primary">
                        <i class="bi bi-box-seam"></i> View My Orders
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Shipping Restrictions</h5>
                </div>
                <div class="card-body">
                    <ul class="mb-0">
                        <li>We currently ship within the United States only</li>
                        <li>P.O. Box addresses may only receive Standard Shipping</li>
                        <li>Some oversized items may have additional shipping charges</li>
                        <li>Certain items may have shipping restrictions due to regulations</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-4 border-success">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-truck"></i> Free Shipping</h5>
                </div>
                <div class="card-body text-center">
                    <p class="fs-4 fw-bold mb-2">Orders Over $50</p>
                    <p class="text-muted mb-0">Standard shipping is free on all orders over $50!</p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-geo-alt"></i> Delivery Areas</h5>
                </div>
                <div class="card-body">
                    <p>We ship to all 50 US states including:</p>
                    <ul class="mb-0">
                        <li>Continental United States</li>
                        <li>Alaska</li>
                        <li>Hawaii</li>
                    </ul>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-question-circle"></i> Questions?</h5>
                </div>
                <div class="card-body">
                    <p>Need help with shipping? Our team is happy to assist.</p>
                    <a href="{{ route('contact') }}" class="btn btn-primary w-100">
                        <i class="bi bi-envelope"></i> Contact Us
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
