<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class HomepageService
{
    protected $apiBaseUrl;

    public function __construct()
    {
        $this->apiBaseUrl = rtrim(config('services.api.base_url', 'http://localhost:8300/api/v1'), '/');
    }

    /**
     * Get featured categories configuration from API
     */
    public function getFeaturedCategoriesConfig(): array
    {
        $cached = Cache::get('featured_categories_config');
        if ($cached) {
            return $cached;
        }

        try {
            $response = Http::timeout(2)->get($this->apiBaseUrl . '/featured-categories');

            if ($response->successful()) {
                $data = $response->json();
                if ($data && isset($data['success']) && $data['success'] && isset($data['data'])) {
                    $config = [
                        'is_visible' => $data['data']['is_visible'] ?? true,
                        'categories' => $data['data']['featured_categories'] ?? []
                    ];
                    Cache::put('featured_categories_config', $config, 5);
                    return $config;
                }
            }
        } catch (\Exception $e) {
            \Log::warning('Failed to fetch featured categories: ' . $e->getMessage());
        }

        return $this->getDefaultFeaturedCategoriesConfig();
    }

    /**
     * Get featured categories data ready for display
     */
    public function getFeaturedCategoriesData(): ?array
    {
        $config = $this->getFeaturedCategoriesConfig();

        if (!$config['is_visible']) {
            return []; // Section is hidden
        }

        if (!empty($config['categories'])) {
            // Transform API data to match expected format
            $categories = [];
            foreach ($config['categories'] as $fc) {
                $categories[] = [
                    'CategoryCode' => $fc['category_id'],
                    'Category' => $fc['label'],
                    'image' => $fc['category_image'],
                    'products_count' => $fc['products_count'],
                    'description' => $fc['description']
                ];
            }
            return $categories;
        }

        return null; // Use default database query
    }

    /**
     * Get featured products configuration from API
     */
    public function getFeaturedProductsConfig(): array
    {
        $cached = Cache::get('featured_products_config');
        if ($cached) {
            return $cached;
        }

        try {
            $response = Http::timeout(2)->get($this->apiBaseUrl . '/featured-products');

            if ($response->successful()) {
                $data = $response->json();
                if ($data && isset($data['success']) && $data['success'] && isset($data['data'])) {
                    $config = [
                        'is_visible' => $data['data']['is_visible'] ?? false,
                        'section_title' => $data['data']['section_title'] ?? 'Featured Products',
                        'products' => $data['data']['featured_products'] ?? []
                    ];
                    Cache::put('featured_products_config', $config, 5);
                    return $config;
                }
            }
        } catch (\Exception $e) {
            \Log::warning('Failed to fetch featured products: ' . $e->getMessage());
        }

        return $this->getDefaultFeaturedProductsConfig();
    }

    /**
     * Get featured products data
     */
    public function getFeaturedProductsData(): array
    {
        $config = $this->getFeaturedProductsConfig();

        if (!$config['is_visible']) {
            return [];
        }

        return $config['products'] ?? [];
    }

    /**
     * Get featured products section title
     */
    public function getFeaturedProductsSectionTitle(): string
    {
        $config = $this->getFeaturedProductsConfig();
        return $config['section_title'] ?? 'Featured Products';
    }

    /**
     * Check if featured products section is visible
     */
    public function isFeaturedProductsVisible(): bool
    {
        $config = $this->getFeaturedProductsConfig();
        return $config['is_visible'] ?? false;
    }

    /**
     * Check if featured categories section is visible
     */
    public function isFeaturedCategoriesVisible(): bool
    {
        $config = $this->getFeaturedCategoriesConfig();
        return $config['is_visible'] ?? true;
    }

    /**
     * Get category display settings from API
     */
    public function getCategoryDisplaySettings(): array
    {
        $cached = Cache::get('category_display_settings');
        if ($cached) {
            return $cached;
        }

        try {
            $response = Http::timeout(2)->get($this->apiBaseUrl . '/admin/settings/category_display');

            if ($response->successful()) {
                $data = $response->json();
                if ($data && isset($data['success']) && $data['success'] && isset($data['data'])) {
                    Cache::put('category_display_settings', $data['data'], 5);
                    return $data['data'];
                }
            }
        } catch (\Exception $e) {
            \Log::warning('Failed to fetch category display settings: ' . $e->getMessage());
        }

        return $this->getDefaultCategoryDisplaySettings();
    }

    /**
     * Get category display style
     */
    public function getCategoryDisplayStyle(): string
    {
        $settings = $this->getCategoryDisplaySettings();
        return $settings['category_display_style'] ?? 'cards';
    }

    /**
     * Get featured category IDs
     */
    public function getFeaturedCategoryIds(): array
    {
        $settings = $this->getCategoryDisplaySettings();
        $ids = $settings['featured_category_ids'] ?? '59,67,65,58,62,66';
        return array_map('intval', explode(',', $ids));
    }

    /**
     * Get Bootstrap column classes for category cards
     */
    public function getCategoryCardColumnClasses(): string
    {
        $settings = $this->getCategoryDisplaySettings();

        $desktop = $settings['category_cards_per_row_desktop'] ?? 3;
        $tablet = $settings['category_cards_per_row_tablet'] ?? 2;
        $mobile = $settings['category_cards_per_row_mobile'] ?? 1;

        $lgCol = floor(12 / $desktop);
        $mdCol = floor(12 / $tablet);
        $smCol = floor(12 / $mobile);

        return "col-{$smCol} col-md-{$mdCol} col-lg-{$lgCol}";
    }

    /**
     * Default featured categories config
     */
    protected function getDefaultFeaturedCategoriesConfig(): array
    {
        return [
            'is_visible' => true,
            'categories' => null
        ];
    }

    /**
     * Default featured products config
     */
    protected function getDefaultFeaturedProductsConfig(): array
    {
        return [
            'is_visible' => false,
            'section_title' => 'Featured Products',
            'products' => []
        ];
    }

    /**
     * Default category display settings
     */
    protected function getDefaultCategoryDisplaySettings(): array
    {
        return [
            'category_display_style' => 'cards',
            'category_cards_per_row_desktop' => '3',
            'category_cards_per_row_tablet' => '2',
            'category_cards_per_row_mobile' => '1',
            'category_hover_effect' => 'lift',
            'category_show_product_count' => true,
            'category_show_description' => true,
            'featured_category_ids' => '59,67,65,58,62,66',
        ];
    }

    /**
     * Get homepage banners from API
     */
    public function getBanners(): array
    {
        $cached = Cache::get('homepage_banners');
        if ($cached) {
            return $cached;
        }

        try {
            $response = Http::timeout(3)->get($this->apiBaseUrl . '/banners');

            if ($response->successful()) {
                $data = $response->json();
                if ($data) {
                    $result = [
                        'banners' => $data['data'] ?? [],
                        'settings' => $data['settings'] ?? []
                    ];
                    Cache::put('homepage_banners', $result, 30);
                    return $result;
                }
            }
        } catch (\Exception $e) {
            \Log::warning('Failed to fetch homepage banners: ' . $e->getMessage());
        }

        return ['banners' => [], 'settings' => []];
    }

    /**
     * Check if there are banners to display
     */
    public function hasBanners(): bool
    {
        $data = $this->getBanners();
        return !empty($data['banners']);
    }

    /**
     * Get banners HTML for homepage
     */
    public function getBannersHtml(): string
    {
        $data = $this->getBanners();
        $banners = $data['banners'] ?? [];
        $settings = $data['settings'] ?? [];

        if (empty($banners)) {
            return '';
        }

        $slideDuration = ($settings['slide_duration'] ?? 5) * 1000;
        $showIndicators = $settings['show_indicators'] ?? true;
        $showControls = $settings['show_controls'] ?? true;
        $transition = $settings['transition'] ?? 'slide';
        $bannerHeight = $settings['banner_height'] ?? 400;
        $mobileBannerHeight = $settings['mobile_banner_height'] ?? 250;

        $carouselId = 'homepage-banner-carousel';
        $slideClass = $transition === 'fade' ? 'carousel-fade' : '';

        $html = "
<style>
.homepage-banner-carousel {
    width: 100%;
    overflow: hidden;
}
.homepage-banner-carousel .carousel-item {
    position: relative;
    height: {$bannerHeight}px;
}
.homepage-banner-carousel .carousel-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.homepage-banner-carousel .carousel-caption {
    background: transparent;
    bottom: 0;
    top: 0;
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: 2rem;
}
.homepage-banner-carousel .carousel-caption h2 {
    font-size: 2.5rem;
    font-weight: 700;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
}
.homepage-banner-carousel .carousel-caption p {
    font-size: 1.25rem;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
}
.homepage-banner-carousel .carousel-caption .btn {
    display: inline-block;
    width: auto;
}
.banner-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
}
@media (max-width: 768px) {
    .homepage-banner-carousel .carousel-item {
        height: {$mobileBannerHeight}px;
    }
    .homepage-banner-carousel .carousel-caption h2 {
        font-size: 1.5rem;
    }
    .homepage-banner-carousel .carousel-caption p {
        font-size: 1rem;
    }
}
</style>

<div id=\"{$carouselId}\" class=\"carousel {$slideClass} homepage-banner-carousel\" data-bs-ride=\"carousel\" data-bs-interval=\"{$slideDuration}\">";

        // Indicators
        if ($showIndicators && count($banners) > 1) {
            $html .= '<div class="carousel-indicators">';
            foreach ($banners as $index => $banner) {
                $active = $index === 0 ? 'class="active"' : '';
                $html .= "<button type=\"button\" data-bs-target=\"#{$carouselId}\" data-bs-slide-to=\"{$index}\" {$active}></button>";
            }
            $html .= '</div>';
        }

        // Slides
        $html .= '<div class="carousel-inner">';
        foreach ($banners as $index => $banner) {
            $active = $index === 0 ? ' active' : '';
            $title = e($banner['title'] ?? '');
            $subtitle = e($banner['subtitle'] ?? '');
            $desktopImage = $banner['desktop_image'] ?? '';
            $linkUrl = e($banner['link_url'] ?? '');
            $linkText = e($banner['link_text'] ?? 'Shop Now');
            $altText = e($banner['alt_text'] ?? $title);
            $textPosition = $banner['text_position'] ?? 'center';
            $overlayColor = e($banner['overlay_color'] ?? 'rgba(0,0,0,0.3)');
            $textColor = e($banner['text_color'] ?? '#FFFFFF');

            // Build image URL - use storefront URL for browser access
            $storefrontUrl = rtrim(config('services.storefront.url', 'http://localhost:8300'), '/');
            $imageUrl = strpos($desktopImage, 'http') === 0 ? $desktopImage : "{$storefrontUrl}/{$desktopImage}";

            // Text alignment
            $alignClass = '';
            switch ($textPosition) {
                case 'left':
                    $alignClass = 'text-start start-0 end-auto';
                    break;
                case 'right':
                    $alignClass = 'text-end start-auto end-0';
                    break;
                default:
                    $alignClass = 'text-center';
            }

            $html .= "
<div class=\"carousel-item{$active}\">
    <div class=\"banner-overlay\" style=\"background: {$overlayColor};\"></div>
    <img src=\"{$imageUrl}\" alt=\"{$altText}\" class=\"d-block w-100\">
    <div class=\"carousel-caption {$alignClass}\" style=\"color: {$textColor};\">";

            if (!empty($title)) {
                $html .= "<h2 style=\"color: {$textColor};\">{$title}</h2>";
            }
            if (!empty($subtitle)) {
                $html .= "<p style=\"color: {$textColor};\">{$subtitle}</p>";
            }
            if (!empty($linkUrl)) {
                $html .= "<a href=\"{$linkUrl}\" class=\"btn btn-primary btn-lg mt-3\">{$linkText}</a>";
            }

            $html .= "</div></div>";
        }
        $html .= '</div>';

        // Controls
        if ($showControls && count($banners) > 1) {
            $html .= "
<button class=\"carousel-control-prev\" type=\"button\" data-bs-target=\"#{$carouselId}\" data-bs-slide=\"prev\">
    <span class=\"carousel-control-prev-icon\"></span>
</button>
<button class=\"carousel-control-next\" type=\"button\" data-bs-target=\"#{$carouselId}\" data-bs-slide=\"next\">
    <span class=\"carousel-control-next-icon\"></span>
</button>";
        }

        $html .= '</div>';

        return $html;
    }
}
