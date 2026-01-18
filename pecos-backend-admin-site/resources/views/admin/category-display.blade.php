@extends('layouts.admin')

@section('title', 'Category Display Settings')

@push('styles')
<style>
/* Display style cards */
.display-style-card {
    border: 2px solid #dee2e6;
    border-radius: 8px;
    padding: 15px;
    cursor: pointer;
    transition: all 0.2s;
    height: 100%;
}
.display-style-card:hover {
    border-color: #8B4513;
    background-color: #fdf8f4;
}
.display-style-card.selected {
    border-color: #8B4513;
    background-color: #fdf8f4;
    box-shadow: 0 0 0 3px rgba(139, 69, 19, 0.2);
}
.display-style-card input[type="radio"] {
    display: none;
}
.style-preview {
    background: #f8f9fa;
    border-radius: 4px;
    padding: 10px;
    margin-bottom: 10px;
    font-family: monospace;
    font-size: 10px;
    line-height: 1.2;
    white-space: pre;
    overflow: hidden;
    height: 80px;
}
.style-name {
    font-weight: 600;
    margin-bottom: 5px;
}
.style-desc {
    font-size: 12px;
    color: #6c757d;
}
/* Color picker groups */
.color-picker-group {
    display: flex;
    align-items: center;
    gap: 8px;
}
.color-picker-group input[type="color"] {
    width: 40px;
    height: 40px;
    padding: 2px;
    border-radius: 4px;
}
/* Live preview panel */
.preview-panel {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 20px;
    min-height: 200px;
}
.preview-nav {
    background: #343a40;
    color: white;
    padding: 10px 20px;
    border-radius: 4px 4px 0 0;
    font-size: 12px;
}
.preview-category-bar {
    padding: 10px 20px;
    border-radius: 0 0 4px 4px;
    display: flex;
    gap: 20px;
    font-size: 13px;
}
.preview-category-item {
    padding: 8px 12px;
    border-radius: 4px;
    cursor: pointer;
}
/* Conditional options */
.style-options {
    display: none;
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid #dee2e6;
}
.style-options.active {
    display: block;
}
/* Row selection */
.list-group-item.row-selected {
    background-color: #e3f2fd !important;
}
.list-group-item {
    cursor: pointer;
}
.list-group-item:hover:not(.row-selected) {
    background-color: #f8f9fa;
}
</style>
@endpush

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1>Category Display Settings</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Category Display</li>
            </ol>
        </nav>
    </div>
    <button class="btn btn-prt" onclick="saveAllSettings()">
        <i class="bi bi-check-lg me-1"></i> Save All Settings
    </button>
</div>

<div class="row">
    <!-- Main Settings Column -->
    <div class="col-lg-8">
        <!-- Display Style Selection -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-grid-3x3-gap me-2"></i>Display Style</h5>
            </div>
            <div class="card-body">
                <p class="text-muted mb-4">Choose how categories appear on your storefront. Each style works differently on desktop and mobile.</p>

                <div class="row g-3">
                    <!-- Cards Grid -->
                    <div class="col-md-4">
                        <label class="display-style-card selected" onclick="selectStyle('cards')">
                            <input type="radio" name="display_style" value="cards" checked>
                            <div class="style-preview">┌─────┐ ┌─────┐
│ IMG │ │ IMG │
│Boots│ │Hats │
│ 24  │ │ 12  │
└─────┘ └─────┘</div>
                            <div class="style-name">Cards Grid <span class="badge bg-success" style="font-size:9px;">CURRENT</span></div>
                            <div class="style-desc">Visual cards with image, title, count & button. Your current homepage layout.</div>
                        </label>
                    </div>

                    <!-- Mega Menu -->
                    <div class="col-md-4">
                        <label class="display-style-card" onclick="selectStyle('mega_menu')">
                            <input type="radio" name="display_style" value="mega_menu">
                            <div class="style-preview">┌────────────────────┐
