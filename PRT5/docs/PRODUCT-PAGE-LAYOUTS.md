# Product Page Layout Options - Planning Document

## Overview

This document outlines the implementation plan for toggleable product page layouts, allowing different visual presentations of the product catalog. The goal is to provide flexibility so future e-commerce sites don't all look the same.

---

## Current State

**Layout: Sidebar + Grid**
- Left sidebar with scrollable category menu
- Product grid on the right
- Categories displayed as a vertical list with expand/collapse
- Works well but is the "only" option

---

## Proposed Layout Options

### Option 1: Sidebar Layout (Current)
```
┌─────────────────────────────────────────────────┐
│                    NAV BAR                       │
├─────────────────────────────────────────────────┤
│                  HERO (if enabled)               │
├─────────┬───────────────────────────────────────┤
│         │                                        │
│ SIDEBAR │         PRODUCT GRID                   │
│  MENU   │                                        │
│         │                                        │
│ - Cat 1 │   [Card] [Card] [Card] [Card]         │
│ - Cat 2 │   [Card] [Card] [Card] [Card]         │
│ - Cat 3 │   [Card] [Card] [Card] [Card]         │
│         │                                        │
└─────────┴───────────────────────────────────────┘
```

### Option 2: Horizontal Category Bar (Guitar Center Style)
```
┌─────────────────────────────────────────────────┐
│                    NAV BAR                       │
├─────────────────────────────────────────────────┤
│                  HERO (if enabled)               │
├─────────────────────────────────────────────────┤
│  Cat1 ▼ │ Cat2 ▼ │ Cat3 ▼ │ Cat4 ▼ │ More ▼   │  <- Category Bar
├─────────────────────────────────────────────────┤
│                                                  │
│            FULL-WIDTH PRODUCT GRID               │
│                                                  │
│   [Card] [Card] [Card] [Card] [Card] [Card]     │
│   [Card] [Card] [Card] [Card] [Card] [Card]     │
│   [Card] [Card] [Card] [Card] [Card] [Card]     │
│                                                  │
└─────────────────────────────────────────────────┘
```

**Category Bar Dropdown Example:**
```
┌─────────────────────────────────────────────────┐
│  Boots ▼ │ Clothing │ Accessories │ Sale        │
├──────────┴──────────────────────────────────────┤
│ ┌─────────────────────┐                         │
│ │ Work Boots          │ <- Dropdown menu        │
│ │ Western Boots       │                         │
│ │ Casual Boots        │                         │
│ │ Women's Boots       │                         │
│ │ ─────────────────── │                         │
│ │ Shop All Boots →    │                         │
│ └─────────────────────┘                         │
└─────────────────────────────────────────────────┘
```

### Option 3: Top Filters + Grid (Amazon/Modern Style)
```
┌─────────────────────────────────────────────────┐
│                    NAV BAR                       │
├─────────────────────────────────────────────────┤
│  [Category ▼] [Price ▼] [Brand ▼] [Sort ▼]     │  <- Filter Bar
├─────────────────────────────────────────────────┤
│                                                  │
│            FULL-WIDTH PRODUCT GRID               │
│                                                  │
└─────────────────────────────────────────────────┘
```

### Option 4: Mega Menu (Best Buy/Home Depot Style)
```
┌─────────────────────────────────────────────────┐
│                    NAV BAR                       │
├─────────────────────────────────────────────────┤
│  Boots │ Clothing │ Accessories │ Brands │ Sale │
├─────────────────────────────────────────────────┤
│ ┌─────────────────────────────────────────────┐ │
│ │  WORK BOOTS    │  WESTERN     │  [IMAGE]    │ │
│ │  ───────────   │  ──────────  │             │ │
│ │  Steel Toe     │  Cowboy      │  Featured:  │ │
│ │  Composite     │  Roper       │  New        │ │
│ │  Waterproof    │  Exotic      │  Arrivals   │ │
│ │                │              │             │ │
│ │  CASUAL        │  WOMEN'S     │  [Shop Now] │ │
│ │  ───────────   │  ──────────  │             │ │
│ │  Chelsea       │  Fashion     │             │ │
│ │  Chukka        │  Western     │             │ │
│ └─────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────┘
```

