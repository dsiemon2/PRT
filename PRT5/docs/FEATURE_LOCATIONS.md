# Feature Locations - Quick Reference Guide

**Date**: November 18, 2025
**Purpose**: Quick reference for finding implemented features and where to access them

---

## ✅ Blog/News Section (COMPLETE)

**Location**: `/blog/`
**Files**:
- `/blog/index.php` - Blog listing page with search, categories, pagination
- `/blog/post.php` - Individual blog post page

**Access**:
- Direct URL: `http://localhost/PRT2/blog/`
- **NOT currently in main navigation** ⚠️

**Features**:
- 5 blog categories (Company News, Product Spotlight, Western Heritage, Style Guide, Events)
- Search functionality
- Category filtering
- View tracking
- Social sharing
- Related posts
- Tag system

**Database Tables**:
- `blog_categories`
- `blog_posts`
- `blog_tags`
- `blog_post_tags`

---

## ✅ FAQ Page (COMPLETE)

**Location**: `/pages/faq.php` (MOVED from root)
**Handler**: `/includes/faq-handler.php` (MOVED from root)

**Access**:
- Direct URL: `http://localhost/PRT2/pages/faq.php`
- **NOT currently in main navigation** ⚠️

**Features**:
- 6 FAQ categories (Orders & Shipping, Returns & Exchanges, Products, Account & Payment, Size & Fit, General)
- 11 sample FAQs
- Search functionality
- Category filtering with icons
- Accordion interface
- Helpful/Not Helpful voting
- View tracking

**Database Tables**:
- `faq_categories`
- `faqs`

---

## ✅ Gift Card System (COMPLETE - Front-End)

**Location**: `/pages/gift-cards.php` and `/pages/gift-card-balance.php` (MOVED from root)

**Access**:
- Purchase: `http://localhost/PRT2/pages/gift-cards.php`
- Check Balance: `http://localhost/PRT2/pages/gift-card-balance.php`
- **Accessible via Account dropdown** ✅

**Features**:
- Select amount ($25/$50/$100/$150 or custom $10-$500)
- Quantity selection (1-10 cards)
- Recipient information
- Personal message (up to 200 characters)
- Delivery methods: Email, Print at Home, Physical Card (+$2.99)
- Real-time gift card preview
- Balance checking with PIN validation
- Help section with FAQs
- Consistent page header styling
- Proper navigation links (fixed)

**Status**: Front-end complete, backend payment processing needs integration

**Recent Fixes**:
- Fixed hero section to match site-wide styling (page-header class)
- Fixed "Start Shopping" link path (was `Products/products.php`, now `../products/products.php`)
- Fixed Contact Us link (was `info/contact-us.php`, now `contact-us.php`)
- Removed duplicate Bootstrap JS loading that broke dropdown functionality

---

## ✅ Events Calendar (COMPLETE)

**Location**: `/pages/events.php` (Public), `/admin/Events.php` (Admin)

**Access**:
- Public URL: `http://localhost/PRT2/pages/events.php`
- Admin URL: `http://localhost/PRT2/admin/Events.php` (requires manager access)
- **Accessible via main navigation (Events link)** ✅

**Features**:
- Display events grouped by month
- Event details: Name, Start/End Date, Time
- Responsive card-based layout
- Bootstrap 5 styling
- No authentication required for public view
- Admin view includes "Entered By" information

**Database Table**:
- `Events` (StartDate, EndDate, StartTime, EndTime, EventName, EnteredBy)

**Recent Changes**:
- Created public version at `/pages/events.php` (no authentication required)
- Updated main navigation to link to public events page
- Removed admin-only information from public view

---

## ✅ Coupon/Discount Code System (COMPLETE)

**Location**: Built into checkout process
**Functions**: `/includes/coupon-functions.php`
**Handler**: `/cart/apply-coupon.php`

**Access**:
- Automatically available at checkout
- Apply coupon field in cart/checkout pages
- **Visible to users in cart** ✅

**Features**:
- Percentage and fixed discounts
- Min order amount requirements
- Max discount caps
- Usage limits (total and per customer)
- Start/expiration date management
- Free shipping option
- Category and product-specific coupons
- Track usage and prevent abuse

**Sample Coupons**:
- WELCOME10 (10% off first order)
- SAVE20 (20% off $100+)
- FREESHIP (Free shipping)
- SPRING25 (25% off)
- LOYALTY15 (15% for loyalty members)

