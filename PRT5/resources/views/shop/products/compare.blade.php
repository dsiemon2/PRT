@extends('layouts.app')

@section('title', 'Compare Products')

@section('content')
<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Products</a></li>
            <li class="breadcrumb-item active">Compare</li>
        </ol>
    </nav>
</div>

<div class="container my-4">
    <h1 style="color: var(--prt-brown);"><i class="bi bi-arrow-left-right"></i> Compare Products</h1>

    @if($products->count() > 0)
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th style="width: 150px;">Feature</th>
                        @foreach($products as $product)
                            <th class="text-center" style="min-width: 200px;">
                                <button type="button"
                                        class="btn btn-sm btn-outline-danger float-end"
                                        title="Remove from comparison"
                                        onclick="removeFromComparison({{ $product->ID }})">
                                    <i class="bi bi-x"></i>
                                </button>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    {{-- Product Image --}}
                    <tr>
                        <th>Image</th>
                        @foreach($products as $product)
                            <td class="text-center">
                                <a href="{{ route('products.show', $product->ID) }}">
                                    <img src="{{ $product->primaryImage }}"
                                         alt="{{ $product->ShortDescription }}"
                                         style="max-height: 150px; max-width: 100%; object-fit: contain;"
                                         onerror="this.src='{{ asset('assets/images/no-image.svg') }}'">
                                </a>
                            </td>
                        @endforeach
                    </tr>

                    {{-- Product Name --}}
                    <tr>
                        <th>Name</th>
                        @foreach($products as $product)
                            <td class="text-center">
                                <a href="{{ route('products.show', $product->ID) }}"
                                   class="text-decoration-none fw-bold" style="color: var(--prt-brown);">
                                    {{ $product->ShortDescription }}
                                </a>
                            </td>
                        @endforeach
                    </tr>

                    {{-- Item Number --}}
                    <tr>
                        <th>Item #</th>
                        @foreach($products as $product)
                            <td class="text-center">{{ $product->ItemNumber }}</td>
                        @endforeach
                    </tr>

                    {{-- Price --}}
                    <tr>
                        <th>Price</th>
                        @foreach($products as $product)
                            <td class="text-center">
                                <span class="fs-5 fw-bold text-primary">${{ number_format($product->UnitPrice, 2) }}</span>
                            </td>
                        @endforeach
                    </tr>

                    {{-- Category --}}
                    <tr>
                        <th>Category</th>
                        @foreach($products as $product)
                            <td class="text-center">{{ $product->category->Category ?? 'N/A' }}</td>
                        @endforeach
                    </tr>

                    {{-- Size --}}
                    <tr>
                        <th>Size</th>
                        @foreach($products as $product)
                            <td class="text-center">{{ $product->ItemSize ?: 'N/A' }}</td>
                        @endforeach
                    </tr>

                    {{-- UPC --}}
                    <tr>
                        <th>UPC</th>
                        @foreach($products as $product)
                            <td class="text-center">{{ $product->UPC ?: 'N/A' }}</td>
                        @endforeach
                    </tr>

                    {{-- Availability --}}
                    <tr>
                        <th>Availability</th>
                        @foreach($products as $product)
                            <td class="text-center">
                                @if($product->stock_quantity > 0)
                                    <span class="badge bg-success">In Stock</span>
                                @else
                                    <span class="badge bg-danger">Out of Stock</span>
                                @endif
                            </td>
                        @endforeach
                    </tr>

                    {{-- Add to Cart --}}
                    <tr>
                        <th>Action</th>
                        @foreach($products as $product)
                            <td class="text-center">
                                <a href="{{ url('cart/add') }}?upc={{ $product->UPC ?: $product->ItemNumber }}&qty=1"
                                   class="btn btn-primary btn-sm">
                                    <i class="bi bi-cart-plus"></i> Add to Cart
                                </a>
                            </td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between mt-4">
            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Continue Shopping
            </a>
            <button type="button" class="btn btn-outline-danger" onclick="clearComparison()">
                <i class="bi bi-trash"></i> Clear All
            </button>
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bi bi-arrow-left-right text-muted" style="font-size: 4rem;"></i>
                <h3 class="mt-3">No Products to Compare</h3>
                <p class="text-muted">Add products to your comparison list to see them side by side.</p>
                <a href="{{ route('products.index') }}" class="btn btn-primary">
                    <i class="bi bi-shop"></i> Browse Products
                </a>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
function clearComparison() {
    if (!confirm('Remove all products from comparison?')) return;

    fetch('{{ route("products.compare.remove") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN
        },
        body: JSON.stringify({ clear_all: true })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showToast('success', 'Comparison cleared');
            location.reload();
        } else {
            showToast('danger', data.message || 'Failed to clear comparison');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('danger', 'An error occurred. Please try again.');
    });
}

function removeFromComparison(productId) {
    if (!confirm('Remove this product from comparison?')) return;

    fetch('{{ route("products.compare.remove") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN
        },
        body: JSON.stringify({ product_id: productId })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showToast('success', 'Product removed from comparison');
            location.reload();
        } else {
            showToast('danger', data.message || 'Failed to remove product');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('danger', 'An error occurred. Please try again.');
    });
}
</script>
@endpush
@endsection
