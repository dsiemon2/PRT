@extends('layouts.app')

@section('title', 'Terms of Service')

@section('content')
<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item active">Terms of Service</li>
        </ol>
    </nav>
</div>

<div class="container my-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <h1 style="color: var(--prt-brown);">Terms of Service</h1>
            <p class="text-muted">Last updated: {{ date('F j, Y') }}</p>

            <div class="card">
                <div class="card-body">
                    <h4>Acceptance of Terms</h4>
                    <p>
                        By accessing and using this website, you accept and agree to be bound by the terms
                        and conditions of this agreement.
                    </p>

                    <h4>Use License</h4>
                    <p>
                        Permission is granted to temporarily download one copy of the materials on
                        Pecos River Trading's website for personal, non-commercial transitory viewing only.
                    </p>

                    <h4>Product Information</h4>
                    <p>
                        We strive to provide accurate product information, but we do not warrant that
                        product descriptions or other content is accurate, complete, reliable, current,
                        or error-free.
                    </p>

                    <h4>Pricing</h4>
                    <p>
                        Prices for our products are subject to change without notice. We reserve the right
                        to modify or discontinue any product without notice at any time.
                    </p>

                    <h4>Order Acceptance</h4>
                    <p>
                        We reserve the right to refuse or cancel any order for any reason, including
                        limitations on quantities available for purchase, inaccuracies, or errors in
                        product or pricing information.
                    </p>

                    <h4>Limitation of Liability</h4>
                    <p>
                        In no event shall Pecos River Trading be liable for any damages arising out of
                        the use or inability to use the materials on our website.
                    </p>

                    <h4>Governing Law</h4>
                    <p>
                        These terms shall be governed by and construed in accordance with the laws of
                        the State of Texas.
                    </p>

                    <h4>Contact Information</h4>
                    <p>
                        Questions about the Terms of Service should be sent to us at
                        <a href="{{ route('contact') }}">our contact page</a>.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
