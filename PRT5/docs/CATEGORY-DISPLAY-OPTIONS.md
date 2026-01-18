# Category Display Options - Planning Document

## Overview

This document outlines the implementation plan for configurable category display styles across the Pecos River Trading site. Currently, categories display as **cards** on the homepage. This enhancement allows switching between multiple display styles with full customization of colors, fonts, and behavior.

**Related:** See [PRODUCT-PAGE-LAYOUTS.md](PRODUCT-PAGE-LAYOUTS.md) for product page layout options.

---

## Current State

**Display: Category Cards Grid**
- Categories shown as image cards on homepage
- Click card to navigate to category products
- Works for visual browsing
- Limited flexibility for sites with many categories

---

## Proposed Display Options

### Option 1: Category Cards Grid (Current)
```
┌─────────────────────────────────────────────────┐
│                    NAV BAR                       │
├─────────────────────────────────────────────────┤
│                  HERO BANNER                     │
├─────────────────────────────────────────────────┤
│  SHOP BY CATEGORY                                │
│  ┌─────────┐ ┌─────────┐ ┌─────────┐ ┌───────┐ │
│  │  IMAGE  │ │  IMAGE  │ │  IMAGE  │ │ IMAGE │ │
│  │  Boots  │ │Clothing │ │  Hats   │ │ Belts │ │
│  └─────────┘ └─────────┘ └─────────┘ └───────┘ │
│  ┌─────────┐ ┌─────────┐ ┌─────────┐ ┌───────┐ │
│  │  IMAGE  │ │  IMAGE  │ │  IMAGE  │ │ IMAGE │ │
│  │  Bags   │ │ Jewelry │ │  Sale   │ │ New   │ │
│  └─────────┘ └─────────┘ └─────────┘ └───────┘ │
└─────────────────────────────────────────────────┘
```

**Best for:** Visual products, lifestyle/fashion sites, smaller catalogs
**Pros:** Eye-catching, great for mobile, encourages browsing
**Cons:** Takes up vertical space, not ideal for 20+ categories

---

### Option 2: Mega Menu Dropdown (Lumber Liquidators Style)
```
┌─────────────────────────────────────────────────┐
│  LOGO    [Boots ▼] [Clothing ▼] [Accessories ▼] │ <- Main Nav
├─────────────────────────────────────────────────┤
│ ┌─────────────────────────────────────────────┐ │
│ │ WORK BOOTS      │ WESTERN       │ [IMAGE]   │ │
│ │ ────────────    │ ──────────    │           │ │
│ │ • Steel Toe     │ • Cowboy      │ Featured: │ │
│ │ • Composite     │ • Roper       │ New       │ │
│ │ • Waterproof    │ • Exotic      │ Arrivals  │ │
│ │ • Insulated     │ • Square Toe  │           │ │
│ │                 │               │ [Shop →]  │ │
│ │ CASUAL          │ WOMEN'S       │           │ │
│ │ ────────────    │ ──────────    │           │ │
│ │ • Chelsea       │ • Fashion     │           │ │
│ │ • Chukka        │ • Western     │           │ │
│ │                 │               │           │ │
│ │ [Shop All Boots →]              │           │ │
│ └─────────────────────────────────────────────┘ │
├─────────────────────────────────────────────────┤
│                  HERO BANNER                     │
└─────────────────────────────────────────────────┘
```

**Best for:** Large catalogs, deep category hierarchies, B2B sites
**Pros:** Shows all subcategories at once, professional look, saves page space
**Cons:** Requires hover (tricky on mobile), complex to implement

---

### Option 3: Horizontal Category Bar with Dropdowns
```
┌─────────────────────────────────────────────────┐
│                    NAV BAR                       │
├─────────────────────────────────────────────────┤
│  Boots ▼ │ Clothing ▼ │ Hats │ Accessories ▼    │ <- Category Bar
├─────────────────────────────────────────────────┤
│  ┌─────────────────┐                             │
│  │ Work Boots      │  <- Simple dropdown         │
│  │ Western Boots   │                             │
│  │ Casual Boots    │                             │
│  │ ─────────────── │                             │
│  │ Shop All Boots  │                             │
│  └─────────────────┘                             │
├─────────────────────────────────────────────────┤
│                  HERO BANNER                     │
└─────────────────────────────────────────────────┘
```

**Best for:** Medium catalogs, traditional e-commerce
**Pros:** Familiar pattern, works with hover or click, saves space
**Cons:** Limited subcategory visibility

