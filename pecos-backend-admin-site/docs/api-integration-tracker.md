# API Integration Tracker

## Overview

This document tracks the progress of connecting the pecos-backend-admin-site (Laravel Blade UI) to the pecos-backendadmin-api (REST API).

**API Base URL**: `http://localhost:8300/api/v1`
**Admin Site URL**: `http://localhost:8301/admin`

---

## Infrastructure Created

### ApiService
- **File**: `app/Services/ApiService.php`
- **Purpose**: Centralized HTTP client for all API calls
- **Features**: GET, POST, PUT, DELETE methods with error handling and logging

### AdminController
- **File**: `app/Http/Controllers/AdminController.php`
- **Purpose**: Controller that fetches data from API and passes to views

---

## Integration Status by Page

### Dashboard (`/admin`)
- **Status**: ✅ Complete
- **API Endpoints**:
  - `GET /admin/orders/stats` - Order statistics
  - `GET /admin/customers/stats` - Customer statistics
  - `GET /admin/inventory/stats` - Inventory statistics
- **Data Passed**: `$orderStats`, `$customerStats`, `$inventoryStats`
- **Blade File**: `admin/dashboard.blade.php`
- **Notes**: Stats cards and quick links updated with real data

### Products (`/admin/products`)
- **Status**: ✅ Complete
- **API Endpoints**:
  - `GET /products` - List products with filters and pagination
  - `GET /categories` - List categories for filter dropdown
- **Data Passed**: `$products` (with pagination), `$categories`, `$filters`
- **Blade File**: `admin/products.blade.php`
- **Notes**: Table with filters, pagination, add/delete modals. Uses API field names (ItemNumber, ShortDescription, UnitPrice, stock_quantity)

### Categories (`/admin/categories`)
- **Status**: ✅ Complete
- **API Endpoints**:
  - `GET /categories` - List all categories
- **Data Passed**: `$categories`
- **Blade File**: `admin/categories.blade.php`
- **Notes**: Table with real categories, product counts, levels, parent dropdown in modal

### Orders (`/admin/orders`)
- **Status**: ✅ Complete
- **API Endpoints**:
  - `GET /admin/orders` - List orders with filters
  - `GET /admin/orders/stats` - Order statistics
- **Data Passed**: `$orders`, `$stats`, `$filters`
- **Blade File**: `admin/orders.blade.php`
- **Notes**: Stats cards, filters, table with real data, pagination

### Order Detail (`/admin/orders/{id}`)
- **Status**: ✅ Complete
- **API Endpoints**:
  - `GET /admin/orders/{id}` - Single order with items
- **Data Passed**: `$order`
- **Blade File**: `admin/order-detail.blade.php`
- **Notes**: Items table, totals, timeline, customer info, shipping address with real data

### Customers (`/admin/customers`)
- **Status**: ✅ Complete
- **API Endpoints**:
  - `GET /admin/customers` - List customers with filters
  - `GET /admin/customers/stats` - Customer statistics
- **Data Passed**: `$customers`, `$stats`, `$filters`
- **Blade File**: `admin/customers.blade.php`
- **Notes**: Stats cards, filters, table with real data, pagination

### Customer Detail (`/admin/customers/{id}`)
- **Status**: ✅ Complete
- **API Endpoints**:
  - `GET /admin/customers/{id}` - Customer details
  - `GET /admin/customers/{id}/orders` - Customer orders
- **Data Passed**: `$customer`, `$orders`
- **Blade File**: `admin/customer-detail.blade.php`
- **Notes**: Stats cards, order history, wishlist, contact info, loyalty with real data

### Inventory (`/admin/inventory`)
- **Status**: ✅ Complete
- **API Endpoints**:
  - `GET /admin/inventory/products` - Products with inventory data
  - `GET /admin/inventory/stats` - Inventory statistics
  - `GET /categories` - Categories for filter
- **Data Passed**: `$products`, `$stats`, `$categories`, `$filters`
- **Blade File**: `admin/inventory.blade.php`
- **Notes**: Stats cards, filters, table with stock adjustment modal, pagination

