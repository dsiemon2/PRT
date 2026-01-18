@extends('layouts.admin')

@section('title', 'Settings')

@section('content')
<div class="page-header">
    <h1>Settings</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Settings</li>
        </ol>
    </nav>
</div>

<div id="loadingSettings" class="text-center py-5">
    <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
    <p class="mt-2">Loading settings...</p>
</div>

<div id="settingsContent" class="row" style="display: none;">
    <!-- Settings Navigation -->
    <div class="col-md-3">
        <div class="list-group">
            <a href="#general" class="list-group-item list-group-item-action active" data-bs-toggle="list">
                <i class="bi bi-gear me-2"></i> General
            </a>
            <a href="#store" class="list-group-item list-group-item-action" data-bs-toggle="list">
                <i class="bi bi-shop me-2"></i> Store Info
            </a>
            <a href="#branding" class="list-group-item list-group-item-action" data-bs-toggle="list">
                <i class="bi bi-palette me-2"></i> Branding
            </a>
            <a href="#email" class="list-group-item list-group-item-action" data-bs-toggle="list">
                <i class="bi bi-envelope me-2"></i> Email
            </a>
            <a href="#security" class="list-group-item list-group-item-action" data-bs-toggle="list">
                <i class="bi bi-shield-lock me-2"></i> Security
            </a>
            <a href="#api" class="list-group-item list-group-item-action" data-bs-toggle="list">
                <i class="bi bi-code-slash me-2"></i> API
            </a>
            <a href="#notifications" class="list-group-item list-group-item-action" data-bs-toggle="list">
                <i class="bi bi-bell me-2"></i> Notifications
            </a>
        </div>
    </div>

    <!-- Settings Content -->
    <div class="col-md-9">
        <div class="tab-content">
            <!-- General Settings -->
            <div class="tab-pane fade show active" id="general">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">General Settings</h5>
                    </div>
                    <div class="card-body">
                        <form id="generalForm" class="admin-form" onsubmit="saveSettings(event, 'general')">
                            <div class="mb-3">
                                <label class="form-label">Timezone</label>
                                <select class="form-select" id="general_timezone" name="timezone">
                                    <option value="America/Chicago">America/Chicago</option>
                                    <option value="America/New_York">America/New_York</option>
                                    <option value="America/Los_Angeles">America/Los_Angeles</option>
                                    <option value="America/Denver">America/Denver</option>
                                </select>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Currency</label>
                                    <select class="form-select" id="general_currency" name="currency">
                                        <option value="USD">USD ($)</option>
                                        <option value="EUR">EUR (&euro;)</option>
                                        <option value="GBP">GBP (&pound;)</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Decimal Places</label>
                                    <input type="number" class="form-control" id="general_decimal_places" name="decimal_places" min="0" max="4">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-prt">Save Changes</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Store Info -->
            <div class="tab-pane fade" id="store">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Store Information</h5>
                    </div>
                    <div class="card-body">
                        <form id="storeForm" class="admin-form" onsubmit="saveSettings(event, 'store')">
                            <div class="mb-3">
                                <label class="form-label">Store Name</label>
                                <input type="text" class="form-control" id="store_store_name" name="store_name">
                                <small class="text-muted">This is the main business name used throughout the site</small>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Store Name Font Size</label>
                                    <select class="form-select" id="store_store_name_size" name="store_name_size">
                                        <option value="1rem">Small (1rem)</option>
                                        <option value="1.25rem" selected>Medium (1.25rem)</option>
                                        <option value="1.5rem">Large (1.5rem)</option>
                                        <option value="1.75rem">Extra Large (1.75rem)</option>
                                        <option value="2rem">Huge (2rem)</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Store Name Color</label>
                                    <div class="input-group">
                                        <input type="color" class="form-control form-control-color" id="store_store_name_color" name="store_name_color" value="#8B4513" title="Click on color to see Color Wheel" data-bs-toggle="tooltip" data-bs-placement="top">
                                        <input type="text" class="form-control" id="store_store_name_color_text" value="#8B4513" onchange="syncStoreColor('store_store_name_color', this.value)" title="Click on color to see Color Wheel">
                                    </div>
                                    <small class="text-muted">Text color for store name display</small>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Store Tagline / Description</label>
                                <input type="text" class="form-control" id="store_store_tagline" name="store_tagline" placeholder="e.g., Quality Western Goods Since 1995">
                                <small class="text-muted">Used for SEO and as fallback when logo fails to load</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Store Address</label>
                                <input type="text" class="form-control" id="store_store_address" name="store_address">
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">City</label>
                                    <input type="text" class="form-control" id="store_store_city" name="store_city">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">State</label>
                                    <input type="text" class="form-control" id="store_store_state" name="store_state">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">ZIP</label>
                                    <input type="text" class="form-control" id="store_store_zip" name="store_zip">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Phone</label>
                                <input type="tel" class="form-control" id="store_store_phone" name="store_phone">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" id="store_store_email" name="store_email">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Business Hours</label>
                                <input type="text" class="form-control" id="store_business_hours" name="business_hours">
                            </div>
                            <button type="submit" class="btn btn-prt">Save Changes</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Branding Settings -->
            <div class="tab-pane fade" id="branding">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Branding & Appearance</h5>
                        <small class="text-muted">Customize logo, colors, and header appearance</small>
                    </div>
                    <div class="card-body">
                        <form id="brandingForm" class="admin-form" onsubmit="saveBrandingSettings(event)">
                            <!-- Logo Section -->
                            <h6 class="border-bottom pb-2 mb-3"><i class="bi bi-image me-2"></i>Logo Settings</h6>

                            <div class="mb-3">
                                <label class="form-label">Current Logo</label>
                                <div class="d-flex align-items-center gap-3">
                                    <img id="currentLogoPreview" src="{{ config('services.storefront.url') }}/assets/images/PRT-High-Res-Logo.png" alt="Current Logo" style="max-height: 60px; background: #333; padding: 5px; border-radius: 4px;">
                                    <span class="text-muted small" id="currentLogoPath">PRT-High-Res-Logo.png</span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Upload New Logo</label>
                                <input type="file" class="form-control" id="branding_logo_file" name="logo_file" accept="image/*" onchange="previewLogo(this)">
                                <small class="text-muted">Recommended: PNG or JPG, max height 80px. Transparent PNG works best.</small>
                            </div>

                            <div class="mb-3" id="newLogoPreviewContainer" style="display: none;">
                                <label class="form-label">New Logo Preview</label>
                                <div class="p-2 rounded" style="background: #333;">
                                    <img id="newLogoPreview" src="" alt="New Logo Preview" style="max-height: 60px;">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Logo Alignment</label>
                                <select class="form-select" id="branding_logo_alignment" name="logo_alignment" style="max-width: 200px;">
                                    <option value="left">Left</option>
                                    <option value="center" selected>Center</option>
                                    <option value="right">Right</option>
                                </select>
                                <small class="text-muted d-block mt-1">Position of logo in header. Logo size is controlled by Navigation Bar Height below.</small>
                            </div>

                            <!-- Header Styling -->
                            <h6 class="border-bottom pb-2 mb-3"><i class="bi bi-window-stack me-2"></i>Header Styling</h6>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label">Background Color</label>
                                    <div class="input-group">
                                        <input type="color" class="form-control form-control-color" id="branding_header_bg_color" name="header_bg_color" value="#8B4513" title="Click on color to see Color Wheel" data-bs-toggle="tooltip" data-bs-placement="top">
                                        <input type="text" class="form-control" id="branding_header_bg_color_text" value="#8B4513" onchange="syncColor('branding_header_bg_color', this.value)">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Text/Link Color</label>
                                    <div class="input-group">
                                        <input type="color" class="form-control form-control-color" id="branding_header_text_color" name="header_text_color" value="#FFFFFF" title="Click on color to see Color Wheel" data-bs-toggle="tooltip" data-bs-placement="top">
                                        <input type="text" class="form-control" id="branding_header_text_color_text" value="#FFFFFF" onchange="syncColor('branding_header_text_color', this.value)">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Hover/Active Color</label>
                                    <div class="input-group">
                                        <input type="color" class="form-control form-control-color" id="branding_header_hover_color" name="header_hover_color" value="#FFD700" title="Click on color to see Color Wheel" data-bs-toggle="tooltip" data-bs-placement="top">
                                        <input type="text" class="form-control" id="branding_header_hover_color_text" value="#FFD700" onchange="syncColor('branding_header_hover_color', this.value)">
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Navigation Bar Height</label>
                                    <select class="form-select" id="branding_nav_height" name="nav_height">
                                        <option value="50">Compact (50px)</option>
                                        <option value="60">Small (60px)</option>
                                        <option value="70" selected>Medium (70px)</option>
                                        <option value="80">Large (80px)</option>
                                        <option value="90">Extra Large (90px)</option>
                                        <option value="100">Huge (100px)</option>
                                    </select>
                                    <small class="text-muted">Controls the overall height of the navigation bar</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Header Style</label>
                                    <select class="form-select" id="branding_header_style" name="header_style">
                                        <option value="solid">Solid Color</option>
                                        <option value="gradient">Gradient (Top to Bottom)</option>
                                        <option value="transparent">Transparent (with scroll fade)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="d-flex gap-4 mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="branding_header_sticky" name="header_sticky" checked>
                                    <label class="form-check-label" for="branding_header_sticky">Sticky Header</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="branding_header_shadow" name="header_shadow" checked>
                                    <label class="form-check-label" for="branding_header_shadow">Drop Shadow</label>
                                </div>
                            </div>

                            <!-- Announcement Bar -->
                            <h6 class="border-bottom pb-2 mb-3"><i class="bi bi-megaphone me-2"></i>Announcement Bar</h6>

                            <div class="card bg-light mb-4" id="announcementStatusCard">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                <span class="badge" id="announcementStatusBadge">Loading...</span>
                                                <span class="text-muted" id="announcementCount"></span>
                                            </div>
                                            <small class="text-muted">Manage multiple announcements with icons, links, scheduling, and more</small>
                                        </div>
                                        <a href="{{ route('admin.announcements') }}" class="btn btn-prt">
                                            <i class="bi bi-gear me-1"></i> Manage Announcements
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Homepage Banners -->
                            <h6 class="border-bottom pb-2 mb-3"><i class="bi bi-image me-2"></i>Homepage Banners</h6>

                            <div class="card bg-light mb-4" id="bannerStatusCard">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                <span class="badge" id="bannerStatusBadge">Loading...</span>
                                                <span class="text-muted" id="bannerCount"></span>
                                            </div>
                                            <small class="text-muted">Hero carousel with images, text overlays, call-to-action buttons, and scheduling</small>
                                        </div>
                                        <a href="{{ route('admin.banners') }}" class="btn btn-prt">
                                            <i class="bi bi-gear me-1"></i> Manage Banners
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Theme Colors -->
                            <h6 class="border-bottom pb-2 mb-3"><i class="bi bi-brush me-2"></i>Site Theme Colors</h6>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label">Primary Color</label>
                                    <div class="input-group">
                                        <input type="color" class="form-control form-control-color" id="branding_theme_primary" name="theme_primary" value="#8B4513" title="Click on color to see Color Wheel" data-bs-toggle="tooltip" data-bs-placement="top">
                                        <input type="text" class="form-control" id="branding_theme_primary_text" value="#8B4513" onchange="syncColor('branding_theme_primary', this.value)">
                                    </div>
                                    <small class="text-muted">Buttons, links</small>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Secondary Color</label>
                                    <div class="input-group">
                                        <input type="color" class="form-control form-control-color" id="branding_theme_secondary" name="theme_secondary" value="#C41E3A" title="Click on color to see Color Wheel" data-bs-toggle="tooltip" data-bs-placement="top">
                                        <input type="text" class="form-control" id="branding_theme_secondary_text" value="#C41E3A" onchange="syncColor('branding_theme_secondary', this.value)">
                                    </div>
                                    <small class="text-muted">Sale badges, CTAs</small>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Accent Color</label>
                                    <div class="input-group">
                                        <input type="color" class="form-control form-control-color" id="branding_theme_accent" name="theme_accent" value="#FFD700" title="Click on color to see Color Wheel" data-bs-toggle="tooltip" data-bs-placement="top">
                                        <input type="text" class="form-control" id="branding_theme_accent_text" value="#FFD700" onchange="syncColor('branding_theme_accent', this.value)">
                                    </div>
                                    <small class="text-muted">Stars, highlights</small>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <label class="form-label">Text Dark</label>
                                    <div class="input-group">
                                        <input type="color" class="form-control form-control-color" id="branding_theme_text_dark" name="theme_text_dark" value="#333333" title="Click on color to see Color Wheel" data-bs-toggle="tooltip" data-bs-placement="top">
                                        <input type="text" class="form-control" id="branding_theme_text_dark_text" value="#333333" onchange="syncColor('branding_theme_text_dark', this.value)">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Text Light</label>
                                    <div class="input-group">
                                        <input type="color" class="form-control form-control-color" id="branding_theme_text_light" name="theme_text_light" value="#FFFFFF" title="Click on color to see Color Wheel" data-bs-toggle="tooltip" data-bs-placement="top">
                                        <input type="text" class="form-control" id="branding_theme_text_light_text" value="#FFFFFF" onchange="syncColor('branding_theme_text_light', this.value)">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Page Background</label>
                                    <div class="input-group">
                                        <input type="color" class="form-control form-control-color" id="branding_theme_bg" name="theme_bg" value="#F5F5F5" title="Click on color to see Color Wheel" data-bs-toggle="tooltip" data-bs-placement="top">
                                        <input type="text" class="form-control" id="branding_theme_bg_text" value="#F5F5F5" onchange="syncColor('branding_theme_bg', this.value)">
                                    </div>
                                </div>
                            </div>

                            <!-- Color Preview -->
                            <div class="mb-4">
                                <label class="form-label">Color Preview</label>
                                <div class="d-flex gap-2 flex-wrap" id="themeColorPreview">
                                    <div class="rounded p-2 text-center text-white" style="background: #8B4513; min-width: 80px;">Primary</div>
                                    <div class="rounded p-2 text-center text-white" style="background: #C41E3A; min-width: 80px;">Secondary</div>
                                    <div class="rounded p-2 text-center" style="background: #FFD700; min-width: 80px;">Accent</div>
                                    <div class="rounded p-2 text-center text-white" style="background: #333; min-width: 80px;">Dark</div>
                                    <div class="rounded p-2 text-center" style="background: #F5F5F5; min-width: 80px; border: 1px solid #ddd;">Light BG</div>
                                </div>
                            </div>

                            <!-- Header Preview -->
                            <div class="mb-4">
                                <label class="form-label">Header Preview</label>
                                <div id="headerPreview" class="rounded overflow-hidden position-center" style="background: #8B4513;">
                                    <nav class="d-flex align-items-center px-3 py-2" id="previewNav">
                                        <div id="previewLogoWrapper" style="display: flex; align-items: center;">
                                            <img id="previewLogo" src="{{ config('services.storefront.url') }}/assets/images/PRT-High-Res-Logo.png" alt="Logo" style="max-height: 40px;">
                                        </div>
                                        <div class="d-flex gap-3" id="previewNavLinks" style="color: #fff;">
                                            <span>Home</span>
                                            <span>Products</span>
                                            <span>Contact</span>
                                            <span style="color: #FFD700;">Cart</span>
                                        </div>
                                    </nav>
                                </div>
                                <small class="text-muted">Simplified preview - actual header may vary</small>
                            </div>

                            <button type="submit" class="btn btn-prt">Save Branding Settings</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Email Settings -->
            <div class="tab-pane fade" id="email">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Email Settings</h5>
                    </div>
                    <div class="card-body">
                        <form id="emailForm" class="admin-form" onsubmit="saveSettings(event, 'email')">
                            <div class="mb-3">
                                <label class="form-label">SMTP Host</label>
                                <input type="text" class="form-control" id="email_smtp_host" name="smtp_host">
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">SMTP Port</label>
                                    <input type="number" class="form-control" id="email_smtp_port" name="smtp_port">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Encryption</label>
                                    <select class="form-select" id="email_smtp_encryption" name="smtp_encryption">
                                        <option value="tls">TLS</option>
                                        <option value="ssl">SSL</option>
                                        <option value="none">None</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">SMTP Username</label>
                                <input type="text" class="form-control" id="email_smtp_username" name="smtp_username" autocomplete="username">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">SMTP Password</label>
                                <input type="password" class="form-control" id="email_smtp_password" name="smtp_password" autocomplete="current-password">
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">From Email</label>
                                    <input type="email" class="form-control" id="email_from_email" name="from_email">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">From Name</label>
                                    <input type="text" class="form-control" id="email_from_name" name="from_name">
                                </div>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="email_order_confirmation" name="order_confirmation">
                                <label class="form-check-label" for="email_order_confirmation">
                                    Send Order Confirmation Emails
                                </label>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="email_shipping_notification" name="shipping_notification">
                                <label class="form-check-label" for="email_shipping_notification">
                                    Send Shipping Notification Emails
                                </label>
                            </div>
                            <button type="submit" class="btn btn-prt">Save Changes</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Security Settings -->
            <div class="tab-pane fade" id="security">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Security Settings</h5>
                    </div>
                    <div class="card-body">
                        <form id="securityForm" class="admin-form" onsubmit="saveSettings(event, 'security')">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="security_two_factor_enabled" name="two_factor_enabled">
                                <label class="form-check-label" for="security_two_factor_enabled">
                                    Enable Two-Factor Authentication
                                </label>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Session Timeout (minutes)</label>
                                <input type="number" class="form-control" id="security_session_timeout" name="session_timeout">
                            </div>
                            <h6 class="mt-4">Password Requirements</h6>
                            <div class="mb-3">
                                <label class="form-label">Minimum Password Length</label>
                                <input type="number" class="form-control" id="security_password_min_length" name="password_min_length" min="6" max="32">
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="security_require_uppercase" name="require_uppercase">
                                <label class="form-check-label" for="security_require_uppercase">
                                    Require Uppercase Letters
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="security_require_numbers" name="require_numbers">
                                <label class="form-check-label" for="security_require_numbers">
                                    Require Numbers
                                </label>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="security_require_special" name="require_special">
                                <label class="form-check-label" for="security_require_special">
                                    Require Special Characters
                                </label>
                            </div>
                            <h6 class="mt-4">Login Protection</h6>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Max Login Attempts</label>
                                    <input type="number" class="form-control" id="security_max_login_attempts" name="max_login_attempts">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Lockout Duration (minutes)</label>
                                    <input type="number" class="form-control" id="security_lockout_duration" name="lockout_duration">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-prt">Save Changes</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- API Settings -->
            <div class="tab-pane fade" id="api">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">API Settings</h5>
                    </div>
                    <div class="card-body">
                        <form id="apiForm" class="admin-form" onsubmit="saveSettings(event, 'api')">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="api_rate_limit_enabled" name="rate_limit_enabled">
                                <label class="form-check-label" for="api_rate_limit_enabled">
                                    Enable Rate Limiting
                                </label>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Max Requests</label>
                                    <input type="number" class="form-control" id="api_rate_limit_requests" name="rate_limit_requests">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Time Window (seconds)</label>
                                    <input type="number" class="form-control" id="api_rate_limit_window" name="rate_limit_window">
                                </div>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="api_api_logging" name="api_logging">
                                <label class="form-check-label" for="api_api_logging">
                                    Enable API Logging
                                </label>
                            </div>
                            <button type="submit" class="btn btn-prt">Save Changes</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Notification Settings -->
            <div class="tab-pane fade" id="notifications">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Notification Settings</h5>
                    </div>
                    <div class="card-body">
                        <form id="notificationsForm" class="admin-form" onsubmit="saveSettings(event, 'notifications')">
                            <div class="mb-3">
                                <label class="form-label">Low Stock Threshold</label>
                                <input type="number" class="form-control" id="notifications_low_stock_threshold" name="low_stock_threshold">
                                <small class="text-muted">Alert when product stock falls below this number</small>
                            </div>
                            <h6>Email Notifications</h6>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="notifications_email_low_stock" name="email_low_stock">
                                <label class="form-check-label" for="notifications_email_low_stock">
                                    Low Stock Alerts
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="notifications_email_new_order" name="email_new_order">
                                <label class="form-check-label" for="notifications_email_new_order">
                                    New Order Notifications
                                </label>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="notifications_email_customer_signup" name="email_customer_signup">
                                <label class="form-check-label" for="notifications_email_customer_signup">
                                    New Customer Signups
                                </label>
                            </div>
                            <button type="submit" class="btn btn-prt">Save Changes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Logo alignment preview styles */
