# Loading Spinners System - Planning Document

## Overview

This document outlines a unified loading spinner/indicator system for all asynchronous operations (AJAX, fetch, promises) across the frontend. The goal is to provide consistent visual feedback to users whenever data is being loaded or processed.

---

## Current Problems

1. **Inconsistent Loading States** - Some operations show spinners, others don't
2. **No Global Handler** - Each AJAX call handles loading state individually
3. **User Confusion** - No feedback when clicking buttons that trigger async operations
4. **Multiple Implementations** - Different spinner styles in different places

---

## Spinner Types

### 1. Full Page Overlay Spinner
**Use When:** Initial page data loading, major operations that block the UI

```
┌─────────────────────────────────────────────────────────────────────┐
│                                                                      │
│                                                                      │
│                           ┌──────────┐                              │
│                           │  ⟳       │                              │
│                           │ Loading… │                              │
│                           └──────────┘                              │
│                                                                      │
│                                                                      │
└─────────────────────────────────────────────────────────────────────┘
```

**Examples:**
- Checkout processing
- Account login/logout
- Form submissions that redirect

---

### 2. Section/Container Spinner
**Use When:** Loading content within a specific area while rest of page is usable

```
┌─────────────────────────────────────────────────────────────────────┐
│  Header (usable)                                                     │
├─────────────────────────────────────────────────────────────────────┤
│  Sidebar    │  ┌─────────────────────────────────────┐              │
│  (usable)   │  │            ⟳ Loading...             │              │
│             │  │                                      │              │
│             │  └─────────────────────────────────────┘              │
├─────────────────────────────────────────────────────────────────────┤
│  Footer (usable)                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

**Examples:**
- Product list loading
- Cart items refresh
- Search results
- Tab content loading

---

### 3. Inline/Button Spinner
**Use When:** Button click triggers async operation, button shows loading state

```
Before:  [Add to Cart]
During:  [⟳ Adding...]  (disabled)
After:   [✓ Added!] → [Add to Cart]
```

**Examples:**
- Add to Cart
- Add to Wishlist
- Subscribe to Newsletter
- Save Settings

---

### 4. Skeleton Loading (Placeholder)
**Use When:** Loading structured content where layout is predictable

```
┌─────────────────────────────────────────────────────────────────────┐
│  ████████████████████                                                │
│  ░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░│
│  ░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░│
│  ░░░░░░░░░░░░░░                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

**Examples:**
- Product cards loading
- Review list loading
- Order history loading

---

## HTML Components

### Full Page Overlay

```html
<!-- Global Loading Overlay - Add to footer.php -->
<div id="pageLoader" class="page-loader" style="display: none;">
    <div class="loader-content">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="loader-text mt-3">Loading...</p>
    </div>
</div>
```

### Section Loader

```html
<!-- Container with loader -->
<div class="loadable-container" id="productList">
    <div class="section-loader" style="display: none;">
        <div class="spinner-border spinner-border-sm text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <span class="ms-2">Loading products...</span>
    </div>
    <div class="section-content">
        <!-- Actual content here -->
    </div>
</div>
```

### Button with Spinner

```html
<button type="button" class="btn btn-primary btn-loading" data-loading-text="Adding...">
    <span class="btn-text">Add to Cart</span>
    <span class="btn-spinner spinner-border spinner-border-sm" style="display: none;"></span>
</button>
```

---

## CSS Styles