**Database Tables**:
- `coupons`
- `coupon_usage`
- `coupon_categories`
- `coupon_products`

**Admin Access**: Manage via database directly (admin interface pending)

---

## ✅ Loyalty Points Program (COMPLETE)

**Location**: `/auth/loyalty-rewards.php`
**Functions**: `/includes/loyalty-functions.php`

**Access**:
- URL: `http://localhost/PRT2/auth/loyalty-rewards.php`
- From Account dropdown → **Loyalty Rewards** ✅
- Points visible in account dashboard

**Features**:
- 4-tier system: Bronze, Silver, Gold, Platinum
- Points multipliers increase with tier (1x to 2x)
- Earn 1 point per $1 spent (base rate)
- 6 rewards in catalog ($5-$25 off, free shipping, discounts)
- Points balance and lifetime tracking
- Transaction history
- Tier progression with benefits
- Automatic tier upgrades
- Tier-restricted rewards
- Visual tier badges and progress bars

**Database Tables**:
- `loyalty_points`
- `loyalty_transactions`
- `loyalty_tiers`
- `loyalty_rewards`

**Integration**: Automatically awards points on order completion

---

## Current Navigation Structure

### Main Navigation (Top Bar)
1. Home
2. About Us
3. All Products
4. Blog
5. Events (now points to `/pages/events.php`)
6. Cart
7. Contact
8. Account (dropdown)

### Account Dropdown (Logged In)
- My Account
- Your Orders
- Buy Again
- Your Lists (Wishlist)
- Loyalty Rewards ✅
- Account Settings
- Gift Cards
- FAQ
- Sign Out

### Account Dropdown (Logged Out)
- Login
- Create Account
- Gift Cards
- FAQ

---

## Missing Navigation Links ⚠️

These features exist but are NOT linked in main navigation:

1. **Blog** → Added to main nav ✅
2. **FAQ** → Added to account dropdown ✅
3. **Gift Cards** → Added to account dropdown ✅
4. **Loyalty Rewards** → Added to account dropdown ✅

**Note**: All features are now accessible through navigation!

---

## Recommended Navigation Updates

### Option 1: Add to Main Navigation

```php
<!-- After "All Products" -->
<li class="nav-item">
    <a class="nav-link" href="<?php echo $baseDir; ?>blog/">
        <i class="bi bi-newspaper"></i> Blog
    </a>
</li>

<!-- Or create a "More" dropdown -->
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
        <i class="bi bi-three-dots"></i> More
    </a>
    <ul class="dropdown-menu">
        <li><a class="dropdown-item" href="<?php echo $baseDir; ?>blog/"><i class="bi bi-newspaper"></i> Blog</a></li>
        <li><a class="dropdown-item" href="<?php echo $baseDir; ?>pages/faq.php"><i class="bi bi-question-circle"></i> FAQ</a></li>
        <li><a class="dropdown-item" href="<?php echo $baseDir; ?>pages/gift-cards.php"><i class="bi bi-gift"></i> Gift Cards</a></li>
    </ul>
</li>
```

### Option 2: Add to Account Dropdown (Logged In)

```php
<!-- Add after "Your Lists" -->
<li><a class="dropdown-item" href="<?php echo $baseDir; ?>auth/loyalty-rewards.php"><i class="bi bi-trophy"></i> Loyalty Rewards</a></li>
```

### Option 3: Add to Footer

Footer file location: `/includes/footer.php` (if exists) or end of `layout.php`

Suggested footer sections:
- **Customer Service**: FAQ, Contact Us, Shipping Policy, Returns
- **Shop**: All Products, Events, Blog
- **Account**: Login, Register, Orders, Loyalty Program
- **Gift Cards**: Purchase Gift Cards, Check Balance

---

## Quick Access URLs

| Feature | URL | Status |
|---------|-----|--------|
| Blog | `/blog/` | ✅ Works, Linked in Nav |
| FAQ | `/pages/faq.php` | ✅ Works, Linked in Account Dropdown |
| Gift Cards Purchase | `/pages/gift-cards.php` | ✅ Works, Linked in Account Dropdown |
| Gift Card Balance | `/pages/gift-card-balance.php` | ✅ Works, Linked via Gift Cards Page |
| Loyalty Rewards | `/auth/loyalty-rewards.php` | ✅ Works, Linked in Account Dropdown |
| Apply Coupon | Built into checkout | ✅ Visible in Cart |

