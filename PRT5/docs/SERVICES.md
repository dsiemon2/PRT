# Services Documentation

Last Updated: December 22, 2025

## Overview

PRT5 uses service classes to encapsulate API communication and business logic. Services are located in `app/Services/`.

## BrandingService

**File:** `app/Services/BrandingService.php`

Handles all branding and theme settings from the admin API.

### Methods

#### getSettings(): array
Fetches and caches all branding settings.

```php
$brandingService = new BrandingService();
$settings = $brandingService->getSettings();

// Returns:
[
    'logo_path' => 'assets/images/PRT-High-Res-Logo.png',
    'logo_alignment' => 'left',
    'site_title' => 'Pecos River Traders',
    'bg_color' => '#8B4513',
    'text_color' => '#FFFFFF',
    'hover_color' => '#FFD700',
    'style' => 'gradient',
    'nav_height' => '70',
    'sticky' => true,
    'shadow' => true,
    'announcement_enabled' => false,
    'announcement_text' => '',
    'announcement_bg' => '#C41E3A',
    'announcement_text_color' => '#FFFFFF',
    'theme' => [
        'primary' => '#8B4513',
        'secondary' => '#C41E3A',
        'accent' => '#FFD700',
        'text_dark' => '#333333',
        'text_light' => '#FFFFFF',
        'bg' => '#F5F5F5'
    ]
]
```

#### getNavbarClasses(): string
Returns CSS classes for the navbar element.

```php
$classes = $brandingService->getNavbarClasses();
// "navbar navbar-expand-lg navbar-dark navbar-custom sticky-top logo-align-left"
```

#### getLogoAlignment(): string
Returns the logo alignment setting.

```php
$alignment = $brandingService->getLogoAlignment();
// "left", "center", or "right"
```

#### getLogoWrapperClasses(): string
Returns CSS classes for the logo wrapper div.

```php
$classes = $brandingService->getLogoWrapperClasses();
// "navbar-logo-wrapper logo-left"
```

#### getThemeCSS(): string
Generates CSS variables for theme colors.

```php
echo $brandingService->getThemeCSS();
// Outputs <style> tag with :root CSS variables
```

#### getNavbarCSS(): string
Generates complete navbar styling CSS including logo alignment rules.

```php
echo $brandingService->getNavbarCSS();
// Outputs <style> tag with .navbar-custom rules
```

#### getAnnouncementBar(): string
Generates announcement bar HTML if enabled.

```php
echo $brandingService->getAnnouncementBar();
// Outputs announcement HTML or empty string
```

### Usage in Blade Templates

```blade
@php
    $brandingService = new App\Services\BrandingService();
@endphp

{!! $brandingService->getThemeCSS() !!}
{!! $brandingService->getNavbarCSS() !!}
{!! $brandingService->getAnnouncementBar() !!}

<nav class="{{ $brandingService->getNavbarClasses() }}">
    <div class="{{ $brandingService->getLogoWrapperClasses() }}">
        <!-- Logo -->
    </div>
</nav>
```

---

## FeaturesService

**File:** `app/Services/FeaturesService.php`

Manages feature flags for enabling/disabling site functionality.

### Methods

#### getFeatures(): array
Fetches and caches all feature settings.

```php
$featuresService = new FeaturesService();
$features = $featuresService->getFeatures();

// Returns all feature flags as array
```

#### isEnabled(string $featureName): bool
Checks if a specific feature is enabled.

```php
if ($featuresService->isEnabled('loyalty')) {
    // Show loyalty program features
}

if ($featuresService->isEnabled('wishlists')) {
    // Show wishlist functionality
}
```

**Available Features:**
- `faq` - FAQ page
- `loyalty` - Loyalty rewards program
- `gift_cards` - Gift card functionality
- `wishlists` - User wishlists
- `blog` - Blog section
- `events` - Events calendar
- `reviews` - Product reviews
- `newsletter` - Newsletter signup
- `admin_link` - Admin link in navigation
- `digital_downloads` - Digital product downloads
- `specialty_products` - Specialty product handling

#### getConfig(string $key): mixed
Gets non-boolean configuration values.

```php
$categories = $featuresService->getConfig('digital_download_categories');
// "103"
```

#### clearCache(): void
Clears the feature settings cache.

```php
$featuresService->clearCache();
```

### Usage in Blade Templates

