# Special Products Management - Planning Document

## Overview

This document outlines the plan to create a dedicated **Special Products** management section in the admin, separate from regular Products and Categories.

---

## Current State (Problems)

### Issue 1: Confusing Category Configuration
The Features page has two nearly identical category selectors:
- Digital Download Categories
- Specialty Product Categories

Both show the same category tree, which is confusing and feels duplicative.

### Issue 2: Special Products Tied to Regular Categories
Currently, Special Products (`/products/special-category.php`) uses regular categories from the main catalog. This creates coupling that doesn't make sense.

### Issue 3: No Dedicated Management
Special Products don't have their own admin section - they're managed through the regular Products interface with category filters.

---

## Proposed Solution

### Create Dedicated "Special Products" Section in Admin

```
Admin Left Navigation:
├── Dashboard
├── Products           <- Regular products
├── Categories         <- Regular categories
├── Special Products   <- NEW SECTION
│   ├── Products       <- Manage special products
│   └── Categories     <- Manage special categories
├── Orders
├── ...
```

### Separate Database Structure

**Option A: Flag-Based (Simpler)**
- Add `is_special` flag to existing products
- Add `is_special` flag to existing categories
- Share same tables, filter by flag

**Option B: Separate Tables (Complete Separation)**
- `special_products` table
- `special_categories` table
- Completely independent from regular products

**Option C: Hybrid (Recommended)**
- Special Categories are separate (`special_categories` table)
- Products can be "promoted" to Special Products (flag + special category assignment)
- OR Special Products can be unique items not in main catalog

---

## Answers to Questions (CONFIRMED)

### Q1: What IS a Special Product? ✅ ANSWERED

**ANSWER: Completely Separate Products**

Special Products are SEPARATE from regular products:
- Own product catalog
- Own inventory
- Own categories
- NOT linked to regular products
- Can include Digital Downloads as a product flag

```
Special Products System:
├── Has its own Categories
├── Has its own Products
├── Products can be flagged as "Digital Download"
└── Completely independent from regular Products/Categories
```

---

### Q2: What About Digital Downloads? ✅ ANSWERED

**ANSWER: Digital Downloads are PART of Special Products + Separate Footer Link**

Digital Downloads are products within Special Products flagged as "Digital Download":
- Within Special Products, products can be flagged as "Digital Download"
- This enables download functionality for that product
- No separate admin section needed (managed within Special Products)
- BUT can have its own footer link for quick access

```
Special Products:
├── Categories
│   ├── Clearance
│   ├── Digital Content  <- Can be a category
│   └── Limited Edition
└── Products
    ├── Holiday Boot Set           [Physical]
    ├── eBook: Boot Care Guide     [✓ Digital Download]
    └── Printable Size Chart       [✓ Digital Download]
```

---

### Footer Navigation for Special Products & Digital Downloads

**Both can appear as separate links in footer when their toggles are ON:**

```
Feature Toggles in Admin:
☑ Special Products      <- Shows "Special Products" in footer
☑ Digital Downloads     <- Shows "Digital Downloads" in footer
                           (Only works if Special Products is also ON)
```

**Footer - Shop Section:**
```
Shop
├── Home
├── All Products
├── Special Products     <- Shows if specialty_products_enabled = ON
├── Digital Downloads    <- Shows if digital_downloads_enabled = ON
├── Product List
└── Shopping Cart
```

**URL Structure:**
- Special Products → `/products/special-products.php` (all special products)
- Digital Downloads → `/products/special-products.php?type=digital` (filtered to digital only)

**Toggle Logic:**
```php
// In footer.php
<?php if (isFeatureEnabled('specialty_products')): ?>
    <li><a href="special-products.php">Special Products</a></li>
<?php endif; ?>

<?php if (isFeatureEnabled('specialty_products') && isFeatureEnabled('digital_downloads')): ?>
    <li><a href="special-products.php?type=digital">Digital Downloads</a></li>
<?php endif; ?>
```

**Note:** Digital Downloads toggle only works if Special Products is also enabled (since digital downloads ARE special products with a flag).

---

### Q3: Special Categories Structure ✅ ANSWERED

**ANSWER: Nested Hierarchy (like current categories)**