### Stock Alerts (`/admin/inventory/alerts`)
- **Status**: ✅ Complete
- **API Endpoints**:
  - `GET /admin/inventory/stock-alerts` - Low stock products
  - `GET /admin/inventory/stats` - Statistics
- **Data Passed**: `$alerts`, `$stats`
- **Blade File**: `admin/stock-alerts.blade.php`
- **Notes**: Stats cards, out of stock table, low stock table with real data

### Inventory Reports (`/admin/inventory/reports`)
- **Status**: 🔄 In Progress
- **API Endpoints**:
  - `GET /admin/inventory/stats` - Inventory statistics
- **Data Passed**: `$stats`
- **Blade File**: `admin/inventory-reports.blade.php`
- **Notes**: Need to update blade to display reports

### Blog (`/admin/blog`)
- **Status**: ✅ Complete
- **API Endpoints**:
  - `GET /admin/blog` - List blog posts
- **Data Passed**: `$posts`, `$filters`
- **Blade File**: `admin/blog.blade.php`
- **Notes**: Filters, table with posts, pagination with real data

### Events (`/admin/events`)
- **Status**: ✅ Complete
- **API Endpoints**:
  - `GET /admin/events` - List events
- **Data Passed**: `$events`, `$filters`
- **Blade File**: `admin/events.blade.php`
- **Notes**: Search, table with events, pagination with real data

### Reviews (`/admin/reviews`)
- **Status**: ✅ Complete
- **API Endpoints**:
  - `GET /admin/reviews` - List reviews
- **Data Passed**: `$reviews`, `$filters`
- **Blade File**: `admin/reviews.blade.php`
- **Notes**: Filters, table with star ratings, pagination with real data

### FAQ Statistics (`/admin/faq-stats`)
- **Status**: 🔄 In Progress
- **API Endpoints**:
  - `GET /admin/faq-stats` - FAQ statistics
- **Data Passed**: `$stats`
- **Blade File**: `admin/faq-stats.blade.php`
- **Notes**: Need to update blade to display stats

### Gift Cards (`/admin/gift-cards`)
- **Status**: ✅ Complete
- **API Endpoints**:
  - `GET /admin/gift-cards` - List gift cards
  - `GET /admin/gift-cards/stats` - Gift card statistics
- **Data Passed**: `$giftCards`, `$stats`, `$filters`
- **Blade File**: `admin/gift-cards.blade.php`
- **Notes**: Stats cards, filters, table with gift cards, pagination with real data

### Gift Card Detail (`/admin/gift-cards/{id}`)
- **Status**: ✅ Complete
- **API Endpoints**:
  - `GET /admin/gift-cards/{id}` - Gift card with transactions
- **Data Passed**: `$giftCard`
- **Blade File**: `admin/gift-card-detail.blade.php`
- **Notes**: Balance cards, transaction history, recipient/purchaser info, status with real data

### Coupons (`/admin/coupons`)
- **Status**: ✅ Complete
- **API Endpoints**:
  - `GET /admin/coupons` - List coupons with filters
- **Data Passed**: `$coupons` (with pagination), `$filters`
- **Blade File**: `admin/coupons.blade.php`
- **Notes**: Filters, table with coupons, pagination with real data

### Users (`/admin/users`)
- **Status**: ✅ Complete
- **API Endpoints**:
  - `GET /admin/users` - List users with filters
  - `GET /admin/users/stats` - User statistics
- **Data Passed**: `$users` (with pagination), `$stats`, `$filters`
- **Blade File**: `admin/users.blade.php`
- **Notes**: Stats cards, filters, table with users, pagination with real data

---

### Loyalty (`/admin/loyalty`)
- **Status**: ✅ Complete
- **API Endpoints**:
  - `GET /admin/loyalty/stats` - Loyalty program statistics
  - `GET /admin/loyalty/members` - List members with filters
  - `GET /admin/loyalty/tiers` - Tier configuration
- **Data Passed**: `$members` (with pagination), `$stats`, `$tiers`, `$filters`
- **Blade File**: `admin/loyalty.blade.php`
- **Notes**: Stats cards, members table with tier badges, tier configuration table