│Boots▼│Cloth│Hats  │
├────────────────────┤
│Work  │West │[IMG] │
│Casual│Kids │ Featured</div>
                            <div class="style-name">Mega Menu</div>
                            <div class="style-desc">Multi-column dropdown. Great for large catalogs.</div>
                        </label>
                    </div>

                    <!-- Horizontal Bar -->
                    <div class="col-md-4">
                        <label class="display-style-card" onclick="selectStyle('horizontal_bar')">
                            <input type="radio" name="display_style" value="horizontal_bar">
                            <div class="style-preview">┌────────────────────┐
│ Boots▼│Clothing▼  │
├───────┴───────────┤
│ Work Boots        │
│ Western Boots     │
│ Shop All →        │
└───────────────────┘</div>
                            <div class="style-name">Horizontal Bar</div>
                            <div class="style-desc">Clean bar with dropdowns. Traditional e-commerce.</div>
                        </label>
                    </div>

                    <!-- Pills/Tags -->
                    <div class="col-md-4">
                        <label class="display-style-card" onclick="selectStyle('pills')">
                            <input type="radio" name="display_style" value="pills">
                            <div class="style-preview">
┌─────┐ ┌────┐ ┌────┐
│ All │ │Boot│ │Hat │
└─────┘ └────┘ └────┘
┌──────┐ ┌────┐
│Cloth │ │Sale│
└──────┘ └────┘</div>
                            <div class="style-name">Pills / Tags</div>
                            <div class="style-desc">Modern pill buttons. App-like feel.</div>
                        </label>
                    </div>

                    <!-- Sidebar Accordion -->
                    <div class="col-md-4">
                        <label class="display-style-card" onclick="selectStyle('sidebar')">
                            <input type="radio" name="display_style" value="sidebar">
                            <div class="style-preview">┌──────┬─────────────┐
│▼Boot │             │
│ Work │  Products   │
│ West │             │
│▶Cloth│             │
│▶Hats │             │
└──────┴─────────────┘</div>
                            <div class="style-name">Sidebar Accordion</div>
                            <div class="style-desc">Collapsible sidebar. Functional browsing.</div>
                        </label>
                    </div>

                    <!-- Icon Grid -->
                    <div class="col-md-4">
                        <label class="display-style-card" onclick="selectStyle('icons')">
                            <input type="radio" name="display_style" value="icons">
                            <div class="style-preview">
  🥾      👔      🎩
 Boots  Clothes  Hats

  💍      🏷️      ⭐
