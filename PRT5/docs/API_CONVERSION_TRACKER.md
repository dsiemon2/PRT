# PRT2 SQL to API Conversion Tracker

This document tracks the progress of converting inline SQL queries to Laravel API calls.

## Conversion Pattern
All converted files follow this pattern:
```php
// ============ API INTEGRATION ============
// ... API code ...
// ============ ORIGINAL SQL CODE (COMMENTED OUT) ============
/* ... original SQL ... */
// ============ END ORIGINAL SQL CODE ============
```

---

## Products Pages
- [x] `products/products.php` - Product listing
- [x] `products/product-detail.php` - Single product view
- [x] `products/special-category.php` - Special category products
- [x] `products/special-products.php` - Special products listing
- [x] `products/inventory.php` - Complete inventory list

## Blog Pages
- [x] `blog/index.php` - Blog listing
- [x] `blog/post.php` - Single blog post

## Other Pages
- [x] `pages/events.php` - Events listing
- [x] `pages/faq.php` - FAQ page

## Cart/Checkout
- [x] `cart/cart.php` - Shopping cart
- [x] `cart/AddToCart.php` - Add to cart handler
- [x] `cart/checkout.php` - Checkout page (states dropdown)
- [x] `cart/process_order.php` - Order processing
- [x] `cart/order-confirmation.php` - Order confirmation

## Includes
- [x] `includes/common.php` - MakeMenu() function

## Auth/Account Pages
- [x] `auth/orders.php` - User order history
- [x] `auth/lists.php` - Wishlist/saved items
- [x] `auth/login.php` - User login (uses auth.php)
- [x] `auth/register.php` - User registration (uses auth.php)
- [x] `auth/auth.php` - Login/Register handler
- [x] `auth/account.php` - Account dashboard
- [x] `auth/buy-again.php` - Buy again feature
- [x] `auth/wishlist-handler.php` - Wishlist AJAX handler
- [x] `auth/account-settings.php` - Account settings
- [x] `auth/loyalty-rewards.php` - Loyalty points/rewards
- [x] `auth/settings-handler.php` - Settings AJAX handler (uses direct SQL for write operations - keep as is for transaction safety)

## Admin Pages
- [x] `admin/index.php` - Admin dashboard (no SQL - static links)
- [x] `admin/inventory-dashboard.php` - Inventory dashboard
- [x] `admin/stock-alerts.php` - Stock alerts management
- [x] `admin/inventory-reports.php` - Inventory reports
- [x] `admin/faq-statistics.php` - FAQ statistics
- [x] `admin/inventory-bulk-update.php` - Bulk stock update
- [x] `admin/inventory-export.php` - Export to CSV
- [x] `admin/inventory-edit.php` - Edit inventory item
- [ ] `admin/reviews-management.php` - Customer reviews management (complex file uploads)
- [ ] `admin/blog-management.php` - Blog post management (complex file uploads)
- [ ] `admin/Events-Management.php` - Events management (complex file uploads)

---

## API Endpoints Created

### Public Endpoints
- `GET /api/v1/products` - Products listing
- `GET /api/v1/products/{upc}` - Single product by UPC
- `GET /api/v1/products/by-id/{id}` - Single product by ID
- `GET /api/v1/categories` - Categories listing
- `GET /api/v1/categories/{id}` - Single category
- `GET /api/v1/blog` - Blog posts
- `GET /api/v1/blog/{slug}` - Single blog post
- `GET /api/v1/blog/categories` - Blog categories
- `GET /api/v1/blog/recent` - Recent blog posts
- `GET /api/v1/events` - Events listing
- `GET /api/v1/faqs` - FAQs listing
- `GET /api/v1/faqs/categories` - FAQ categories
- `GET /api/v1/states` - US states list
- `POST /api/v1/orders/guest` - Guest order creation
- `GET /api/v1/orders/lookup/{orderNumber}` - Order by number
- `GET /api/v1/orders/user/{userId}` - User orders
- `GET /api/v1/wishlist/user/{userId}` - User wishlist
- `DELETE /api/v1/wishlist/user/{userId}/{productId}` - Remove wishlist item
- `GET /api/v1/users/{userId}/profile` - User profile with stats
- `POST /api/v1/users/login` - Login by credentials
- `POST /api/v1/users/register` - Register user
- `GET /api/v1/users/{userId}/buy-again` - Buy again items
- `POST /api/v1/wishlist/user/{userId}/add/{productId}` - Add to wishlist
- `POST /api/v1/wishlist/user/{userId}/toggle/{productId}` - Toggle wishlist item
- `GET /api/v1/wishlist/user/{userId}/check/{productId}` - Check if in wishlist
- `DELETE /api/v1/wishlist/user/{userId}/clear` - Clear wishlist
- `GET /api/v1/users/{userId}/settings` - All user settings
- `GET /api/v1/loyalty/user/{userId}` - User loyalty account
- `GET /api/v1/loyalty/user/{userId}/transactions` - User loyalty transactions
- `GET /api/v1/loyalty/tiers` - All loyalty tiers
- `GET /api/v1/loyalty/rewards/{userId}` - User available rewards
- `GET /api/v1/admin/inventory/stats` - Inventory statistics
- `GET /api/v1/admin/inventory/products` - Inventory products with filters
- `GET /api/v1/admin/inventory/stock-alerts` - Stock alerts
- `GET /api/v1/admin/inventory/reports` - Inventory reports

