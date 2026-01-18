# Settings Page Documentation

Last Updated: 2025-12-22

## Overview

The Settings page (`/admin/settings`) provides comprehensive configuration for the Pecos River Trading Post e-commerce platform. Settings are organized into tabs and saved via the API.

## Location

**URL**: `http://localhost:8301/admin/settings`
**File**: `resources/views/admin/settings.blade.php`

## Settings Tabs

### 1. General Settings

Basic store configuration:
- **Timezone**: Store timezone selection
- **Currency**: Currency code and symbol
- **Decimal Places**: Price decimal precision

### 2. Store Info

Store identity and contact:
- **Store Name**: Business name (single source of truth)
- **Tagline**: Store slogan/tagline
- **Address**: Physical store address
- **Email**: Contact email
- **Phone**: Contact phone number
- **Store Name Styling**: Font size (12-36px) and color

### 3. Branding

Visual customization with live preview:

#### Logo Settings
| Setting | Options | Description |
|---------|---------|-------------|
| Logo Alignment | Left, Center, Right | Logo position in header |

#### Header Styling
| Setting | Type | Description |
|---------|------|-------------|
| Background Color | Hex color | Header background |
| Text Color | Hex color | Navigation text |
| Hover Color | Hex color | Link hover state |
| Nav Height | 50-100px | Navigation bar height |
| Header Style | Solid/Gradient/Transparent | Background style |
| Sticky Header | Toggle | Fixed on scroll |
| Drop Shadow | Toggle | Shadow under header |

#### Announcement Bar
| Setting | Type | Description |
|---------|------|-------------|
| Enable | Toggle | Show/hide bar |
| Text | String | Announcement message |
| Background | Hex color | Bar background |
| Text Color | Hex color | Message color |

#### Theme Colors
| Setting | Default | Usage |
|---------|---------|-------|
| Primary | #8B4513 | Buttons, links |
| Secondary | #D2691E | Sale badges, CTAs |
| Accent | #FFD700 | Stars, highlights |
| Text Dark | #333333 | Primary text |
| Text Light | #666666 | Secondary text |
| Background | #FFFFFF | Page background |

### 4. Email Settings

SMTP configuration:
- SMTP Host, Port, Username, Password
- Encryption type (TLS/SSL)
- From address and name

### 5. Security Settings

Security configuration:
- Two-Factor Authentication toggle
- Session timeout duration
- Password requirements

### 6. API Settings

API configuration:
- Rate limiting settings
- API logging toggle

### 7. Notifications

Alert preferences:
- Low stock threshold
- Email notification toggles

## Header Preview

The Branding tab includes a live header preview that updates in real-time:

### Preview Structure
```html
<div id="headerPreview" class="position-center">
    <nav id="previewNav">
        <div id="previewLogoWrapper">
            <img src="PRT-High-Res-Logo.png" />
        </div>
        <div id="previewNavLinks">
            Home | Products | Contact | Cart
        </div>
    </nav>
</div>
```

### Position Classes

**Left Position** (`.position-left`):
```css
#headerPreview.position-left #previewNavLinks {
    margin-left: auto !important;
}
```
- Logo: Far left
- Nav: Far right (pushed by margin-left: auto)

**Center Position** (`.position-center`):
```css
#headerPreview.position-center #previewNav {
    flex-direction: column;
    justify-content: center;
    align-items: center;
}
```
- Logo: Centered horizontally
- Nav: Centered below logo
- Layout: Vertical stack

**Right Position** (`.position-right`):
```css
#headerPreview.position-right #previewLogoWrapper {
    order: 2;
    margin-left: auto !important;
}
#headerPreview.position-right #previewNavLinks {
    order: 1;
}
```
- Logo: Far right (order: 2 + margin-left: auto)
- Nav: Far left (order: 1)
- Mirror of left position

## JavaScript Functions

### updateBrandingPreview()
Updates the header preview when settings change:
```javascript
function updateBrandingPreview() {
    var bgColor = document.getElementById('branding_header_bg_color').value;
    var textColor = document.getElementById('branding_header_text_color').value;
    var logoAlignment = document.getElementById('branding_logo_alignment').value;

    // Update background and text colors
    var preview = document.getElementById('headerPreview');
    preview.style.background = bgColor;

    // Update position class
    preview.classList.remove('position-left', 'position-center', 'position-right');
    preview.classList.add('position-' + logoAlignment);
}
```

### Event Listeners
```javascript
// Logo alignment change
document.getElementById('branding_logo_alignment').addEventListener('change', updateBrandingPreview);

// Color input changes
colorInputs.forEach(input => {
    input.addEventListener('input', updateBrandingPreview);
});
```

## API Integration

### Load Settings
```javascript
GET /api/v1/admin/settings
```
Fetches all settings and populates form fields.

### Save Settings
```javascript
PUT /api/v1/admin/settings/{group}
Content-Type: application/json
```
Saves settings by group (general, store_info, branding, email, security, api, notifications).

## Form Elements

### Color Pickers
Each color setting has:
- Color input (type="color")
- Text input for hex value
- Tooltip: "Click on color to see Color Wheel"

### Logo Alignment Dropdown
```html
<select id="branding_logo_alignment">
    <option value="left">Left</option>
    <option value="center">Center</option>
    <option value="right">Right</option>
</select>
```

## Current Logo

The preview uses the current PRT logo from the storefront:
```
{STOREFRONT_URL}/assets/images/PRT-High-Res-Logo.png
```
Where `STOREFRONT_URL` defaults to `http://localhost:8300` (configured in `.env`).

## Related Files

- `settings.blade.php` - Main settings view
- `layouts/admin.blade.php` - Admin layout
- `SettingsController.php` (API) - Settings endpoints
- `header-helpers.php` (prt4) - Frontend integration

## Troubleshooting

### Preview Not Updating
1. Check JavaScript console for errors
2. Verify `updateBrandingPreview()` is called
3. Hard refresh (Ctrl+F5) to clear cache

### Settings Not Saving
1. Check API response in Network tab
2. Verify API server is running (port 8000)
3. Check for validation errors

### Logo Not Displaying
1. Verify image path is correct
2. Check CORS settings if cross-origin
3. Ensure image exists at specified path