**Best for:** Sites with many subcategories, want to showcase featured items

### Option 5: Category Cards Grid (Etsy/Pinterest Style)
```
┌─────────────────────────────────────────────────┐
│                    NAV BAR                       │
├─────────────────────────────────────────────────┤
│                                                  │
│  ┌─────────┐ ┌─────────┐ ┌─────────┐ ┌───────┐ │
│  │  IMAGE  │ │  IMAGE  │ │  IMAGE  │ │ IMAGE │ │
│  │  Boots  │ │Clothing │ │  Hats   │ │ Belts │ │
│  └─────────┘ └─────────┘ └─────────┘ └───────┘ │
│                                                  │
│  ┌─────────┐ ┌─────────┐ ┌─────────┐ ┌───────┐ │
│  │  IMAGE  │ │  IMAGE  │ │  IMAGE  │ │ IMAGE │ │
│  │  Bags   │ │ Jewelry │ │  Sale   │ │ New   │ │
│  └─────────┘ └─────────┘ └─────────┘ └───────┘ │
│                                                  │
└─────────────────────────────────────────────────┘
```

**Best for:** Visual-first browsing, lifestyle/fashion sites

### Option 6: Collapsible Sidebar (Mobile-First)
```
Desktop:                          Mobile:
┌────────────────────────────┐    ┌─────────────────────┐
│         NAV BAR            │    │      NAV BAR        │
├────────────────────────────┤    ├─────────────────────┤
│ [≡ Filters] Full-width grid│    │ [≡ Filters]         │
├────────────────────────────┤    ├─────────────────────┤
│                            │    │  Product Grid       │
│  [Card] [Card] [Card]      │    │  [Card] [Card]      │
│  [Card] [Card] [Card]      │    │  [Card] [Card]      │
│                            │    │                     │
└────────────────────────────┘    └─────────────────────┘

When [≡ Filters] clicked:
┌────────────────────────────┐
│ ┌──────────┐               │
│ │ FILTERS  │ Product Grid  │
│ │ ──────── │               │
│ │ Category │ [Card] [Card] │
│ │ Price    │ [Card] [Card] │
│ │ Brand    │               │
│ │ [Apply]  │               │
│ └──────────┘               │
└────────────────────────────┘
```

**Best for:** Mobile-optimized, clean desktop view

### Option 7: Split Hero + Categories (Shopify Style)
```
┌─────────────────────────────────────────────────┐
│                    NAV BAR                       │
├─────────────────────────────────────────────────┤
│ ┌───────────────────────┐ ┌───────────────────┐ │
│ │                       │ │   CATEGORIES      │ │
│ │    HERO/BANNER        │ │   ────────────    │ │
│ │    Featured Sale      │ │   → Boots         │ │
│ │    [Shop Now]         │ │   → Clothing      │ │
│ │                       │ │   → Accessories   │ │
│ │                       │ │   → Sale Items    │ │
│ └───────────────────────┘ └───────────────────┘ │
├─────────────────────────────────────────────────┤
│              PRODUCT GRID                        │
│   [Card] [Card] [Card] [Card] [Card]            │
└─────────────────────────────────────────────────┘
```

**Best for:** Highlighting promotions while maintaining category access

