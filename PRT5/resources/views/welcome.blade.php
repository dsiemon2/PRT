@extends('layouts.app')

@section('title', 'Pecos River Traders - Quality Western Footwear & Boots')

@section('content')
@php
    use App\Models\Category;
    use App\Models\Product;
    use App\Services\HomepageService;

    $homepageService = new HomepageService();

    // Try to get featured categories from API first (like prt4)
    $apiFeaturedCategories = $homepageService->getFeaturedCategoriesData();
    $categoryColumnClasses = $homepageService->getCategoryCardColumnClasses();

    if ($apiFeaturedCategories !== null && !empty($apiFeaturedCategories)) {
        // Use API data
        $featuredCategories = collect($apiFeaturedCategories);
        $categoryDescriptions = [];
        foreach ($featuredCategories as $cat) {
            $categoryDescriptions[$cat['CategoryCode']] = $cat['description'] ?? '';
        }
    } else {
        // Fallback to database query with hardcoded IDs (like prt4)
        $featuredCategoryIds = $homepageService->getFeaturedCategoryIds();
        $featuredCategories = Category::whereIn('CategoryCode', $featuredCategoryIds)
            ->withCount('products')
            ->get()
            ->sortBy(function($cat) use ($featuredCategoryIds) {
                return array_search($cat->CategoryCode, $featuredCategoryIds);
            })
            ->map(function($cat) {
                return [
                    'CategoryCode' => $cat->CategoryCode,
                    'Category' => $cat->Category,
                    'image' => $cat->image,
                    'products_count' => $cat->products_count,
                ];
            });

        // Default category descriptions (fallback)
        $categoryDescriptions = [
            59 => "Durable men's boots built for work and outdoor activities. Classic styles and reliable construction.",
            67 => "Stylish and comfortable women's boots. Perfect for any occasion from casual to work wear.",
            65 => "Comfortable sandals for warm weather. Casual and dressy styles for every summer occasion.",
            58 => "Easy on, easy off. Comfortable slip-on footwear for casual everyday wear.",
            62 => "Convenient and comfortable women's slip-on shoes. Perfect for busy lifestyles.",
            66 => "Trendy and stylish footwear for fashion-conscious women. Stand out in style.",
        ];
    }

    // Get featured products from API
    $featuredProductsVisible = $homepageService->isFeaturedProductsVisible();
    $featuredProductsTitle = $homepageService->getFeaturedProductsSectionTitle();

    // Only fetch products if section is visible
    if ($featuredProductsVisible) {
        $apiFeaturedProducts = $homepageService->getFeaturedProductsData();
        if (!empty($apiFeaturedProducts)) {
            $featuredProducts = collect($apiFeaturedProducts);
        } else {
            // Fallback to database - keep as Eloquent objects for simpler handling
            $featuredProducts = Product::where('stock_quantity', '>', 0)
                ->take(6)
                ->get();
        }
    } else {
        $featuredProducts = collect([]);
    }

    // Get homepage banners
    $showFeaturedCategories = $homepageService->isFeaturedCategoriesVisible();
    $showFeaturedProducts = $homepageService->isFeaturedProductsVisible();
@endphp

@if($homepageService->hasBanners())
    {!! $homepageService->getBannersHtml() !!}
@else
<!-- Fallback Hero Section (shown when no banners configured) -->
<section class="hero-section">
    <div class="container text-center">
        <img src="{{ asset('assets/images/PRT-LOGO-sm1.png') }}" alt="Pecos River Traders Logo" class="hero-logo mb-4" onerror="this.style.display='none'">
        <h1 class="display-3 fw-bold text-white mb-3">Welcome to Pecos River Traders</h1>
        <p class="lead text-white mb-4">
            Quality footwear for work, casual wear, and outdoor activities.<br>
            <strong>COMFORTABLE. DURABLE. AFFORDABLE.</strong>
        </p>
        <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg me-2">
            <i class="bi bi-grid"></i> Shop All Products
        </a>
        @if($showFeaturedCategories)
        <a href="#featured" class="btn btn-outline-light btn-lg me-2">
            <i class="bi bi-stars"></i> Featured Categories
        </a>
        @endif
        @if($showFeaturedProducts)
        <a href="#featured-products" class="btn btn-outline-light btn-lg">
            <i class="bi bi-bag-heart"></i> Featured Products
        </a>
        @endif
    </div>
</section>
@endif

<!-- Featured Categories Section -->
@if($homepageService->isFeaturedCategoriesVisible() && count($featuredCategories) > 0)
<section id="featured" class="container my-5">
    <div class="text-center mb-5">
        <h2 class="display-5 fw-bold mb-3" style="color: var(--prt-brown);">Featured Categories</h2>
        <p class="lead text-muted">Explore our collection of quality footwear</p>
    </div>

    <div class="row g-4">
        @foreach($featuredCategories as $category)
            @php
                $catCode = is_array($category) ? $category['CategoryCode'] : $category->CategoryCode;
                $catName = is_array($category) ? $category['Category'] : $category->Category;
                $productCount = is_array($category) ? $category['products_count'] : $category->products_count;
                $description = $categoryDescriptions[$catCode] ?? 'Quality footwear for every occasion.';
                $catImage = is_array($category) ? ($category['image'] ?? '') : ($category->image ?? '');
                $imagePath = !empty($catImage) ? asset('assets/' . $catImage) : asset('assets/images/no-image.svg');
            @endphp
            <div class="{{ $categoryColumnClasses }}">
                <div class="category-card">
                    <img src="{{ $imagePath }}" alt="{{ $catName }}" class="card-img-top" onerror="this.src='{{ asset('assets/images/no-image.svg') }}'">
                    <div class="category-card-body">
                        <h3 class="category-card-title">{{ $catName }}</h3>
                        <p class="category-count">{{ $productCount }} Products Available</p>
                        <p class="card-text">{{ $description }}</p>
                        <a href="{{ route('products.index', ['category' => $catCode]) }}" class="btn btn-primary w-100">
                            <i class="bi bi-arrow-right"></i> Shop {{ $catName }}
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</section>
@endif

