@extends('layouts.app')

@section('title', 'Gift Cards')

@section('content')
<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item active">Gift Cards</li>
        </ol>
    </nav>
</div>

<div class="container my-4">
    <div class="text-center mb-5">
        <h1 style="color: var(--prt-brown);"><i class="bi bi-gift"></i> Gift Cards</h1>
        <p class="lead text-muted">The perfect gift for any occasion</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bi bi-gift text-primary" style="font-size: 5rem;"></i>
                    <h3 class="mt-4">Gift Cards Coming Soon!</h3>
                    <p class="text-muted">
                        We're working on bringing you digital and physical gift cards.
                        Check back soon for this exciting feature!
                    </p>
                    <a href="{{ route('products.index') }}" class="btn btn-primary mt-3">
                        <i class="bi bi-shop"></i> Shop Our Products
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