### Sales Dashboard (`/admin/sales-dashboard`)
- **Status**: ✅ Complete
- **API Endpoints**:
  - `GET /admin/orders/stats` - Order statistics
  - `GET /admin/customers/stats` - Customer statistics
  - `GET /admin/inventory/stats` - Inventory statistics
- **Data Passed**: `$orderStats`, `$customerStats`, `$inventoryStats`
- **Blade File**: `admin/sales-dashboard.blade.php`
- **Notes**: Stats cards with real revenue, orders, avg order value, customer count

### Dropshippers (`/admin/dropshippers`)
- **Status**: ✅ Complete
- **API Endpoints**:
  - `GET /admin/dropshippers` - List dropshippers with stats
  - `GET /admin/dropshippers/{id}` - Dropshipper details
  - `POST /admin/dropshippers` - Create dropshipper
  - `PUT /admin/dropshippers/{id}` - Update dropshipper
  - `DELETE /admin/dropshippers/{id}` - Delete dropshipper
  - `POST /admin/dropshippers/{id}/approve` - Approve pending
  - `POST /admin/dropshippers/{id}/toggle-suspend` - Toggle suspend
  - `POST /admin/dropshippers/{id}/regenerate-key` - Regenerate API key
- **Blade File**: `admin/dropshippers.blade.php`
- **Notes**: Full CRUD, approval flow, suspend/activate, API key management

### Dropship Orders (`/admin/dropship/orders`)
- **Status**: ✅ Complete
- **API Endpoints**:
  - `GET /admin/dropship/orders` - List orders with filters
  - `GET /admin/dropship/orders/{id}` - Order details
  - `PUT /admin/dropship/orders/{id}/status` - Update status with tracking
- **Blade File**: `admin/dropship-orders.blade.php`
- **Notes**: Stats cards, filters, status updates with carrier/tracking

### API Logs (`/admin/api-logs`)
- **Status**: ✅ Complete
- **API Endpoints**:
  - `GET /admin/api-logs` - Paginated logs with filters
  - `GET /admin/api-logs/{id}` - Single log detail
  - `GET /admin/api-logs/stats` - 24h statistics
  - `GET /admin/api-logs/dropshippers` - Dropshippers for filter
  - `GET /admin/api-logs/endpoints` - Unique endpoints for filter
- **Blade File**: `admin/api-logs.blade.php`
- **Notes**: Real paginated data, stats cards, filter dropdowns, detail modal

### Tax Settings (`/admin/settings/tax`)
- **Status**: ✅ Complete
- **API Endpoints**:
  - `GET /admin/tax-rates` - List tax rates
  - `POST /admin/tax-rates` - Create tax rate
  - `DELETE /admin/tax-rates/{id}` - Delete tax rate
  - `GET /admin/tax-exemptions` - List exemptions
- **Blade File**: `admin/tax-settings.blade.php`
- **Notes**: Tax rates with state/city/county, exemptions table

### Shipping Settings (`/admin/settings/shipping`)
- **Status**: ✅ Complete
- **API Endpoints**:
  - `GET /admin/shipping-zones` - List zones
  - `POST /admin/shipping-zones` - Create zone
  - `DELETE /admin/shipping-zones/{id}` - Delete zone
- **Blade File**: `admin/shipping.blade.php`
- **Notes**: Shipping zones and carriers

### Settings (`/admin/settings`)
- **Status**: ✅ Complete
- **API Endpoints**:
  - `GET /admin/settings` - Get all settings grouped by category
  - `GET /admin/settings/{group}` - Get settings for specific group
  - `PUT /admin/settings/{group}` - Update settings for a group
- **Data Passed**: Loaded via JavaScript from API
- **Blade File**: `admin/settings.blade.php`
- **Notes**: 7 setting groups (general, store, email, payment, security, api, notifications), 50 default settings populated, save per group

---

## Pages Not Yet Connected (Using Static Data)

| Page | Route | Priority | Notes |
|------|-------|----------|-------|
| Reports | `/admin/reports` | Medium | Complex aggregations needed |
| Bulk Update | `/admin/inventory/bulk-update` | Medium | POST operation |
| Inventory Export | `/admin/inventory/export` | Medium | Download operation |