```css
/* ============================================
   LOADING SPINNERS SYSTEM
   ============================================ */

/* Full Page Loader */
.page-loader {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.9);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
    backdrop-filter: blur(2px);
}

.page-loader .loader-content {
    text-align: center;
}

.page-loader .loader-text {
    color: var(--prt-brown);
    font-weight: 500;
}

/* Section/Container Loader */
.loadable-container {
    position: relative;
    min-height: 100px;
}

.section-loader {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.8);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 10;
    border-radius: inherit;
}

.loadable-container.loading .section-content {
    opacity: 0.5;
    pointer-events: none;
}

/* Button Loading State */
.btn-loading {
    position: relative;
}

.btn-loading.loading {
    pointer-events: none;
    opacity: 0.8;
}

.btn-loading.loading .btn-text {
    visibility: hidden;
}

.btn-loading.loading .btn-spinner {
    display: inline-block !important;
    position: absolute;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -50%);
}

/* Alternative: Text + Spinner side by side */
.btn-loading.loading-inline .btn-spinner {
    display: inline-block !important;
    margin-right: 0.5rem;
}

.btn-loading.loading-inline.loading .btn-text {
    visibility: visible;
}

/* Skeleton Loading */
.skeleton {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: skeleton-loading 1.5s infinite;
    border-radius: 4px;
}

.skeleton-text {
    height: 1em;
    margin-bottom: 0.5em;
}

.skeleton-text.short {
    width: 40%;
}

.skeleton-text.medium {
    width: 70%;
}

.skeleton-title {
    height: 1.5em;
    width: 60%;
    margin-bottom: 1em;
}

.skeleton-image {
    height: 200px;
    width: 100%;
}

@keyframes skeleton-loading {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}

/* Spinner Sizes */
.spinner-xs {
    width: 0.75rem;
    height: 0.75rem;
    border-width: 0.1em;
}

.spinner-lg {
    width: 3rem;
    height: 3rem;
    border-width: 0.3em;
}

/* Custom Gear Spinner (Alternative to Bootstrap) */
.gear-spinner {
    display: inline-block;
    width: 2rem;
    height: 2rem;
    animation: gear-spin 1s linear infinite;
}

.gear-spinner::before {
    content: "⚙";
    font-size: 2rem;
    color: var(--prt-brown);
}

@keyframes gear-spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
```

---

## JavaScript Utility

### loader.js - Global Loading Utility

