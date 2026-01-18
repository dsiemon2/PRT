# API Integration Guide

Last Updated: December 22, 2025

## Overview

PRT5 integrates with the Admin API (http://localhost:8300) for dynamic settings and configuration. This guide documents all API endpoints used and how they're consumed in Laravel.

## Admin API Endpoints

### Branding Settings

**Endpoint:** `GET /api/v1/admin/settings`

**Used by:** `App\Services\BrandingService`

**Response Structure:**
```json
{
    "success": true,
    "data": {
        "branding": {
            "logo_path": "assets/images/PRT-High-Res-Logo.png",
            "logo_alignment": "left|center|right",
            "site_title": "Pecos River Traders",
            "header_bg_color": "#8B4513",
            "header_text_color": "#FFFFFF",
            "header_hover_color": "#FFD700",
            "header_style": "solid|gradient|transparent",
            "header_sticky": true,
            "header_shadow": true,
            "nav_height": "70",
            "announcement_enabled": false,
            "announcement_text": "",
            "announcement_bg": "#C41E3A",
            "announcement_text_color": "#FFFFFF",
            "theme_primary": "#8B4513",
            "theme_secondary": "#C41E3A",
            "theme_accent": "#FFD700"
        }
    }
}
```

**Laravel Usage:**
```php
use App\Services\BrandingService;

$brandingService = new BrandingService();
$settings = $brandingService->getSettings();
$navbarClasses = $brandingService->getNavbarClasses();
$themeCSS = $brandingService->getThemeCSS();
$navbarCSS = $brandingService->getNavbarCSS();
$announcementBar = $brandingService->getAnnouncementBar();
```

### Feature Flags

**Endpoint:** `GET /api/v1/admin/settings/features`

**Used by:** `App\Services\FeaturesService`

**Response Structure:**
```json
{
    "success": true,
    "data": {
        "faq_enabled": true,
        "loyalty_enabled": true,
        "gift_cards_enabled": true,
        "wishlists_enabled": true,
        "blog_enabled": true,
        "events_enabled": true,
        "reviews_enabled": true,
        "newsletter_enabled": true,
        "admin_link_enabled": true,
        "digital_downloads_enabled": true,
        "digital_download_categories": "103"
    }
}
```

**Laravel Usage:**
```php
use App\Services\FeaturesService;

$featuresService = new FeaturesService();

// Check if feature is enabled
if ($featuresService->isEnabled('loyalty')) {
    // Show loyalty features
}

// Get configuration value
$categories = $featuresService->getConfig('digital_download_categories');
```

### Featured Categories

**Endpoint:** `GET /api/v1/featured-categories`

**Used by:** `App\Services\HomepageService`

**Response Structure:**
```json
{
    "success": true,
    "data": {
        "is_visible": true,
        "featured_categories": [
            {
                "category_id": 59,
                "label": "Western Wear",
                "category_image": "categories/western.jpg",
                "products_count": 45,
                "description": "Authentic western clothing"
            }
        ]
    }
}
```

**Laravel Usage:**
```php
use App\Services\HomepageService;

$homepageService = new HomepageService();
$categories = $homepageService->getFeaturedCategoriesData();
$isVisible = $homepageService->isFeaturedCategoriesVisible();
```

### Featured Products

**Endpoint:** `GET /api/v1/featured-products`

**Used by:** `App\Services\HomepageService`

**Response Structure:**
```json
{
    "success": true,
    "data": {
        "is_visible": true,
        "section_title": "Featured Products",
        "featured_products": [
            {
                "product_id": 123,
                "label": "Custom Label",
                "product_image": "products/item.jpg",
                "price": 29.99,
                "sale_price": 24.99
            }
        ]
    }
}
```

**Laravel Usage:**
```php
$products = $homepageService->getFeaturedProductsData();
$title = $homepageService->getFeaturedProductsSectionTitle();
$isVisible = $homepageService->isFeaturedProductsVisible();
```

### Category Display Settings

**Endpoint:** `GET /api/v1/admin/settings/category_display`

**Used by:** `App\Services\HomepageService`

**Response Structure:**
```json
{
    "success": true,
    "data": {
        "category_display_style": "cards|list|grid",
        "category_cards_per_row_desktop": 3,
        "category_cards_per_row_tablet": 2,
        "category_cards_per_row_mobile": 1,
        "category_hover_effect": "lift|zoom|none",
        "category_show_product_count": true,
        "category_show_description": true,
        "featured_category_ids": "59,67,65,58,62,66"
    }
}
```

**Laravel Usage:**
```php
$style = $homepageService->getCategoryDisplayStyle();
$columnClasses = $homepageService->getCategoryCardColumnClasses();
$categoryIds = $homepageService->getFeaturedCategoryIds();
```

## Caching Strategy

All API responses are cached to minimize requests:

| Service | Cache Key | TTL |
|---------|-----------|-----|
| BrandingService | `branding_settings` | 30 seconds |
| FeaturesService | `feature_settings` | 30 seconds |
| HomepageService | `featured_categories_config` | 5 seconds |
| HomepageService | `featured_products_config` | 5 seconds |
| HomepageService | `category_display_settings` | 5 seconds |

### Clearing Cache

```php
// Clear all caches
Cache::forget('branding_settings');
Cache::forget('feature_settings');
Cache::forget('featured_categories_config');
Cache::forget('featured_products_config');
Cache::forget('category_display_settings');

// Or use service methods
$featuresService->clearCache();
```

## Error Handling

All services implement fallback defaults when API is unavailable:

```php
try {
    $response = Http::timeout(3)->get($this->apiBaseUrl . '/endpoint');

    if ($response->successful()) {
        // Process response
    }
} catch (\Exception $e) {
    \Log::warning('Failed to fetch settings: ' . $e->getMessage());
}

// Return defaults if API fails
return $this->getDefaults();
```

## Database Direct Access

Some data is fetched directly from the database for performance:

### Products
```php
use App\Models\Product;

// Get products with images
$products = Product::where('Active', 1)
    ->with('images')
    ->paginate(20);
```

### Categories
```php
use App\Models\Category;

$categories = Category::withCount('products')
    ->orderBy('Category')
    ->get();
```

### Loyalty Points
```php
// Direct DB query for loyalty balance
$loyaltyAccount = DB::table('loyalty_members')
    ->where('user_id', auth()->id())
    ->first();
$points = $loyaltyAccount->available_points ?? 0;
```

## API vs Database Decision Matrix

| Data Type | Source | Reason |
|-----------|--------|--------|
| Branding settings | API | Admin-controlled, dynamic |
| Feature flags | API | Admin-controlled, dynamic |
| Featured categories | API | Admin-curated selection |
| Featured products | API | Admin-curated selection |
| All products | Database | Performance, pagination |
| User data | Database | Authentication, security |
| Orders | Database | Transaction integrity |
| Loyalty points | Database | Real-time accuracy |

## Testing API Connections

```bash
# Test branding endpoint
curl http://localhost:8300/api/v1/admin/settings

# Test features endpoint
curl http://localhost:8300/api/v1/admin/settings/features

# Test featured categories
curl http://localhost:8300/api/v1/featured-categories

# Test featured products
curl http://localhost:8300/api/v1/featured-products
```