Special Categories should support:
- 2, 3, 4 level deep hierarchies
- Products under each category
- Each category can be toggled on/off individually

```
Special Categories (Hierarchical):
├── Seasonal Specials          [Toggle: ON]
│   ├── Holiday Deals          [Toggle: ON]
│   │   └── Products...
│   └── Summer Sale           [Toggle: OFF]
│       └── Products...
├── Digital Content            [Toggle: ON]
│   ├── eBooks
│   └── Printables
└── Clearance                  [Toggle: ON]
    └── Products...
```

---

### Q4: Relationship to Featured Products ✅ ANSWERED

**ANSWER: Special Products can appear in Featured Products**

Special Products can be "featured" to appear in Featured sections (like homepage).
This is about DISPLAY, not product structure.

**Recommended Approach:**

```
Special Product Edit Form:
┌─────────────────────────────────────────────────────────────────────┐
│  Edit Special Product: Holiday Boot Set                              │
├─────────────────────────────────────────────────────────────────────┤
│  Name: [Holiday Boot Set                    ]                       │
│  Category: [Seasonal Specials ▼]                                    │
│  Price: [$299.00    ]                                               │
│                                                                      │
│  PRODUCT FLAGS:                                                      │
│  ☐ Digital Download    <- Enables download instead of shipping     │
│  ☑ Featured Product    <- Shows in Featured sections on homepage   │
│  ☐ New Arrival         <- Shows "NEW" badge                        │
│  ☐ On Sale             <- Shows sale pricing                       │
│                                                                      │
└─────────────────────────────────────────────────────────────────────┘
```

---

## Featured Products Layout Recommendation

### Where Featured Products Display

Featured Products (from Special Products marked as "Featured") can appear:

1. **Homepage - Featured Section**
```
┌─────────────────────────────────────────────────────────────────────┐
│                         FEATURED PRODUCTS                            │
│                   (From Special Products flagged as Featured)        │
├─────────────────────────────────────────────────────────────────────┤
│                                                                      │
│  ┌─────────┐  ┌─────────┐  ┌─────────┐  ┌─────────┐                │
│  │  IMAGE  │  │  IMAGE  │  │  IMAGE  │  │  IMAGE  │                │
│  │ Product │  │ Product │  │ Product │  │ Product │                │
│  │  $299   │  │  $149   │  │  $199   │  │  $99    │                │
│  └─────────┘  └─────────┘  └─────────┘  └─────────┘                │
│                                                                      │
│                        [View All Special Products]                   │
└─────────────────────────────────────────────────────────────────────┘
```

2. **Special Products Page - Featured at Top**
```
┌─────────────────────────────────────────────────────────────────────┐
│                    SPECIAL PRODUCTS                                  │
├─────────────────────────────────────────────────────────────────────┤
│  FEATURED                                    [See All Featured]     │
│  ┌─────────┐  ┌─────────┐  ┌─────────┐  ┌─────────┐                │
│  │ FEATURED│  │ FEATURED│  │ FEATURED│  │ FEATURED│                │
│  └─────────┘  └─────────┘  └─────────┘  └─────────┘                │
├─────────────────────────────────────────────────────────────────────┤
│  CATEGORIES                                                          │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐              │
│  │   Seasonal   │  │   Digital    │  │  Clearance   │              │
│  │   Specials   │  │   Content    │  │              │              │
│  └──────────────┘  └──────────────┘  └──────────────┘              │
└─────────────────────────────────────────────────────────────────────┘
```

### Admin Control for Featured Display

In Admin > Special Products > Settings:
```
┌─────────────────────────────────────────────────────────────────────┐
│  Featured Products Settings                                          │
├─────────────────────────────────────────────────────────────────────┤
│                                                                      │
│  ☑ Show Featured Section on Homepage                                │
│  ☑ Show Featured Section on Special Products Page                   │
│                                                                      │
│  Featured Section Title: [Featured Products          ]              │
│  Max Products to Show:   [8                          ]              │
│  Display Order:          [Random ▼] / Newest / Manual               │
│                                                                      │
└─────────────────────────────────────────────────────────────────────┘
```

---

## Proposed Admin Interface

### Special Products Section

