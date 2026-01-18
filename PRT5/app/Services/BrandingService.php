<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class BrandingService
{
    protected static $settings = null;
    protected $apiBaseUrl;

    public function __construct()
    {
        $this->apiBaseUrl = rtrim(config('services.api.base_url', 'http://localhost:8300/api/v1'), '/');
    }

    /**
     * Get all branding settings from the admin API
     */
    public function getSettings(): array
    {
        if (self::$settings !== null) {
            return self::$settings;
        }

        // Try cache first (30 second TTL)
        $cached = Cache::get('branding_settings');
        if ($cached) {
            self::$settings = $cached;
            return $cached;
        }

        try {
            $response = Http::timeout(3)->get($this->apiBaseUrl . '/admin/settings');

            if ($response->successful()) {
                $data = $response->json();
                if ($data && isset($data['success']) && $data['success']) {
                    $settings = $this->parseSettings($data['data'] ?? []);
                    Cache::put('branding_settings', $settings, 30);
                    self::$settings = $settings;
                    return $settings;
                }
            }
        } catch (\Exception $e) {
            \Log::warning('Failed to fetch branding settings: ' . $e->getMessage());
        }

        // Return defaults if API fails
        self::$settings = $this->getDefaults();
        return self::$settings;
    }

    /**
     * Parse API response into settings array
     */
    protected function parseSettings(array $data): array
    {
        $defaults = $this->getDefaults();
        $branding = $data['branding'] ?? [];

        // Logo settings
        if (isset($branding['logo_path'])) {
            $defaults['logo_path'] = $branding['logo_path'];
        }
        if (isset($branding['logo_alignment'])) {
            $defaults['logo_alignment'] = $branding['logo_alignment'];
        }
        if (isset($branding['site_title'])) {
            $defaults['site_title'] = $branding['site_title'];
        }

        // Header colors
        if (isset($branding['header_bg_color'])) {
            $defaults['bg_color'] = $branding['header_bg_color'];
        }
        if (isset($branding['header_text_color'])) {
            $defaults['text_color'] = $branding['header_text_color'];
        }
        if (isset($branding['header_hover_color'])) {
            $defaults['hover_color'] = $branding['header_hover_color'];
        }
        if (isset($branding['header_style'])) {
            $defaults['style'] = $branding['header_style'];
        }
        if (isset($branding['nav_height'])) {
            $defaults['nav_height'] = $branding['nav_height'];
        }

        // Behavior - sticky navbar
        if (isset($branding['header_sticky'])) {
            $defaults['sticky'] = filter_var($branding['header_sticky'], FILTER_VALIDATE_BOOLEAN);
        }
        if (isset($branding['header_shadow'])) {
            $defaults['shadow'] = filter_var($branding['header_shadow'], FILTER_VALIDATE_BOOLEAN);
        }

        // Announcement bar
        if (isset($branding['announcement_enabled'])) {
            $defaults['announcement_enabled'] = filter_var($branding['announcement_enabled'], FILTER_VALIDATE_BOOLEAN);
        }
        if (isset($branding['announcement_text'])) {
            $defaults['announcement_text'] = $branding['announcement_text'];
        }
        if (isset($branding['announcement_bg'])) {
            $defaults['announcement_bg'] = $branding['announcement_bg'];
        }
        if (isset($branding['announcement_text_color'])) {
            $defaults['announcement_text_color'] = $branding['announcement_text_color'];
        }

        // Theme colors
        if (isset($branding['theme_primary'])) {
            $defaults['theme']['primary'] = $branding['theme_primary'];
        }
        if (isset($branding['theme_secondary'])) {
            $defaults['theme']['secondary'] = $branding['theme_secondary'];
        }
        if (isset($branding['theme_accent'])) {
            $defaults['theme']['accent'] = $branding['theme_accent'];
        }

        return $defaults;
    }

    /**
     * Get default branding settings
     */
    public function getDefaults(): array
    {
        return [
            'logo_path' => 'assets/images/PRT-High-Res-Logo.png',
            'logo_alignment' => 'left',
            'site_title' => 'Pecos River Traders',

            // Header styling
            'bg_color' => '#8B4513',
            'text_color' => '#FFFFFF',
            'hover_color' => '#FFD700',
            'style' => 'gradient',
            'nav_height' => '70',

            // Behavior
            'sticky' => true,
            'shadow' => true,

            // Announcement bar
            'announcement_enabled' => false,
            'announcement_text' => '',
            'announcement_bg' => '#C41E3A',
            'announcement_text_color' => '#FFFFFF',

            // Theme colors
            'theme' => [
                'primary' => '#8B4513',
                'secondary' => '#C41E3A',
                'accent' => '#FFD700',
                'text_dark' => '#333333',
                'text_light' => '#FFFFFF',
                'bg' => '#F5F5F5'
            ]
        ];
    }

    /**
     * Get navbar CSS classes
     */
    public function getNavbarClasses(): string
    {
        $settings = $this->getSettings();
        $classes = 'navbar navbar-expand-lg navbar-dark navbar-custom';

        if ($settings['sticky']) {
            $classes .= ' sticky-top';
        }

        // Add logo alignment class like prt4
        $classes .= ' logo-align-' . $settings['logo_alignment'];

        return $classes;
    }

    /**
     * Get logo alignment
     */
    public function getLogoAlignment(): string
    {
        $settings = $this->getSettings();
        return $settings['logo_alignment'];
    }

    /**
     * Get logo wrapper classes
     */
    public function getLogoWrapperClasses(): string
    {
        $settings = $this->getSettings();
        return 'navbar-logo-wrapper logo-' . $settings['logo_alignment'];
    }

    /**
     * Generate theme CSS variables
     */
    public function getThemeCSS(): string
    {
        $settings = $this->getSettings();
        $theme = $settings['theme'];

        return "
<style>
:root {
    --prt-primary: {$theme['primary']};
    --prt-secondary: {$theme['secondary']};
    --prt-accent: {$theme['accent']};
    --prt-text-dark: {$theme['text_dark']};
    --prt-text-light: {$theme['text_light']};
    --prt-bg: {$theme['bg']};
    --prt-header-bg: {$settings['bg_color']};
    --prt-header-text: {$settings['text_color']};
    --prt-header-hover: {$settings['hover_color']};
}

/* Apply theme colors */
.btn-prt, .btn-primary {
    background-color: var(--prt-primary) !important;
    border-color: var(--prt-primary) !important;
}

.btn-prt:hover, .btn-primary:hover {
    background-color: var(--prt-secondary) !important;
    border-color: var(--prt-secondary) !important;
}

.text-prt-primary { color: var(--prt-primary) !important; }
.text-prt-secondary { color: var(--prt-secondary) !important; }
.text-prt-accent { color: var(--prt-accent) !important; }

.bg-prt-primary { background-color: var(--prt-primary) !important; }
.bg-prt-secondary { background-color: var(--prt-secondary) !important; }
.bg-prt-accent { background-color: var(--prt-accent) !important; }

/* Sale badge uses secondary color */
.badge-sale, .sale-badge {
    background-color: var(--prt-secondary) !important;
}

/* Star ratings use accent color */
.star-rating .bi-star-fill {
    color: var(--prt-accent) !important;
}

/* Links use primary color */
a:not(.nav-link):not(.btn) {
    color: #dd9663;
}

a:not(.nav-link):not(.btn):hover {
    color: var(--prt-secondary);
}
</style>";
    }

    /**
     * Generate navbar CSS
     */
    public function getNavbarCSS(): string
    {
        $settings = $this->getSettings();

        // Background style
        if ($settings['style'] === 'gradient') {
            $bgStyle = "background: linear-gradient(135deg, {$settings['bg_color']} 0%, " . $this->adjustBrightness($settings['bg_color'], -20) . " 100%);";
        } elseif ($settings['style'] === 'transparent') {
            $bgStyle = "background: transparent;";
        } else {
            $bgStyle = "background: {$settings['bg_color']};";
        }

        // Shadow
        $shadow = $settings['shadow'] ? 'box-shadow: 0 2px 10px rgba(0,0,0,0.15);' : '';

        return "
<style>
.navbar-custom {
    {$bgStyle}
    {$shadow}
    min-height: {$settings['nav_height']}px;
    padding-top: 0;
    padding-bottom: 0;
}

.navbar-custom .container-fluid {
    min-height: {$settings['nav_height']}px;
    position: relative;
}

.navbar-custom .nav-link {
    color: {$settings['text_color']} !important;
}

.navbar-custom .nav-link:hover,
.navbar-custom .nav-link:focus,
.navbar-custom .nav-link.active {
    color: {$settings['hover_color']} !important;
}

.navbar-custom .navbar-toggler-icon {
    filter: brightness(0) invert(1);
}

/* Logo alignment styles - matching prt4 */
.navbar-logo-wrapper {
    display: flex;
    align-items: center;
}

.navbar-logo-wrapper.logo-right {
    order: 3;
    margin-left: auto;
}

/* When logo is right, push nav to left */
.navbar-custom .logo-right ~ .navbar-collapse .navbar-nav,
.navbar-custom.logo-align-right .navbar-collapse .navbar-nav,
.navbar-custom.logo-align-right .navbar-collapse .navbar-nav.ms-auto {
    margin-left: 0 !important;
    margin-right: auto !important;
}

/* Ensure right alignment works */
.navbar-custom.logo-align-right .container-fluid {
    display: flex;
    align-items: center;
}

.navbar-custom.logo-align-right .navbar-logo-wrapper.logo-right {
    order: 3;
    margin-left: auto;
}

.navbar-custom.logo-align-right .navbar-collapse {
    order: 0;
    margin-right: auto;
}

.navbar-custom.logo-align-right .navbar-toggler {
    order: 1;
}

/* When logo is left, ensure nav items stay on right */
.navbar-custom.logo-align-left .navbar-collapse .navbar-nav {
    margin-left: auto !important;
    margin-right: 0 !important;
}

/* Center alignment: column layout */
.navbar-custom.logo-align-center .container-fluid {
    flex-direction: column;
    align-items: center;
    padding-top: 0;
    padding-bottom: 0.5rem;
}

.navbar-custom.logo-align-center .navbar-logo-wrapper.logo-center {
    position: relative;
    left: auto;
    transform: none;
    margin: 0 auto;
    order: 0;
}

.navbar-custom.logo-align-center .navbar-toggler {
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
}

.navbar-custom.logo-align-center .navbar-collapse {
    width: 100%;
    margin-top: 0.5rem;
}

.navbar-custom.logo-align-center .navbar-collapse .navbar-nav {
    margin-left: auto !important;
    margin-right: auto !important;
    justify-content: center;
    width: 100%;
}

@media (max-width: 991px) {
    .navbar-custom.logo-align-center .container-fluid {
        flex-direction: row;
        flex-wrap: wrap;
    }
    .navbar-custom.logo-align-center .navbar-logo-wrapper {
        order: 0;
    }
    .navbar-custom.logo-align-center .navbar-toggler {
        position: static;
        transform: none;
        order: 1;
        margin-left: auto;
    }
    .navbar-custom.logo-align-center .navbar-collapse {
        order: 2;
        width: 100%;
        margin-top: 0;
    }
}
</style>";
    }

    /**
     * Generate announcement bar HTML
     */
    public function getAnnouncementBar(): string
    {
        $settings = $this->getSettings();

        if (!$settings['announcement_enabled'] || empty($settings['announcement_text'])) {
            return '';
        }

        return "
<div class='announcement-bar' style='background: {$settings['announcement_bg']}; color: {$settings['announcement_text_color']}; padding: 8px 15px; text-align: center; font-size: 0.9rem;'>
    <i class='bi bi-megaphone me-2'></i>
    {$settings['announcement_text']}
</div>";
    }

    /**
     * Adjust color brightness
     */
    protected function adjustBrightness(string $hex, int $percent): string
    {
        $hex = ltrim($hex, '#');

        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        $r = max(0, min(255, $r + ($r * $percent / 100)));
        $g = max(0, min(255, $g + ($g * $percent / 100)));
        $b = max(0, min(255, $b + ($b * $percent / 100)));

        return sprintf('#%02x%02x%02x', $r, $g, $b);
    }

    /**
     * Encode color for SVG URL
     */
    protected function encodeColor(string $color): string
    {
        return str_replace('#', '%23', $color);
    }

    /**
     * Get notification settings from admin API
     */
    public function getNotificationSettings(): array
    {
        // Try cache first
        $cached = Cache::get('notification_settings');
        if ($cached) {
            return $cached;
        }

        try {
            $response = Http::timeout(3)->get($this->apiBaseUrl . '/admin/settings');

            if ($response->successful()) {
                $data = $response->json();
                if ($data && isset($data['success']) && $data['success']) {
                    $settings = $data['data']['notifications'] ?? [];
                    $result = [
                        'notifications_enabled' => $settings['notifications_enabled'] ?? true,
                        'notif_email_enabled' => $settings['notif_email_enabled'] ?? true,
                        'notif_sms_enabled' => $settings['notif_sms_enabled'] ?? true,
                        'notif_push_enabled' => $settings['notif_push_enabled'] ?? true,
                        'notif_delivery_enabled' => $settings['notif_delivery_enabled'] ?? true,
                        'notif_promo_enabled' => $settings['notif_promo_enabled'] ?? true,
                        'notif_payment_enabled' => $settings['notif_payment_enabled'] ?? true,
                        'notif_security_enabled' => $settings['notif_security_enabled'] ?? true,
                    ];
                    Cache::put('notification_settings', $result, 30);
                    return $result;
                }
            }
        } catch (\Exception $e) {
            \Log::warning('Failed to fetch notification settings: ' . $e->getMessage());
        }

        // Return defaults if API fails
        return [
            'notifications_enabled' => true,
            'notif_email_enabled' => true,
            'notif_sms_enabled' => true,
            'notif_push_enabled' => true,
            'notif_delivery_enabled' => true,
            'notif_promo_enabled' => true,
            'notif_payment_enabled' => true,
            'notif_security_enabled' => true,
        ];
    }
}
