@extends('layouts.app')

@section('title', $product->ShortDescription)

@push('styles')
<style>
    .product-gallery img {
        max-height: 400px;
        object-fit: contain;
        width: 100%;
        background: #f8f9fa;
        border-radius: 8px;
    }
    .product-thumbnail {
        width: 80px;
        height: 80px;
        object-fit: contain;
        cursor: pointer;
        border: 2px solid transparent;
        border-radius: 4px;
        transition: border-color 0.2s;
    }
    .product-thumbnail:hover,
    .product-thumbnail.active {
        border-color: var(--prt-red);
    }
    .product-price {
        font-size: 2rem;
        font-weight: bold;
        color: var(--prt-brown);
    }
    .rating-stars {
        color: #ffc107;
    }
    .review-card {
        border-left: 3px solid var(--prt-red);
    }
    .related-product {
        transition: transform 0.2s;
    }
    .related-product:hover {
        transform: translateY(-5px);
    }
    /* Star Rating Input */
    .star-rating {
        display: flex;
        flex-direction: row-reverse;
        justify-content: flex-end;
    }
    .star-rating input {
        display: none;
    }
    .star-rating label {
        font-size: 2rem;
        color: #ddd;
        cursor: pointer;
        transition: color 0.2s;
    }
    .star-rating label:hover,
    .star-rating label:hover ~ label,
    .star-rating input:checked ~ label {
        color: #ffc107;
    }

    /* Product Navigation Bar (Guitar Center Style) */
    .product-nav-bar {
        transition: all 0.3s ease;
    }

    .product-nav-bar.sticky {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 1030;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        animation: slideDown 0.3s ease;
    }

    @keyframes slideDown {
        from {
            transform: translateY(-100%);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .product-nav-bar .nav-link {
        color: var(--prt-brown, #5c4033);
        font-weight: 500;
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        white-space: nowrap;
        transition: all 0.2s ease;
    }

    .product-nav-bar .nav-link:hover {
        background-color: rgba(92, 64, 51, 0.1);
        color: var(--prt-brown, #5c4033);
    }

    .product-nav-bar .nav-link.active {
        background-color: var(--prt-brown, #5c4033);
        color: white;
    }

    .product-nav-bar .nav-pills {
        scrollbar-width: none;
        -ms-overflow-style: none;
    }

    .product-nav-bar .nav-pills::-webkit-scrollbar {
        display: none;
    }

    /* Section anchors offset for sticky nav */
    .product-section-anchor {
        scroll-margin-top: 70px;
    }

    /* Mobile responsiveness */
    @media (max-width: 576px) {
        .product-nav-bar .nav-link {
            padding: 0.4rem 0.7rem;
            font-size: 0.9rem;
        }
    }
</style>
@endpush

@section('content')
<div class="container mt-4">
    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Products</a></li>
            @if($product->category)
                <li class="breadcrumb-item">
                    <a href="{{ route('products.index', ['catid' => $product->CategoryCode]) }}">
                        {{ $product->category->Category }}
                    </a>
                </li>
            @endif
            <li class="breadcrumb-item active">{{ Str::limit($product->ShortDescription, 30) }}</li>
        </ol>
    </nav>
</div>

@php
    $featuresService = app(\App\Services\FeaturesService::class);
    $reviewCount = $product->reviews ? $product->reviews->count() : 0;
@endphp

@if($featuresService->isEnabled('product_sticky_bar'))
<!-- Product Page Navigation Bar (Guitar Center Style) -->
<nav class="product-nav-bar bg-light border-bottom" id="productNavBar">
    <div class="container">
        <ul class="nav nav-pills justify-content-center py-2 flex-nowrap overflow-auto" id="productNavTabs">
            <li class="nav-item">
                <a class="nav-link active" href="#gallery-section" data-section="gallery-section">
                    <i class="bi bi-images d-none d-sm-inline"></i> Gallery
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#description-section" data-section="description-section">
                    <i class="bi bi-file-text d-none d-sm-inline"></i> Description
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#specs-section" data-section="specs-section">
                    <i class="bi bi-list-ul d-none d-sm-inline"></i> Specs
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#reviews-section" data-section="reviews-section">
                    <i class="bi bi-star d-none d-sm-inline"></i> Reviews
                    <span class="badge bg-secondary ms-1">{{ $reviewCount }}</span>
                </a>
            </li>
        </ul>
    </div>
</nav>
@endif

<div class="container my-4">
    <div class="row">
        {{-- Product Images (Gallery Section) --}}
        <div class="col-lg-6 mb-4" id="gallery-section">
            <div class="product-gallery product-section-anchor">
                <img src="{{ $product->primaryImage }}"
                     alt="{{ $product->ShortDescription }}"
                     id="mainImage"
                     class="mb-3"
                     onerror="this.src='{{ asset('assets/images/no-image.svg') }}'">

                @if($product->images && $product->images->count() > 0)
                    <div class="d-flex gap-2 flex-wrap">
                        <img src="{{ $product->primaryImage }}"
                             class="product-thumbnail active"
                             onclick="changeImage(this, '{{ $product->primaryImage }}')">
                        @foreach($product->images as $image)
                            <img src="{{ asset('assets/' . $image->image_path) }}"
                                 class="product-thumbnail"
                                 onclick="changeImage(this, '{{ asset('assets/' . $image->image_path) }}')">
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Product Details (Description Section) --}}
        <div class="col-lg-6" id="description-section">
            <h1 class="product-section-anchor" style="color: var(--prt-brown);">{{ $product->ShortDescription }}</h1>

            @if($product->reviews && $product->reviews->count() > 0)
                <div class="mb-3">
                    <span class="rating-stars">
                        @php $avgRating = $product->reviews->avg('rating'); @endphp
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= $avgRating)
                                &#9733;
                            @else
                                &#9734;
                            @endif
                        @endfor
                    </span>
                    <span class="text-muted">({{ $product->reviews->count() }} reviews)</span>
                </div>
            @endif

            <div class="product-price mb-3">
                @if($product->UnitPrice)
                    ${{ number_format($product->UnitPrice, 2) }}
                @endif
            </div>

            @if($product->track_inventory)
                <div class="mb-3">
                    @php $status = $product->stockStatus; @endphp
                    <span class="badge bg-{{ $status == 'In Stock' ? 'success' : ($status == 'Low Stock' ? 'warning' : 'danger') }} fs-6">
                        <i class="bi bi-box-seam"></i> {{ $status }}
                    </span>
                    @if($product->availableQuantity > 0 && $product->availableQuantity <= $product->low_stock_threshold)
                        <small class="text-warning d-block mt-1">
                            <i class="bi bi-exclamation-triangle"></i> Only {{ $product->availableQuantity }} left!
                        </small>
                    @endif
                </div>
            @endif

            <p class="lead mb-4">{{ $product->LngDescription }}</p>

            {{-- Specs Section --}}
            <div id="specs-section" class="product-section-anchor"></div>
            <table class="table table-sm mb-4" style="max-width: 400px;">
                @if($product->ItemNumber)
                    <tr>
                        <td class="text-muted">Item Number</td>
                        <td>{{ $product->ItemNumber }}</td>
                    </tr>
                @endif
                @if($product->UPC)
                    <tr>
                        <td class="text-muted">UPC</td>
                        <td>{{ $product->UPC }}</td>
                    </tr>
                @endif
                @if($product->ItemSize)
                    <tr>
                        <td class="text-muted">Size</td>
                        <td>{{ $product->ItemSize }}</td>
                    </tr>
                @endif
                @if($product->category)
                    <tr>
                        <td class="text-muted">Category</td>
                        <td>{{ $product->category->Category }}</td>
                    </tr>
                @endif
            </table>

            {{-- Quantity Selector --}}
            <div class="mb-4">
                <label class="form-label fw-bold">Quantity:</label>
                <div class="input-group" style="max-width: 150px;">
                    <button class="btn btn-outline-secondary" type="button" onclick="adjustQty(-1)" data-bs-toggle="tooltip" title="Decrease quantity">
                        <i class="bi bi-dash"></i>
                    </button>
                    <input type="number" class="form-control text-center" id="quantity" value="1" min="1" max="99">
                    <button class="btn btn-outline-secondary" type="button" onclick="adjustQty(1)" data-bs-toggle="tooltip" title="Increase quantity">
                        <i class="bi bi-plus"></i>
                    </button>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="d-grid gap-2 mb-4" style="max-width: 400px;">
                <button onclick="addToCart()" class="btn btn-primary btn-lg" data-bs-toggle="tooltip" title="Add this product to your shopping cart">
                    <i class="bi bi-cart-plus"></i> Add to Cart
                </button>
                @auth
                    <button onclick="toggleWishlist({{ $product->ID }})" class="btn btn-outline-secondary" data-bs-toggle="tooltip" title="Save this product to your wishlist">
                        <i class="bi bi-heart"></i> Add to Wishlist
                    </button>
                @endauth
                <button onclick="addToCompare({{ $product->ID }})" class="btn btn-outline-info" data-bs-toggle="tooltip" title="Compare this product with others">
                    <i class="bi bi-arrow-left-right"></i> Add to Compare
                </button>
            </div>

            {{-- Share --}}
            <div class="mb-4">
                <span class="me-2">Share:</span>
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}"
                   target="_blank" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="Share on Facebook">
                    <i class="bi bi-facebook"></i>
                </a>
                <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($product->ShortDescription) }}"
                   target="_blank" class="btn btn-sm btn-outline-info" data-bs-toggle="tooltip" title="Share on Twitter">
                    <i class="bi bi-twitter"></i>
                </a>
                <a href="https://pinterest.com/pin/create/button/?url={{ urlencode(request()->url()) }}&media={{ urlencode($product->primaryImage) }}&description={{ urlencode($product->ShortDescription) }}"
                   target="_blank" class="btn btn-sm btn-outline-danger" data-bs-toggle="tooltip" title="Pin on Pinterest">
                    <i class="bi bi-pinterest"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- Reviews Section --}}
    <div class="row mt-5" id="reviews-section">
        <div class="col-12">
            <h3 class="mb-4 product-section-anchor"><i class="bi bi-star"></i> Customer Reviews</h3>

            @if($product->reviews && $product->reviews->count() > 0)
                <div class="row mb-4">
                    @foreach($product->reviews as $review)
                        <div class="col-md-6 mb-3">
                            <div class="card review-card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="rating-stars">
                                            @for($i = 1; $i <= 5; $i++)
                                                {{ $i <= $review->rating ? '★' : '☆' }}
                                            @endfor
                                        </span>
                                        <small class="text-muted">{{ $review->created_at->format('M j, Y') }}</small>
                                    </div>
                                    @if($review->title)
                                        <h6>{{ $review->title }}</h6>
                                    @endif
                                    <p class="mb-2">{{ $review->review }}</p>
                                    <small class="text-muted">
                                        By {{ $review->user->first_name ?? 'Customer' }}
                                        @if($review->is_verified_purchase)
                                            <span class="badge bg-info">Verified Purchase</span>
                                        @endif
                                    </small>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-muted mb-4">No reviews yet. Be the first to review this product!</p>
            @endif

            {{-- Write a Review Form --}}
            @auth
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-pencil"></i> Write a Review</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('reviews.store', $product->ID) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Your Rating <span class="text-danger">*</span></label>
                                <div class="star-rating">
                                    @for($i = 5; $i >= 1; $i--)
                                        <input type="radio" id="star{{ $i }}" name="rating" value="{{ $i }}" {{ old('rating') == $i ? 'checked' : '' }} required>
                                        <label for="star{{ $i }}" title="{{ $i }} stars">&#9733;</label>
                                    @endfor
                                </div>
                                @error('rating')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Review Title (Optional)</label>
                                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                                       value="{{ old('title') }}" placeholder="Summarize your review">
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Your Review <span class="text-danger">*</span></label>
                                <textarea name="review" class="form-control @error('review') is-invalid @enderror"
                                          rows="4" required placeholder="Share your experience with this product...">{{ old('review') }}</textarea>
                                @error('review')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Minimum 10 characters</small>
                            </div>
                            <button type="submit" class="btn btn-primary" data-bs-toggle="tooltip" title="Submit your review for this product">
                                <i class="bi bi-send"></i> Submit Review
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <div class="card bg-light">
                    <div class="card-body text-center py-4">
                        <p class="mb-3">Please log in to write a review.</p>
                        <a href="{{ route('login') }}" class="btn btn-primary" data-bs-toggle="tooltip" title="Sign in to write a review">
                            <i class="bi bi-person"></i> Login to Review
                        </a>
                    </div>
                </div>
            @endauth
        </div>
    </div>

    {{-- Frequently Bought Together --}}
    @if($frequentlyBought->count() > 0)
        <div class="row mt-5">
            <div class="col-12">
                <h3 class="mb-4" style="color: var(--prt-brown);">
                    <i class="bi bi-cart-check"></i> Customers Also Bought
                </h3>
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
                    @foreach($frequentlyBought as $fbProduct)
                        <div class="col">
                            <div class="card h-100 shadow-sm related-product">
                                <a href="{{ route('products.show', $fbProduct->ID) }}">
                                    <img src="{{ $fbProduct->primaryImage }}"
                                         class="card-img-top"
                                         style="height: 200px; object-fit: contain; background: var(--prt-cream);"
                                         alt="{{ $fbProduct->ShortDescription }}"
                                         onerror="this.src='{{ asset('assets/images/no-image.svg') }}'">
                                </a>
                                <div class="card-body">
                                    <h6 class="card-title" style="min-height: 40px;">
                                        <a href="{{ route('products.show', $fbProduct->ID) }}"
                                           class="text-decoration-none" style="color: var(--prt-brown);">
                                            {{ $fbProduct->ShortDescription }}
                                        </a>
                                    </h6>
                                    <p class="text-primary fw-bold mb-2">
                                        ${{ number_format($fbProduct->UnitPrice, 2) }}
                                    </p>
                                    @if($fbProduct->track_inventory)
                                        @php $fbStatus = $fbProduct->stockStatus; @endphp
                                        @if($fbStatus === 'In Stock')
                                            <span class="badge bg-success mb-2"><i class="bi bi-check-circle"></i> In Stock</span>
                                        @elseif($fbStatus === 'Low Stock')
                                            <span class="badge bg-warning mb-2"><i class="bi bi-exclamation-triangle"></i> Low Stock</span>
                                        @else
                                            <span class="badge bg-danger mb-2"><i class="bi bi-x-circle"></i> Out of Stock</span>
                                        @endif
                                    @endif
                                    <div class="d-grid">
                                        <a href="{{ route('products.show', $fbProduct->ID) }}" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="View full product details">
                                            <i class="bi bi-eye"></i> View Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    {{-- Related Products --}}
    @if($relatedProducts->count() > 0)
        <div class="row mt-5">
            <div class="col-12">
                <h3 class="mb-4" style="color: var(--prt-brown);"><i class="bi bi-grid"></i> Related Products You May Like</h3>
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
                    @foreach($relatedProducts as $related)
                        <div class="col">
                            <div class="card related-product h-100">
                                <a href="{{ route('products.show', $related->ID) }}">
                                    <img src="{{ $related->primaryImage }}"
                                         class="card-img-top"
                                         style="height: 150px; object-fit: contain;"
                                         alt="{{ $related->ShortDescription }}">
                                </a>
                                <div class="card-body">
                                    <h6>
                                        <a href="{{ route('products.show', $related->ID) }}"
                                           class="text-decoration-none" style="color: var(--prt-brown);">
                                            {{ Str::limit($related->ShortDescription, 40) }}
                                        </a>
                                    </h6>
                                    <p class="fw-bold mb-0">${{ number_format($related->UnitPrice, 2) }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
// Initialize Bootstrap tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

function changeImage(thumbnail, src) {
    document.getElementById('mainImage').src = src;
    document.querySelectorAll('.product-thumbnail').forEach(t => t.classList.remove('active'));
    thumbnail.classList.add('active');
}

function adjustQty(change) {
    const input = document.getElementById('quantity');
    const newValue = Math.max(1, Math.min(99, parseInt(input.value) + change));
    input.value = newValue;
}

function addToCart() {
    const qty = document.getElementById('quantity').value;
    window.location.href = '{{ url("cart/add") }}?upc={{ $product->UPC ?: $product->ItemNumber }}&qty=' + qty;
}

function addToCompare(productId) {
    fetch('{{ route("products.compare.add") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ product_id: productId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('success', data.message);
        } else {
            showToast('warning', data.message);
        }
    });
}