---

### Option 4: Sidebar Accordion Menu
```
┌─────────────────────────────────────────────────┐
│                    NAV BAR                       │
├─────────────────────────────────────────────────┤
│ ┌───────────┐                                    │
│ │ CATEGORIES│                                    │
│ │ ──────────│                                    │
│ │ ▼ Boots   │  MAIN CONTENT AREA                │
│ │   • Work  │                                    │
│ │   • West. │  Products / Hero / etc.           │
│ │   • Casual│                                    │
│ │ ▶ Clothing│                                    │
│ │ ▶ Hats    │                                    │
│ │ ▶ Access. │                                    │
│ │ ▶ Sale    │                                    │
│ └───────────┘                                    │
└─────────────────────────────────────────────────┘
```

**Best for:** Functional sites, users who know what they want
**Pros:** Always visible, easy navigation, works great on mobile
**Cons:** Takes horizontal space, less visual appeal

---

### Option 5: Pill/Tag Style Navigation
```
┌─────────────────────────────────────────────────┐
│                    NAV BAR                       │
├─────────────────────────────────────────────────┤
│                                                  │
│  ┌───────┐ ┌─────────┐ ┌──────┐ ┌───────────┐  │
│  │ All   │ │  Boots  │ │ Hats │ │ Clothing  │  │
│  └───────┘ └─────────┘ └──────┘ └───────────┘  │
│  ┌───────────┐ ┌──────┐ ┌──────┐ ┌─────────┐   │
│  │Accessories│ │ Sale │ │ New  │ │  More ▼ │   │
│  └───────────┘ └──────┘ └──────┘ └─────────┘   │
│                                                  │
├─────────────────────────────────────────────────┤
│                  HERO BANNER                     │
└─────────────────────────────────────────────────┘
```

**Best for:** Modern/app-like feel, quick filtering
**Pros:** Touch-friendly, modern look, horizontal scroll on mobile
**Cons:** Limited to top-level categories, no subcategory visibility

---

### Option 6: Tab-Style Category Navigation
```
┌─────────────────────────────────────────────────┐
│                    NAV BAR                       │
├─────────────────────────────────────────────────┤
│ ┌────────┬──────────┬───────┬────────┬────────┐ │
│ │ Boots  │ Clothing │ Hats  │ Access │  Sale  │ │
│ │ (act.) │          │       │        │        │ │
│ └────────┴──────────┴───────┴────────┴────────┘ │
├─────────────────────────────────────────────────┤
│  Subcategories for "Boots":                      │
│  [Work] [Western] [Casual] [Women's] [Kids]     │
├─────────────────────────────────────────────────┤
│                                                  │
│         PRODUCTS FOR SELECTED CATEGORY           │
│                                                  │
└─────────────────────────────────────────────────┘
```

**Best for:** Sites focused on categories, comparison shopping
**Pros:** Clear hierarchy, easy to understand
**Cons:** Requires click to see subcategories

---

### Option 7: Icon Grid with Labels
```
┌─────────────────────────────────────────────────┐
│                    NAV BAR                       │
├─────────────────────────────────────────────────┤
│  SHOP BY CATEGORY                                │
│                                                  │
│    🥾         👔          🎩         👜         │
│   Boots    Clothing     Hats       Bags         │
│                                                  │
│    💍         🏷️          ⭐         📦         │
│  Jewelry     Sale       New       Brands        │
│                                                  │
├─────────────────────────────────────────────────┤
│                  HERO BANNER                     │
└─────────────────────────────────────────────────┘
```

**Best for:** Clean minimal design, icon-focused brands
**Pros:** Very clean, quick recognition, works great on mobile
**Cons:** Requires good icons, limited text space

---

### Option 8: Vertical Sidebar with Flyout (Amazon Style)
```
┌─────────────────────────────────────────────────┐
│                    NAV BAR                       │
├──────────┬──────────────────────────────────────┤
│ BROWSE   │ ┌────────────────────────────────┐   │
│ ──────── │ │ WORK BOOTS                     │   │
│ Boots ▶  │ │ ──────────                     │   │
│ Clothing │ │ Steel Toe    Composite Toe     │   │
│ Hats     │ │ Waterproof   Insulated         │   │
│ Access.  │ │ EH Rated     Slip Resistant    │   │
│ Bags     │ │                                │   │
│ Jewelry  │ │ WESTERN BOOTS                  │   │
│ ──────── │ │ ──────────                     │   │
│ Sale     │ │ Cowboy       Roper             │   │
│ New      │ │ Exotic       Square Toe        │   │
│          │ └────────────────────────────────┘   │
└──────────┴──────────────────────────────────────┘
```

