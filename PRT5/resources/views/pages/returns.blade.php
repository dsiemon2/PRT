@extends('layouts.app')

@section('title', 'Returns & Exchanges')

@section('content')
<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item active">Returns & Exchanges</li>
        </ol>
    </nav>
</div>

<div class="container my-4">
    <h1 style="color: var(--prt-brown);"><i class="bi bi-arrow-return-left"></i> Returns & Exchanges</h1>

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Return Policy</h5>
                </div>
                <div class="card-body">
                    <p>
                        We want you to be completely satisfied with your purchase. If you're not happy
                        with your order, we offer a 30-day return policy.
                    </p>
                    <h6>Eligible Items</h6>
                    <ul>
                        <li>Items must be returned within 30 days of delivery</li>
                        <li>Items must be unused and in original packaging</li>
                        <li>Items must have all original tags attached</li>
                    </ul>
                    <h6>Non-Returnable Items</h6>
                    <ul>
                        <li>Personalized or custom-made items</li>
                        <li>Items marked as final sale</li>
                        <li>Gift cards</li>
                    </ul>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">How to Return</h5>
                </div>
                <div class="card-body">
                    <ol>
                        <li class="mb-2">
                            <strong>Contact Us:</strong> Reach out to our customer service team to
                            initiate your return and receive a Return Authorization (RA) number.
                        </li>
                        <li class="mb-2">
                            <strong>Package Your Item:</strong> Securely pack the item in its original
                            packaging with all tags attached.
                        </li>
                        <li class="mb-2">
                            <strong>Ship Your Return:</strong> Send your package to the address provided
                            with your RA number visible on the outside.
                        </li>
                        <li class="mb-2">
                            <strong>Receive Your Refund:</strong> Once we receive and inspect your return,
                            we'll process your refund within 5-7 business days.
                        </li>
                    </ol>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Exchanges</h5>
                </div>
                <div class="card-body">
                    <p>
                        Want a different size or color? We're happy to help! For exchanges, please
                        contact our customer service team and we'll guide you through the process.
                    </p>
                    <p class="mb-0">
                        Exchanges are subject to product availability. If your desired item is out of stock,
                        we'll process a refund for your return.
                    </p>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-clock"></i> Refund Timeline</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success"></i>
                            Processing: 2-3 business days
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success"></i>
                            Credit card: 5-7 business days
                        </li>
                        <li>
                            <i class="bi bi-check-circle text-success"></i>
                            PayPal: 3-5 business days
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-question-circle"></i> Need Help?</h5>
                </div>
                <div class="card-body">
                    <p>Our customer service team is here to assist you with any return or exchange questions.</p>
                    <a href="{{ route('contact') }}" class="btn btn-primary w-100">
                        <i class="bi bi-envelope"></i> Contact Us
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