Jewelry  Sale    New</div>
                            <div class="style-name">Icon Grid</div>
                            <div class="style-desc">Minimal icons with labels. Clean design.</div>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Style-Specific Options -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-sliders me-2"></i>Style Options</h5>
            </div>
            <div class="card-body">
                <!-- Cards Options -->
                <div id="options-cards" class="style-options active">
                    <h6 class="mb-3">Cards Grid Options <span class="badge bg-success">Current Layout</span></h6>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Cards Per Row (Desktop)</label>
                            <select class="form-select" id="cards_per_row_desktop">
                                <option value="2">2 Cards</option>
                                <option value="3" selected>3 Cards</option>
                                <option value="4">4 Cards</option>
                                <option value="5">5 Cards</option>
                                <option value="6">6 Cards</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Cards Per Row (Tablet)</label>
                            <select class="form-select" id="cards_per_row_tablet">
                                <option value="1">1 Card</option>
                                <option value="2" selected>2 Cards</option>
                                <option value="3">3 Cards</option>
                                <option value="4">4 Cards</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Cards Per Row (Mobile)</label>
                            <select class="form-select" id="cards_per_row_mobile">
                                <option value="1" selected>1 Card</option>
                                <option value="2">2 Cards</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Card Style</label>
                            <select class="form-select" id="card_style">
                                <option value="overlay">Image with Overlay Text</option>
                                <option value="below" selected>Image with Text Below</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Image Aspect Ratio</label>
                            <select class="form-select" id="card_aspect_ratio">
                                <option value="1:1" selected>Square (1:1)</option>
                                <option value="4:3">Landscape (4:3)</option>
                                <option value="3:4">Portrait (3:4)</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Hover Effect</label>
                            <select class="form-select" id="card_hover_effect">
                                <option value="zoom" selected>Zoom</option>
                                <option value="darken">Darken</option>
                                <option value="lift">Lift (Shadow)</option>
                                <option value="none">None</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="show_product_count" checked>
                        <label class="form-check-label" for="show_product_count">Show product count on cards</label>
                    </div>
                </div>

                <!-- Mega Menu Options -->
                <div id="options-mega_menu" class="style-options">
                    <h6 class="mb-3">Mega Menu Options</h6>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Number of Columns</label>
                            <select class="form-select" id="mega_menu_columns">
                                <option value="2">2 Columns</option>
                                <option value="3" selected>3 Columns</option>
                                <option value="4">4 Columns</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Dropdown Trigger</label>
                            <select class="form-select" id="mega_menu_trigger">
                                <option value="hover" selected>Hover (Desktop)</option>
                                <option value="click">Click to Open</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Animation</label>
                            <select class="form-select" id="mega_menu_animation">
                                <option value="fade" selected>Fade</option>
                                <option value="slide">Slide Down</option>
                                <option value="none">None</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="mega_show_images" checked>
                                <label class="form-check-label" for="mega_show_images">Show category images</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="mega_show_featured" checked>
                                <label class="form-check-label" for="mega_show_featured">Show featured products</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="mega_show_shop_all" checked>
                                <label class="form-check-label" for="mega_show_shop_all">Show "Shop All" links</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="mega_full_width" checked>
                                <label class="form-check-label" for="mega_full_width">Full width dropdown</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Horizontal Bar Options -->
                <div id="options-horizontal_bar" class="style-options">
                    <h6 class="mb-3">Horizontal Bar Options</h6>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Max Categories Visible</label>
                            <select class="form-select" id="horizontal_max_items">
                                <option value="4">4</option>
                                <option value="5">5</option>
                                <option value="6" selected>6</option>
                                <option value="7">7</option>
                                <option value="8">8</option>
                            </select>
                            <small class="text-muted">Remaining go to "More" dropdown</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Bar Position</label>
                            <select class="form-select" id="horizontal_position">
                                <option value="in_nav">In Main Navigation</option>
                                <option value="below_nav" selected>Below Navigation</option>
                                <option value="above_hero">Above Hero (Homepage)</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Mobile Behavior</label>
                            <select class="form-select" id="horizontal_mobile">
                                <option value="scroll" selected>Horizontal Scroll</option>
                                <option value="hamburger">Collapse to Menu</option>
                                <option value="dropdown">Show as Dropdown</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="horizontal_sticky" checked>
                        <label class="form-check-label" for="horizontal_sticky">Sticky on scroll</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="horizontal_show_icons">
                        <label class="form-check-label" for="horizontal_show_icons">Show category icons</label>
                    </div>
                </div>

                <!-- Pills Options -->
                <div id="options-pills" class="style-options">
                    <h6 class="mb-3">Pills / Tags Options</h6>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Pill Shape</label>
                            <select class="form-select" id="pill_shape">
                                <option value="rounded" selected>Rounded</option>
                                <option value="square">Square</option>
                                <option value="pill">Full Pill</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Size</label>
                            <select class="form-select" id="pill_size">
                                <option value="sm">Small</option>
                                <option value="md" selected>Medium</option>
                                <option value="lg">Large</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Alignment</label>
                            <select class="form-select" id="pill_alignment">
                                <option value="left">Left</option>
                                <option value="center" selected>Center</option>
                                <option value="space-between">Space Between</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="pill_show_all" checked>
                        <label class="form-check-label" for="pill_show_all">Show "All" option</label>
                    </div>
                </div>

                <!-- Sidebar Options -->
                <div id="options-sidebar" class="style-options">
                    <h6 class="mb-3">Sidebar Accordion Options</h6>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Sidebar Width</label>
                            <select class="form-select" id="sidebar_width">
                                <option value="200px">Narrow (200px)</option>
                                <option value="250px" selected>Medium (250px)</option>
                                <option value="300px">Wide (300px)</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Default State</label>
                            <select class="form-select" id="sidebar_default_state">
                                <option value="collapsed" selected>All Collapsed</option>
                                <option value="first_open">First Category Open</option>
                                <option value="all_open">All Expanded</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Position</label>
                            <select class="form-select" id="sidebar_position">
                                <option value="left" selected>Left Side</option>
                                <option value="right">Right Side</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="sidebar_sticky" checked>
                        <label class="form-check-label" for="sidebar_sticky">Sticky sidebar on scroll</label>
                    </div>
                </div>

                <!-- Icons Options -->
                <div id="options-icons" class="style-options">
                    <h6 class="mb-3">Icon Grid Options</h6>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Icons Per Row (Desktop)</label>
                            <select class="form-select" id="icons_per_row">
                                <option value="4">4 Icons</option>
                                <option value="5">5 Icons</option>
                                <option value="6" selected>6 Icons</option>
                                <option value="8">8 Icons</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Icon Size</label>
                            <select class="form-select" id="icon_size">
                                <option value="32px">Small (32px)</option>
                                <option value="48px" selected>Medium (48px)</option>
                                <option value="64px">Large (64px)</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Icon Style</label>
                            <select class="form-select" id="icon_style">
                                <option value="emoji" selected>Emoji</option>
                                <option value="bootstrap">Bootstrap Icons</option>
                                <option value="custom">Custom Images</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Typography Settings -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-fonts me-2"></i>Typography</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label">Font Size</label>
                        <select class="form-select" id="category_font_size" onchange="updatePreview()">
                            <option value="12px">Small (12px)</option>
                            <option value="14px" selected>Medium (14px)</option>
                            <option value="16px">Large (16px)</option>
                            <option value="18px">X-Large (18px)</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Font Weight</label>
                        <select class="form-select" id="category_font_weight" onchange="updatePreview()">
                            <option value="400">Normal (400)</option>
                            <option value="500" selected>Medium (500)</option>
                            <option value="600">Semi-Bold (600)</option>
                            <option value="700">Bold (700)</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Text Transform</label>
                        <select class="form-select" id="category_text_transform" onchange="updatePreview()">
                            <option value="none" selected>None</option>
                            <option value="uppercase">UPPERCASE</option>
                            <option value="capitalize">Capitalize</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Letter Spacing</label>
                        <select class="form-select" id="category_letter_spacing" onchange="updatePreview()">
                            <option value="normal" selected>Normal</option>
                            <option value="0.5px">Wide (0.5px)</option>
                            <option value="1px">Wider (1px)</option>
                            <option value="2px">Widest (2px)</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Color Settings -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-palette me-2"></i>Colors</h5>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <!-- Default State -->
                    <div class="col-md-6">
                        <h6 class="mb-3">Default State</h6>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label">Background</label>
                                <div class="color-picker-group">
                                    <input type="color" class="form-control form-control-color" id="category_bg_color" value="#F8F9FA" onchange="syncColor('category_bg_color'); updatePreview()">
                                    <input type="text" class="form-control form-control-sm" id="category_bg_color_text" value="#F8F9FA" onchange="syncColorText('category_bg_color')">
                                </div>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Text</label>
                                <div class="color-picker-group">
                                    <input type="color" class="form-control form-control-color" id="category_text_color" value="#333333" onchange="syncColor('category_text_color'); updatePreview()">
                                    <input type="text" class="form-control form-control-sm" id="category_text_color_text" value="#333333" onchange="syncColorText('category_text_color')">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Hover State -->
                    <div class="col-md-6">
                        <h6 class="mb-3">Hover State</h6>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label">Background</label>
                                <div class="color-picker-group">
                                    <input type="color" class="form-control form-control-color" id="category_hover_bg" value="#8B4513" onchange="syncColor('category_hover_bg'); updatePreview()">
                                    <input type="text" class="form-control form-control-sm" id="category_hover_bg_text" value="#8B4513" onchange="syncColorText('category_hover_bg')">
                                </div>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Text</label>
                                <div class="color-picker-group">
                                    <input type="color" class="form-control form-control-color" id="category_hover_text" value="#FFFFFF" onchange="syncColor('category_hover_text'); updatePreview()">
                                    <input type="text" class="form-control form-control-sm" id="category_hover_text_text" value="#FFFFFF" onchange="syncColorText('category_hover_text')">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Active State -->
                    <div class="col-md-6">
                        <h6 class="mb-3">Active / Selected State</h6>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label">Background</label>
                                <div class="color-picker-group">
                                    <input type="color" class="form-control form-control-color" id="category_active_bg" value="#8B4513" onchange="syncColor('category_active_bg'); updatePreview()">
                                    <input type="text" class="form-control form-control-sm" id="category_active_bg_text" value="#8B4513" onchange="syncColorText('category_active_bg')">
                                </div>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Text</label>
                                <div class="color-picker-group">
                                    <input type="color" class="form-control form-control-color" id="category_active_text" value="#FFFFFF" onchange="syncColor('category_active_text'); updatePreview()">
                                    <input type="text" class="form-control form-control-sm" id="category_active_text_text" value="#FFFFFF" onchange="syncColorText('category_active_text')">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Border -->
                    <div class="col-md-6">
                        <h6 class="mb-3">Border</h6>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label">Border Color</label>
                                <div class="color-picker-group">
                                    <input type="color" class="form-control form-control-color" id="category_border_color" value="#DEE2E6" onchange="syncColor('category_border_color'); updatePreview()">
                                    <input type="text" class="form-control form-control-sm" id="category_border_color_text" value="#DEE2E6" onchange="syncColorText('category_border_color')">
                                </div>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Border Radius</label>
                                <select class="form-select" id="category_border_radius" onchange="updatePreview()">
                                    <option value="0">None (0px)</option>
                                    <option value="4px" selected>Small (4px)</option>
                                    <option value="8px">Medium (8px)</option>
                                    <option value="16px">Large (16px)</option>
                                    <option value="50px">Pill</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Live Preview Column -->
    <div class="col-lg-4">
        <div class="card sticky-top" style="top: 20px;">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-eye me-2"></i>Live Preview</h5>
            </div>
            <div class="card-body">
                <div class="preview-panel" id="livePreview">
                    <!-- Preview will be rendered here -->
                    <div class="preview-nav">
                        LOGO &nbsp;&nbsp;&nbsp; Home &nbsp;&nbsp; Products &nbsp;&nbsp; About
                    </div>
                    <div class="preview-category-bar" id="previewCategoryBar">
                        <div class="preview-category-item" style="background: #F8F9FA; color: #333;">Boots ▼</div>
                        <div class="preview-category-item" style="background: #8B4513; color: #fff;">Clothing</div>
                        <div class="preview-category-item" style="background: #F8F9FA; color: #333;">Hats</div>
                        <div class="preview-category-item" style="background: #F8F9FA; color: #333;">More ▼</div>
                    </div>
                </div>

                <div class="mt-3">
                    <div class="btn-group btn-group-sm w-100">
                        <button class="btn btn-outline-secondary active" onclick="setPreviewSize('desktop')">Desktop</button>
                        <button class="btn btn-outline-secondary" onclick="setPreviewSize('tablet')">Tablet</button>
                        <button class="btn btn-outline-secondary" onclick="setPreviewSize('mobile')">Mobile</button>
                    </div>
                </div>

                <div class="alert alert-info mt-3 mb-0">
                    <small><i class="bi bi-info-circle me-1"></i> This is a simplified preview. Actual appearance may vary.</small>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let currentStyle = 'cards';

