@extends('layouts.app')

@section('title', $categoryName . ' - Products')

@push('styles')
<style>
    .product-card {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        transition: transform 0.3s, box-shadow 0.3s;
        overflow: hidden;
        height: 100%;
    }
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    }
    .product-card img {
        height: 200px;
        object-fit: contain;
        width: 100%;
        background: #f8f9fa;
    }
    .product-card-body {
        padding: 15px;
    }
    .product-card-title {
        font-size: 1rem;
        min-height: 48px;
    }
    .product-price {
        font-size: 1.25rem;
        font-weight: bold;
        color: var(--prt-brown);
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
            @if($filters['catid'])
                <li class="breadcrumb-item active">{{ $categoryName }}</li>
            @else
                <li class="breadcrumb-item active">All Products</li>
            @endif
        </ol>
    </nav>
</div>

<div class="container my-4">
    <div class="row">
        {{-- Sidebar --}}
        <div class="col-lg-3 mb-4">
            <div class="sticky-sidebar">
                {{-- Categories --}}
                <div class="sidebar-categories">
                    <h5><i class="bi bi-funnel"></i> Filter by Category</h5>
                    <a href="{{ route('products.index') }}"
                       class="category-link {{ !$filters['catid'] ? 'active' : '' }}">
                        <i class="bi bi-grid-3x3-gap"></i> All Products
                        <span class="badge bg-secondary float-end">{{ $products->total() }}</span>
                    </a>
                    @foreach($categories as $cat)
                        <a href="{{ route('products.index', ['catid' => $cat->CategoryCode]) }}"
                           class="category-link {{ $filters['catid'] == $cat->CategoryCode ? 'active' : '' }}">
                            <i class="bi bi-box-seam"></i> {{ $cat->Category }}
                            <span class="badge bg-secondary float-end">{{ $cat->products_count }}</span>
                        </a>
                    @endforeach
                </div>

                {{-- Search --}}
                <div class="sidebar-categories">
                    <h5><i class="bi bi-search"></i> Search Products</h5>
                    <form method="GET" action="{{ route('products.index') }}">
                        @if($filters['catid'])
                            <input type="hidden" name="catid" value="{{ $filters['catid'] }}">
                        @endif
                        <div class="input-group">
                            <input type="text" name="search" class="form-control"
                                   placeholder="Search..." value="{{ $filters['search'] }}">
                            <button class="btn btn-primary" type="submit" data-bs-toggle="tooltip" title="Search for products">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Advanced Filters --}}
                <div class="sidebar-categories">
                    <h5><i class="bi bi-sliders"></i> Advanced Filters</h5>
                    <form method="GET" action="{{ route('products.index') }}">
                        @if($filters['catid'])
                            <input type="hidden" name="catid" value="{{ $filters['catid'] }}">
                        @endif
                        @if($filters['search'])
                            <input type="hidden" name="search" value="{{ $filters['search'] }}">
                        @endif

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Price Range</label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <input type="number" name="min_price" class="form-control form-control-sm"
                                           placeholder="Min" step="0.01" min="0" value="{{ $filters['min_price'] }}">
                                </div>
                                <div class="col-6">
                                    <input type="number" name="max_price" class="form-control form-control-sm"
                                           placeholder="Max" step="0.01" min="0" value="{{ $filters['max_price'] }}">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Size</label>
                            <select name="size" class="form-select form-select-sm">
                                <option value="">All Sizes</option>
                                @foreach($sizes as $size)
                                    <option value="{{ $size }}" {{ $filters['size'] == $size ? 'selected' : '' }}>
                                        {{ $size }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Sort By</label>
                            <select name="sort" class="form-select form-select-sm">
                                <option value="" {{ $filters['sort'] == '' ? 'selected' : '' }}>Default Order</option>
                                <option value="newest" {{ $filters['sort'] == 'newest' ? 'selected' : '' }}>Newest First</option>
                                <option value="price_low" {{ $filters['sort'] == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                                <option value="price_high" {{ $filters['sort'] == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                                <option value="name_asc" {{ $filters['sort'] == 'name_asc' ? 'selected' : '' }}>Name: A to Z</option>
                                <option value="name_desc" {{ $filters['sort'] == 'name_desc' ? 'selected' : '' }}>Name: Z to A</option>
                            </select>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-sm" data-bs-toggle="tooltip" title="Apply selected filters to products">
                                <i class="bi bi-funnel-fill"></i> Apply Filters
                            </button>
                            <a href="{{ route('products.index', $filters['catid'] ? ['catid' => $filters['catid']] : []) }}"
                               class="btn btn-outline-secondary btn-sm" data-bs-toggle="tooltip" title="Reset all filters to default">
                                <i class="bi bi-x-circle"></i> Clear Filters
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Products Grid --}}
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-2" style="color: var(--prt-brown);">{{ $categoryName }}</h2>
                    <p class="text-muted">
                        Showing {{ $products->firstItem() ?? 0 }} - {{ $products->lastItem() ?? 0 }}
                        of {{ $products->total() }} products
                        @if($filters['search'])
                            for "{{ $filters['search'] }}"
                        @endif
                    </p>
                </div>
            </div>

            @if($products->count() > 0)
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mb-4">
                    @foreach($products as $product)
                        <div class="col">
                            <div class="product-card position-relative">
                                {{-- Wishlist Heart Icon --}}
                                <button class="btn btn-sm position-absolute top-0 end-0 m-2 wishlist-btn"
                                        style="z-index: 10; background: white; border-radius: 50%; width: 40px; height: 40px; border: none; box-shadow: 0 2px 5px rgba(0,0,0,0.2);"
                                        onclick="toggleWishlist({{ $product->ID }})"
                                        data-product-id="{{ $product->ID }}"
                                        title="Add to wishlist">
                                    <i class="bi bi-heart" style="color: var(--prt-red, #c41e3a); font-size: 1.2rem;"></i>
                                </button>

                                <a href="{{ route('products.show', $product->ID) }}">
                                    <img src="{{ $product->primaryImage }}"
                                         alt="{{ $product->ShortDescription }}"
                                         onerror="this.src='{{ asset('assets/images/no-image.svg') }}'">
                                </a>

                                <div class="product-card-body">
                                    <h5 class="product-card-title">
                                        <a href="{{ route('products.show', $product->ID) }}"
                                           class="text-decoration-none" style="color: var(--prt-brown);">
                                            {{ $product->ShortDescription ?: 'Product #' . $product->ItemNumber }}
                                        </a>
                                    </h5>

                                    @if($product->LngDescription)
                                        <p class="text-muted small mb-3" style="min-height: 3rem;">
                                            {{ Str::limit($product->LngDescription, 100) }}
                                        </p>
                                    @else
                                        <div style="min-height: 3rem;"></div>
                                    @endif

                                    <p class="text-muted small mb-2">
                                        @if($product->ItemNumber)
                                            <i class="bi bi-upc"></i> {{ $product->ItemNumber }}
                                        @endif
                                    </p>

                                    <div class="product-price mb-2">
                                        @if($product->UnitPrice)
                                            ${{ number_format($product->UnitPrice, 2) }}
                                        @endif
                                    </div>

                                    @if($product->track_inventory)
                                        <div class="mb-2">
                                            @php $status = $product->stockStatus; @endphp
                                            <span class="badge bg-{{ $status == 'In Stock' ? 'success' : ($status == 'Low Stock' ? 'warning' : 'danger') }}">
                                                <i class="bi bi-box-seam"></i> {{ $status }}
                                            </span>
                                        </div>
                                    @endif

                                    <div class="d-grid gap-2">
                                        <button onclick="showQuickView({{ $product->ID }})"
                                                class="btn btn-outline-primary btn-sm" data-bs-toggle="tooltip" title="Preview product details in a popup">
                                            <i class="bi bi-eye-fill"></i> Quick View
                                        </button>
                                        <button onclick="addToCart('{{ $product->UPC ?: $product->ItemNumber }}', {{ $product->ID }})"
                                                class="btn btn-primary" data-bs-toggle="tooltip" title="Add this product to your shopping cart">
                                            <i class="bi bi-cart-plus"></i> Add to Cart
                                        </button>
                                        <button onclick="addToCompareList({{ $product->ID }}, this)"
                                                class="btn btn-outline-info btn-sm" data-bs-toggle="tooltip" title="Add to compare list to compare with other products">
                                            <i class="bi bi-arrow-left-right"></i> Compare
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                @if($products->hasPages())
                    <nav aria-label="Product pagination">
                        <ul class="pagination justify-content-center">
                            <li class="page-item {{ $products->onFirstPage() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $products->previousPageUrl() }}" data-bs-toggle="tooltip" title="Go to previous page">
                                    <i class="bi bi-chevron-left"></i> Previous
                                </a>
                            </li>

                            @foreach($products->getUrlRange(max(1, $products->currentPage() - 2), min($products->lastPage(), $products->currentPage() + 2)) as $page => $url)
                                <li class="page-item {{ $page == $products->currentPage() ? 'active' : '' }}">
                                    <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                </li>
                            @endforeach

                            <li class="page-item {{ !$products->hasMorePages() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $products->nextPageUrl() }}" data-bs-toggle="tooltip" title="Go to next page">
                                    Next <i class="bi bi-chevron-right"></i>
                                </a>
                            </li>
                        </ul>
                    </nav>
                @endif
            @else
                <div class="alert alert-info text-center py-5">
                    <i class="bi bi-search display-1 d-block mb-3" style="color: var(--prt-brown);"></i>
                    <h3>No Products Found</h3>
                    <p class="mb-3">
                        @if($filters['search'])
                            No products match your search for "{{ $filters['search'] }}".
                        @else
                            No products available in this category.
                        @endif
                    </p>
                    <a href="{{ route('products.index') }}" class="btn btn-primary" data-bs-toggle="tooltip" title="Browse all available products">
                        <i class="bi bi-grid"></i> View All Products
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Quick View Modal -->
<div class="modal fade" id="quickViewModal" tabindex="-1" aria-labelledby="quickViewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="quickViewModalLabel">
                    <i class="bi bi-eye-fill"></i> Quick View
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="quickViewBody">
                <!-- Content loaded dynamically -->
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Initialize Bootstrap tooltips and check comparison status
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Check comparison status
    checkComparisonStatus();
});

// Check comparison status on page load
async function checkComparisonStatus() {
    try {
        const response = await fetch('{{ route("products.compare.count") }}');
        const data = await response.json();

        if (data.count > 0) {
            // Show comparison widget
            updateComparisonWidget(data.count);

            // Mark buttons for products already in comparison
            data.product_ids.forEach(productId => {
                const button = document.querySelector(`button[onclick*="addToCompareList(${productId}"]`);
                if (button) {
                    button.classList.remove('btn-outline-info');
                    button.classList.add('btn-info', 'text-white');
                    button.innerHTML = '<i class="bi bi-check"></i> Added';
                    button.disabled = true;
                }
            });
        }
    } catch (error) {
        console.error('Error checking comparison status:', error);
    }
}

function addToCart(upc, productId) {
    window.location.href = '{{ url("cart/add") }}?upc=' + encodeURIComponent(upc);
}

function addToCompareList(productId, button) {
    fetch('{{ route("products.compare.add") }}', {
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
            // Update button UI
            button.classList.remove('btn-outline-info');
            button.classList.add('btn-info', 'text-white');
            button.innerHTML = '<i class="bi bi-check"></i> Added';
            button.disabled = true;

            // Update comparison widget
            updateComparisonWidget(data.count);

            showToast('success', data.message + ' <a href="{{ route("products.compare") }}" class="alert-link text-white">View Comparison</a>');
        } else {
            showToast('warning', data.message || 'Failed to add to compare');
        }
    })
    .catch(error => {
        console.error('Comparison error:', error);
        showToast('danger', 'Failed to add to comparison. Please try again.');
    });
}