**Best for:** Very large catalogs, detailed browsing
**Pros:** Shows all levels, powerful navigation
**Cons:** Complex, requires significant screen space

---

## Display Comparison Matrix

| Display Style | Best For | Mobile | Complexity | Visual Appeal | Space Usage |
|--------------|----------|--------|------------|---------------|-------------|
| 1. Cards Grid | Visual products | Excellent | Low | High | High |
| 2. Mega Menu | Large catalogs | Fair | High | Medium | Low |
| 3. Horizontal Bar | Medium catalogs | Good | Medium | Medium | Low |
| 4. Sidebar Accordion | Functional sites | Excellent | Low | Low | Medium |
| 5. Pills/Tags | Modern sites | Excellent | Low | Medium | Low |
| 6. Tabs | Category focus | Good | Medium | Medium | Medium |
| 7. Icon Grid | Minimal design | Excellent | Low | High | Medium |
| 8. Flyout Sidebar | Large catalogs | Poor | High | Medium | High |

---

## Customization Options

### Global Category Display Settings

These settings would appear in **Admin > Settings > Category Display** or as a new tab:

```
┌─────────────────────────────────────────────────┐
│ Category Display Settings                        │
├─────────────────────────────────────────────────┤
│                                                  │
│ Display Style:                                   │
│ ┌─────────────────────────────────────────────┐ │
│ │ ○ Cards Grid (Current)                      │ │
│ │ ● Mega Menu Dropdown                        │ │
│ │ ○ Horizontal Bar with Dropdowns             │ │
│ │ ○ Sidebar Accordion                         │ │
│ │ ○ Pills/Tags                                │ │
│ │ ○ Icon Grid                                 │ │
│ └─────────────────────────────────────────────┘ │
│                                                  │
│ [Preview thumbnails for each option]            │
│                                                  │
└─────────────────────────────────────────────────┘
```

### Typography Settings

```
┌─────────────────────────────────────────────────┐
│ Category Typography                              │
├─────────────────────────────────────────────────┤
│                                                  │
│ Category Name Font Size:                         │
│ ┌─────────────────────────────────────────────┐ │
│ │ [Small ▼] [Medium] [Large] [XL] [Custom: __]│ │
│ └─────────────────────────────────────────────┘ │
│                                                  │
│ Font Weight:                                     │
│ [Normal ▼] [Medium] [Semi-Bold] [Bold]          │
│                                                  │
│ Text Transform:                                  │
│ [None ▼] [Uppercase] [Capitalize]               │
│                                                  │
│ Letter Spacing:                                  │
│ [Normal ▼] [Wide] [Wider]                       │
│                                                  │
└─────────────────────────────────────────────────┘
```

### Color Settings

```
┌─────────────────────────────────────────────────┐
│ Category Colors                                  │
├─────────────────────────────────────────────────┤
│                                                  │
│ Background Color:        Text Color:            │
│ ┌────────┬──────────┐   ┌────────┬──────────┐  │
│ │ [████] │ #F8F9FA  │   │ [████] │ #333333  │  │
│ └────────┴──────────┘   └────────┴──────────┘  │
│                                                  │
│ Hover Background:        Hover Text:            │
│ ┌────────┬──────────┐   ┌────────┬──────────┐  │
│ │ [████] │ #8B4513  │   │ [████] │ #FFFFFF  │  │
│ └────────┴──────────┘   └────────┴──────────┘  │
│                                                  │
│ Active/Selected Background:  Active Text:       │
│ ┌────────┬──────────┐   ┌────────┬──────────┐  │
│ │ [████] │ #8B4513  │   │ [████] │ #FFFFFF  │  │
│ └────────┴──────────┘   └────────┴──────────┘  │
│                                                  │
│ Dropdown Background:     Dropdown Text:         │
│ ┌────────┬──────────┐   ┌────────┬──────────┐  │
│ │ [████] │ #FFFFFF  │   │ [████] │ #333333  │  │
│ └────────┴──────────┘   └────────┴──────────┘  │
│                                                  │
│ Border Color:            Border Radius:         │
│ ┌────────┬──────────┐   ┌───────────────────┐  │
│ │ [████] │ #DEE2E6  │   │ [0px ▼] 4px 8px   │  │
│ └────────┴──────────┘   └───────────────────┘  │
│                                                  │
└─────────────────────────────────────────────────┘
```