### Protected Endpoints (require auth)
- See `pecos-backendadmin-api/routes/api.php` for full list

---

## Notes
- API runs on `http://localhost:8300/api/v1`
- PRT2 frontend runs on `http://localhost:8300`
- All original SQL is preserved in comments for easy rollback
- Sort orders match original SQL queries

Last Updated: 2025-11-23 - Comprehensive API documentation with all 107 endpoints. Added API logging with country tracking, fixed stats loading, updated all sale prices, fixed dropshipper status.

## Conversion Summary

### API Statistics
- **Total Endpoints**: 107
- **Fully Implemented**: 104 (97.2%)
- **Partial/Stub**: 3 (2.8%)
- **Public Routes**: 103 (96.3%)

### Completed Pages: 33
- Products: 5 pages
- Blog: 2 pages
- Other pages: 2 pages
- Cart/Checkout: 5 pages
- Includes: 1 file
- Auth/Account: 11 pages
- Admin: 8 pages (index, inventory-dashboard, stock-alerts, inventory-reports, faq-statistics, inventory-bulk-update, inventory-export, inventory-edit)

### Pages Intentionally Kept with Direct SQL:
These files use direct SQL by design and do not need API conversion:
- `admin/reviews-management.php` - Complex file uploads for review images
- `admin/blog-management.php` - Complex file uploads for blog images
- `admin/Events-Management.php` - Complex file uploads for event images
- `auth/settings-handler.php` - Transaction safety for user settings (see docs/settings-handler-documentation.md)

### New API Endpoints Created: 100+

### Admin Site Pages (http://localhost:8301):
- Dashboard with stats cards and charts
- Orders management (list, view, status update, refund, notes)
- Customers management (list, view, orders, update)
- Inventory dashboard (stats, products, filters)
- Stock alerts (low stock, out of stock, add stock, reorder)
- Inventory reports (with export)
- Loyalty program (members, tiers, adjust points)
- Coupons management (CRUD)
- Reviews management (approve/reject)
- Blog management (CRUD posts)
- Events management (CRUD)
- Gift cards management (create, void, adjust balance)
- Users/Staff management (CRUD with roles)
- Categories management (CRUD with images)
- Export functionality (orders, customers, products)