#headerPreview {
    min-height: 70px;
}
#previewNav {
    min-height: 70px;
    flex-wrap: wrap;
}

/* Left position: logo left, nav right */
#headerPreview.position-left #previewNavLinks {
    margin-left: auto !important;
}

/* Center position: logo centered on top, nav centered below */
#headerPreview.position-center #previewNav {
    flex-direction: column;
    justify-content: center;
    align-items: center;
    padding-top: 10px;
    padding-bottom: 10px;
}
#headerPreview.position-center #previewLogoWrapper {
    order: 0;
    margin-bottom: 8px;
}
#headerPreview.position-center #previewNavLinks {
    order: 1;
    margin-left: 0 !important;
}

/* Right position: nav far left, logo far right */
#headerPreview.position-right #previewLogoWrapper {
    order: 2;
    margin-left: auto !important;
}
#headerPreview.position-right #previewNavLinks {
    order: 1;
    margin-left: 0 !important;
    margin-right: 0 !important;
}
</style>
@endpush

@push('scripts')
<script>
var API_BASE = '{{ rtrim(env("API_PUBLIC_URL", "http://localhost:8300/api/v1"), "/") }}';
var allSettings = {};

// Load all settings on page load
document.addEventListener('DOMContentLoaded', function() {
    loadSettings();
    loadAnnouncementStatus();
    loadBannerStatus();

    // Initialize Bootstrap tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Sync store color input with text input
    var storeColorInput = document.getElementById('store_store_name_color');
    if (storeColorInput) {
        storeColorInput.addEventListener('input', function() {
            var textInput = document.getElementById('store_store_name_color_text');
            if (textInput) textInput.value = this.value.toUpperCase();
        });
    }
});