### Option 8: Infinite Scroll + Floating Filters (Social Media Style)
```
┌─────────────────────────────────────────────────┐
│                    NAV BAR                       │
├─────────────────────────────────────────────────┤
│  [All] [Boots] [Clothing] [Hats] [On Sale]     │  <- Pill filters
├─────────────────────────────────────────────────┤
│                                                  │
│   [Card]  [Card]  [Card]                        │
│   [Card]  [Card]  [Card]                        │
│   [Card]  [Card]  [Card]                        │
│          ↓ Loading more...                      │
│                                                  │
│                          ┌─────────┐            │
│                          │ Filters │  <- Floating
│                          │   ≡     │     button
│                          └─────────┘            │
└─────────────────────────────────────────────────┘
```

**Best for:** Modern, app-like experience, mobile-native feel

### Option 9: Magazine/Editorial Layout (Luxury Brands)
```
┌─────────────────────────────────────────────────┐
│                    NAV BAR                       │
├─────────────────────────────────────────────────┤
│ ┌─────────────────────────────────────────────┐ │
│ │                                             │ │
│ │            LARGE FEATURED IMAGE             │ │
│ │               New Collection                │ │
│ │                                             │ │
│ └─────────────────────────────────────────────┘ │
├─────────────────────────────────────────────────┤
│                                                  │
│  ┌──────────────────┐  ┌──────────────────────┐ │
│  │                  │  │     Description      │ │
│  │   PRODUCT IMG    │  │     Price: $299      │ │
│  │                  │  │     [Add to Cart]    │ │
│  └──────────────────┘  └──────────────────────┘ │
│                                                  │
│  ┌──────────────────────┐  ┌──────────────────┐ │
│  │    Description       │  │                  │ │
│  │    Price: $199       │  │   PRODUCT IMG    │ │
│  │    [Add to Cart]     │  │                  │ │
│  └──────────────────────┘  └──────────────────┘ │
│                                                  │
└─────────────────────────────────────────────────┘
```

**Best for:** High-end products, storytelling, brand experience

---

## Layout Comparison Matrix

| Layout | Best For | Complexity | Mobile | SEO |
|--------|----------|------------|--------|-----|
| 1. Sidebar | Traditional stores | Low | Fair | Good |
| 2. Horizontal Bar | Large catalogs | Medium | Good | Good |
| 3. Filter Bar | Many filters needed | Medium | Good | Good |
| 4. Mega Menu | Deep categories | High | Fair | Good |
| 5. Category Cards | Visual products | Low | Excellent | Good |
| 6. Collapsible | Mobile-first | Medium | Excellent | Good |
| 7. Split Hero | Promotions focus | Medium | Good | Fair |
| 8. Infinite Scroll | Social/modern | Medium | Excellent | Fair |
| 9. Magazine | Luxury/editorial | High | Fair | Fair |

---

## Recommended Implementation Order

**Phase 1 (MVP):**
1. Sidebar Layout (already done) ✓
2. Horizontal Category Bar (Guitar Center style)

**Phase 2 (Enhanced):**
3. Collapsible Sidebar (mobile optimization)
4. Category Cards Grid

**Phase 3 (Advanced):**
5. Mega Menu
6. Filter Bar + Infinite Scroll

**Future/Optional:**
7. Split Hero
8. Magazine Layout
9. Infinite Scroll Social Style

---

## Implementation Approach

### Admin Control Location

**Recommended: Settings > Branding > Product Display**

Add a new section in the Branding settings:

```
┌─────────────────────────────────────────────────┐
│ Product Page Layout                              │
├─────────────────────────────────────────────────┤
│                                                  │
│ Layout Style:                                    │
│ ○ Sidebar + Grid (Traditional)                  │
│ ● Horizontal Category Bar (Modern)              │
│ ○ Filter Bar Only (Minimal)                     │
│                                                  │
│ [Preview images for each option]                │
│                                                  │
├─────────────────────────────────────────────────┤
│ Category Bar Options (when Horizontal selected) │
│ ─────────────────────────────────────────────── │
│ □ Show subcategories in dropdown                │
│ □ Sticky category bar on scroll                 │
│ Max categories to show: [6] (rest in "More")    │
│ Category bar background: [#_____]               │
│                                                  │
└─────────────────────────────────────────────────┘
```