function toggleWishlist(productId) {
    fetch('{{ route("account.wishlist.toggle") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN
        },
        body: JSON.stringify({ product_id: productId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('success', data.message);
            // Update button text
            const btn = document.querySelector('[onclick*="toggleWishlist"]');
            if (btn) {
                const icon = btn.querySelector('i');
                if (data.in_wishlist) {
                    icon.classList.remove('bi-heart');
                    icon.classList.add('bi-heart-fill');
                    btn.innerHTML = '<i class="bi bi-heart-fill"></i> Remove from Wishlist';
                } else {
                    icon.classList.remove('bi-heart-fill');
                    icon.classList.add('bi-heart');
                    btn.innerHTML = '<i class="bi bi-heart"></i> Add to Wishlist';
                }
            }
        } else {
            showToast('warning', data.message || 'Failed to update wishlist');
        }
    })
    .catch(error => {
        console.error('Wishlist error:', error);
        showToast('danger', 'Failed to update wishlist');
    });
}

// ============================================
// Product Page Sticky Navigation Bar
// ============================================
@if($featuresService->isEnabled('product_sticky_bar'))
(function() {
    const navBar = document.getElementById('productNavBar');
    const navLinks = document.querySelectorAll('#productNavTabs .nav-link');
    const sections = ['gallery-section', 'description-section', 'specs-section', 'reviews-section'];
    let navBarOffset = 0;
    let isScrolling = false;
    let navPlaceholder = null;

    // Get nav bar position on load
    if (navBar) {
        navBarOffset = navBar.offsetTop;

        // Create placeholder to prevent content jump when nav becomes sticky
        navPlaceholder = document.createElement('div');
        navPlaceholder.style.display = 'none';
        navPlaceholder.style.height = navBar.offsetHeight + 'px';
        navBar.parentNode.insertBefore(navPlaceholder, navBar.nextSibling);
    }

    // Sticky scroll handler
    function handleScroll() {
        if (!navBar) return;

        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

        if (scrollTop > navBarOffset) {
            if (!navBar.classList.contains('sticky')) {
                navBar.classList.add('sticky');
                navPlaceholder.style.display = 'block';
            }
        } else {
            navBar.classList.remove('sticky');
            navPlaceholder.style.display = 'none';
        }

        // Update active section if not currently scrolling from click
        if (!isScrolling) {
            updateActiveSection();
        }
    }

    // Update active nav link based on scroll position
    function updateActiveSection() {
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        const navHeight = navBar ? navBar.offsetHeight + 20 : 70;

        let currentSection = sections[0]; // Default to first section

        for (const sectionId of sections) {
            const section = document.getElementById(sectionId);
            if (section) {
                const sectionTop = section.offsetTop - navHeight - 50;
                if (scrollTop >= sectionTop) {
                    currentSection = sectionId;
                }
            }
        }

        // Update active class
        navLinks.forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('data-section') === currentSection) {
                link.classList.add('active');
            }
        });
    }

    // Smooth scroll to section on click
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();

            const targetId = this.getAttribute('data-section');
            const targetSection = document.getElementById(targetId);

            if (targetSection) {
                isScrolling = true;

                // Update active class immediately
                navLinks.forEach(l => l.classList.remove('active'));
                this.classList.add('active');

                // Calculate offset (accounting for sticky nav)
                const navHeight = navBar ? navBar.offsetHeight : 0;
                const targetPosition = targetSection.offsetTop - navHeight - 10;

                // Smooth scroll
                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });

                // Reset scrolling flag after animation completes
                setTimeout(() => {
                    isScrolling = false;
                }, 1000);
            }
        });
    });

    // Throttle scroll event
    let scrollTimeout;
    window.addEventListener('scroll', function() {
        if (scrollTimeout) {
            window.cancelAnimationFrame(scrollTimeout);
        }
        scrollTimeout = window.requestAnimationFrame(handleScroll);
    });

    // Initial check
    handleScroll();
})();
@endif
</script>
@endpush
