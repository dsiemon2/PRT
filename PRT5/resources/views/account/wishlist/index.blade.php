@extends('layouts.app')

@section('title', 'My Wishlist')

@section('content')
<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('account.index') }}">My Account</a></li>
            <li class="breadcrumb-item active">Wishlist</li>
        </ol>
    </nav>
</div>

<div class="container my-4">
    <div class="row">
        {{-- Sidebar --}}
        <div class="col-md-3 mb-4">
            @include('account.partials.sidebar', ['active' => 'wishlist'])
        </div>

        {{-- Main Content --}}
        <div class="col-md-9">
            <h1 style="color: var(--prt-brown);"><i class="bi bi-heart"></i> My Wishlist</h1>

            @if($wishlistItems->count() > 0)
                <div class="row">
                    @foreach($wishlistItems as $item)
                        <div class="col-md-4 col-sm-6 mb-4">
                            <div class="card h-100">
                                @if($item->product)
                                    <a href="{{ route('products.show', $item->product->ID) }}">
                                        <img src="{{ $item->product->primaryImage }}"
                                             class="card-img-top"
                                             alt="{{ $item->product->ShortDescription }}"
                                             style="height: 200px; object-fit: contain; padding: 10px;"
                                             onerror="this.src='{{ asset('assets/images/no-image.svg') }}'">
                                    </a>
                                    <div class="card-body">
                                        <a href="{{ route('products.show', $item->product->ID) }}"
                                           class="text-decoration-none" style="color: var(--prt-brown);">
                                            <h6 class="card-title">{{ Str::limit($item->product->ShortDescription, 50) }}</h6>
                                        </a>
                                        <p class="card-text">
                                            <span class="fs-5 fw-bold text-primary">
                                                ${{ number_format($item->product->UnitPrice, 2) }}
                                            </span>
                                        </p>
                                        @if($item->product->stock_quantity > 0)
                                            <span class="badge bg-success mb-2">In Stock</span>
                                        @else
                                            <span class="badge bg-danger mb-2">Out of Stock</span>
                                        @endif
                                    </div>
                                    <div class="card-footer d-flex justify-content-between">
                                        <a href="{{ route('cart.add', ['product_id' => $item->product->ID, 'quantity' => 1]) }}"
                                           class="btn btn-primary btn-sm" data-bs-toggle="tooltip" title="Add this item to your cart">
                                            <i class="bi bi-cart-plus"></i> Add to Cart
                                        </a>
                                        <form action="{{ route('account.wishlist.destroy', $item) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm"
                                                    onclick="return confirm('Remove from wishlist?')" data-bs-toggle="tooltip" title="Remove from wishlist">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <div class="card-body text-center">
                                        <img src="{{ asset('assets/images/no-image.svg') }}"
                                             alt="Product unavailable"
                                             style="width: 100px; opacity: 0.5;">
                                        <p class="text-muted mt-3">Product no longer available</p>
                                        <form action="{{ route('account.wishlist.destroy', $item) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm" data-bs-toggle="tooltip" title="Remove unavailable item">
                                                <i class="bi bi-trash"></i> Remove
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $wishlistItems->links() }}
                </div>
            @else
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-heart text-muted" style="font-size: 4rem;"></i>
                        <h3 class="mt-3">Your wishlist is empty</h3>
                        <p class="text-muted">Save items you like to your wishlist and find them here.</p>
                        <a href="{{ route('products.index') }}" class="btn btn-primary" data-bs-toggle="tooltip" title="Find products to add to your wishlist">
                            <i class="bi bi-shop"></i> Browse Products
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