// Update comparison widget count
function updateComparisonWidget(count) {
    let widget = document.getElementById('comparisonWidget');

    if (!widget && count > 0) {
        // Create widget if it doesn't exist
        widget = document.createElement('div');
        widget.id = 'comparisonWidget';
        widget.className = 'position-fixed shadow-lg';
        widget.style.cssText = 'bottom: 20px; right: 20px; z-index: 1050; background: white; border-radius: 10px; padding: 15px; min-width: 200px;';
        widget.innerHTML = `
            <div class="d-flex align-items-center justify-content-between mb-2">
                <strong><i class="bi bi-arrow-left-right"></i> Compare</strong>
                <span class="badge bg-primary comparison-count">${count}</span>
            </div>
            <a href="{{ route('products.compare') }}" class="btn btn-primary btn-sm w-100">
                <i class="bi bi-eye"></i> View Comparison
            </a>
        `;
        document.body.appendChild(widget);
    } else if (widget) {
        if (count > 0) {
            widget.style.display = 'block';
            widget.querySelector('.comparison-count').textContent = count;
        } else {
            widget.style.display = 'none';
        }
    }
}

// Quick View Modal
async function showQuickView(productId) {
    const modal = new bootstrap.Modal(document.getElementById('quickViewModal'));
    const modalBody = document.getElementById('quickViewBody');

    // Show loading state
    modalBody.innerHTML = '<div class="text-center p-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>';
    modal.show();

    try {
        const response = await fetch(`{{ url('products') }}/${productId}`);
        const html = await response.text();

        // Parse the HTML to extract product info
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');

        // Extract product details
        const title = doc.querySelector('h1')?.textContent || 'Product';
        const image = doc.querySelector('#mainImage')?.src || '{{ asset("assets/images/no-image.svg") }}';
        const priceEl = doc.querySelector('.product-price');
        const price = priceEl ? priceEl.textContent.trim() : '';
        const description = doc.querySelector('.lead')?.textContent || '';

        // Extract item number from the table
        let itemNumber = '';
        const tableRows = doc.querySelectorAll('table tr');
        tableRows.forEach(row => {
            if (row.textContent.includes('Item Number')) {
                itemNumber = row.querySelector('td:last-child')?.textContent || '';
            }
        });

        // Try to get UPC from add to cart button
        let upc = itemNumber; // fallback to item number
        const addToCartBtn = doc.querySelector('[onclick*="addToCart"]');
        if (addToCartBtn) {
            const match = addToCartBtn.getAttribute('onclick').match(/addToCart\(\)/);
        }

        // Build modal content
        modalBody.innerHTML = `
            <div class="row">
                <div class="col-md-6 mb-3">
                    <img src="${image}" class="img-fluid rounded" alt="${title}" onerror="this.src='{{ asset("assets/images/no-image.svg") }}'">
                </div>
                <div class="col-md-6">
                    <h3 style="color: var(--prt-brown);">${title}</h3>
                    <p class="text-muted"><i class="bi bi-upc"></i> ${itemNumber}</p>
                    <h2 class="text-primary mb-3">${price}</h2>
                    <p>${description}</p>
                    <div class="mb-3">
                        <label class="form-label"><strong>Quantity:</strong></label>
                        <div class="input-group" style="max-width: 150px;">
                            <button class="btn btn-outline-secondary" type="button" onclick="adjustQuickViewQty(-1)">
                                <i class="bi bi-dash"></i>
                            </button>
                            <input type="number" class="form-control text-center" id="quickViewQuantity" value="1" min="1" max="99">
                            <button class="btn btn-outline-secondary" type="button" onclick="adjustQuickViewQty(1)">
                                <i class="bi bi-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="d-grid gap-2 mt-4">
                        <a href="{{ url('products') }}/${productId}" class="btn btn-outline-primary">
                            <i class="bi bi-eye"></i> View Full Details
                        </a>
                        <button onclick="addToCartFromQuickView('${itemNumber}')" class="btn btn-primary">
                            <i class="bi bi-cart-plus"></i> Add to Cart
                        </button>
                    </div>
                </div>
            </div>
        `;
    } catch (error) {
        console.error('Error loading quick view:', error);
        modalBody.innerHTML = '<div class="alert alert-danger">Failed to load product details. Please try again.</div>';
    }
}

