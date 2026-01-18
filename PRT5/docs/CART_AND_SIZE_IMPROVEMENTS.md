# Cart and Size Management Improvements

**Date**: November 18, 2025
**Version**: 2.0.0
**Status**: Completed

---

## Overview

This document outlines the improvements made to the shopping cart system and product size management to fix duplicate cart items, implement dynamic size dropdowns, and add proper validation.

---

## Issues Fixed

### 1. Cart Duplicate Items Issue

**Problem**: Products were being duplicated in the cart instead of incrementing quantities when:
- Adding from different pages (products.php vs product-detail.php vs Quick View)
- Products with the same UPC/ItemNumber were being treated as the same item
- Size parameter wasn't being properly passed from Quick View

**Root Cause**:
- AddToCart.php was using UPC as the unique identifier, which can have duplicates
- Quick View wasn't passing size parameter correctly
- No Product ID tracking in cart session

**Solution**:
Modified `cart/AddToCart.php` to use **Product ID** as the primary unique identifier:
- Line 45: Inventory check now uses `$_SESSION["ProductID_" . $x]`
- Line 70: Duplicate detection now uses `$_SESSION["ProductID_" . $x]`
- Line 91: Added `$_SESSION["ProductID_" . $itemIndex] = $product['ID'];`

Cart items are now uniquely identified by:
1. Product ID (primary)
2. Size
3. Color

---

## New Features

### 1. Dynamic Size Dropdowns from Database

**Created**: `includes/size-functions.php`

**Functions**:
```php
getSizesForCategory($dbConnect, $categoryCode)
// Fetches available sizes for a product category from the sizes table

renderSizeDropdown($dbConnect, $categoryCode, $selectId, $selectedSize, $label)
// Renders a complete HTML size dropdown with validation
```

**Database Schema**:
```sql
Table: sizes
- id (int) - Primary key
- categorycode (int) - Links to categories table
- sizes (varchar) - Size value (e.g., "7", "7 1/2", "8")
```

**Implementation**:
- Each category has its own set of available sizes
- Size dropdowns are dynamically generated based on product category
- Products without sizes in database don't show a size dropdown
- Replaces static hardcoded size lists

**Files Modified**:
- `Products/products.php` - Added dynamic size dropdown to product grid
- `includes/size-functions.php` - New centralized size helper functions

### 2. Size Selection Validation

**Requirement**: Users must select a size before adding to cart if a size dropdown exists

**Implementation** in 3 locations:

**A. products.php - Product Grid** (lines 649-656)
```javascript
function addToCart(upc, catid, productId) {
    const sizeSelect = document.getElementById('sizeSelect_' + productId);

    if (sizeSelect && !sizeSelect.value) {
        showToast('warning', 'Please select a size before adding to cart');
        sizeSelect.focus();
        sizeSelect.classList.add('is-invalid');
        setTimeout(() => sizeSelect.classList.remove('is-invalid'), 3000);
        return;
    }
    // ... rest of function
}
```

**B. products.php - Quick View Modal** (lines 772-779)
```javascript
function addToCartFromQuickView(upc) {
    const sizeSelect = document.getElementById('quickViewSize');

    if (sizeSelect && !sizeSelect.value) {
        showToast('warning', 'Please select a size before adding to cart');
        sizeSelect.focus();
        sizeSelect.classList.add('is-invalid');
        setTimeout(() => sizeSelect.classList.remove('is-invalid'), 3000);
        return;
    }
    // ... rest of function
}
```

**C. product-detail.php - Product Detail Page** (lines 564-570)
```javascript
function addToCartWithOptions() {
    // ... other validations

    if (sizeSelect && !size) {
        showAlert('warning', 'Please select a size before adding to cart.');
        sizeSelect.focus();
        sizeSelect.classList.add('is-invalid');
        setTimeout(() => sizeSelect.classList.remove('is-invalid'), 3000);
        return;
    }
    // ... rest of function
}
```

**User Experience Features**:
- Visual feedback: Red border on dropdown (Bootstrap `is-invalid` class)
- Auto-focus: Cursor moves to size dropdown
- Warning message: Toast/alert notification
- Prevents submission: Returns early to stop add to cart

### 3. Quick View Enhancements

**Added Quantity Selector** (products.php lines 695-706):
```html
<div class="mb-3">
    <label class="form-label"><strong>Quantity:</strong></label>
    <div class="input-group" style="max-width: 150px;">
        <button onclick="adjustQuickViewQty(-1)">-</button>
        <input type="number" id="quickViewQuantity" value="1" min="1" max="99">
        <button onclick="adjustQuickViewQty(1)">+</button>
    </div>
</div>
```

