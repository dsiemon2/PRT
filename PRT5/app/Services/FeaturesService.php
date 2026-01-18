<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class FeaturesService
{
    protected static $features = null;
    protected $apiBaseUrl;

    public function __construct()
    {
        $this->apiBaseUrl = rtrim(config('services.api.base_url', 'http://localhost:8300/api/v1'), '/');
    }

    /**
     * Get all feature settings from the admin API
     */
    public function getFeatures(): array
    {
        if (self::$features !== null) {
            return self::$features;
        }

        // Try cache first (30 second TTL like prt4)
        $cached = Cache::get('feature_settings');
        if ($cached) {
            self::$features = $cached;
            return $cached;
        }

        try {
            $response = Http::timeout(5)->get($this->apiBaseUrl . '/admin/settings/features');

            if ($response->successful()) {
                $data = $response->json();
                if ($data && isset($data['success']) && $data['success'] && isset($data['data'])) {
                    Cache::put('feature_settings', $data['data'], 30);
                    self::$features = $data['data'];
                    return self::$features;
                }
            }
        } catch (\Exception $e) {
            \Log::warning('Failed to fetch feature settings: ' . $e->getMessage());
        }

        // Return defaults if API fails
        self::$features = $this->getDefaults();
        return self::$features;
    }

    /**
     * Check if a feature is enabled
     */
    public function isEnabled(string $featureName): bool
    {
        $features = $this->getFeatures();
        $key = $featureName . '_enabled';

        if (!isset($features[$key])) {
            return false;
        }

        $value = $features[$key];

        // Check for various truthy values
        if ($value === true || $value === 'true' || $value === '1' || $value === 1) {
            return true;
        }

        return false;
    }

    /**
     * Get feature configuration value (for non-boolean settings)
     */
    public function getConfig(string $key)
    {
        $features = $this->getFeatures();
        return $features[$key] ?? null;
    }

    /**
     * Clear feature cache
     */
    public function clearCache(): void
    {
        Cache::forget('feature_settings');
        self::$features = null;
    }

    /**
     * Get default feature values if API fails
     */
    public function getDefaults(): array
    {
        return [
            'faq_enabled' => true,
            'loyalty_enabled' => true,
            'digital_downloads_enabled' => true,
            'specialty_products_enabled' => true,
            'gift_cards_enabled' => true,
            'wishlists_enabled' => true,
            'blog_enabled' => true,
            'events_enabled' => true,
            'reviews_enabled' => true,
            'product_sticky_bar_enabled' => true,
            'admin_link_enabled' => true,
            'tell_a_friend_enabled' => true,
            'newsletter_enabled' => true,
            'digital_download_categories' => '103',
            'specialty_product_categories' => '103',
            // Live Chat defaults (disabled until configured)
            'live_chat_enabled' => false,
            'tawkto_enabled' => false,
            'tidio_enabled' => false,
            'tawkto_property_id' => '',
            'tawkto_widget_id' => '',
            'tidio_public_key' => ''
        ];
    }
}
