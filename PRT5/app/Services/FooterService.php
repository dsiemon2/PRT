<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class FooterService
{
    protected $apiBaseUrl;

    public function __construct()
    {
        $this->apiBaseUrl = rtrim(config('services.api.base_url', 'http://localhost:8300/api/v1'), '/');
    }

    /**
     * Get footer configuration from API with caching
     */
    public function getConfig(): array
    {
        // Check cache first (30 second TTL)
        $cached = Cache::get('footer_config');
        if ($cached) {
            return $cached;
        }

        try {
            $response = Http::timeout(2)->get($this->apiBaseUrl . '/footer');

            if ($response->successful()) {
                $data = $response->json();
                if ($data && $data['success'] && isset($data['data'])) {
                    Cache::put('footer_config', $data['data'], 30);
                    return $data['data'];
                }
            }
        } catch (\Exception $e) {
            \Log::warning('Failed to fetch footer config: ' . $e->getMessage());
        }

        // Return defaults if API fails
        return $this->getDefaultConfig();
    }

    /**
     * Get default footer configuration matching prt4 structure
     */
    public function getDefaultConfig(): array
    {
        return [
            [
                'id' => 1,
                'title' => 'Shop',
                'position' => 1,
                'is_visible' => true,
                'column_type' => 'links',
                'links' => [
                    ['label' => 'Home', 'url' => '/', 'is_visible' => true, 'feature_flag' => null],
                    ['label' => 'All Products', 'url' => '/products', 'is_visible' => true, 'feature_flag' => null],
                    ['label' => 'Special Products', 'url' => '/products?special=1', 'is_visible' => true, 'feature_flag' => 'specialty_products'],
                    ['label' => 'Product List', 'url' => '/products', 'is_visible' => true, 'feature_flag' => null],
                    ['label' => 'Shopping Cart', 'url' => '/cart', 'is_visible' => true, 'feature_flag' => null],
                ]
            ],
            [
                'id' => 2,
                'title' => 'Resources',
                'position' => 2,
                'is_visible' => true,
                'column_type' => 'links',
                'links' => [
                    ['label' => 'Blog', 'url' => '/blog', 'is_visible' => true, 'feature_flag' => 'blog'],
                    ['label' => 'Events', 'url' => '/events', 'is_visible' => true, 'feature_flag' => 'events'],
                    ['label' => 'Sizing Guide', 'url' => '/sizing-guide', 'is_visible' => true, 'feature_flag' => null],
                    ['label' => 'Pecos Bill Legend', 'url' => '/pecos-bill', 'is_visible' => true, 'feature_flag' => null],
                    ['label' => 'About Pecos River', 'url' => '/pecos-river', 'is_visible' => true, 'feature_flag' => null],
                    ['label' => 'About Us', 'url' => '/about', 'is_visible' => true, 'feature_flag' => null],
                ]
            ],
            [
                'id' => 3,
                'title' => 'Customer Service',
                'position' => 3,
                'is_visible' => true,
                'column_type' => 'links',
                'links' => [
                    ['label' => 'Contact Us', 'url' => '/contact', 'is_visible' => true, 'feature_flag' => null],
                    ['label' => 'Shipping Policy', 'url' => '/shipping', 'is_visible' => true, 'feature_flag' => null],
                    ['label' => 'Return Policy', 'url' => '/returns', 'is_visible' => true, 'feature_flag' => null],
                    ['label' => 'Privacy Policy', 'url' => '/privacy', 'is_visible' => true, 'feature_flag' => null],
                ]
            ],
            [
                'id' => 4,
                'title' => 'Newsletter Signup',
                'position' => 4,
                'is_visible' => true,
                'column_type' => 'newsletter',
                'links' => []
            ]
        ];
    }

    /**
     * Clear footer cache
     */
    public function clearCache(): void
    {
        Cache::forget('footer_config');
    }
}