```javascript
/**
 * PRT Loading Spinner Utility
 * Provides consistent loading states for all async operations
 */
const PRTLoader = {

    // ============================================
    // FULL PAGE LOADER
    // ============================================

    /**
     * Show full page loading overlay
     * @param {string} message - Optional loading message
     */
    showPage: function(message = 'Loading...') {
        let loader = document.getElementById('pageLoader');
        if (!loader) {
            loader = this._createPageLoader();
        }
        loader.querySelector('.loader-text').textContent = message;
        loader.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    },

    /**
     * Hide full page loading overlay
     */
    hidePage: function() {
        const loader = document.getElementById('pageLoader');
        if (loader) {
            loader.style.display = 'none';
            document.body.style.overflow = '';
        }
    },

    _createPageLoader: function() {
        const loader = document.createElement('div');
        loader.id = 'pageLoader';
        loader.className = 'page-loader';
        loader.innerHTML = `
            <div class="loader-content">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="loader-text mt-3">Loading...</p>
            </div>
        `;
        document.body.appendChild(loader);
        return loader;
    },

    // ============================================
    // SECTION/CONTAINER LOADER
    // ============================================

    /**
     * Show loading state for a container
     * @param {string|Element} container - Container selector or element
     * @param {string} message - Optional loading message
     */
    showSection: function(container, message = 'Loading...') {
        const el = typeof container === 'string'
            ? document.querySelector(container)
            : container;

        if (!el) return;

        el.classList.add('loadable-container', 'loading');

        let loader = el.querySelector('.section-loader');
        if (!loader) {
            loader = document.createElement('div');
            loader.className = 'section-loader';
            loader.innerHTML = `
                <div class="spinner-border spinner-border-sm text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <span class="ms-2">${message}</span>
            `;
            el.insertBefore(loader, el.firstChild);
        }
        loader.style.display = 'flex';
    },

    /**
     * Hide loading state for a container
     * @param {string|Element} container - Container selector or element
     */
    hideSection: function(container) {
        const el = typeof container === 'string'
            ? document.querySelector(container)
            : container;

        if (!el) return;

        el.classList.remove('loading');
        const loader = el.querySelector('.section-loader');
        if (loader) {
            loader.style.display = 'none';
        }
    },

    // ============================================
    // BUTTON LOADER
    // ============================================

    /**
     * Show loading state on a button
     * @param {string|Element} button - Button selector or element
     * @param {string} loadingText - Optional text to show while loading
     */
    showButton: function(button, loadingText = null) {
        const btn = typeof button === 'string'
            ? document.querySelector(button)
            : button;

        if (!btn) return;

        // Store original state
        btn.dataset.originalText = btn.innerHTML;
        btn.dataset.originalDisabled = btn.disabled;

        // Get loading text from data attribute or parameter
        const text = loadingText || btn.dataset.loadingText || 'Loading...';

        btn.disabled = true;
        btn.classList.add('loading');
        btn.innerHTML = `
            <span class="spinner-border spinner-border-sm me-2" role="status"></span>
            ${text}
        `;
    },

    /**
     * Hide loading state on a button
     * @param {string|Element} button - Button selector or element
     * @param {boolean} success - Show success state briefly
     * @param {string} successText - Text to show on success
     */
    hideButton: function(button, success = false, successText = 'Done!') {
        const btn = typeof button === 'string'
            ? document.querySelector(button)
            : button;

        if (!btn) return;

        btn.classList.remove('loading');

        if (success) {
            btn.innerHTML = `<i class="bi bi-check-lg me-1"></i> ${successText}`;
            btn.classList.add('btn-success');

            setTimeout(() => {
                btn.innerHTML = btn.dataset.originalText;
                btn.disabled = btn.dataset.originalDisabled === 'true';
                btn.classList.remove('btn-success');
            }, 1500);
        } else {
            btn.innerHTML = btn.dataset.originalText;
            btn.disabled = btn.dataset.originalDisabled === 'true';
        }
    },

    // ============================================
    // SKELETON LOADER
    // ============================================

    /**
     * Show skeleton loading in a container
     * @param {string|Element} container - Container selector or element
     * @param {string} template - Skeleton template type: 'card', 'list', 'table'
     * @param {number} count - Number of skeleton items
     */
    showSkeleton: function(container, template = 'card', count = 3) {
        const el = typeof container === 'string'
            ? document.querySelector(container)
            : container;

        if (!el) return;

        // Store original content
        el.dataset.originalContent = el.innerHTML;

        let skeletonHTML = '';
        for (let i = 0; i < count; i++) {
            skeletonHTML += this._getSkeletonTemplate(template);
        }

        el.innerHTML = skeletonHTML;
    },

    /**
     * Hide skeleton and restore/replace content
     * @param {string|Element} container - Container selector or element
     * @param {string} newContent - New HTML content (optional)
     */
    hideSkeleton: function(container, newContent = null) {
        const el = typeof container === 'string'
            ? document.querySelector(container)
            : container;

        if (!el) return;

        if (newContent) {
            el.innerHTML = newContent;
        } else if (el.dataset.originalContent) {
            el.innerHTML = el.dataset.originalContent;
        }
    },

    _getSkeletonTemplate: function(type) {
        const templates = {
            card: `
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="skeleton skeleton-image"></div>
                        <div class="card-body">
                            <div class="skeleton skeleton-title"></div>
                            <div class="skeleton skeleton-text"></div>
                            <div class="skeleton skeleton-text short"></div>
                        </div>
                    </div>
                </div>
            `,
            list: `
                <div class="d-flex align-items-center p-3 border-bottom">
                    <div class="skeleton" style="width: 50px; height: 50px; border-radius: 50%;"></div>
                    <div class="ms-3 flex-grow-1">
                        <div class="skeleton skeleton-text medium"></div>
                        <div class="skeleton skeleton-text short"></div>
                    </div>
                </div>
            `,
            table: `
                <tr>
                    <td><div class="skeleton skeleton-text"></div></td>
                    <td><div class="skeleton skeleton-text"></div></td>
                    <td><div class="skeleton skeleton-text short"></div></td>
                </tr>
            `
        };
        return templates[type] || templates.card;
    },

    // ============================================
    // FETCH WRAPPER WITH AUTO-LOADING
    // ============================================

    /**
     * Fetch with automatic loading state
     * @param {string} url - Fetch URL
     * @param {Object} options - Fetch options
     * @param {Object} loaderOptions - Loader configuration
     * @returns {Promise}
     */
    fetch: async function(url, options = {}, loaderOptions = {}) {
        const {
            type = 'page',      // 'page', 'section', 'button', 'none'
            target = null,      // Element for section/button loader
            message = 'Loading...',
            showSuccess = false,
            successText = 'Done!'
        } = loaderOptions;

        // Show loader
        switch(type) {
            case 'page':
                this.showPage(message);
                break;
            case 'section':
                if (target) this.showSection(target, message);
                break;
            case 'button':
                if (target) this.showButton(target, message);
                break;
        }

        try {
            const response = await fetch(url, options);
            const data = await response.json();

            // Hide loader
            switch(type) {
                case 'page':
                    this.hidePage();
                    break;
                case 'section':
                    if (target) this.hideSection(target);
                    break;
                case 'button':
                    if (target) this.hideButton(target, showSuccess, successText);
                    break;
            }

            return data;
        } catch (error) {
            // Always hide loader on error
            this.hidePage();
            if (target) {
                this.hideSection(target);
                this.hideButton(target);
            }
            throw error;
        }
    }
};

// Make globally available
window.PRTLoader = PRTLoader;
```