// Load announcement status for the status card
async function loadAnnouncementStatus() {
    try {
        var response = await fetch(API_BASE + '/admin/announcements');
        var data = await response.json();

        var badge = document.getElementById('announcementStatusBadge');
        var count = document.getElementById('announcementCount');

        var isEnabled = data.settings && data.settings.enabled;
        var activeCount = data.data ? data.data.filter(a => a.is_active).length : 0;
        var totalCount = data.data ? data.data.length : 0;

        if (isEnabled) {
            badge.className = 'badge bg-success';
            badge.textContent = 'Enabled';
        } else {
            badge.className = 'badge bg-secondary';
            badge.textContent = 'Disabled';
        }

        count.textContent = activeCount + ' active of ' + totalCount + ' total';
    } catch (error) {
        console.error('Error loading announcement status:', error);
        document.getElementById('announcementStatusBadge').textContent = 'Error';
        document.getElementById('announcementStatusBadge').className = 'badge bg-danger';
    }
}

// Load banner status for the status card
async function loadBannerStatus() {
    try {
        var response = await fetch(API_BASE + '/admin/banners');
        var data = await response.json();

        var badge = document.getElementById('bannerStatusBadge');
        var count = document.getElementById('bannerCount');

        var isEnabled = data.settings && data.settings.carousel_enabled;
        var activeCount = data.data ? data.data.filter(b => b.is_active).length : 0;
        var totalCount = data.data ? data.data.length : 0;

        if (isEnabled && activeCount > 0) {
            badge.className = 'badge bg-success';
            badge.textContent = 'Enabled';
        } else if (isEnabled) {
            badge.className = 'badge bg-warning text-dark';
            badge.textContent = 'No Banners';
        } else {
            badge.className = 'badge bg-secondary';
            badge.textContent = 'Disabled';
        }

        count.textContent = activeCount + ' active of ' + totalCount + ' total';
    } catch (error) {
        console.error('Error loading banner status:', error);
        document.getElementById('bannerStatusBadge').textContent = 'Error';
        document.getElementById('bannerStatusBadge').className = 'badge bg-danger';
    }
}