function adjustQuickViewQty(change) {
    const qtyInput = document.getElementById('quickViewQuantity');
    if (qtyInput) {
        const currentQty = parseInt(qtyInput.value) || 1;
        const newQty = Math.max(1, Math.min(99, currentQty + change));
        qtyInput.value = newQty;
    }
}

function addToCartFromQuickView(upc) {
    let url = '{{ url("cart/add") }}?upc=' + encodeURIComponent(upc);

    // Get quantity
    const qtyInput = document.getElementById('quickViewQuantity');
    const quantity = qtyInput ? parseInt(qtyInput.value) || 1 : 1;
    url += '&qty=' + quantity;

    window.location.href = url;
}

// Wishlist toggle function
async function toggleWishlist(productId) {
    @auth
    try {
        const response = await fetch('{{ route("account.wishlist.toggle") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN
            },
            body: JSON.stringify({ product_id: productId })
        });
        const data = await response.json();
        if (data.success) {
            showToast('success', data.message);
            // Update heart icon
            const btn = document.querySelector(`[data-product-id="${productId}"]`);
            if (btn) {
                const icon = btn.querySelector('i');
                if (data.in_wishlist) {
                    icon.classList.remove('bi-heart');
                    icon.classList.add('bi-heart-fill');
                } else {
                    icon.classList.remove('bi-heart-fill');
                    icon.classList.add('bi-heart');
                }
            }
        } else {
            showToast('warning', data.message || 'Failed to update wishlist');
        }
    } catch (error) {
        console.error('Wishlist error:', error);
        showToast('danger', 'Failed to update wishlist');
    }
    @else
    showToast('warning', 'Please login to add items to your wishlist');
    setTimeout(() => window.location.href = '{{ route("login") }}', 1500);
    @endauth
}

// Check wishlist status on page load
@auth
document.addEventListener('DOMContentLoaded', async function() {
    const wishlistButtons = document.querySelectorAll('.wishlist-btn');
    if (wishlistButtons.length === 0) return;

    const productIds = Array.from(wishlistButtons).map(btn => btn.dataset.productId);

    try {
        const response = await fetch('{{ route("account.wishlist.check") }}?product_ids=' + productIds.join(','));
        const data = await response.json();

        if (data.wishlisted && data.wishlisted.length > 0) {
            data.wishlisted.forEach(productId => {
                const btn = document.querySelector(`[data-product-id="${productId}"]`);
                if (btn) {
                    const icon = btn.querySelector('i');
                    icon.classList.remove('bi-heart');
                    icon.classList.add('bi-heart-fill');
                    btn.title = 'Remove from wishlist';
                }
            });
        }
    } catch (error) {
        console.error('Error checking wishlist status:', error);
    }
});
@endauth
</script>
@endpush