<!-- Featured Products Section -->
@if($featuredProductsVisible && count($featuredProducts) > 0)
<section id="featured-products" class="container my-5">
    <div class="text-center mb-5">
        <h2 class="display-5 fw-bold mb-3" style="color: var(--prt-brown);">{{ $featuredProductsTitle }}</h2>
        <p class="lead text-muted">Handpicked products just for you</p>
    </div>

    <div class="row g-4">
        @foreach($featuredProducts as $product)
            @php
                $isArray = is_array($product);
                $productId = $isArray ? ($product['ID'] ?? $product['id'] ?? $product['product_id'] ?? 0) : $product->ID;
                $productName = $isArray ? ($product['ShortDescription'] ?? $product['label'] ?? $product['name'] ?? '') : $product->ShortDescription;

                // Handle image path - prt4 API uses 'product_image', database uses 'Image'
                if ($isArray) {
                    // From API: uses product_image field
                    $productImage = $product['product_image'] ?? $product['Image'] ?? $product['image'] ?? '';
                    if (!empty($productImage)) {
                        $imagePath = asset('assets/' . $productImage);
                    } else {
                        $imagePath = asset('assets/images/no-image.svg');
                    }
                } else {
                    // From Eloquent: use the primaryImage accessor (already includes asset())
                    $imagePath = $product->primaryImage;
                }

                $price = $isArray ? floatval($product['price'] ?? $product['UnitPrice'] ?? 0) : floatval($product->UnitPrice);
                $salePrice = $isArray ? (!empty($product['sale_price']) ? floatval($product['sale_price']) : null) : (!empty($product->sale_price) ? floatval($product->sale_price) : null);
                $displayPrice = ($salePrice && $salePrice < $price) ? $salePrice : $price;
                $isOnSale = $salePrice && $salePrice < $price;
                $stockQty = $isArray ? ($product['quantity'] ?? $product['stock_quantity'] ?? 1) : ($product->stock_quantity ?? 1);
                $inStock = intval($stockQty) > 0;
            @endphp
            <div class="col-12 col-md-6 col-lg-4">
                <div class="featured-product-card">
                    <a href="{{ route('products.show', $productId) }}" class="text-decoration-none">
                        <img src="{{ $imagePath }}"
                             alt="{{ $productName }}"
                             class="card-img-top"
                             onerror="this.src='{{ asset('assets/images/no-image.svg') }}'">
                    </a>
                    @if($isOnSale)
                        <span class="badge bg-danger position-absolute" style="top: 10px; right: 10px;">SALE</span>
                    @endif
                    <div class="featured-product-card-body">
                        <a href="{{ route('products.show', $productId) }}" class="text-decoration-none">
                            <h3 class="featured-product-card-title">{{ $productName }}</h3>
                        </a>
                        <p class="featured-product-card-price">
                            @if($isOnSale)
                                <span class="text-decoration-line-through text-muted me-2">${{ number_format($price, 2) }}</span>
                            @endif
                            <span class="{{ $isOnSale ? 'text-danger' : '' }}">${{ number_format($displayPrice, 2) }}</span>
                        </p>
                        @if($inStock)
                            <a href="{{ route('cart.add', ['product_id' => $productId]) }}"
                               class="btn btn-primary w-100 add-to-cart-btn">
                                <i class="bi bi-cart-plus"></i> Add to Cart
                            </a>
                        @else
                            <span class="btn btn-secondary w-100 disabled">Out of Stock</span>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="text-center mt-5">
        <a href="{{ route('products.index') }}" class="btn btn-outline-primary btn-lg">
            <i class="bi bi-grid me-2"></i>View All Products
        </a>
    </div>
</section>
@endif

<!-- Why Choose Us Section -->
<section class="py-5" style="background: white;">
    <div class="container">
        <div class="row g-4">
            <div class="col-12 col-md-4 text-center">
                <div class="info-card">
                    <i class="bi bi-truck display-4 mb-3" style="color: var(--prt-red);"></i>
                    <h3>Fast Shipping</h3>
                    <p class="text-muted">Quick and reliable delivery on all orders</p>
                </div>
            </div>
            <div class="col-12 col-md-4 text-center">
                <div class="info-card">
                    <i class="bi bi-shield-check display-4 mb-3" style="color: var(--prt-red);"></i>
                    <h3>Quality Guaranteed</h3>
                    <p class="text-muted">100% satisfaction guarantee on all products</p>
                </div>
            </div>
            <div class="col-12 col-md-4 text-center">
                <div class="info-card">
                    <i class="bi bi-headset display-4 mb-3" style="color: var(--prt-red);"></i>
                    <h3>Customer Support</h3>
                    <p class="text-muted">Friendly support team ready to help</p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