**Benefits**:
- Users can select quantity directly in Quick View
- Reduces need to go to product detail page
- Consistent UX with product detail page

---

## Files Created

1. **includes/size-functions.php**
   - Purpose: Centralized size management functions
   - Functions: `getSizesForCategory()`, `renderSizeDropdown()`
   - Used by: All product pages

---

## Files Modified

### 1. cart/AddToCart.php
- **Lines 45-53**: Changed inventory check to use Product ID
- **Lines 70-81**: Changed duplicate detection to use Product ID
- **Lines 91**: Added Product ID to session storage
- **Impact**: Fixes duplicate cart items, proper item identification

### 2. Products/products.php
- **Line 6**: Added `require_once` for size-functions.php
- **Lines 410-433**: Dynamic size dropdown from database
- **Lines 436**: Updated addToCart button to pass product ID
- **Lines 645-666**: Added size validation to addToCart()
- **Lines 695-706**: Added quantity selector to Quick View
- **Lines 724-731**: Added adjustQuickViewQty() function
- **Lines 770-796**: Updated addToCartFromQuickView() with size validation
- **Impact**: Dynamic sizes, proper validation, better UX

### 3. Products/product-detail.php
- **Lines 543-547**: Fixed PHP syntax errors with json_encode
- **Lines 564-570**: Added size selection validation
- **Lines 649-650**: Fixed undefined PHP variables
- **Lines 664**: Fixed undefined $baseDir variable
- **Impact**: Fixed JavaScript errors, added validation

---

## Testing Checklist

- [x] Add product from grid - validates size selection
- [x] Add product from Quick View - validates size selection
- [x] Add product from detail page - validates size selection
- [x] Same product + same size = quantity increments
- [x] Same product + different size = separate cart items
- [x] Size dropdowns show correct sizes per category
- [x] Products without sizes work without dropdowns
- [x] Quick View quantity selector works
- [x] Cart no longer creates duplicates

---

## Database Requirements

**Table**: `sizes`
```sql
CREATE TABLE IF NOT EXISTS `sizes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `categorycode` int(11) NOT NULL,
  `sizes` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `categorycode` (`categorycode`)
);
```

**Sample Data**:
```sql
INSERT INTO sizes (categorycode, sizes) VALUES
(58, '7'),
(58, '7 1/2'),
(58, '8'),
(58, '8 1/2'),
-- etc.
```

---

## Usage Examples

### Using Size Functions in Other Pages

```php
// At top of file
require_once(__DIR__ . '/../includes/size-functions.php');

// Get sizes array
$sizes = getSizesForCategory($dbConnect, $product['CategoryCode']);

// Render complete dropdown
renderSizeDropdown($dbConnect, $product['CategoryCode'], 'sizeSelect', '', 'Size');
```

### JavaScript Validation Pattern

```javascript
function yourAddToCartFunction() {
    const sizeSelect = document.getElementById('sizeSelect');

    // Validate if dropdown exists
    if (sizeSelect && !sizeSelect.value) {
        showAlert('warning', 'Please select a size');
        sizeSelect.focus();
        sizeSelect.classList.add('is-invalid');
        setTimeout(() => sizeSelect.classList.remove('is-invalid'), 3000);
        return;
    }

    // Continue with add to cart...
}
```

---

## Best Practices

1. **Always use Product ID** for cart item identification, not UPC
2. **Always validate size** when size dropdown exists
3. **Use size-functions.php** for consistent size handling
4. **Pass size parameter** even if empty to AddToCart.php
5. **Test cart merging** when adding same product multiple times

---

## Future Enhancements

- [ ] Add size availability per product (not just category)
- [ ] Display "Out of Stock" for specific sizes
- [ ] Add size guide/chart modal
- [ ] Remember last selected size per category
- [ ] Add "Add All Sizes" functionality for multi-size orders

---

## Related Documentation

- [TODO.md](TODO.md) - Current development tasks
- [CODING_STANDARDS.md](CODING_STANDARDS.md) - Code standards followed
- [DATABASE.md](DATABASE.md) - Database schema details
- [BACKEND.md](BACKEND.md) - Backend architecture

---

## Changelog

### November 18, 2025
- Created size-functions.php helper file
- Fixed cart duplicate items using Product ID
- Added dynamic size dropdowns from database
- Implemented size validation on all add to cart functions
- Added quantity selector to Quick View modal
- Fixed JavaScript syntax errors in product-detail.php
- Updated products.php with size validation