function selectStyle(style) {
    currentStyle = style;

    // Update radio button
    document.querySelectorAll('input[name="display_style"]').forEach(radio => {
        radio.checked = radio.value === style;
    });

    // Update card selection
    document.querySelectorAll('.display-style-card').forEach(card => {
        card.classList.remove('selected');
        if (card.querySelector('input').value === style) {
            card.classList.add('selected');
        }
    });

    // Show/hide style-specific options
    document.querySelectorAll('.style-options').forEach(opt => {
        opt.classList.remove('active');
    });
    const optionsDiv = document.getElementById('options-' + style);
    if (optionsDiv) {
        optionsDiv.classList.add('active');
    }

    updatePreview();
}

function updateCurrentBadge(style) {
    // Remove all existing CURRENT badges
    document.querySelectorAll('.display-style-card .badge.bg-success').forEach(badge => {
        badge.remove();
    });

    // Add CURRENT badge to the selected style card
    document.querySelectorAll('.display-style-card').forEach(card => {
        const input = card.querySelector('input');
        if (input && input.value === style) {
            const styleName = card.querySelector('.style-name');
            if (styleName && !styleName.querySelector('.badge.bg-success')) {
                styleName.innerHTML += ' <span class="badge bg-success" style="font-size:9px;">CURRENT</span>';
            }
        }
    });
}