---

## Next Steps

1. **Add navigation links** for Blog, FAQ, Gift Cards, Loyalty Rewards
2. **Update footer** with comprehensive link structure
3. **Create footer sections** for better organization
4. **Test all links** after adding to navigation
5. **Update TODO.md** to mark navigation as complete

---

## Related Documentation

- [TODO.md](TODO.md) - Full project task list
- [FILE_ORGANIZATION.md](FILE_ORGANIZATION.md) - File locations after reorganization
- [CART_AND_SIZE_IMPROVEMENTS.md](CART_AND_SIZE_IMPROVEMENTS.md) - Cart/size system
- [CODING_STANDARDS.md](CODING_STANDARDS.md) - Code organization standards

---

---

## Recent Updates (November 18, 2025)

### Navigation Improvements
- ✅ Added Blog to main navigation
- ✅ Added Loyalty Rewards to account dropdown
- ✅ Added Gift Cards to account dropdown
- ✅ Added FAQ to account dropdown
- ✅ Fixed Loyalty Rewards page redirect issue (session_start() missing)
- ✅ Standardized hero section styling across all pages
- ✅ Fixed footer positioning on empty content pages (Blog, Compare)
- ✅ Updated all pages to use Bootstrap 5.3.2
- ✅ Removed duplicate Bootstrap JS to fix dropdown issues
- ✅ Fixed Events link to point to public page (`/pages/events.php` instead of `/admin/Events.php`)
- ✅ Created public Events page that doesn't require authentication

### Events Page Updates
- ✅ Created `/pages/events.php` - Public-facing events calendar
- ✅ Updated header navigation to link to public events page
- ✅ Maintains same event display functionality without admin access requirement
- ✅ Removed "Entered By" field from public view (admin-only information)

### Gift Cards Page Fixes
- ✅ Fixed hero section styling on gift-card-balance.php to match other pages
- ✅ Fixed "Start Shopping" button URL (corrected capitalization: `products/products.php`)
- ✅ Fixed Contact Us link (removed incorrect `info/` subdirectory)
- ✅ Removed duplicate Bootstrap JS loading that caused dropdown malfunction

### Styling Fixes
- All hero sections now use consistent brown-to-green gradient
- All hero text uses brown color (var(--prt-brown))
- Min-height added to prevent footer from appearing mid-page
- Consistent page-header class across all pages
- Gift card balance page now uses standard `.page-header` instead of custom `.balance-header`

---

## ✅ Feature Configuration System (COMPLETE)

**Admin Location**: `http://localhost:8301/admin/features`

**Purpose**: Enable/disable features site-wide from admin panel

**Configurable Features**:
- FAQ
- Loyalty Points
- Digital Downloads
- Specialty Products
- Gift Cards
- Wishlists
- Blog
- Events
- Reviews
- Admin Link

**Frontend Integration**:
- `/config/features.php` - Loads features from API, caches in session
- `isFeatureEnabled($name)` - Helper function to check feature status
- Header nav items conditionally hidden
- Account dropdown items conditionally hidden
- Wishlist heart icons hidden when disabled
- Cart/checkout loyalty points calculation skipped when disabled

**Admin Integration**:
- Sidebar links grayed out with "(off)" indicator for disabled features
- Features loaded from API and cached in session

**API Endpoints**:
- `GET /api/v1/admin/settings/features` - Get all feature settings
- `PUT /api/v1/admin/settings/features` - Update feature settings

**Database**:
- Settings stored in `settings` table with `setting_group = 'features'`

---

*Last Updated: November 25, 2025

## Purchase Order Management

**Database Tables**:
- `purchase_orders` - Main PO records
- `purchase_order_items` - Line items
- `purchase_order_receiving` - Receiving log

**API Controller**:
- `pecos-backendadmin-api/app/Http/Controllers/Api/V1/Admin/PurchaseOrderController.php`

**Frontend Pages** (Laravel Admin):
- `pecos-backend-admin-site/resources/views/admin/purchase-orders.blade.php`
- `pecos-backend-admin-site/resources/views/admin/inventory-receive.blade.php`

**Routes**:
- `pecos-backendadmin-api/routes/api.php` (lines 285-295)