---

## Usage Examples

### Example 1: Add to Cart Button

```javascript
// Add to cart with button loading
async function addToCart(productId, button) {
    PRTLoader.showButton(button, 'Adding to Cart...');

    try {
        const response = await fetch('/api/cart/add', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ product_id: productId })
        });

        const data = await response.json();

        if (data.success) {
            PRTLoader.hideButton(button, true, 'Added!');
            updateCartCount(data.cartCount);
        } else {
            PRTLoader.hideButton(button);
            showError(data.message);
        }
    } catch (error) {
        PRTLoader.hideButton(button);
        showError('Failed to add to cart');
    }
}
```

### Example 2: Load Products with Section Loader

```javascript
// Load products with container loading
async function loadProducts(categoryId) {
    const container = document.getElementById('productGrid');
    PRTLoader.showSection(container, 'Loading products...');

    try {
        const response = await fetch(`/api/products?category=${categoryId}`);
        const data = await response.json();

        renderProducts(data.products);
        PRTLoader.hideSection(container);
    } catch (error) {
        PRTLoader.hideSection(container);
        showError('Failed to load products');
    }
}
```

### Example 3: Checkout with Full Page Loader

```javascript
// Process checkout with full page loader
async function processCheckout(formData) {
    PRTLoader.showPage('Processing your order...');

    try {
        const response = await fetch('/api/checkout', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            PRTLoader.showPage('Order complete! Redirecting...');
            window.location.href = '/order-confirmation/' + data.orderId;
        } else {
            PRTLoader.hidePage();
            showError(data.message);
        }
    } catch (error) {
        PRTLoader.hidePage();
        showError('Checkout failed. Please try again.');
    }
}
```

### Example 4: Using the Fetch Wrapper

```javascript
// Simplified fetch with auto-loading
async function loadUserProfile() {
    const data = await PRTLoader.fetch('/api/user/profile', {}, {
        type: 'section',
        target: '#profileContainer',
        message: 'Loading profile...'
    });

    renderProfile(data);
}

// Button with success feedback
async function saveSettings(form, button) {
    const formData = new FormData(form);

    await PRTLoader.fetch('/api/settings', {
        method: 'POST',
        body: formData
    }, {
        type: 'button',
        target: button,
        message: 'Saving...',
        showSuccess: true,
        successText: 'Saved!'
    });
}
```

### Example 5: Skeleton Loading for Product Cards