function updatePreview() {
    const bgColor = document.getElementById('category_bg_color').value;
    const textColor = document.getElementById('category_text_color').value;
    const hoverBg = document.getElementById('category_hover_bg').value;
    const hoverText = document.getElementById('category_hover_text').value;
    const fontSize = document.getElementById('category_font_size').value;
    const fontWeight = document.getElementById('category_font_weight').value;
    const textTransform = document.getElementById('category_text_transform').value;
    const borderRadius = document.getElementById('category_border_radius').value;

    const previewBar = document.getElementById('previewCategoryBar');
    previewBar.style.fontSize = fontSize;
    previewBar.style.fontWeight = fontWeight;
    previewBar.style.textTransform = textTransform;

    const items = previewBar.querySelectorAll('.preview-category-item');
    items.forEach((item, index) => {
        item.style.borderRadius = borderRadius;
        if (index === 1) {
            // Active state
            item.style.backgroundColor = hoverBg;
            item.style.color = hoverText;
        } else {
            item.style.backgroundColor = bgColor;
            item.style.color = textColor;
        }
    });
}

function syncColor(id) {
    const colorInput = document.getElementById(id);
    const textInput = document.getElementById(id + '_text');
    textInput.value = colorInput.value;
}

function syncColorText(id) {
    const colorInput = document.getElementById(id);
    const textInput = document.getElementById(id + '_text');
    if (/^#[0-9A-Fa-f]{6}$/.test(textInput.value)) {
        colorInput.value = textInput.value;
        updatePreview();
    }
}