```
┌─────────────────────────────────────────────────────────────────────┐
│  Special Products                                                    │
├─────────────────────────────────────────────────────────────────────┤
│                                                                      │
│  [Products] [Categories] [Settings]                                 │
│                                                                      │
│  ┌─────────────────────────────────────────────────────────────┐    │
│  │  Special Products (24 items)                    [+ Add New]  │    │
│  ├──────────────────────────────────────────────────────────────┤    │
│  │  [Search...]           [Category: All ▼] [Status: All ▼]    │    │
│  ├──────────────────────────────────────────────────────────────┤    │
│  │  □ │ Image │ Name              │ Category    │ Price │ Act  │    │
│  ├───┼───────┼───────────────────┼─────────────┼───────┼──────┤    │
│  │  □ │ [img] │ Holiday Boot Set  │ Seasonal    │ $299  │ Edit │    │
│  │  □ │ [img] │ Collector's Hat   │ Limited Ed  │ $149  │ Edit │    │
│  │  □ │ [img] │ Clearance Jacket  │ Clearance   │ $49   │ Edit │    │
│  └──────────────────────────────────────────────────────────────┘    │
│                                                                      │
│  Feature Toggle: [✓] Special Products Enabled                       │
│  (This also shows in Admin > Features for quick access)             │
│                                                                      │
└─────────────────────────────────────────────────────────────────────┘
```

### Special Categories Section

```
┌─────────────────────────────────────────────────────────────────────┐
│  Special Products > Categories                                       │
├─────────────────────────────────────────────────────────────────────┤
│                                                                      │
│  ┌─────────────────────────────────────────────────────────────┐    │
│  │  Special Categories (5)                         [+ Add New]  │    │
│  ├──────────────────────────────────────────────────────────────┤    │
│  │  Name              │ Products │ Status  │ Actions            │    │
│  ├────────────────────┼──────────┼─────────┼────────────────────┤    │
│  │  Clearance         │ 12       │ Active  │ [Edit] [Delete]    │    │
│  │  New Arrivals      │ 8        │ Active  │ [Edit] [Delete]    │    │
│  │  Limited Edition   │ 3        │ Active  │ [Edit] [Delete]    │    │
│  │  Holiday Specials  │ 6        │ Inactive│ [Edit] [Delete]    │    │
│  │  Weekly Deals      │ 5        │ Active  │ [Edit] [Delete]    │    │
│  └──────────────────────────────────────────────────────────────┘    │
│                                                                      │
└─────────────────────────────────────────────────────────────────────┘
```

---

## Database Schema (Proposed)

### New Tables

```sql
-- Special Categories (separate from regular categories)
CREATE TABLE special_categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE,
    description TEXT,
    image_url VARCHAR(500),
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Option A: If products can be BOTH regular and special
ALTER TABLE products ADD COLUMN is_special BOOLEAN DEFAULT FALSE;
ALTER TABLE products ADD COLUMN special_category_id INT NULL;

-- Option B: If special products are completely separate
CREATE TABLE special_products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2),
    sale_price DECIMAL(10,2),
    special_category_id INT,
    image_url VARCHAR(500),
    stock_quantity INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    -- Can optionally link to a regular product
    linked_product_id INT NULL,  -- FK to products table if shared
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (special_category_id) REFERENCES special_categories(id)
);
```

---

## Frontend Changes

### Special Products Page

The frontend page at `/products/special-products.php` would:
1. Query `special_categories` instead of regular categories
2. Display special products from dedicated table/flag
3. Have its own layout/styling if desired

```
URL Structure:
/products/special-products.php              <- All special products
/products/special-products.php?cat=clearance <- Filtered by special category
```

---

## Features Page Cleanup

### REMOVE Entire "Category Configuration" Section from Features Page

The current Features page (`/admin/features`) has a "Category Configuration" card that contains:
- Digital Download Categories (multi-select)
- Specialty Product Categories (multi-select)

**This entire section should be REMOVED** because:
1. It's confusing - shows same categories twice
2. It doesn't belong in "Features" - it's category management
3. Special Products will have its own Categories section
4. Digital Downloads (if kept) will have its own section