---

## Completed Integration Summary

### Phase 1 - Core Pages (Complete)
- Dashboard with real stats
- Orders list and detail
- Customers list and detail
- Products list with filters
- Inventory management (with product images)
- Categories management

### Phase 2 - Content & Alerts (Complete)
- Stock Alerts
- Blog management
- Events management
- Reviews management
- Gift Cards list and detail
- FAQ Statistics

### Phase 3 - User & Coupon Management (Complete)
- Coupons management with filters and CRUD
- Users management with stats and CRUD modals

### Phase 4 - Loyalty & Sales (Complete)
- Loyalty program with members, stats, tiers
- Sales Dashboard with real stats

### Phase 5 - Drop Shipping & Settings (Complete)
- Dropshippers management with full CRUD, approve, suspend, API key regeneration
- Dropship Orders with status updates and tracking
- API Logs with real paginated data, stats, and filters
- Tax Settings with US/Canada/Mexico rates and city-level taxes
- Shipping Settings with zones and carriers
- General Settings with 7 groups (general, store, email, payment, security, api, notifications)

## Next Steps

### Short-term
1. **Reports** - Need API endpoints for complex aggregations
2. **Categories CRUD** - Connect create/edit/delete modals to API

### Medium-term (Features)
1. **Bulk Operations** - POST endpoints for inventory bulk updates
2. **General Settings** - Store configuration management

---

## Future Enhancements

### High Priority
1. **AJAX Form Submissions** - Connect create/edit/delete buttons to API endpoints
2. **Form Validation** - Client and server-side validation with error display
3. **Stock Adjustment Modal** - Connect to POST /admin/inventory/adjust-stock
4. **Order Status Updates** - Connect dropdown to PUT /admin/orders/{id}/status

### Medium Priority
1. **File Uploads** - Product images, blog images, CSV imports
2. **Export Functionality** - Download CSV/PDF using export endpoints
3. **Real-time Search** - Debounced search with API calls
4. **Pagination Improvements** - Page size selector, showing count
5. **Sorting** - Connect table headers to API sort parameters

### Low Priority
1. **Dashboard Charts** - Connect Chart.js to real API data
2. **Activity Logging** - Track admin actions in database
3. **Notification System** - Toast messages for operations
4. **Print Functionality** - Invoice and packing slip generation
5. **Email Integration** - Order confirmation, shipping notifications

---

## API Endpoints Available

### Orders
- `GET /admin/orders` - List orders
- `GET /admin/orders/stats` - Statistics
- `GET /admin/orders/{id}` - Single order
- `PUT /admin/orders/{id}/status` - Update status
- `POST /admin/orders/{id}/notes` - Add notes
- `POST /admin/orders/{id}/refund` - Process refund

### Customers
- `GET /admin/customers` - List customers
- `GET /admin/customers/stats` - Statistics
- `GET /admin/customers/{id}` - Single customer
- `GET /admin/customers/{id}/orders` - Customer orders
- `PUT /admin/customers/{id}` - Update customer

### Inventory
- `GET /admin/inventory/stats` - Statistics
- `GET /admin/inventory/products` - Products with stock
- `GET /admin/inventory/alerts` - Low stock alerts
- `GET /admin/inventory/stock-alerts` - Stock alerts
- `POST /admin/inventory/adjust-stock` - Adjust stock
- `POST /admin/inventory/bulk-adjust-csv` - Bulk CSV update
- `POST /admin/inventory/bulk-adjust-manual` - Manual bulk update

### Categories
- `GET /categories` - List categories
- `POST /admin/categories` - Create category
- `PUT /admin/categories/{code}` - Update category
- `DELETE /admin/categories/{code}` - Delete category
- `PUT /admin/categories/reorder` - Reorder categories

### Gift Cards
- `GET /admin/gift-cards` - List gift cards
- `GET /admin/gift-cards/stats` - Statistics
- `GET /admin/gift-cards/{id}` - Single gift card
- `POST /admin/gift-cards` - Create gift card
- `PUT /admin/gift-cards/{id}/void` - Void gift card
- `POST /admin/gift-cards/{id}/adjust` - Adjust balance