```javascript
// Show skeleton while loading products
function loadProductCards() {
    const grid = document.getElementById('productGrid');
    PRTLoader.showSkeleton(grid, 'card', 6); // 6 skeleton cards

    fetch('/api/products')
        .then(res => res.json())
        .then(data => {
            PRTLoader.hideSkeleton(grid, renderProductCards(data.products));
        });
}
```

---

## Implementation Locations

### Frontend (PRT2)

| File | What to Add |
|------|-------------|
| `assets/css/custom.css` | Add loading spinner CSS styles |
| `assets/js/loader.js` | Create new file with PRTLoader utility |
| `includes/footer.php` | Include loader.js and page loader HTML |
| `assets/js/main.js` | Update existing AJAX calls to use PRTLoader |

### Files to Update

| File | Current State | Update Needed |
|------|---------------|---------------|
| `cart/cart.php` | Manual spinner handling | Use PRTLoader |
| `products/product-detail.php` | Some spinners, inconsistent | Standardize with PRTLoader |
| `includes/newsletter-signup` | Has inline spinner | Can use PRTLoader.showButton |
| `auth/login.php` | May need page loader | Add checkout-style loader |

---

## Admin Backend (Optional)

The admin site can use the same system:

| File | What to Add |
|------|-------------|
| `resources/views/layouts/admin.blade.php` | Add page loader HTML |
| `public/css/admin.css` | Add loading styles |
| `public/js/admin.js` | Include PRTLoader or similar |

---

## Global AJAX Interceptor (Optional Advanced)

For catching ALL fetch/AJAX calls automatically:

```javascript
// Auto-show loader for all fetch requests (optional)
const originalFetch = window.fetch;
window.fetch = async function(...args) {
    // Could show a subtle loading indicator in navbar
    document.body.classList.add('ajax-loading');

    try {
        const response = await originalFetch.apply(this, args);
        return response;
    } finally {
        document.body.classList.remove('ajax-loading');
    }
};
```

```css
/* Subtle navbar loading indicator */
body.ajax-loading .navbar::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 2px;
    background: linear-gradient(90deg, transparent, var(--prt-red), transparent);
    animation: loading-bar 1s infinite;
}

@keyframes loading-bar {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}
```

---

## Implementation Phases

### Phase 1: Core Setup
- [ ] Create `assets/js/loader.js` with PRTLoader utility
- [ ] Add loading CSS to `custom.css`
- [ ] Add page loader HTML to `footer.php`
- [ ] Include loader.js in footer

### Phase 2: Button Loaders
- [ ] Update Add to Cart buttons
- [ ] Update Wishlist buttons
- [ ] Update Newsletter signup
- [ ] Update all form submit buttons

### Phase 3: Section Loaders
- [ ] Product grid loading
- [ ] Cart items refresh
- [ ] Search results
- [ ] Tab content switching

### Phase 4: Page Loaders
- [ ] Checkout processing
- [ ] Login/Logout
- [ ] Order placement

### Phase 5: Skeleton Loaders (Optional)
- [ ] Product card skeletons
- [ ] Review list skeletons
- [ ] Order history skeletons

---

## Design Considerations

### Spinner Style Options

1. **Bootstrap Spinner** (Current)
   - Pros: Built-in, familiar
   - Cons: Generic

2. **Gear Icon** (Western Theme)
   - Pros: Matches brand
   - Cons: May look dated

3. **Custom SVG**
   - Pros: Unique branding
   - Cons: More work to create

### Recommendation
Use Bootstrap spinners for consistency, but consider a branded full-page loader with the PRT logo.

---

## Summary

| Loader Type | Use Case | Method |
|-------------|----------|--------|
| Full Page | Checkout, Login, Major ops | `PRTLoader.showPage()` |
| Section | Content areas, Grids | `PRTLoader.showSection()` |
| Button | Any clickable action | `PRTLoader.showButton()` |
| Skeleton | Predictable content loading | `PRTLoader.showSkeleton()` |
| Fetch Wrapper | Simplified async calls | `PRTLoader.fetch()` |

---

*Document created: November 29, 2025*
*Status: Planning/Review*