### Mega Menu Specific Options

```
┌─────────────────────────────────────────────────┐
│ Mega Menu Options                                │
├─────────────────────────────────────────────────┤
│                                                  │
│ Number of Columns: [3 ▼] (1-4)                  │
│                                                  │
│ Show Category Images:  [✓]                      │
│ Show Featured Products: [✓]                     │
│ Show "Shop All" Link:  [✓]                      │
│                                                  │
│ Dropdown Trigger:                               │
│ ○ Hover (Desktop) / Tap (Mobile)               │
│ ● Click to Open                                 │
│                                                  │
│ Animation Style:                                │
│ [Fade ▼] [Slide Down] [None]                   │
│                                                  │
│ Animation Duration: [200ms ▼]                   │
│                                                  │
│ Full Width Dropdown: [✓]                        │
│ (Spans entire screen width)                     │
│                                                  │
└─────────────────────────────────────────────────┘
```

### Cards Grid Specific Options

```
┌─────────────────────────────────────────────────┐
│ Cards Grid Options                               │
├─────────────────────────────────────────────────┤
│                                                  │
│ Cards Per Row:                                   │
│ Desktop: [4 ▼]  Tablet: [3 ▼]  Mobile: [2 ▼]   │
│                                                  │
│ Card Style:                                      │
│ ○ Image with overlay text                       │
│ ● Image with text below                         │
│ ○ Rounded corners                               │
│ ○ Square edges                                  │
│                                                  │
│ Card Spacing: [16px ▼]                          │
│                                                  │
│ Image Aspect Ratio:                             │
│ [Square 1:1 ▼] [Landscape 4:3] [Portrait 3:4]  │
│                                                  │
│ Hover Effect:                                    │
│ [Zoom ▼] [Darken] [Lift] [None]                │
│                                                  │
│ Show Product Count: [✓]                         │
│                                                  │
└─────────────────────────────────────────────────┘
```

### Horizontal Bar Specific Options

```
┌─────────────────────────────────────────────────┐
│ Horizontal Bar Options                           │
├─────────────────────────────────────────────────┤
│                                                  │
│ Max Categories Before "More": [6 ▼]             │
│                                                  │
│ Sticky on Scroll: [✓]                           │
│                                                  │
│ Show Subcategories in Dropdown: [✓]             │
│                                                  │
│ Show Icons: [✓]                                 │
│                                                  │
│ Bar Position:                                    │
│ ○ In Main Navigation                            │
│ ● Below Navigation                              │
│ ○ Above Hero (Homepage only)                    │
│                                                  │
│ Alignment:                                       │
│ [Left ▼] [Center] [Space Between]              │
│                                                  │
│ Mobile Behavior:                                 │
│ ○ Horizontal Scroll                             │
│ ● Collapse to Hamburger                         │
│ ○ Show as Dropdown                              │
│                                                  │
└─────────────────────────────────────────────────┘
```

---

## Database Settings

New settings in `settings` table (group: `category_display`):

| Setting Key | Type | Default | Description |
|------------|------|---------|-------------|
| `category_display_style` | string | `cards` | Options: `cards`, `mega_menu`, `horizontal_bar`, `sidebar`, `pills`, `tabs`, `icons`, `flyout` |
| `category_font_size` | string | `1rem` | Font size for category names |
| `category_font_weight` | string | `500` | Font weight |
| `category_text_transform` | string | `none` | `none`, `uppercase`, `capitalize` |
| `category_bg_color` | string | `#F8F9FA` | Background color |
| `category_text_color` | string | `#333333` | Text color |
| `category_hover_bg` | string | `#8B4513` | Hover background |
| `category_hover_text` | string | `#FFFFFF` | Hover text color |
| `category_active_bg` | string | `#8B4513` | Active/selected background |
| `category_active_text` | string | `#FFFFFF` | Active text color |
| `category_border_color` | string | `#DEE2E6` | Border color |
| `category_border_radius` | string | `4px` | Border radius |
| `mega_menu_columns` | number | `3` | Columns in mega menu |
| `mega_menu_show_images` | boolean | `true` | Show category images |
| `mega_menu_trigger` | string | `hover` | `hover` or `click` |
| `mega_menu_animation` | string | `fade` | `fade`, `slide`, `none` |
| `cards_per_row_desktop` | number | `4` | Cards per row on desktop |
| `cards_per_row_tablet` | number | `3` | Cards per row on tablet |
| `cards_per_row_mobile` | number | `2` | Cards per row on mobile |
| `cards_hover_effect` | string | `zoom` | `zoom`, `darken`, `lift`, `none` |
| `horizontal_max_items` | number | `6` | Max items before "More" |
| `horizontal_sticky` | boolean | `true` | Sticky on scroll |
| `horizontal_position` | string | `below_nav` | `in_nav`, `below_nav`, `above_hero` |