### Export
- `GET /admin/export/orders` - Export orders CSV
- `GET /admin/export/customers` - Export customers CSV
- `GET /admin/export/products` - Export products CSV

### Content
- `GET /admin/blog` - List blog posts
- `GET /admin/reviews` - List reviews
- `GET /admin/events` - List events
- `GET /admin/faq-stats` - FAQ statistics

### Coupons
- `GET /admin/coupons` - List coupons
- `POST /admin/coupons` - Create coupon
- `PUT /admin/coupons/{id}` - Update coupon
- `DELETE /admin/coupons/{id}` - Delete coupon

### Users
- `GET /admin/users` - List users
- `GET /admin/users/stats` - User statistics
- `GET /admin/users/{id}` - Single user
- `POST /admin/users` - Create user
- `PUT /admin/users/{id}` - Update user
- `DELETE /admin/users/{id}` - Delete user

### Loyalty
- `GET /admin/loyalty/stats` - Loyalty program statistics
- `GET /admin/loyalty/account/{userId}` - Member account with transactions
- `POST /admin/loyalty/adjust-points` - Adjust member points
- `GET /admin/loyalty/tiers` - List tiers
- `GET /admin/loyalty/rewards` - List rewards

### Dropshippers
- `GET /admin/dropshippers` - List dropshippers with stats
- `GET /admin/dropshippers/{id}` - Dropshipper details
- `POST /admin/dropshippers` - Create dropshipper
- `PUT /admin/dropshippers/{id}` - Update dropshipper
- `DELETE /admin/dropshippers/{id}` - Delete dropshipper
- `POST /admin/dropshippers/{id}/approve` - Approve pending
- `POST /admin/dropshippers/{id}/toggle-suspend` - Toggle suspend
- `POST /admin/dropshippers/{id}/regenerate-key` - Regenerate API key

### Dropship Orders
- `GET /admin/dropship/orders` - List orders with filters
- `GET /admin/dropship/orders/{id}` - Order details
- `PUT /admin/dropship/orders/{id}/status` - Update status with tracking

### API Logs
- `GET /admin/api-logs` - Paginated logs with filters
- `GET /admin/api-logs/{id}` - Single log detail
- `GET /admin/api-logs/stats` - 24h statistics
- `GET /admin/api-logs/dropshippers` - Dropshippers for filter dropdown
- `GET /admin/api-logs/endpoints` - Unique endpoints for filter dropdown

### Tax Settings
- `GET /admin/tax-rates` - List tax rates
- `POST /admin/tax-rates` - Create tax rate
- `DELETE /admin/tax-rates/{id}` - Delete tax rate
- `GET /admin/tax-exemptions` - List exemptions

### Shipping Settings
- `GET /admin/shipping-zones` - List zones
- `POST /admin/shipping-zones` - Create zone
- `DELETE /admin/shipping-zones/{id}` - Delete zone

### Settings
- `GET /admin/settings` - Get all settings grouped by category
- `GET /admin/settings/{group}` - Get settings for specific group
- `PUT /admin/settings/{group}` - Update settings for a group

### Feature Configuration
- `GET /admin/settings/features` - Get all feature flags
- `PUT /admin/settings/features` - Update feature flags
- Features: FAQ, Loyalty, Digital Downloads, Specialty Products, Gift Cards, Wishlists, Blog, Events, Reviews, Admin Link

---

## Related Documentation

- [International Tax Guide](./international-tax-guide.md) - How international sales tax works

---

**Last Updated**: 2025-11-22

## Purchase Orders
| Page | API Endpoint | Status | Notes |
|------|--------------|--------|-------|
| purchase-orders.blade.php | /api/v1/admin/purchase-orders | ✅ Complete | Full CRUD operations |
| purchase-orders.blade.php | /api/v1/admin/purchase-orders/stats | ✅ Complete | Statistics dashboard |
| inventory-receive.blade.php | /api/v1/admin/purchase-orders/pending-receiving | ✅ Complete | Pending POs list |
| inventory-receive.blade.php | /api/v1/admin/purchase-orders/{id}/receive | ✅ Complete | Receive items |