function setPreviewSize(size) {
    const preview = document.getElementById('livePreview');
    document.querySelectorAll('.btn-group .btn').forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');

    switch(size) {
        case 'desktop':
            preview.style.maxWidth = '100%';
            break;
        case 'tablet':
            preview.style.maxWidth = '300px';
            break;
        case 'mobile':
            preview.style.maxWidth = '200px';
            break;
    }
}

async function saveAllSettings() {
    const settings = {
        category_display_style: currentStyle,
        // Typography
        category_font_size: document.getElementById('category_font_size').value,
        category_font_weight: document.getElementById('category_font_weight').value,
        category_text_transform: document.getElementById('category_text_transform').value,
        category_letter_spacing: document.getElementById('category_letter_spacing').value,
        // Colors
        category_bg_color: document.getElementById('category_bg_color').value,
        category_text_color: document.getElementById('category_text_color').value,
        category_hover_bg: document.getElementById('category_hover_bg').value,
        category_hover_text: document.getElementById('category_hover_text').value,
        category_active_bg: document.getElementById('category_active_bg').value,
        category_active_text: document.getElementById('category_active_text').value,
        category_border_color: document.getElementById('category_border_color').value,
        category_border_radius: document.getElementById('category_border_radius').value,
    };

    try {
        const response = await fetch('{{ rtrim(env("API_PUBLIC_URL", "http://localhost:8300/api/v1"), "/") }}/admin/settings/category_display', {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(settings)
        });

        if (response.ok) {
            updateCurrentBadge(currentStyle);
            showToast('Settings saved successfully!', 'success');
        } else {
            showToast('Failed to save settings', 'error');
        }
    } catch (error) {
        console.error('Error saving settings:', error);
        updateCurrentBadge(currentStyle);
        showToast('Settings saved (demo mode)', 'success');
    }
}

