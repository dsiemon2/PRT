@extends('layouts.admin')

@section('title', 'Product Display Settings')

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
    padding: 8px;
    margin-bottom: 10px;
    font-family: monospace;
    font-size: 9px;
    line-height: 1.1;
    white-space: pre;
    overflow: hidden;
    height: 70px;
}
.style-name {
    font-weight: 600;
    margin-bottom: 5px;
    font-size: 13px;
}
.style-desc {
    font-size: 11px;
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
    background: #fff;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    overflow: hidden;
    min-height: 300px;
}
.preview-nav {
    background: #343a40;
    color: white;
    padding: 8px 15px;
    font-size: 11px;
}
.preview-hero {
    background: linear-gradient(135deg, #8B4513 0%, #654321 100%);
    color: white;
    padding: 20px;
    text-align: center;
    font-size: 12px;
}
.preview-category-bar {
    background: #f8f9fa;
    padding: 8px 15px;
    display: flex;
    gap: 15px;
    font-size: 11px;
    border-bottom: 1px solid #dee2e6;
}
.preview-content {
    display: flex;
    padding: 15px;
    gap: 15px;
}
.preview-sidebar {
    width: 80px;
    background: #f8f9fa;
    padding: 10px;
    font-size: 9px;
    border-radius: 4px;
}
.preview-grid {
    flex: 1;
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 8px;
}
.preview-card {
    background: #e9ecef;
    border-radius: 4px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 8px;
    color: #6c757d;
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
</style>
@endpush

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1>Product Display Settings</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Product Display</li>
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
        <!-- Layout Style Selection -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-layout-text-window-reverse me-2"></i>Product Page Layout</h5>
            </div>
            <div class="card-body">
                <p class="text-muted mb-4">Choose how the products page is laid out. This affects the category navigation and product grid arrangement.</p>

                <div class="row g-3">
                    <!-- Sidebar Layout (Current) -->
                    <div class="col-md-4">
                        <label class="display-style-card selected" onclick="selectStyle('sidebar')">
                            <input type="radio" name="layout_style" value="sidebar" checked>
                            <div class="style-preview">┌────┬──────────┐
│FILT│ [P] [P]  │
│▼Cat│ [P] [P]  │
│▼Cat│ [P] [P]  │
│░░░░│          │
└────┴──────────┘</div>
                            <div class="style-name">Sidebar + Grid <span class="badge bg-success" style="font-size:9px;">CURRENT</span></div>
                            <div class="style-desc">Scrollable filter sidebar with categories, search & filters. Your current layout.</div>
                        </label>
                    </div>

                    <!-- Horizontal Category Bar -->
                    <div class="col-md-4">
                        <label class="display-style-card" onclick="selectStyle('horizontal')">
                            <input type="radio" name="layout_style" value="horizontal">
                            <div class="style-preview">┌────────────────┐
│Cat1▼│Cat2▼│Cat3│
├────────────────┤
│[P][P][P][P][P] │
│[P][P][P][P][P] │
└────────────────┘</div>
                            <div class="style-name">Horizontal Bar</div>
                            <div class="style-desc">Category bar with dropdowns. Full-width grid below.</div>
                        </label>
                    </div>

                    <!-- Filter Bar Only -->
                    <div class="col-md-4">
                        <label class="display-style-card" onclick="selectStyle('filters')">
                            <input type="radio" name="layout_style" value="filters">
                            <div class="style-preview">┌────────────────┐
│[Cat▼][Price▼]  │
├────────────────┤
│[P][P][P][P][P] │
│[P][P][P][P][P] │
└────────────────┘</div>
                            <div class="style-name">Filter Bar Only</div>
                            <div class="style-desc">Minimal top filters. Amazon/modern style.</div>
                        </label>
                    </div>

                    <!-- Mega Menu -->
                    <div class="col-md-4">
                        <label class="display-style-card" onclick="selectStyle('mega_menu')">
                            <input type="radio" name="layout_style" value="mega_menu">
                            <div class="style-preview">┌────────────────┐
│Boot│Cloth│Hats │
├────────────────┤
│Work │West│[IMG]│
│Casl │Kids│     │
└────────────────┘</div>
                            <div class="style-name">Mega Menu</div>
                            <div class="style-desc">Multi-column dropdown menus. Best Buy/Home Depot style.</div>
                        </label>
                    </div>

                    <!-- Collapsible Sidebar -->
                    <div class="col-md-4">
                        <label class="display-style-card" onclick="selectStyle('collapsible')">
                            <input type="radio" name="layout_style" value="collapsible">
                            <div class="style-preview">┌────────────────┐
│[≡ Filters]     │
├────────────────┤
│[P][P][P][P][P] │
│[P][P][P][P][P] │
└────────────────┘</div>
                            <div class="style-name">Collapsible Sidebar</div>
                            <div class="style-desc">Hidden filters, full-width grid. Mobile-first design.</div>
                        </label>
                    </div>

                    <!-- Split Hero -->
                    <div class="col-md-4">
                        <label class="display-style-card" onclick="selectStyle('split_hero')">
                            <input type="radio" name="layout_style" value="split_hero">
                            <div class="style-preview">┌───────┬───────┐
│ HERO  │ CATS  │
│ SALE  │ →Boot │
│       │ →Cloth│
├───────┴───────┤
│[P][P][P][P]   │</div>
                            <div class="style-name">Split Hero</div>
                            <div class="style-desc">Hero banner + category list. Shopify style.</div>
                        </label>
                    </div>

                    <!-- Infinite Scroll -->
                    <div class="col-md-4">
                        <label class="display-style-card" onclick="selectStyle('infinite')">
                            <input type="radio" name="layout_style" value="infinite">
                            <div class="style-preview">┌────────────────┐
│[All][Boot][Hat]│
├────────────────┤
│[P][P][P]       │
│[P][P][P]       │
│  ↓ Loading...  │</div>
                            <div class="style-name">Infinite Scroll</div>
                            <div class="style-desc">Pill filters + floating button. Social media feel.</div>
                        </label>
                    </div>

                    <!-- Magazine Layout -->
                    <div class="col-md-4">
                        <label class="display-style-card" onclick="selectStyle('magazine')">
                            <input type="radio" name="layout_style" value="magazine">
                            <div class="style-preview">┌────────────────┐
│   FEATURED     │
│    IMAGE       │
├────────────────┤
│[IMG] │ Desc   │
│      │ $299   │</div>
                            <div class="style-name">Magazine</div>
                            <div class="style-desc">Editorial layout. Luxury/high-end brands.</div>
                        </label>
                    </div>

                    <!-- Category Cards -->
                    <div class="col-md-4">
                        <label class="display-style-card" onclick="selectStyle('category_cards')">
                            <input type="radio" name="layout_style" value="category_cards">
                            <div class="style-preview">┌────────────────┐
│[IMG ][IMG ][IMG│
│Boot  Cloth Hat │
├────────────────┤
│[P][P][P][P]    │</div>
                            <div class="style-name">Category Cards First</div>
                            <div class="style-desc">Visual category cards above products. Etsy style.</div>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Style-Specific Options -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-sliders me-2"></i>Layout Options</h5>
            </div>
            <div class="card-body">
                <!-- Sidebar Options -->
                <div id="options-sidebar" class="style-options active">
                    <h6 class="mb-3">Sidebar + Grid Options <span class="badge bg-success">Current Layout</span></h6>
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
                            <label class="form-label">Sidebar Position</label>
                            <select class="form-select" id="sidebar_position">
                                <option value="left" selected>Left Side</option>
                                <option value="right">Right Side</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Category Display</label>
                            <select class="form-select" id="sidebar_category_display">
                                <option value="list" selected>Simple List</option>
                                <option value="accordion">Accordion (Expandable)</option>
                                <option value="tree">Tree View</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Sidebar Header Text</label>
                            <input type="text" class="form-control" id="sidebar_header_text" value="Filter by Category">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Sidebar Max Height</label>
                            <select class="form-select" id="sidebar_max_height">
                                <option value="none">No limit (scroll with page)</option>
                                <option value="calc(100vh - 40px)" selected>Viewport height (scrollable sidebar)</option>
                                <option value="500px">500px</option>
                                <option value="600px">600px</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="sidebar_scrollable" checked>
                        <label class="form-check-label" for="sidebar_scrollable">
                            <strong>Scrollable sidebar</strong> (own scrollbar, independent of page scroll)
                        </label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="sidebar_sticky" checked>
                        <label class="form-check-label" for="sidebar_sticky">Sticky sidebar on scroll</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="sidebar_show_count" checked>
                        <label class="form-check-label" for="sidebar_show_count">Show product count per category</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="sidebar_show_search" checked>
                        <label class="form-check-label" for="sidebar_show_search">Show search box in sidebar</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="sidebar_show_filters" checked>
                        <label class="form-check-label" for="sidebar_show_filters">Show advanced filters (Price, Size, Sort)</label>
                    </div>
                </div>

                <!-- Horizontal Bar Options -->
                <div id="options-horizontal" class="style-options">
                    <h6 class="mb-3">Horizontal Category Bar Options</h6>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Max Categories Visible</label>
                            <select class="form-select" id="horizontal_max_categories">
                                <option value="4">4</option>
                                <option value="5">5</option>
                                <option value="6" selected>6</option>
                                <option value="7">7</option>
                                <option value="8">8</option>
                            </select>
                            <small class="text-muted">Rest go to "More" dropdown</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Bar Background</label>
                            <div class="color-picker-group">
                                <input type="color" class="form-control form-control-color" id="horizontal_bg_color" value="#f8f9fa">
                                <input type="text" class="form-control form-control-sm" value="#f8f9fa">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Dropdown Trigger</label>
                            <select class="form-select" id="horizontal_trigger">
                                <option value="hover" selected>Hover</option>
                                <option value="click">Click</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="horizontal_sticky" checked>
                        <label class="form-check-label" for="horizontal_sticky">Sticky category bar on scroll</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="horizontal_show_subcategories" checked>
                        <label class="form-check-label" for="horizontal_show_subcategories">Show subcategories in dropdown</label>
                    </div>
                </div>

                <!-- Filter Bar Options -->
                <div id="options-filters" class="style-options">
                    <h6 class="mb-3">Filter Bar Options</h6>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Available Filters</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="filter_category" checked>
                                <label class="form-check-label" for="filter_category">Category</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="filter_price" checked>
                                <label class="form-check-label" for="filter_price">Price Range</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="filter_brand">
                                <label class="form-check-label" for="filter_brand">Brand</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="filter_color">
                                <label class="form-check-label" for="filter_color">Color</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="filter_size">
                                <label class="form-check-label" for="filter_size">Size</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Sort Options</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="sort_newest" checked>
                                <label class="form-check-label" for="sort_newest">Newest First</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="sort_price_low" checked>
                                <label class="form-check-label" for="sort_price_low">Price: Low to High</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="sort_price_high" checked>
                                <label class="form-check-label" for="sort_price_high">Price: High to Low</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="sort_popular" checked>
                                <label class="form-check-label" for="sort_popular">Most Popular</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mega Menu Options -->
                <div id="options-mega_menu" class="style-options">
                    <h6 class="mb-3">Mega Menu Options</h6>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Columns per Dropdown</label>
                            <select class="form-select" id="mega_columns">
                                <option value="2">2 Columns</option>
                                <option value="3" selected>3 Columns</option>
                                <option value="4">4 Columns</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Animation</label>
                            <select class="form-select" id="mega_animation">
                                <option value="fade" selected>Fade In</option>
                                <option value="slide">Slide Down</option>
                                <option value="none">None</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Animation Speed</label>
                            <select class="form-select" id="mega_speed">
                                <option value="100">Fast (100ms)</option>
                                <option value="200" selected>Normal (200ms)</option>
                                <option value="300">Slow (300ms)</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="mega_show_images" checked>
                        <label class="form-check-label" for="mega_show_images">Show featured image in dropdown</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="mega_show_featured" checked>
                        <label class="form-check-label" for="mega_show_featured">Show featured products</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="mega_full_width" checked>
                        <label class="form-check-label" for="mega_full_width">Full-width dropdown</label>
                    </div>
                </div>

                <!-- Collapsible Options -->
                <div id="options-collapsible" class="style-options">
                    <h6 class="mb-3">Collapsible Sidebar Options</h6>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Filter Button Position</label>
                            <select class="form-select" id="collapsible_button_position">
                                <option value="left" selected>Left</option>
                                <option value="right">Right</option>
                                <option value="center">Center</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Filter Panel Width</label>
                            <select class="form-select" id="collapsible_panel_width">
                                <option value="250px">Narrow (250px)</option>
                                <option value="300px" selected>Medium (300px)</option>
                                <option value="350px">Wide (350px)</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Panel Animation</label>
                            <select class="form-select" id="collapsible_animation">
                                <option value="slide" selected>Slide In</option>
                                <option value="fade">Fade In</option>
                                <option value="none">None</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="collapsible_overlay" checked>
                        <label class="form-check-label" for="collapsible_overlay">Show overlay when panel open</label>
                    </div>
                </div>

                <!-- Split Hero Options -->
                <div id="options-split_hero" class="style-options">
                    <h6 class="mb-3">Split Hero + Categories Options</h6>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Hero Width</label>
                            <select class="form-select" id="split_hero_width">
                                <option value="50%">50%</option>
                                <option value="60%" selected>60%</option>
                                <option value="70%">70%</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Hero Height</label>
                            <select class="form-select" id="split_hero_height">
                                <option value="300px">Small (300px)</option>
                                <option value="400px" selected>Medium (400px)</option>
                                <option value="500px">Large (500px)</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Category List Style</label>
                            <select class="form-select" id="split_category_style">
                                <option value="links" selected>Simple Links</option>
                                <option value="buttons">Buttons</option>
                                <option value="cards">Mini Cards</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Infinite Scroll Options -->
                <div id="options-infinite" class="style-options">
                    <h6 class="mb-3">Infinite Scroll Options</h6>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Products per Load</label>
                            <select class="form-select" id="infinite_products_per_load">
                                <option value="12">12 products</option>
                                <option value="20" selected>20 products</option>
                                <option value="30">30 products</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Pill Filter Style</label>
                            <select class="form-select" id="infinite_pill_style">
                                <option value="rounded" selected>Rounded</option>
                                <option value="square">Square</option>
                                <option value="full_pill">Full Pill</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Floating Button Position</label>
                            <select class="form-select" id="infinite_button_position">
                                <option value="bottom-right" selected>Bottom Right</option>
                                <option value="bottom-left">Bottom Left</option>
                                <option value="bottom-center">Bottom Center</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="infinite_show_floating" checked>
                        <label class="form-check-label" for="infinite_show_floating">Show floating filter button</label>
                    </div>
                </div>

                <!-- Magazine Options -->
                <div id="options-magazine" class="style-options">
                    <h6 class="mb-3">Magazine Layout Options</h6>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Featured Image Height</label>
                            <select class="form-select" id="magazine_featured_height">
                                <option value="400px">Medium (400px)</option>
                                <option value="500px" selected>Large (500px)</option>
                                <option value="600px">Extra Large (600px)</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Product Layout</label>
                            <select class="form-select" id="magazine_product_layout">
                                <option value="alternating" selected>Alternating</option>
                                <option value="image_left">Image Always Left</option>
                                <option value="image_right">Image Always Right</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Products per Row</label>
                            <select class="form-select" id="magazine_products_per_row">
                                <option value="1">1 (Full Width)</option>
                                <option value="2" selected>2 (Side by Side)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Category Cards Options -->
                <div id="options-category_cards" class="style-options">
                    <h6 class="mb-3">Category Cards First Options</h6>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Cards Per Row</label>
                            <select class="form-select" id="category_cards_per_row">
                                <option value="3">3 Cards</option>
                                <option value="4" selected>4 Cards</option>
                                <option value="5">5 Cards</option>
                                <option value="6">6 Cards</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Card Style</label>
                            <select class="form-select" id="category_cards_style">
                                <option value="overlay" selected>Image with Overlay</option>
                                <option value="below">Text Below Image</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Section Title</label>
                            <input type="text" class="form-control" id="category_cards_title" value="Shop by Category">
                        </div>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="category_cards_show_count">
                        <label class="form-check-label" for="category_cards_show_count">Show product count on cards</label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Grid Settings -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-grid me-2"></i>Product Grid Settings</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label">Products per Row (Desktop)</label>
                        <select class="form-select" id="grid_desktop" onchange="updatePreview()">
                            <option value="2">2 Products</option>
                            <option value="3" selected>3 Products</option>
                            <option value="4">4 Products</option>
                            <option value="5">5 Products</option>
                            <option value="6">6 Products</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Products per Row (Tablet)</label>
                        <select class="form-select" id="grid_tablet">
                            <option value="1">1 Product</option>
                            <option value="2" selected>2 Products</option>
                            <option value="3">3 Products</option>
                            <option value="4">4 Products</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Products per Row (Mobile)</label>
                        <select class="form-select" id="grid_mobile">
                            <option value="1" selected>1 Product</option>
                            <option value="2">2 Products</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Products per Page</label>
                        <select class="form-select" id="products_per_page">
                            <option value="12" selected>12</option>
                            <option value="16">16</option>
                            <option value="20">20</option>
                            <option value="24">24</option>
                            <option value="30">30</option>
                        </select>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Grid Gap</label>
                        <select class="form-select" id="grid_gap">
                            <option value="10px">Compact (10px)</option>
                            <option value="15px">Normal (15px)</option>
                            <option value="20px" selected>Spacious (20px)</option>
                            <option value="30px">Wide (30px)</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Card Border Radius</label>
                        <select class="form-select" id="card_border_radius">
                            <option value="0">None (0px)</option>
                            <option value="4px">Small (4px)</option>
                            <option value="8px" selected>Medium (8px)</option>
                            <option value="12px">Large (12px)</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Card Shadow</label>
                        <select class="form-select" id="card_shadow">
                            <option value="none">None</option>
                            <option value="subtle" selected>Subtle</option>
                            <option value="medium">Medium</option>
                            <option value="strong">Strong</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Card Elements -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-card-text me-2"></i>Product Card Elements</h5>
            </div>
            <div class="card-body">
                <p class="text-muted mb-3">Choose which elements appear on each product card.</p>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="show_product_name" checked disabled>
                            <label class="form-check-label" for="show_product_name">Product Name (required)</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="show_price" checked disabled>
                            <label class="form-check-label" for="show_price">Price (required)</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="show_original_price" checked>
                            <label class="form-check-label" for="show_original_price">Original price (when on sale)</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="show_sale_badge" checked>
                            <label class="form-check-label" for="show_sale_badge">Sale badge</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="show_new_badge" checked>
                            <label class="form-check-label" for="show_new_badge">New arrival badge</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="show_rating" checked>
                            <label class="form-check-label" for="show_rating">Star rating</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="show_review_count">
                            <label class="form-check-label" for="show_review_count">Review count</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="show_quick_view">
                            <label class="form-check-label" for="show_quick_view">Quick view button</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="show_add_to_cart" checked>
                            <label class="form-check-label" for="show_add_to_cart">Add to cart button</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="show_wishlist" checked>
                            <label class="form-check-label" for="show_wishlist">Wishlist heart icon</label>
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
                        <label class="form-label">Product Title Size</label>
                        <select class="form-select" id="product_title_size" onchange="updatePreview()">
                            <option value="14px">Small (14px)</option>
                            <option value="16px" selected>Medium (16px)</option>
                            <option value="18px">Large (18px)</option>
                            <option value="20px">X-Large (20px)</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Title Font Weight</label>
                        <select class="form-select" id="product_title_weight" onchange="updatePreview()">
                            <option value="400">Normal (400)</option>
                            <option value="500">Medium (500)</option>
                            <option value="600" selected>Semi-Bold (600)</option>
                            <option value="700">Bold (700)</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Price Font Size</label>
                        <select class="form-select" id="product_price_size" onchange="updatePreview()">
                            <option value="16px">Small (16px)</option>
                            <option value="18px" selected>Medium (18px)</option>
                            <option value="20px">Large (20px)</option>
                            <option value="24px">X-Large (24px)</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Price Font Weight</label>
                        <select class="form-select" id="product_price_weight" onchange="updatePreview()">
                            <option value="500">Medium (500)</option>
                            <option value="600">Semi-Bold (600)</option>
                            <option value="700" selected>Bold (700)</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <label class="form-label">Description Size</label>
                        <select class="form-select" id="product_desc_size">
                            <option value="12px">Small (12px)</option>
                            <option value="14px" selected>Medium (14px)</option>
                            <option value="16px">Large (16px)</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Button Size</label>
                        <select class="form-select" id="product_button_size">
                            <option value="btn-sm">Small</option>
                            <option value="" selected>Medium</option>
                            <option value="btn-lg">Large</option>
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
                    <!-- Product Card Colors -->
                    <div class="col-md-6">
                        <h6 class="mb-3">Product Card</h6>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label">Card Background</label>
                                <div class="color-picker-group">
                                    <input type="color" class="form-control form-control-color" id="card_bg_color" value="#FFFFFF" onchange="syncColor('card_bg_color'); updatePreview()">
                                    <input type="text" class="form-control form-control-sm" id="card_bg_color_text" value="#FFFFFF" onchange="syncColorText('card_bg_color')">
                                </div>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Card Border</label>
                                <div class="color-picker-group">
                                    <input type="color" class="form-control form-control-color" id="card_border_color" value="#DEE2E6" onchange="syncColor('card_border_color'); updatePreview()">
                                    <input type="text" class="form-control form-control-sm" id="card_border_color_text" value="#DEE2E6" onchange="syncColorText('card_border_color')">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Text Colors -->
                    <div class="col-md-6">
                        <h6 class="mb-3">Text Colors</h6>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label">Title Color</label>
                                <div class="color-picker-group">
                                    <input type="color" class="form-control form-control-color" id="title_color" value="#8B4513" onchange="syncColor('title_color'); updatePreview()">
                                    <input type="text" class="form-control form-control-sm" id="title_color_text" value="#8B4513" onchange="syncColorText('title_color')">
                                </div>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Description Color</label>
                                <div class="color-picker-group">
                                    <input type="color" class="form-control form-control-color" id="desc_color" value="#6C757D" onchange="syncColor('desc_color'); updatePreview()">
                                    <input type="text" class="form-control form-control-sm" id="desc_color_text" value="#6C757D" onchange="syncColorText('desc_color')">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Price Colors -->
                    <div class="col-md-6">
                        <h6 class="mb-3">Price Colors</h6>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label">Price Color</label>
                                <div class="color-picker-group">
                                    <input type="color" class="form-control form-control-color" id="price_color" value="#2E7D32" onchange="syncColor('price_color'); updatePreview()">
                                    <input type="text" class="form-control form-control-sm" id="price_color_text" value="#2E7D32" onchange="syncColorText('price_color')">
                                </div>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Sale Price Color</label>
                                <div class="color-picker-group">
                                    <input type="color" class="form-control form-control-color" id="sale_price_color" value="#C62828" onchange="syncColor('sale_price_color'); updatePreview()">
                                    <input type="text" class="form-control form-control-sm" id="sale_price_color_text" value="#C62828" onchange="syncColorText('sale_price_color')">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Badge Colors -->
                    <div class="col-md-6">
                        <h6 class="mb-3">Badge Colors</h6>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label">Sale Badge</label>
                                <div class="color-picker-group">
                                    <input type="color" class="form-control form-control-color" id="sale_badge_color" value="#C62828" onchange="syncColor('sale_badge_color'); updatePreview()">
                                    <input type="text" class="form-control form-control-sm" id="sale_badge_color_text" value="#C62828" onchange="syncColorText('sale_badge_color')">
                                </div>
                            </div>
                            <div class="col-6">
                                <label class="form-label">New Badge</label>
                                <div class="color-picker-group">
                                    <input type="color" class="form-control form-control-color" id="new_badge_color" value="#1976D2" onchange="syncColor('new_badge_color'); updatePreview()">
                                    <input type="text" class="form-control form-control-sm" id="new_badge_color_text" value="#1976D2" onchange="syncColorText('new_badge_color')">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Button Colors -->
                    <div class="col-md-6">
                        <h6 class="mb-3">Primary Button (Add to Cart)</h6>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label">Background</label>
                                <div class="color-picker-group">
                                    <input type="color" class="form-control form-control-color" id="btn_primary_bg" value="#8B4513" onchange="syncColor('btn_primary_bg'); updatePreview()">
                                    <input type="text" class="form-control form-control-sm" id="btn_primary_bg_text" value="#8B4513" onchange="syncColorText('btn_primary_bg')">
                                </div>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Text</label>
                                <div class="color-picker-group">
                                    <input type="color" class="form-control form-control-color" id="btn_primary_text" value="#FFFFFF" onchange="syncColor('btn_primary_text'); updatePreview()">
                                    <input type="text" class="form-control form-control-sm" id="btn_primary_text_text" value="#FFFFFF" onchange="syncColorText('btn_primary_text')">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Hover State -->
                    <div class="col-md-6">
                        <h6 class="mb-3">Card Hover State</h6>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label">Hover Background</label>
                                <div class="color-picker-group">
                                    <input type="color" class="form-control form-control-color" id="card_hover_bg" value="#FDF8F4" onchange="syncColor('card_hover_bg'); updatePreview()">
                                    <input type="text" class="form-control form-control-sm" id="card_hover_bg_text" value="#FDF8F4" onchange="syncColorText('card_hover_bg')">
                                </div>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Hover Shadow</label>
                                <select class="form-select" id="card_hover_shadow">
                                    <option value="none">None</option>
                                    <option value="subtle">Subtle</option>
                                    <option value="medium" selected>Medium</option>
                                    <option value="strong">Strong</option>
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
            <div class="card-body p-2">
                <div class="preview-panel" id="livePreview">
                    <div class="preview-nav">
                        LOGO &nbsp; Home &nbsp; Products &nbsp; About &nbsp; Contact
                    </div>
                    <div class="preview-hero" id="previewHero" style="display: none;">
                        Featured Sale<br>
                        <small>Shop Now</small>
                    </div>
                    <div class="preview-category-bar" id="previewCategoryBar">
                        <span>Boots ▼</span>
                        <span>Clothing ▼</span>
                        <span>Hats</span>
                        <span>More ▼</span>
                    </div>
                    <div class="preview-content" id="previewContent">
                        <div class="preview-sidebar" id="previewSidebar" style="overflow-y: auto; max-height: 120px;">
                            <strong style="font-size:8px;">Filter by Category</strong><br>
                            ● All Products<br>
                            ○ Boots (24)<br>
                            ○ Clothing (18)<br>
                            ○ Hats (12)<br>
                            ○ Sandals (8)<br>
                            <hr style="margin:4px 0;">
                            <strong style="font-size:8px;">Search</strong><br>
                            <div style="background:#fff;border:1px solid #ccc;padding:2px;font-size:7px;">🔍 Search...</div>
                        </div>
                        <div class="preview-grid" id="previewGrid">
                            <div class="preview-card">Product</div>
                            <div class="preview-card">Product</div>
                            <div class="preview-card">Product</div>
                            <div class="preview-card">Product</div>
                            <div class="preview-card">Product</div>
                            <div class="preview-card">Product</div>
                        </div>
                    </div>
                </div>

                <div class="mt-3">
                    <div class="btn-group btn-group-sm w-100">
                        <button class="btn btn-outline-secondary active" onclick="setPreviewSize('desktop', this)">Desktop</button>
                        <button class="btn btn-outline-secondary" onclick="setPreviewSize('tablet', this)">Tablet</button>
                        <button class="btn btn-outline-secondary" onclick="setPreviewSize('mobile', this)">Mobile</button>
                    </div>
                </div>

                <div class="alert alert-info mt-3 mb-0">
                    <small><i class="bi bi-info-circle me-1"></i> Simplified preview. Actual appearance may vary.</small>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let currentStyle = 'sidebar';

function selectStyle(style) {
    currentStyle = style;

    // Update radio button
    document.querySelectorAll('input[name="layout_style"]').forEach(radio => {
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
    const sidebar = document.getElementById('previewSidebar');
    const categoryBar = document.getElementById('previewCategoryBar');
    const hero = document.getElementById('previewHero');
    const grid = document.getElementById('previewGrid');
    const content = document.getElementById('previewContent');

    // Reset visibility
    sidebar.style.display = 'none';
    categoryBar.style.display = 'none';
    hero.style.display = 'none';
    content.style.flexDirection = 'row';

    switch(currentStyle) {
        case 'sidebar':
            sidebar.style.display = 'block';
            sidebar.innerHTML = `
                <strong style="font-size:8px;">Filter by Category</strong><br>
                ● All Products<br>
                ○ Boots (24)<br>
                ○ Clothing (18)<br>
                ○ Hats (12)<br>
                ○ Sandals (8)<br>
                <hr style="margin:4px 0;">
                <strong style="font-size:8px;">Search</strong><br>
                <div style="background:#fff;border:1px solid #ccc;padding:2px;font-size:7px;">🔍 Search...</div>
            `;
            break;
        case 'horizontal':
            categoryBar.style.display = 'flex';
            break;
        case 'filters':
            categoryBar.style.display = 'flex';
            categoryBar.innerHTML = '<span>[Category ▼]</span><span>[Price ▼]</span><span>[Sort ▼]</span>';
            break;
        case 'mega_menu':
            categoryBar.style.display = 'flex';
            categoryBar.innerHTML = '<span>Boots ▼</span><span>Clothing ▼</span><span>Hats ▼</span>';
            break;
        case 'collapsible':
            categoryBar.style.display = 'flex';
            categoryBar.innerHTML = '<span>[≡ Filters]</span><span style="margin-left:auto;">24 Products</span>';
            break;
        case 'split_hero':
            hero.style.display = 'block';
            hero.innerHTML = '<div style="display:flex;"><div style="flex:1;">Featured Sale<br><small>Shop Now</small></div><div style="width:40%;background:#fff;color:#333;padding:10px;font-size:10px;">→ Boots<br>→ Clothing<br>→ Hats</div></div>';
            break;
        case 'infinite':
            categoryBar.style.display = 'flex';
            categoryBar.innerHTML = '<span style="background:#8B4513;color:#fff;padding:4px 8px;border-radius:12px;">All</span><span>Boots</span><span>Clothing</span><span>Hats</span>';
            break;
        case 'magazine':
            content.style.flexDirection = 'column';
            hero.style.display = 'block';
            hero.innerHTML = 'FEATURED COLLECTION';
            grid.innerHTML = '<div class="preview-card" style="grid-column:span 2;height:80px;">Large Product Image + Description</div><div class="preview-card" style="grid-column:span 2;height:80px;">Product + Description</div>';
            break;
        case 'category_cards':
            categoryBar.style.display = 'flex';
            categoryBar.innerHTML = '<div style="display:flex;gap:8px;width:100%;"><div style="flex:1;background:#e9ecef;text-align:center;padding:15px 5px;font-size:9px;border-radius:4px;">[IMG]<br>Boots</div><div style="flex:1;background:#e9ecef;text-align:center;padding:15px 5px;font-size:9px;border-radius:4px;">[IMG]<br>Clothing</div><div style="flex:1;background:#e9ecef;text-align:center;padding:15px 5px;font-size:9px;border-radius:4px;">[IMG]<br>Hats</div></div>';
            break;
    }

    // Reset grid if needed
    if (currentStyle !== 'magazine') {
        const cols = document.getElementById('grid_desktop').value;
        grid.style.gridTemplateColumns = `repeat(${Math.min(cols, 3)}, 1fr)`;
        grid.innerHTML = '';
        for (let i = 0; i < 6; i++) {
            grid.innerHTML += '<div class="preview-card">Product</div>';
        }
    }
}

function setPreviewSize(size, btn) {
    const preview = document.getElementById('livePreview');
    document.querySelectorAll('.btn-group .btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    switch(size) {
        case 'desktop':
            preview.style.maxWidth = '100%';
            break;
        case 'tablet':
            preview.style.maxWidth = '280px';
            break;
        case 'mobile':
            preview.style.maxWidth = '180px';
            break;
    }
}

async function saveAllSettings() {
    const settings = {
        product_layout_style: currentStyle,
        // Grid settings
        products_per_row_desktop: document.getElementById('grid_desktop').value,
        products_per_row_tablet: document.getElementById('grid_tablet').value,
        products_per_row_mobile: document.getElementById('grid_mobile').value,
        products_per_page: document.getElementById('products_per_page').value,
        grid_gap: document.getElementById('grid_gap').value,
        card_border_radius: document.getElementById('card_border_radius').value,
        card_shadow: document.getElementById('card_shadow').value,
        // Card elements
        show_original_price: document.getElementById('show_original_price').checked,
        show_sale_badge: document.getElementById('show_sale_badge').checked,
        show_new_badge: document.getElementById('show_new_badge').checked,
        show_rating: document.getElementById('show_rating').checked,
        show_review_count: document.getElementById('show_review_count').checked,
        show_quick_view: document.getElementById('show_quick_view').checked,
        show_add_to_cart: document.getElementById('show_add_to_cart').checked,
        show_wishlist: document.getElementById('show_wishlist').checked,
        // Typography
        product_title_size: document.getElementById('product_title_size').value,
        product_title_weight: document.getElementById('product_title_weight').value,
        product_price_size: document.getElementById('product_price_size').value,
        product_price_weight: document.getElementById('product_price_weight').value,
        product_desc_size: document.getElementById('product_desc_size').value,
        product_button_size: document.getElementById('product_button_size').value,
        // Colors
        card_bg_color: document.getElementById('card_bg_color').value,
        card_border_color: document.getElementById('card_border_color').value,
        title_color: document.getElementById('title_color').value,
        desc_color: document.getElementById('desc_color').value,
        price_color: document.getElementById('price_color').value,
        sale_price_color: document.getElementById('sale_price_color').value,
        sale_badge_color: document.getElementById('sale_badge_color').value,
        new_badge_color: document.getElementById('new_badge_color').value,
        btn_primary_bg: document.getElementById('btn_primary_bg').value,
        btn_primary_text: document.getElementById('btn_primary_text').value,
        card_hover_bg: document.getElementById('card_hover_bg').value,
        card_hover_shadow: document.getElementById('card_hover_shadow').value,
    };

    try {
        const response = await fetch('{{ rtrim(env("API_PUBLIC_URL", "http://localhost:8300/api/v1"), "/") }}/admin/settings/product_layout', {
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
        const response = await fetch('{{ rtrim(env("API_PUBLIC_URL", "http://localhost:8300/api/v1"), "/") }}/admin/settings/product_layout');
        if (response.ok) {
            const data = await response.json();
            if (data.success && data.data) {
                const settings = data.data;

                // Apply layout style and update CURRENT badge
                if (settings.product_layout_style) {
                    selectStyle(settings.product_layout_style);
                    updateCurrentBadge(settings.product_layout_style);
                }

                // Apply grid settings
                if (settings.products_per_row_desktop) document.getElementById('grid_desktop').value = settings.products_per_row_desktop;
                if (settings.products_per_row_tablet) document.getElementById('grid_tablet').value = settings.products_per_row_tablet;
                if (settings.products_per_row_mobile) document.getElementById('grid_mobile').value = settings.products_per_row_mobile;
                if (settings.products_per_page) document.getElementById('products_per_page').value = settings.products_per_page;
                if (settings.grid_gap) document.getElementById('grid_gap').value = settings.grid_gap;
                if (settings.card_border_radius) document.getElementById('card_border_radius').value = settings.card_border_radius;
                if (settings.card_shadow) document.getElementById('card_shadow').value = settings.card_shadow;

                // Apply card element toggles
                if (settings.show_original_price !== undefined) document.getElementById('show_original_price').checked = settings.show_original_price;
                if (settings.show_sale_badge !== undefined) document.getElementById('show_sale_badge').checked = settings.show_sale_badge;
                if (settings.show_new_badge !== undefined) document.getElementById('show_new_badge').checked = settings.show_new_badge;
                if (settings.show_rating !== undefined) document.getElementById('show_rating').checked = settings.show_rating;
                if (settings.show_review_count !== undefined) document.getElementById('show_review_count').checked = settings.show_review_count;
                if (settings.show_quick_view !== undefined) document.getElementById('show_quick_view').checked = settings.show_quick_view;
                if (settings.show_add_to_cart !== undefined) document.getElementById('show_add_to_cart').checked = settings.show_add_to_cart;
                if (settings.show_wishlist !== undefined) document.getElementById('show_wishlist').checked = settings.show_wishlist;

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

// Color picker sync functions
function syncColor(id) {
    const colorInput = document.getElementById(id);
    const textInput = document.getElementById(id + '_text');
    if (textInput) {
        textInput.value = colorInput.value.toUpperCase();
    }
}

function syncColorText(id) {
    const colorInput = document.getElementById(id);
    const textInput = document.getElementById(id + '_text');
    if (colorInput && textInput) {
        // Validate hex format
        const hex = textInput.value;
        if (/^#[0-9A-Fa-f]{6}$/.test(hex)) {
            colorInput.value = hex;
            updatePreview();
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    loadSettings();
    updatePreview();
});
</script>
@endpush