### Database Settings

New settings in `settings` table (group: `branding`):

| Setting Key | Type | Default | Description |
|------------|------|---------|-------------|
| `product_layout_style` | string | `sidebar` | Options: `sidebar`, `horizontal`, `filters` |
| `category_bar_sticky` | boolean | false | Make category bar sticky on scroll |
| `category_bar_max_items` | number | 6 | Max items before "More" dropdown |
| `category_bar_bg_color` | string | `#f8f9fa` | Background color |
| `category_bar_show_subcategories` | boolean | true | Show subcategories in dropdown |

---

## Category Bar Placement Logic

**Important Clarification:**
- Hero/Banner section with carousel is **ONLY on index.php (homepage)**
- Homepage Banners (managed at `/admin/banners`) display only on the homepage
- Other pages do NOT have hero sections (they have smaller page-header title bars)

### Placement Rules:

```
PAGE: Homepage (index.php)
├── Nav Bar
├── Hero/Banner Carousel (from /admin/banners)
├── Category Bar (if horizontal layout enabled)  <-- BELOW hero
└── Content (Featured Products, etc.)

PAGE: Products (products.php)
├── Nav Bar
├── Category Bar (if horizontal layout enabled)  <-- DIRECTLY below nav
└── Product Grid

PAGE: Other Pages (about, contact, blog, etc.)
├── Nav Bar
├── Page Header (title bar)
└── Content
    (No category bar needed - these aren't product listing pages)
```

### Category Bar Visibility:

| Page | Show Category Bar? | Placement |
|------|-------------------|-----------|
| Homepage (index.php) | Optional | Below hero banners |
| Products (products.php) | Yes (when horizontal layout) | Below nav |
| Product Detail | No | N/A |
| About, Contact, etc. | No | N/A |
| Blog | No | N/A |
| Cart/Checkout | No | N/A |

### Sticky Behavior:

```
IF category_bar_sticky THEN
    Category Bar sticks below nav when scrolling
    (similar to Guitar Center behavior)
END IF
```

### Z-Index Hierarchy
```
Nav Bar (sticky):        z-index: 1030
Category Bar (sticky):   z-index: 1025
Hero Section:            z-index: 1
Product Content:         z-index: auto
```

---

## Category Bar Technical Details

### Data Structure

Use existing category hierarchy from database:
```php
// Parent categories become menu items
// Child categories become dropdown items

$categories = [
    [
        'id' => 1,
        'name' => 'Boots',
        'slug' => 'boots',
        'children' => [
            ['id' => 10, 'name' => 'Work Boots', 'slug' => 'work-boots'],
            ['id' => 11, 'name' => 'Western Boots', 'slug' => 'western-boots'],
        ]
    ],
    // ...
];
```

### HTML Structure (Horizontal Layout)
```html
<nav class="category-bar bg-light border-bottom" id="categoryBar">
    <div class="container">
        <ul class="category-nav">
            <li class="category-item dropdown">
                <a href="/products?category=boots" class="category-link dropdown-toggle">
                    Boots <i class="bi bi-chevron-down"></i>
                </a>
                <div class="category-dropdown">
                    <a href="/products?category=work-boots">Work Boots</a>
                    <a href="/products?category=western-boots">Western Boots</a>
                    <div class="dropdown-divider"></div>
                    <a href="/products?category=boots" class="view-all">
                        Shop All Boots <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </li>
            <!-- More items... -->
            <li class="category-item dropdown">
                <a href="#" class="category-link dropdown-toggle">
                    More <i class="bi bi-chevron-down"></i>
                </a>
                <div class="category-dropdown">
                    <!-- Overflow categories -->
                </div>
            </li>
        </ul>
    </div>
</nav>
```

### Mobile Considerations

**Desktop (>768px):**
- Horizontal bar with hover dropdowns
- Full category names visible