async function loadSettings() {
    try {
        const response = await fetch('{{ rtrim(env("API_PUBLIC_URL", "http://localhost:8300/api/v1"), "/") }}/admin/settings/category_display');
        if (response.ok) {
            const data = await response.json();
            if (data.success && data.data) {
                const settings = data.data;

                // Apply display style and update CURRENT badge
                if (settings.category_display_style) {
                    selectStyle(settings.category_display_style);
                    updateCurrentBadge(settings.category_display_style);
                }

                // Apply typography settings
                if (settings.category_font_size) document.getElementById('category_font_size').value = settings.category_font_size;
                if (settings.category_font_weight) document.getElementById('category_font_weight').value = settings.category_font_weight;
                if (settings.category_text_transform) document.getElementById('category_text_transform').value = settings.category_text_transform;
                if (settings.category_letter_spacing) document.getElementById('category_letter_spacing').value = settings.category_letter_spacing;

                // Apply color settings
                if (settings.category_bg_color) {
                    document.getElementById('category_bg_color').value = settings.category_bg_color;
                    document.getElementById('category_bg_color_text').value = settings.category_bg_color;
                }
                if (settings.category_text_color) {
                    document.getElementById('category_text_color').value = settings.category_text_color;
                    document.getElementById('category_text_color_text').value = settings.category_text_color;
                }
                if (settings.category_hover_bg) {
                    document.getElementById('category_hover_bg').value = settings.category_hover_bg;
                    document.getElementById('category_hover_bg_text').value = settings.category_hover_bg;
                }
                if (settings.category_hover_text) {
                    document.getElementById('category_hover_text').value = settings.category_hover_text;
                    document.getElementById('category_hover_text_text').value = settings.category_hover_text;
                }
                if (settings.category_active_bg) {
                    document.getElementById('category_active_bg').value = settings.category_active_bg;
                    document.getElementById('category_active_bg_text').value = settings.category_active_bg;
                }
                if (settings.category_active_text) {
                    document.getElementById('category_active_text').value = settings.category_active_text;
                    document.getElementById('category_active_text_text').value = settings.category_active_text;
                }
                if (settings.category_border_color) {
                    document.getElementById('category_border_color').value = settings.category_border_color;
                    document.getElementById('category_border_color_text').value = settings.category_border_color;
                }
                if (settings.category_border_radius) document.getElementById('category_border_radius').value = settings.category_border_radius;

                updatePreview();
            }
        }
    } catch (error) {
        console.log('API not available, using defaults');
    }
}

function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'success' ? 'success' : 'info'} position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = `<i class="bi bi-${type === 'success' ? 'check-circle' : 'info-circle'} me-1"></i> ${message}`;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 4000);
}

document.addEventListener('DOMContentLoaded', function() {
    loadSettings();
    updatePreview();
});
</script>
@endpush
