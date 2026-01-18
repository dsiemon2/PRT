# Branding System API

Last Updated: 2025-12-22

## Overview

The Branding System allows administrators to customize the visual appearance of the storefront including logo positioning, header styling, colors, and theme settings. All branding settings are managed through the Settings API.

## API Endpoints

### Get Branding Settings

```http
GET /api/v1/admin/settings/branding
```

**Response:**
```json
{
    "success": true,
    "data": {
        "logo_alignment": "center",
        "nav_height": "70",
        "header_bg_color": "#8B4513",
        "header_text_color": "#FFFFFF",
        "header_hover_color": "#FFD700",
        "header_style": "solid",
        "sticky_header": "1",
        "header_shadow": "1",
        "announcement_enabled": "0",
        "announcement_text": "",
        "announcement_bg_color": "#1a1a1a",
        "announcement_text_color": "#ffffff",
        "theme_primary": "#8B4513",
        "theme_secondary": "#D2691E",
        "theme_accent": "#FFD700",
        "theme_text_dark": "#333333",
        "theme_text_light": "#666666",
        "theme_background": "#FFFFFF"
    }
}
```

### Update Branding Settings

```http
PUT /api/v1/admin/settings/branding
Content-Type: application/json
```

**Request Body:**
```json
{
    "logo_alignment": "left",
    "nav_height": "60",
    "header_bg_color": "#8B4513",
    "header_text_color": "#FFFFFF",
    "header_hover_color": "#FFD700"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Branding settings updated successfully"
}
```

## Branding Settings Reference

### Logo Settings

| Setting | Type | Options | Description |
|---------|------|---------|-------------|
| `logo_alignment` | string | `left`, `center`, `right` | Logo position in header |
| `nav_height` | string | `50` - `100` | Navigation bar height in pixels |

### Header Styling

| Setting | Type | Default | Description |
|---------|------|---------|-------------|
| `header_bg_color` | hex color | `#8B4513` | Header background color |
| `header_text_color` | hex color | `#FFFFFF` | Navigation text color |
| `header_hover_color` | hex color | `#FFD700` | Link hover/active color |
| `header_style` | string | `solid` | `solid`, `gradient`, `transparent` |
| `sticky_header` | boolean | `1` | Sticky header on scroll |
| `header_shadow` | boolean | `1` | Drop shadow under header |

### Announcement Bar

| Setting | Type | Default | Description |
|---------|------|---------|-------------|
| `announcement_enabled` | boolean | `0` | Show announcement bar |
| `announcement_text` | string | | Announcement message |
| `announcement_bg_color` | hex color | `#1a1a1a` | Bar background color |
| `announcement_text_color` | hex color | `#ffffff` | Bar text color |

### Theme Colors

| Setting | Type | Default | Description |
|---------|------|---------|-------------|
| `theme_primary` | hex color | `#8B4513` | Buttons, links |
| `theme_secondary` | hex color | `#D2691E` | Sale badges, CTAs |
| `theme_accent` | hex color | `#FFD700` | Stars, highlights |
| `theme_text_dark` | hex color | `#333333` | Primary text |
| `theme_text_light` | hex color | `#666666` | Secondary text |
| `theme_background` | hex color | `#FFFFFF` | Page background |

## Logo Alignment Behavior

### Left Alignment
- Logo positioned on the far left
- Navigation links aligned to the far right
- Standard e-commerce layout

### Center Alignment
- Logo centered horizontally
- Navigation links centered below logo
- Header height increases to accommodate stacked layout

### Right Alignment
- Logo positioned on the far right
- Navigation links aligned to the far left
- Mirror of left alignment

## Frontend Integration

### PHP Helper Functions (prt4)

Located in `includes/header-helpers.php`:

```php
// Get logo alignment
$alignment = getLogoAlignment(); // Returns: 'left', 'center', or 'right'

// Get all header settings
$settings = getHeaderSettings();

// Generate navbar CSS
$css = getNavbarCSS();

// Generate theme CSS variables
$css = getThemeCSS();

// Get announcement bar HTML
$html = getAnnouncementBar();
```

### CSS Classes Applied

```html
<!-- Logo alignment class -->
<div class="logo-container logo-<?php echo getLogoAlignment(); ?>">

<!-- Header with dynamic styling -->
<header class="site-header <?php echo getHeaderSettings()['sticky'] ? 'sticky' : ''; ?>">
```

### CSS Variables Generated

```css
:root {
    --primary-color: #8B4513;
    --secondary-color: #D2691E;
    --accent-color: #FFD700;
    --text-dark: #333333;
    --text-light: #666666;
    --bg-color: #FFFFFF;
}
```

## Admin Panel Integration

### Settings Page Location
`http://localhost:8301/admin/settings` → Branding tab

### Header Preview
The admin panel includes a live header preview that updates in real-time when branding settings are changed:

- **Left position**: Logo left, nav right (flexbox)
- **Center position**: Logo top, nav below (column layout)
- **Right position**: Nav left, logo right (order swap)

### CSS for Preview (settings.blade.php)

```css
/* Left position */
#headerPreview.position-left #previewNavLinks {
    margin-left: auto !important;
}

/* Center position */
#headerPreview.position-center #previewNav {
    flex-direction: column;
    align-items: center;
}

/* Right position */
#headerPreview.position-right #previewLogoWrapper {
    order: 2;
    margin-left: auto !important;
}
#headerPreview.position-right #previewNavLinks {
    order: 1;
}
```

## Database Storage

Settings are stored in the `settings` table:

```sql
CREATE TABLE settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_group VARCHAR(50),
    setting_key VARCHAR(100),
    setting_value TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY group_key (setting_group, setting_key)
);

-- Example data
INSERT INTO settings (setting_group, setting_key, setting_value) VALUES
('branding', 'logo_alignment', 'center'),
('branding', 'header_bg_color', '#8B4513'),
('branding', 'theme_primary', '#8B4513');
```

## Caching

Frontend caches branding settings in PHP session for 5 minutes to reduce API calls:

```php
// Check cache
if (isset($_SESSION['branding_cache']) &&
    time() - $_SESSION['branding_cache_time'] < 300) {
    return $_SESSION['branding_cache'];
}

// Fetch from API and cache
$settings = fetchFromAPI('/admin/settings/branding');
$_SESSION['branding_cache'] = $settings;
$_SESSION['branding_cache_time'] = time();
```

## Related Documentation

- [Settings API](./SETTINGS_API.md)
- [Frontend Integration](../../prt4/docs/settings-handler-documentation.md)
- [Admin Panel Features](../../pecos-backend-admin-site/docs/SETTINGS_PAGE.md)