// Sync store color from text input
function syncStoreColor(id, value) {
    if (value.match(/^#[0-9A-Fa-f]{6}$/)) {
        document.getElementById(id).value = value;
        document.getElementById(id + '_text').value = value.toUpperCase();
    }
}

async function loadSettings() {
    try {
        var response = await fetch(API_BASE + '/admin/settings');
        var data = await response.json();

        if (data.success) {
            allSettings = data.data;
            populateAllForms();
            document.getElementById('loadingSettings').style.display = 'none';
            document.getElementById('settingsContent').style.display = '';
        } else {
            alert('Failed to load settings');
        }
    } catch (error) {
        console.error('Error loading settings:', error);
        alert('Error loading settings: ' + error.message);
    }
}

function populateAllForms() {
    // For each group, populate the form fields
    var groups = ['general', 'store', 'email', 'security', 'api', 'notifications'];

    groups.forEach(function(group) {
        if (allSettings[group]) {
            Object.keys(allSettings[group]).forEach(function(key) {
                var value = allSettings[group][key];
                var element = document.getElementById(group + '_' + key);

                if (element) {
                    if (element.type === 'checkbox') {
                        element.checked = value === true || value === '1' || value === 'true';
                    } else if (element.type === 'color') {
                        // Handle color inputs with paired text inputs
                        element.value = value || element.value;
                        var textInput = document.getElementById(group + '_' + key + '_text');
                        if (textInput) {
                            textInput.value = (value || element.value).toUpperCase();
                        }
                    } else if (element.tagName === 'SELECT') {
                        element.value = value;
                    } else {
                        element.value = value || '';
                    }
                }
            });
        }
    });

    // Populate branding settings
    populateBrandingForm();
}

function populateBrandingForm() {
    var branding = allSettings.branding || {};

    // Logo settings
    document.getElementById('branding_logo_alignment').value = branding.logo_alignment || 'center';

    // Nav height
    document.getElementById('branding_nav_height').value = branding.nav_height || '70';

    // Header colors
    setColorValue('branding_header_bg_color', branding.header_bg_color || '#8B4513');
    setColorValue('branding_header_text_color', branding.header_text_color || '#FFFFFF');
    setColorValue('branding_header_hover_color', branding.header_hover_color || '#FFD700');

    // Header behavior
    document.getElementById('branding_header_style').value = branding.header_style || 'solid';
    document.getElementById('branding_header_sticky').checked = branding.header_sticky !== false;
    document.getElementById('branding_header_shadow').checked = branding.header_shadow !== false;

    // Theme colors
    setColorValue('branding_theme_primary', branding.theme_primary || '#8B4513');
    setColorValue('branding_theme_secondary', branding.theme_secondary || '#C41E3A');
    setColorValue('branding_theme_accent', branding.theme_accent || '#FFD700');
    setColorValue('branding_theme_text_dark', branding.theme_text_dark || '#333333');
    setColorValue('branding_theme_text_light', branding.theme_text_light || '#FFFFFF');
    setColorValue('branding_theme_bg', branding.theme_bg || '#F5F5F5');

    // Update UI
    updateBrandingPreview();
}

function setColorValue(id, value) {
    var colorInput = document.getElementById(id);
    var textInput = document.getElementById(id + '_text');
    if (colorInput) colorInput.value = value;
    if (textInput) textInput.value = value.toUpperCase();
}

function syncColor(id, value) {
    if (value.match(/^#[0-9A-Fa-f]{6}$/)) {
        document.getElementById(id).value = value;
        document.getElementById(id + '_text').value = value.toUpperCase();
        updateBrandingPreview();
    }
}

function previewLogo(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('newLogoPreview').src = e.target.result;
            document.getElementById('newLogoPreviewContainer').style.display = 'block';
            document.getElementById('previewLogo').src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function updateBrandingPreview() {
    // Header preview
    var bgColor = document.getElementById('branding_header_bg_color').value;
    var textColor = document.getElementById('branding_header_text_color').value;
    var hoverColor = document.getElementById('branding_header_hover_color').value;
    var logoAlignment = document.getElementById('branding_logo_alignment').value;

    var preview = document.getElementById('headerPreview');
    if (preview) {
        preview.style.background = bgColor;
        var navLinks = document.getElementById('previewNavLinks');
        if (navLinks) navLinks.style.color = textColor;
        var cartSpan = preview.querySelector('nav span:last-child');
        if (cartSpan) cartSpan.style.color = hoverColor;
    }

    // Update logo positioning in preview
    var headerPreviewEl = document.getElementById('headerPreview');
    if (headerPreviewEl) {
        // Remove existing position classes
        headerPreviewEl.classList.remove('position-left', 'position-center', 'position-right');
        // Add new position class
        headerPreviewEl.classList.add('position-' + logoAlignment);
    }

    // Theme color preview
    var primary = document.getElementById('branding_theme_primary').value;
    var secondary = document.getElementById('branding_theme_secondary').value;
    var accent = document.getElementById('branding_theme_accent').value;
    var textDark = document.getElementById('branding_theme_text_dark').value;
    var themeBg = document.getElementById('branding_theme_bg').value;

    var previewDivs = document.querySelectorAll('#themeColorPreview > div');
    if (previewDivs.length >= 5) {
        previewDivs[0].style.background = primary;
        previewDivs[1].style.background = secondary;
        previewDivs[2].style.background = accent;
        previewDivs[3].style.background = textDark;
        previewDivs[4].style.background = themeBg;
    }
}

async function saveBrandingSettings(event) {
    event.preventDefault();

    var formData = {
        logo_alignment: document.getElementById('branding_logo_alignment').value,
        nav_height: document.getElementById('branding_nav_height').value,
        header_bg_color: document.getElementById('branding_header_bg_color').value,
        header_text_color: document.getElementById('branding_header_text_color').value,
        header_hover_color: document.getElementById('branding_header_hover_color').value,
        header_style: document.getElementById('branding_header_style').value,
        header_sticky: document.getElementById('branding_header_sticky').checked,
        header_shadow: document.getElementById('branding_header_shadow').checked,
        theme_primary: document.getElementById('branding_theme_primary').value,
        theme_secondary: document.getElementById('branding_theme_secondary').value,
        theme_accent: document.getElementById('branding_theme_accent').value,
        theme_text_dark: document.getElementById('branding_theme_text_dark').value,
        theme_text_light: document.getElementById('branding_theme_text_light').value,
        theme_bg: document.getElementById('branding_theme_bg').value
    };

    try {
        var response = await fetch(API_BASE + '/admin/settings/branding', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(formData)
        });

        var data = await response.json();

        if (data.success) {
            allSettings.branding = formData;
            showAlert('success', 'Branding settings saved successfully');
        } else {
            showAlert('danger', data.message || 'Failed to save branding settings');
        }
    } catch (error) {
        console.error('Error saving branding:', error);
        showAlert('danger', 'Error saving branding settings: ' + error.message);
    }
}

// Set up color input sync on page load
document.addEventListener('DOMContentLoaded', function() {
    var colorInputs = document.querySelectorAll('#brandingForm input[type="color"]');
    colorInputs.forEach(function(input) {
        input.addEventListener('input', function() {
            var textInput = document.getElementById(this.id + '_text');
            if (textInput) textInput.value = this.value.toUpperCase();
            updateBrandingPreview();
        });
    });

    // Set up logo alignment change listener
    var logoAlignmentSelect = document.getElementById('branding_logo_alignment');
    if (logoAlignmentSelect) {
        logoAlignmentSelect.addEventListener('change', function() {
            updateBrandingPreview();
        });
    }
});

async function saveSettings(event, group) {
    event.preventDefault();

    var form = document.getElementById(group + 'Form');
    var formData = {};

    // Get all form inputs
    var inputs = form.querySelectorAll('input, select');
    inputs.forEach(function(input) {
        var key = input.name;
        if (key) {
            if (input.type === 'checkbox') {
                formData[key] = input.checked;
            } else if (input.type === 'number') {
                formData[key] = parseFloat(input.value) || 0;
            } else {
                formData[key] = input.value;
            }
        }
    });

    try {
        var response = await fetch(API_BASE + '/admin/settings/' + group, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(formData)
        });

        var data = await response.json();

        if (data.success) {
            // Update local cache
            allSettings[group] = formData;

            // Show success message
            showAlert('success', data.message || 'Settings saved successfully');
        } else {
            showAlert('danger', data.message || 'Failed to save settings');
        }
    } catch (error) {
        console.error('Error saving settings:', error);
        showAlert('danger', 'Error saving settings: ' + error.message);
    }
}

function showAlert(type, message) {
    // Remove any existing alerts
    var existing = document.querySelector('.settings-alert');
    if (existing) {
        existing.remove();
    }

    // Create new alert
    var alert = document.createElement('div');
    alert.className = 'alert alert-' + type + ' alert-dismissible fade show settings-alert';
    alert.style.position = 'fixed';
    alert.style.top = '20px';
    alert.style.right = '20px';
    alert.style.zIndex = '9999';
    alert.style.minWidth = '300px';
    alert.innerHTML = message + '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';

    document.body.appendChild(alert);

    // Auto-dismiss after 3 seconds
    setTimeout(function() {
        if (alert.parentNode) {
            alert.remove();
        }
    }, 3000);
}
</script>
@endpush