### New Admin Endpoints Added:
- `GET /api/v1/admin/stock-alerts/full` - Stock alerts with full filters
- `POST /api/v1/admin/stock-alerts/resolve` - Resolve single alert
- `POST /api/v1/admin/stock-alerts/resolve-all` - Resolve all alerts
- `GET /api/v1/admin/inventory/reports-export` - Reports with full export data
- `GET /api/v1/admin/inventory/product/{id}` - Get product for editing
- `GET /api/v1/admin/inventory/bulk-products` - Products for bulk update
- `GET /api/v1/admin/inventory/export-data` - Export inventory data
- `POST /api/v1/admin/inventory/adjust-stock` - Adjust stock for a product
- `POST /api/v1/admin/inventory/update-settings` - Update inventory settings
- `POST /api/v1/admin/inventory/bulk-adjust-csv` - Bulk adjust via CSV data
- `POST /api/v1/admin/inventory/bulk-adjust-manual` - Bulk adjust selected products
- `GET /api/v1/admin/faq-stats` - FAQ statistics for admin
- `GET /api/v1/admin/loyalty/stats` - Loyalty program statistics
- `GET /api/v1/admin/loyalty/members` - List loyalty members with tiers
- `GET /api/v1/admin/loyalty/members/{userId}/transactions` - Member transactions
- `POST /api/v1/admin/loyalty/adjust-points` - Adjust member points
- `GET /api/v1/admin/loyalty/tiers` - Loyalty tier configuration
- `PUT /api/v1/admin/loyalty/tiers/{id}` - Update loyalty tier
- `GET /api/v1/admin/loyalty/rewards` - Available rewards
- `GET /api/v1/admin/orders` - List all orders with filters
- `GET /api/v1/admin/orders/stats` - Order statistics
- `GET /api/v1/admin/orders/{id}` - Order details
- `PUT /api/v1/admin/orders/{id}/status` - Update order status
- `POST /api/v1/admin/orders/{id}/notes` - Add order note
- `POST /api/v1/admin/orders/{id}/refund` - Process refund
- `GET /api/v1/admin/customers` - List customers
- `GET /api/v1/admin/customers/stats` - Customer statistics
- `GET /api/v1/admin/customers/{id}` - Customer details
- `GET /api/v1/admin/customers/{id}/orders` - Customer orders
- `PUT /api/v1/admin/customers/{id}` - Update customer
- `GET /api/v1/admin/users` - List admin users
- `GET /api/v1/admin/users/stats` - User statistics
- `GET /api/v1/admin/users/{id}` - User details
- `POST /api/v1/admin/users` - Create user
- `PUT /api/v1/admin/users/{id}` - Update user
- `DELETE /api/v1/admin/users/{id}` - Delete user
- `GET /api/v1/admin/coupons` - List coupons
- `POST /api/v1/admin/coupons` - Create coupon
- `PUT /api/v1/admin/coupons/{id}` - Update coupon
- `DELETE /api/v1/admin/coupons/{id}` - Delete coupon
- `GET /api/v1/admin/reviews` - List reviews with status filter
- `PATCH /api/v1/admin/reviews/{id}/status` - Update review status
- `GET /api/v1/admin/blog` - List blog posts (admin)
- `GET /api/v1/admin/blog/{id}` - Blog post by ID
- `POST /api/v1/admin/blog` - Create blog post
- `PUT /api/v1/admin/blog/{id}` - Update blog post
- `DELETE /api/v1/admin/blog/{id}` - Delete blog post
- `GET /api/v1/admin/events` - List events (admin)
- `POST /api/v1/admin/events` - Create event
- `PUT /api/v1/admin/events/{id}` - Update event
- `DELETE /api/v1/admin/events/{id}` - Delete event
- `GET /api/v1/admin/gift-cards` - List gift cards
- `GET /api/v1/admin/gift-cards/stats` - Gift card statistics
- `POST /api/v1/admin/gift-cards` - Create gift card
- `GET /api/v1/admin/gift-cards/balance/{code}` - Check balance
- `PUT /api/v1/admin/gift-cards/{id}/void` - Void gift card
- `POST /api/v1/admin/gift-cards/{id}/adjust` - Adjust balance
- `POST /api/v1/admin/categories` - Create category
- `PUT /api/v1/admin/categories/{id}` - Update category
- `DELETE /api/v1/admin/categories/{id}` - Delete category
- `GET /api/v1/admin/export/orders` - Export orders
- `GET /api/v1/admin/export/customers` - Export customers
- `GET /api/v1/admin/export/products` - Export products

### Gift Card Endpoints
- `GET /api/v1/admin/gift-cards` - List gift cards with filters (status, search, dates)
- `GET /api/v1/admin/gift-cards/stats` - Gift card statistics
- `GET /api/v1/admin/gift-cards/{id}` - Single gift card with transactions
- `POST /api/v1/admin/gift-cards` - Create new gift card
- `GET /api/v1/admin/gift-cards/balance/{code}` - Check balance by code
- `PUT /api/v1/admin/gift-cards/{id}/void` - Void gift card
- `POST /api/v1/admin/gift-cards/{id}/adjust` - Adjust gift card balance

### Tax Settings Endpoints
- `GET /api/v1/admin/tax/rates` - List all tax rates
- `GET /api/v1/admin/tax/settings` - Get tax settings
- `POST /api/v1/admin/tax/settings` - Update tax settings
- `POST /api/v1/admin/tax/rates` - Create tax rate
- `PUT /api/v1/admin/tax/rates/{id}` - Update tax rate
- `DELETE /api/v1/admin/tax/rates/{id}` - Delete tax rate
- `GET /api/v1/admin/tax/classes` - List tax classes
- `POST /api/v1/admin/tax/classes` - Create tax class
- `DELETE /api/v1/admin/tax/classes/{id}` - Delete tax class
- `GET /api/v1/admin/tax/report` - Tax collected report
- `GET /api/v1/admin/tax/exemptions` - List tax exemptions
- `GET /api/v1/admin/tax/customers-for-exemption` - Get customers without exemptions
- `POST /api/v1/admin/tax/exemptions` - Create tax exemption
- `PUT /api/v1/admin/tax/exemptions/{id}/revoke` - Revoke tax exemption