### Current Features Page Structure:
```
┌─────────────────────────────────────────────────────────────────────┐
│  Feature Configuration                                               │
├─────────────────────────────────────────────────────────────────────┤
│  Feature Toggles                    <- KEEP                         │
│  ├── FAQ, Loyalty, etc.                                             │
│  └── All toggle switches                                            │
├─────────────────────────────────────────────────────────────────────┤
│  Category Configuration             <- REMOVE ENTIRE CARD           │
│  ├── Digital Download Categories    <- REMOVE                       │
│  └── Specialty Product Categories   <- REMOVE                       │
├─────────────────────────────────────────────────────────────────────┤
│  Live Chat Configuration            <- KEEP                         │
└─────────────────────────────────────────────────────────────────────┘
```

### New Features Page Structure (After Cleanup):
```
┌─────────────────────────────────────────────────────────────────────┐
│  Feature Configuration                                               │
├─────────────────────────────────────────────────────────────────────┤
│  Feature Toggles                                                     │
│  ├── FAQ                                                            │
│  ├── Loyalty Program                                                │
│  ├── Digital Downloads      <- Just toggle, no category picker     │
│  ├── Special Products       <- Just toggle, no category picker     │
│  ├── Gift Cards                                                     │
│  ├── Wishlists                                                      │
│  ├── Blog                                                           │
│  ├── Events                                                         │
│  ├── Reviews                                                        │
│  ├── Product Sticky Bar                                             │
│  ├── Admin Link                                                     │
│  ├── Tell-A-Friend                                                  │
│  └── Newsletter                                                     │
├─────────────────────────────────────────────────────────────────────┤
│  Live Chat Configuration                                             │
│  ├── Enable Live Chat                                               │
│  ├── Provider selection                                             │
│  └── API keys                                                       │
└─────────────────────────────────────────────────────────────────────┘
```

**The Category Configuration card is completely removed.**

### Where Category Management Moves To:

| Feature | Where Categories Are Managed |
|---------|------------------------------|
| Special Products | Admin > Special Products > Categories |
| Digital Downloads | Admin > Digital Downloads > Categories (if separate section) OR just use the toggle |

### Keep in Features (Simple Toggles Only):
- Toggle: "Special Products Enabled" (simple on/off)
- Toggle: "Digital Downloads Enabled" (simple on/off)

---

## Implementation Phases

### Phase 1: Database Setup
- [ ] Create `special_categories` table
- [ ] Decide on products approach (flag vs separate table)
- [ ] Create migration

### Phase 2: Admin - Special Categories
- [ ] Create Special Categories list page
- [ ] Create Add/Edit Special Category form
- [ ] Add to admin left navigation

### Phase 3: Admin - Special Products
- [ ] Create Special Products list page
- [ ] Create Add/Edit Special Product form
- [ ] Ability to link to regular product OR create standalone

### Phase 4: Frontend Updates
- [ ] Update special-products.php to use new system
- [ ] Create special category pages
- [ ] Update navigation/links

### Phase 5: Cleanup
- [ ] Remove category selectors from Features page
- [ ] Keep simple toggle only
- [ ] Update documentation

---

## Navigation Structure

### Current:
```
Admin Left Nav:
├── Dashboard
├── Products
├── Categories
├── ...
```

### Proposed:
```
Admin Left Nav:
├── Dashboard
├── Products              <- Regular products only
├── Categories            <- Regular categories only
├── Special Products      <- NEW (collapsible)
│   ├── All Products      <- List special products
│   ├── Categories        <- Manage special categories
│   └── Settings          <- Toggle, display options
├── Orders
├── ...
```

---

## Open Questions Summary

1. **Product Relationship:** Can products be both regular AND special? Or completely separate?

2. **Inventory:** If shared, should inventory be linked?

3. **Digital Downloads:** Should this also be a separate section, or remove it?

4. **Category Hierarchy:** Flat or nested special categories?

5. **Import from Regular:** Should there be an "Add to Special Products" button on regular products?

---

## Benefits of This Approach

1. **Clear Separation** - Special Products have dedicated management
2. **No Confusion** - Removes confusing category selectors from Features
3. **Scalable** - Can add more special categories easily
4. **Toggleable** - Still respects feature flag for showing/hiding
5. **Reusable** - Pattern can be used for Digital Downloads or other special sections

---

*Document created: November 29, 2025*
*Status: Planning - Awaiting Answers to Questions*