---

## Implementation Phases

### Phase 1: Settings UI
- [ ] Add "Category Display" tab to Settings page
- [ ] Create display style radio buttons with previews
- [ ] Add typography controls (font size, weight, transform)
- [ ] Add color pickers for all color options
- [ ] Save/retrieve settings via API

### Phase 2: Mega Menu Component
- [ ] Create `mega-menu.php` include file
- [ ] Build multi-column dropdown structure
- [ ] Add category images and featured products
- [ ] Implement hover/click trigger
- [ ] Add smooth animations
- [ ] Mobile adaptation (full-screen menu)

### Phase 3: Horizontal Bar Component
- [ ] Create `category-bar.php` include file
- [ ] Build dropdown menus
- [ ] Implement "More" overflow behavior
- [ ] Add sticky scroll functionality
- [ ] Mobile horizontal scroll

### Phase 4: Other Display Options
- [ ] Pills/Tags style
- [ ] Icon grid style
- [ ] Tab navigation
- [ ] Sidebar accordion

### Phase 5: Frontend Integration
- [ ] Conditionally load correct component based on setting
- [ ] Apply custom colors/fonts from settings
- [ ] Test all combinations
- [ ] Performance optimization

---

## Files to Create/Modify

### New Files
1. `includes/category-display/mega-menu.php`
2. `includes/category-display/category-bar.php`
3. `includes/category-display/category-pills.php`
4. `includes/category-display/category-icons.php`
5. `assets/css/category-display.css`
6. `assets/js/category-display.js`

### Modified Files
1. `index.php` - Load correct category display component
2. `products.php` - Integrate category navigation
3. `includes/header.php` - Add mega menu to nav if enabled
4. `admin-site: settings.blade.php` - Add Category Display tab
5. `admin-api: SettingsController.php` - Handle new settings

---

## Live Preview Feature

Consider adding a live preview panel in the settings:

```
┌─────────────────────────────────────────────────┐
│ Preview                                          │
├─────────────────────────────────────────────────┤
│                                                  │
│  ┌─────────────────────────────────────────┐    │
│  │  [Simulated category display based on    │    │
│  │   current settings - updates in          │    │
│  │   real-time as user changes options]     │    │
│  └─────────────────────────────────────────┘    │
│                                                  │
│  Desktop │ Tablet │ Mobile   <- Preview sizes   │
│                                                  │
└─────────────────────────────────────────────────┘
```

---

## Recommended Implementation Order

**Phase 1 (MVP):**
1. Cards Grid (already done) ✓
2. Horizontal Category Bar with dropdowns
3. Mega Menu

**Phase 2 (Enhanced):**
4. Pills/Tags style
5. Full customization options (colors, fonts)

**Phase 3 (Advanced):**
6. Icon Grid
7. Tab Navigation
8. Live preview in settings

---

## Open Questions

1. **Category Images**: Should mega menu pull images from category table or allow separate "menu image" upload?

2. **Subcategory Depth**: How many levels deep should dropdowns go? (Suggested: 2 levels max)

3. **SEO Impact**: How to maintain SEO-friendly category links across all display styles?

4. **Performance**: Should mega menu data be cached? How often refresh?

5. **Integration**: Should category display settings sync with main navigation settings?

---

## Visual References

**Lumber Liquidators Style:**
- Full-width mega menu
- Multi-column layout
- Category images on right
- Clear section headers
- "Shop All" links

**Guitar Center Style:**
- Horizontal bar below main nav
- Clean dropdowns
- Sticky on scroll
- "Deals" highlighted

**Amazon Style:**
- Left sidebar with flyout
- Extensive subcategory listing
- "See All" links

---

*Document created: December 2, 2025*
*Status: Planning/Review*