**Mobile (<768px):**
- Options:
  1. Collapse to hamburger-style category menu
  2. Horizontal scroll (like app store categories)
  3. Full-width accordion

**Recommended for Mobile:** Horizontal scroll with touch support
```
┌─────────────────────────────────────────────────┐
│ ← [Boots] [Clothing] [Accessories] [More...] → │
└─────────────────────────────────────────────────┘
```

---

## Files to Create/Modify

### New Files
1. `includes/category-bar.php` - Renders the horizontal category bar
2. `assets/css/category-bar.css` - Styles for category bar
3. `assets/js/category-bar.js` - Dropdown/mobile interactions

### Modified Files
1. `products/products.php` - Add layout conditional logic
2. `includes/header-helpers.php` - Add layout setting retrieval
3. `admin-site: settings.blade.php` - Add layout controls
4. `admin-api: SettingsController.php` - Handle new settings

---

## Implementation Phases

### Phase 1: Backend Settings
- [ ] Add new settings to admin Branding section
- [ ] Add radio buttons for layout selection
- [ ] Add category bar options (when horizontal selected)
- [ ] Save/retrieve from API

### Phase 2: Category Bar Component
- [ ] Create `category-bar.php` include
- [ ] Build category hierarchy query
- [ ] Create dropdown HTML structure
- [ ] Style with CSS (match site theme)
- [ ] Add hover/click interactions

### Phase 3: Products Page Integration
- [ ] Add layout conditional in products.php
- [ ] Show sidebar OR category bar based on setting
- [ ] Adjust grid width (full-width when no sidebar)
- [ ] Handle category bar placement (after nav or hero)

### Phase 4: Mobile Optimization
- [ ] Add responsive breakpoints
- [ ] Implement horizontal scroll for mobile
- [ ] Touch-friendly dropdowns
- [ ] Test on various devices

### Phase 5: Polish & Extras
- [ ] Sticky category bar option
- [ ] Smooth animations
- [ ] Keyboard navigation
- [ ] Active state highlighting

---

## Open Questions

1. **Category Display Order**: Should we use existing sort order from categories table, or allow manual ordering in admin?

2. **"More" Overflow**: How many categories before items go to "More" dropdown? (Suggested: configurable, default 6)

3. **Mega Menu Option**: Should we support mega menus (multi-column dropdowns with images) for sites with many subcategories?

4. **URL Behavior**: When clicking a top-level category:
   - Option A: Go to that category's products
   - Option B: Just open the dropdown (click subcategory to navigate)
   - Option C: Configurable

5. **Integration with Filters**: When using horizontal layout, where do filters (price, brand, etc.) appear?
   - Option A: Below category bar
   - Option B: In a collapsible filter panel
   - Option C: Modal/sidebar overlay

---

## Visual Reference

**Guitar Center Style:**
- Horizontal category bar below main nav
- Categories with small down arrows
- Hover reveals dropdown with subcategories
- "Deals" highlighted in red
- Full-width product grid below

**Key Elements to Replicate:**
- Clean horizontal layout
- Subtle hover effects
- Organized dropdown menus
- "Shop All [Category]" links
- Sticky on scroll behavior

---

## Estimated Effort

| Phase | Effort | Priority |
|-------|--------|----------|
| Phase 1: Backend Settings | 2-3 hours | High |
| Phase 2: Category Bar Component | 4-5 hours | High |
| Phase 3: Products Page Integration | 2-3 hours | High |
| Phase 4: Mobile Optimization | 3-4 hours | Medium |
| Phase 5: Polish & Extras | 2-3 hours | Low |

**Total Estimated: 13-18 hours**

---

## Next Steps

1. Review this document and provide feedback
2. Decide on open questions above
3. Confirm which layout options to implement (start with 2?)
4. Begin Phase 1 implementation

---

*Document created: November 29, 2025*
*Status: Planning/Review*
