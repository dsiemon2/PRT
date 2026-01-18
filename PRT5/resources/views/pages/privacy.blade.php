@extends('layouts.app')

@section('title', 'Privacy Policy')

@section('content')
<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item active">Privacy Policy</li>
        </ol>
    </nav>
</div>

<div class="container my-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <h1 style="color: var(--prt-brown);">Privacy Policy</h1>
            <p class="text-muted">Last updated: {{ date('F j, Y') }}</p>

            <div class="card">
                <div class="card-body">
                    <h4>Information We Collect</h4>
                    <p>We collect information you provide directly to us, including:</p>
                    <ul>
                        <li>Name, email address, and contact information</li>
                        <li>Billing and shipping addresses</li>
                        <li>Payment information (processed securely through our payment providers)</li>
                        <li>Order history and preferences</li>
                    </ul>

                    <h4>How We Use Your Information</h4>
                    <p>We use the information we collect to:</p>
                    <ul>
                        <li>Process and fulfill your orders</li>
                        <li>Communicate with you about your orders and account</li>
                        <li>Send promotional communications (with your consent)</li>
                        <li>Improve our products and services</li>
                        <li>Comply with legal obligations</li>
                    </ul>

                    <h4>Information Sharing</h4>
                    <p>
                        We do not sell your personal information. We may share your information with:
                    </p>
                    <ul>
                        <li>Service providers who assist in our operations</li>
                        <li>Payment processors to complete transactions</li>
                        <li>Shipping carriers to deliver your orders</li>
                        <li>Law enforcement when required by law</li>
                    </ul>

                    <h4>Data Security</h4>
                    <p>
                        We implement appropriate security measures to protect your personal information.
                        However, no method of transmission over the Internet is 100% secure.
                    </p>

                    <h4>Your Rights</h4>
                    <p>You have the right to:</p>
                    <ul>
                        <li>Access your personal information</li>
                        <li>Correct inaccurate information</li>
                        <li>Request deletion of your information</li>
                        <li>Opt-out of marketing communications</li>
                    </ul>

                    <h4>Contact Us</h4>
                    <p>
                        If you have questions about this Privacy Policy, please
                        <a href="{{ route('contact') }}">contact us</a>.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