### Shipping Settings Endpoints
- `GET /api/v1/admin/shipping/zones` - List shipping zones with methods
- `POST /api/v1/admin/shipping/zones` - Create shipping zone
- `PUT /api/v1/admin/shipping/zones/{id}` - Update shipping zone
- `DELETE /api/v1/admin/shipping/zones/{id}` - Delete shipping zone
- `POST /api/v1/admin/shipping/methods` - Add shipping method
- `PUT /api/v1/admin/shipping/methods/{id}` - Update shipping method
- `DELETE /api/v1/admin/shipping/methods/{id}` - Delete shipping method
- `GET /api/v1/admin/shipping/classes` - List shipping classes
- `POST /api/v1/admin/shipping/classes` - Create shipping class
- `PUT /api/v1/admin/shipping/classes/{id}` - Update shipping class
- `DELETE /api/v1/admin/shipping/classes/{id}` - Delete shipping class
- `GET /api/v1/admin/shipping/settings` - Get shipping settings
- `POST /api/v1/admin/shipping/settings` - Update shipping settings
- `GET /api/v1/admin/shipping/carriers` - List carrier integrations
- `GET /api/v1/admin/shipping/carriers/{id}` - Get single carrier
- `PUT /api/v1/admin/shipping/carriers/{id}` - Update carrier credentials
- `POST /api/v1/admin/shipping/carriers/{id}/connect` - Connect carrier (simulated)
- `POST /api/v1/admin/shipping/carriers/{id}/disconnect` - Disconnect carrier

### Dropshipper Endpoints
- `GET /api/v1/admin/dropshippers` - List dropshippers with stats
- `GET /api/v1/admin/dropshippers/{id}` - Dropshipper details
- `POST /api/v1/admin/dropshippers` - Create dropshipper
- `PUT /api/v1/admin/dropshippers/{id}` - Update dropshipper
- `DELETE /api/v1/admin/dropshippers/{id}` - Delete dropshipper
- `POST /api/v1/admin/dropshippers/{id}/approve` - Approve pending
- `POST /api/v1/admin/dropshippers/{id}/toggle-suspend` - Toggle suspend
- `POST /api/v1/admin/dropshippers/{id}/regenerate-key` - Regenerate API key

### API Logs Endpoints
- `GET /api/v1/admin/api-logs` - List logs with country tracking
- `GET /api/v1/admin/api-logs/stats` - 24hr statistics
- `GET /api/v1/admin/api-logs/{id}` - Log entry details
- `GET /api/v1/admin/api-logs/dropshippers` - Dropshippers for filter
- `GET /api/v1/admin/api-logs/endpoints` - Unique endpoints list

### Settings Endpoints
- `GET /api/v1/admin/settings` - All settings by category
- `GET /api/v1/admin/settings/{group}` - Settings for group
- `PUT /api/v1/admin/settings/{group}` - Update group settings
- `GET /api/v1/admin/settings/features` - Feature flags
- `PUT /api/v1/admin/settings/features` - Update features

---

## Incomplete Features & TODOs

### STUB Implementations (need completion)
1. **Tax Report** (`TaxController.php:255-289`)
   - Currently returns hardcoded sample data
   - TODO: Calculate from actual order data

2. **Shipping Carrier Connection** (`ShippingController.php:402-437`)
   - Currently simulates connection
   - TODO: Implement real carrier API integration

3. **Category Reorder** (`routes/api.php:202`)
   - Route defined but implementation needs review

---

## Future Enhancements

### Security
- [ ] Add API token authentication for all admin endpoints
- [ ] Implement rate limiting
- [ ] Add request signing for dropshipper API calls
- [ ] Add audit logging for all admin changes

### Features
- [ ] Real-time inventory webhooks
- [ ] Bulk product import/export with validation
- [ ] Advanced reporting with custom date ranges
- [ ] Email notification system for orders/alerts
- [ ] Dashboard analytics improvements

### Integrations
- [ ] Real carrier API connections (UPS, FedEx, USPS)
- [ ] Payment gateway webhooks (Stripe, PayPal)
- [ ] Accounting software sync (QuickBooks)
- [ ] Analytics integration (Google Analytics)

### Frontend Conversions Still Needed
- [ ] `admin/reviews-management.php` - Complex file uploads
- [ ] `admin/blog-management.php` - Complex file uploads
- [ ] `admin/Events-Management.php` - Complex file uploads

---

**See also:** `pecos-backendadmin-api/docs/API_REFERENCE.md` for complete endpoint documentation with implementation status.

**Version**: 2.0
**Last Updated**: November 23, 2025