```blade
@php
    $featuresService = new App\Services\FeaturesService();
@endphp

@if($featuresService->isEnabled('loyalty'))
    <a href="{{ route('loyalty') }}">Rewards Program</a>
@endif

@if($featuresService->isEnabled('gift_cards'))
    <a href="{{ route('gift-cards') }}">Gift Cards</a>
@endif
```

---

## HomepageService

**File:** `app/Services/HomepageService.php`

Handles homepage configuration including featured categories and products.

### Methods

#### getFeaturedCategoriesConfig(): array
Gets raw featured categories configuration from API.

```php
$homepageService = new HomepageService();
$config = $homepageService->getFeaturedCategoriesConfig();

// Returns:
[
    'is_visible' => true,
    'categories' => [...]
]
```

#### getFeaturedCategoriesData(): ?array
Gets categories formatted for display.

```php
$categories = $homepageService->getFeaturedCategoriesData();

// Returns array of categories or null to use database fallback
foreach ($categories as $category) {
    echo $category['Category'];
    echo $category['CategoryCode'];
    echo $category['image'];
    echo $category['products_count'];
}
```

#### isFeaturedCategoriesVisible(): bool
Checks if featured categories section should be displayed.

```php
if ($homepageService->isFeaturedCategoriesVisible()) {
    // Show featured categories section
}
```

#### getFeaturedProductsConfig(): array
Gets raw featured products configuration from API.

```php
$config = $homepageService->getFeaturedProductsConfig();

// Returns:
[
    'is_visible' => false,
    'section_title' => 'Featured Products',
    'products' => [...]
]
```

#### getFeaturedProductsData(): array
Gets products formatted for display.

```php
$products = $homepageService->getFeaturedProductsData();
```

#### getFeaturedProductsSectionTitle(): string
Gets the section title for featured products.

```php
$title = $homepageService->getFeaturedProductsSectionTitle();
// "Featured Products" or custom title
```

#### isFeaturedProductsVisible(): bool
Checks if featured products section should be displayed.

```php
if ($homepageService->isFeaturedProductsVisible()) {
    // Show featured products section
}
```

#### getCategoryDisplaySettings(): array
Gets category display configuration.

```php
$settings = $homepageService->getCategoryDisplaySettings();
```

#### getCategoryDisplayStyle(): string
Gets the display style for categories.

```php
$style = $homepageService->getCategoryDisplayStyle();
// "cards", "list", or "grid"
```

#### getCategoryCardColumnClasses(): string
Gets Bootstrap column classes based on cards per row settings.

```php
$classes = $homepageService->getCategoryCardColumnClasses();
// "col-12 col-md-6 col-lg-4"
```

#### getFeaturedCategoryIds(): array
Gets array of featured category IDs.

```php
$ids = $homepageService->getFeaturedCategoryIds();
// [59, 67, 65, 58, 62, 66]
```

### Usage in Blade Templates

```blade
@php
    $homepageService = new App\Services\HomepageService();
    $featuredCategories = $homepageService->getFeaturedCategoriesData();
    $columnClasses = $homepageService->getCategoryCardColumnClasses();
@endphp

@if($homepageService->isFeaturedCategoriesVisible())
    <section class="featured-categories">
        <div class="row">
            @foreach($featuredCategories as $category)
                <div class="{{ $columnClasses }}">
                    <!-- Category card -->
                </div>
            @endforeach
        </div>
    </section>
@endif
```

---

## Creating New Services

To create a new service:

1. Create file in `app/Services/`:

```php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class MyNewService
{
    protected $apiBaseUrl = 'http://localhost:8300/api/v1';

    public function getData(): array
    {
        $cached = Cache::get('my_cache_key');
        if ($cached) {
            return $cached;
        }

        try {
            $response = Http::timeout(3)->get($this->apiBaseUrl . '/endpoint');

            if ($response->successful()) {
                $data = $response->json();
                if ($data['success'] ?? false) {
                    Cache::put('my_cache_key', $data['data'], 30);
                    return $data['data'];
                }
            }
        } catch (\Exception $e) {
            \Log::warning('API failed: ' . $e->getMessage());
        }

        return $this->getDefaults();
    }

    protected function getDefaults(): array
    {
        return [
            // Default values
        ];
    }
}
```

2. Use in controller or view:

```php
use App\Services\MyNewService;

$service = new MyNewService();
$data = $service->getData();
```
