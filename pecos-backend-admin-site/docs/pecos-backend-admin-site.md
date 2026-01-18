# Pecos River Traders Admin Site

## Overview

A modern Laravel Blade admin dashboard for Pecos River Traders e-commerce platform. This is a separate Laravel application that provides a comprehensive admin interface for managing all aspects of the online store.

**URL**: http://localhost:8301/admin
**Framework**: Laravel 12 with Blade templating
**Styling**: Bootstrap 5 + Custom CSS
**Charts**: Chart.js
**Database**: MySQL (pecosriver)

---

## What Has Been Done

### Core Setup

- [x] Created new Laravel 12 application at `C:\xampp\htdocs\pecos-backend-admin-site`
- [x] Configured database connection to `pecosriver` database
- [x] Set up file-based sessions
- [x] Created custom admin CSS with PRT brand colors
- [x] Implemented responsive sidebar navigation
- [x] Created main admin layout with header and navigation

### API Integration Infrastructure ✅ COMPLETE

- [x] **ApiService.php** - Centralized HTTP client for all API calls to `pecos-backendadmin-api`
- [x] **AdminController.php** - Controller that fetches data from API and passes to views
- [x] **Routes configured** - All admin routes using AdminController methods
- [x] **Error handling** - Graceful fallbacks when API unavailable

### Blade Pages Connected to Real API Data ✅ COMPLETE

| Page | Status | API Endpoints Used |
|------|--------|-------------------|
| Dashboard | ✅ | /admin/orders/stats, /admin/customers/stats, /admin/inventory/stats |
| Products | ✅ | /products, /categories |
| Orders | ✅ | /admin/orders, /admin/orders/stats |
| Order Detail | ✅ | /admin/orders/{id} |
| Customers | ✅ | /admin/customers, /admin/customers/stats |
| Customer Detail | ✅ | /admin/customers/{id}, /admin/customers/{id}/orders |
| Users | ✅ | /admin/users (CRUD with modals) |
| Inventory | ✅ | /admin/inventory/products, /admin/inventory/stats, /categories (with product images) |
| Stock Alerts | ✅ | /admin/inventory/stock-alerts, /admin/inventory/stats |
| Blog | ✅ | /admin/blog |
| Events | ✅ | /admin/events |
| Reviews | ✅ | /admin/reviews |
| Coupons | ✅ | /admin/coupons (CRUD with modals) |
| Gift Cards | ✅ | /admin/gift-cards, /admin/gift-cards/stats |
| Gift Card Detail | ✅ | /admin/gift-cards/{id} |
| Loyalty | ✅ | /admin/loyalty/account/{userId}, /admin/loyalty/stats |
| Dropshippers | ✅ | /admin/dropshippers (CRUD, approve, suspend, regenerate key) |
| Dropship Orders | ✅ | /admin/dropship/orders, /admin/dropship/orders/{id}/status |
| API Logs | ✅ | /admin/api-logs, /admin/api-logs/stats (paginated, filters) |
| Tax Settings | ✅ | /admin/tax-rates, /admin/tax-exemptions |
| Shipping | ✅ | /admin/shipping-zones |
| Settings | ✅ | /admin/settings (7 groups: general, store, email, payment, security, api, notifications) |
| Categories | ✅ | /categories |

### Pages Created (All with Sample/Placeholder Data)

#### Main
- [x] **Dashboard** (`/admin`) - Stats cards, charts, section cards, recent activity

#### Inventory Management
- [x] **Inventory** (`/admin/inventory`) - Stock levels, filters, adjustments
- [x] **Inventory Reports** (`/admin/inventory/reports`) - Valuation, stock status, movement
- [x] **Stock Alerts** (`/admin/inventory/alerts`) - Out of stock, low stock items
- [x] **Bulk Update** (`/admin/inventory/bulk-update`) - CSV upload, manual bulk updates
- [x] **Export** (`/admin/inventory/export`) - Export inventory to CSV/Excel/PDF

#### Catalog
- [x] **Products** (`/admin/products`) - Full product table, add/edit modal, bulk actions
- [x] **Categories** (`/admin/categories`) - Category hierarchy, add category modal

#### Sales
- [x] **Orders** (`/admin/orders`) - Order management, status updates, filters
- [x] **Order Detail** (`/admin/orders/{id}`) - Single order view with timeline, notes
- [x] **Customers** (`/admin/customers`) - Customer list, order history
- [x] **Customer Detail** (`/admin/customers/{id}`) - Customer profile, orders, wishlist, reviews

#### Users
- [x] **Users** (`/admin/users`) - User management, role assignment (Admin/Manager/Customer)

#### Content
- [x] **Blog** (`/admin/blog`) - Blog post management, create/edit
- [x] **Events** (`/admin/events`) - Event management, scheduling
- [x] **Reviews** (`/admin/reviews`) - Review moderation, approve/reject
- [x] **FAQ Statistics** (`/admin/faq-stats`) - FAQ performance metrics, category breakdown

#### Marketing
- [x] **Coupons** (`/admin/coupons`) - Coupon creation, usage tracking
- [x] **Loyalty Program** (`/admin/loyalty`) - Points management, tier configuration
- [x] **Gift Cards** (`/admin/gift-cards`) - Gift card creation, balance tracking
- [x] **Gift Card Detail** (`/admin/gift-cards/{id}`) - Balance, transactions, recipient info

#### Drop Shipping
- [x] **Drop Shippers** (`/admin/dropshippers`) - Partner management, API credentials
- [x] **Add Drop Shipper** (`/admin/dropshippers/add`) - Create new partner with permissions
- [x] **Drop Shipper Detail** (`/admin/dropshippers/{id}`) - Orders, API activity, credentials
- [x] **Drop Ship Orders** (`/admin/dropship/orders`) - Orders from drop ship partners
- [x] **API Logs** (`/admin/api-logs`) - API request logs, errors, response times

#### System
- [x] **Sales Dashboard** (`/admin/sales-dashboard`) - Revenue charts, metrics, analytics
- [x] **Reports** (`/admin/reports`) - Sales reports, analytics charts
- [x] **Settings** (`/admin/settings`) - Tabbed settings (General, Store, Email, Payment, Shipping, Tax)
- [x] **Shipping Settings** (`/admin/settings/shipping`) - Zones, rates, carriers
- [x] **Tax Settings** (`/admin/settings/tax`) - Tax rates, regions, classes

### UI Components

- [x] Stats cards with icons and color coding
- [x] Data tables with sorting/filtering placeholders
- [x] Modal dialogs for create/edit forms
- [x] Status badges (active, inactive, pending, etc.)
- [x] Action buttons (view, edit, delete)
- [x] Breadcrumb navigation
- [x] Search and filter forms
- [x] Chart containers with Chart.js

### Styling

- [x] PRT brand colors (Brown: #8B4513, Tan: #D2B48C, Green: #228B22)
- [x] Responsive sidebar (collapsible on mobile)
- [x] Hover effects and transitions
- [x] Consistent form styling
- [x] Table styling with hover states

---

## What Needs To Be Done

### Authentication & Security

- [ ] Implement Laravel authentication (Laravel Breeze or Sanctum)
- [ ] Add admin login page
- [ ] Implement role-based access control (RBAC)
- [ ] Add password reset functionality
- [ ] Session timeout handling
- [ ] CSRF protection verification
- [ ] Rate limiting for login attempts

### API Integration & Database Access

The admin site should use API calls similar to the frontend website, consuming endpoints from `pecos-backendadmin-api`. Some endpoints are shared between frontend and backend, while others are admin-specific.

#### Shared API Endpoints (Frontend & Admin)

- [ ] **Products**
  - `GET /api/products` - List products (with pagination, filters)
  - `GET /api/products/{id}` - Get single product
  - `GET /api/categories` - List categories
  - `GET /api/categories/{id}/products` - Products by category

- [ ] **Content**
  - `GET /api/blog` - List blog posts
  - `GET /api/blog/{id}` - Get single post
  - `GET /api/events` - List events
  - `GET /api/events/{id}` - Get single event
  - `GET /api/faqs` - List FAQs
  - `GET /api/reviews/{product_id}` - Get product reviews

- [ ] **User/Auth**
  - `POST /api/login` - Authentication
  - `GET /api/user` - Get current user
  - `POST /api/logout` - Logout

#### Admin-Only API Endpoints

- [ ] **Products Management**
  - `POST /api/admin/products` - Create product
  - `PUT /api/admin/products/{id}` - Update product
  - `DELETE /api/admin/products/{id}` - Delete product
  - `POST /api/admin/products/{id}/images` - Upload product images
  - `POST /api/admin/products/bulk-update` - Bulk update products

- [ ] **Categories Management**
  - `POST /api/admin/categories` - Create category
  - `PUT /api/admin/categories/{id}` - Update category
  - `DELETE /api/admin/categories/{id}` - Delete category
  - `PUT /api/admin/categories/reorder` - Reorder categories

- [x] **Orders Management** ✅ COMPLETE
  - `GET /api/admin/orders` - List all orders (with filters)
  - `GET /api/admin/orders/{id}` - Get order details
  - `GET /api/admin/orders/stats` - Order statistics
  - `PUT /api/admin/orders/{id}/status` - Update order status
  - `POST /api/admin/orders/{id}/refund` - Process refund
  - `POST /api/admin/orders/{id}/notes` - Add order notes

- [x] **Customers Management** ✅ COMPLETE
  - `GET /api/admin/customers` - List all customers
  - `GET /api/admin/customers/{id}` - Get customer details
  - `GET /api/admin/customers/{id}/orders` - Customer order history
  - `GET /api/admin/customers/stats` - Customer statistics
  - `PUT /api/admin/customers/{id}` - Update customer

- [x] **Users Management** ✅ COMPLETE
  - `GET /api/admin/users` - List admin users
  - `GET /api/admin/users/{id}` - Get user details
  - `POST /api/admin/users` - Create admin user
  - `PUT /api/admin/users/{id}` - Update user
  - `DELETE /api/admin/users/{id}` - Delete user

- [ ] **Inventory Management**
  - `GET /api/admin/inventory` - List inventory levels
  - `POST /api/admin/inventory/adjust` - Adjust stock
  - `GET /api/admin/inventory/history/{product_id}` - Stock history
  - `GET /api/admin/inventory/alerts` - Low stock alerts
  - `POST /api/admin/inventory/bulk-update` - Bulk stock update
  - `PUT /api/admin/inventory/thresholds` - Update alert thresholds

- [ ] **Content Management**
  - `POST /api/admin/blog` - Create blog post
  - `PUT /api/admin/blog/{id}` - Update blog post
  - `DELETE /api/admin/blog/{id}` - Delete blog post
  - `POST /api/admin/events` - Create event
  - `PUT /api/admin/events/{id}` - Update event
  - `DELETE /api/admin/events/{id}` - Delete event
  - `PUT /api/admin/reviews/{id}/status` - Approve/reject review
  - `DELETE /api/admin/reviews/{id}` - Delete review
  - `POST /api/admin/faqs` - Create FAQ
  - `PUT /api/admin/faqs/{id}` - Update FAQ
  - `DELETE /api/admin/faqs/{id}` - Delete FAQ

- [ ] **Marketing**
  - `GET /api/admin/coupons` - List coupons
  - `POST /api/admin/coupons` - Create coupon
  - `PUT /api/admin/coupons/{id}` - Update coupon
  - `DELETE /api/admin/coupons/{id}` - Delete/deactivate coupon
  - `GET /api/admin/loyalty/transactions` - Loyalty transactions
  - `POST /api/admin/loyalty/adjust` - Adjust customer points
  - `PUT /api/admin/loyalty/tiers` - Configure loyalty tiers
  - `GET /api/admin/gift-cards` - List gift cards
  - `POST /api/admin/gift-cards` - Create gift card
  - `PUT /api/admin/gift-cards/{id}/void` - Void gift card
  - `GET /api/admin/gift-cards/{code}/balance` - Check balance

- [ ] **Reports & Analytics**
  - `GET /api/admin/reports/sales` - Sales report
  - `GET /api/admin/reports/products` - Product performance
  - `GET /api/admin/reports/customers` - Customer analytics
  - `GET /api/admin/reports/inventory` - Inventory valuation
  - `GET /api/admin/dashboard/stats` - Dashboard statistics

- [ ] **Settings**
  - `GET /api/admin/settings` - Get all settings
  - `PUT /api/admin/settings` - Update settings
  - `GET /api/admin/settings/{group}` - Get settings by group

#### API Service Layer (Laravel)

Create a service class to handle API calls:

```php
// app/Services/ApiService.php
class ApiService
{
    protected $baseUrl = 'http://localhost:8300/api';
    protected $token;

    public function get($endpoint, $params = [])
    public function post($endpoint, $data = [])
    public function put($endpoint, $data = [])
    public function delete($endpoint)
}
```

#### Database Tables (Existing in pecosriver)

**Core Tables ✅ EXISTING:**
- [x] `products` - Product catalog
- [x] `categories` - Product categories
- [x] `orders` - Customer orders
- [x] `order_items` - Order line items
- [x] `users` - User accounts
- [x] `blog_posts` - Blog content
- [x] `events` - Store events
- [x] `product_reviews` - Product reviews
- [x] `faqs` - Frequently asked questions
- [x] `coupons` - Discount codes
- [x] `loyalty_transactions` - Points history
- [x] `loyalty_tiers` - Loyalty tier definitions
- [x] `loyalty_rewards` - Available rewards
- [x] `user_wishlists` - User wishlists
- [x] `user_addresses` - Customer addresses

**Newly Created Tables ✅ COMPLETE:**
- [x] `order_status_history` - Track order status changes
- [x] `gift_cards` - Gift card records
- [x] `gift_card_transactions` - Gift card usage history
- [x] `loyalty_members` - User loyalty membership
- [x] `loyalty_settings` - Loyalty program configuration
- [x] `inventory_movements` - Stock change history
- [x] `dropshippers` - Drop ship partner records
- [x] `dropshipper_permissions` - Partner API permissions
- [x] `dropship_orders` - Orders from drop shippers
- [x] `api_logs` - API request logging
- [x] `dropship_webhooks` - Webhook configurations
- [x] `settings` - Site configuration

### CRUD Operations

- [ ] **Products**: Create, Read, Update, Delete with image upload
- [ ] **Categories**: Full CRUD with parent/child relationships
- [ ] **Orders**: View, update status, process refunds
- [ ] **Customers**: View, edit, order history
- [ ] **Users**: CRUD with role management
- [ ] **Blog**: CRUD with rich text editor
- [ ] **Events**: CRUD with date/time handling
- [ ] **Reviews**: Approve, reject, respond
- [ ] **FAQs**: CRUD with category management
- [ ] **Coupons**: Create, edit, deactivate
- [ ] **Loyalty**: Configure tiers, adjust points
- [ ] **Gift Cards**: Generate, void, check balance
- [ ] **Inventory**: Stock adjustments, history logging

### Form Validation

- [ ] Server-side validation for all forms
- [ ] Client-side validation with error messages
- [ ] File upload validation (images, CSVs)
- [ ] Unique field validation (emails, SKUs, codes)

### Features to Implement

- [ ] **Search functionality** - Global search across all entities
- [ ] **Bulk actions** - Select multiple items for bulk operations
- [ ] **Export functionality** - CSV/PDF export for reports and tables
- [ ] **Import functionality** - CSV import for products, inventory
- [ ] **Image management** - Upload, crop, resize product images
- [ ] **Rich text editor** - For blog posts, event descriptions
- [ ] **Email notifications** - Order updates, low stock alerts
- [ ] **Activity logging** - Track admin actions
- [ ] **Dashboard widgets** - Real-time data from database
- [ ] **Charts** - Dynamic charts from actual data

### API Integration

- [ ] Connect to `pecos-backendadmin-api` for data operations
- [ ] Or implement direct database operations via Eloquent
- [ ] API authentication tokens
- [ ] Error handling for API failures

### Inventory Management

- [ ] Real-time stock tracking
- [ ] Automatic low stock alerts
- [ ] Stock movement history
- [ ] Reorder point configuration
- [ ] Supplier management
- [ ] Purchase orders

### Order Processing

- [ ] Order status workflow
- [ ] Payment processing integration
- [ ] Shipping label generation
- [ ] Invoice generation
- [ ] Refund processing
- [ ] Order notes/comments

### Reporting

- [ ] Sales reports with date ranges
- [ ] Product performance reports
- [ ] Customer analytics
- [ ] Inventory valuation reports
- [ ] Export to CSV/PDF
- [ ] Scheduled report emails

### Performance

- [ ] Database query optimization
- [ ] Pagination for large datasets
- [ ] Caching for frequently accessed data
- [ ] Lazy loading for images
- [ ] Asset minification

### Testing

- [ ] Unit tests for models
- [ ] Feature tests for controllers
- [ ] Browser tests for UI
- [ ] API integration tests

---

## File Structure

```
pecos-backend-admin-site/
├── app/
├── bootstrap/
├── config/
├── database/
├── docs/
│   └── pecos-backend-admin-site.md
├── public/
│   └── css/
│       └── admin.css
├── resources/
│   └── views/
│       ├── layouts/
│       │   └── admin.blade.php
│       └── admin/
│           ├── dashboard.blade.php
│           ├── inventory.blade.php
│           ├── inventory-reports.blade.php
│           ├── inventory-export.blade.php
│           ├── stock-alerts.blade.php
│           ├── bulk-update.blade.php
│           ├── products.blade.php
│           ├── categories.blade.php
│           ├── orders.blade.php
│           ├── order-detail.blade.php
│           ├── customers.blade.php
│           ├── customer-detail.blade.php
│           ├── users.blade.php
│           ├── blog.blade.php
│           ├── events.blade.php
│           ├── reviews.blade.php
│           ├── faq-stats.blade.php
│           ├── coupons.blade.php
│           ├── loyalty.blade.php
│           ├── gift-cards.blade.php
│           ├── gift-card-detail.blade.php
│           ├── reports.blade.php
│           ├── sales-dashboard.blade.php
│           ├── settings.blade.php
│           ├── shipping.blade.php
│           ├── tax-settings.blade.php
│           ├── dropshippers.blade.php
│           ├── dropshipper-add.blade.php
│           ├── dropshipper-detail.blade.php
│           ├── dropship-orders.blade.php
│           └── api-logs.blade.php
├── routes/
│   └── web.php
├── storage/
├── tests/
├── .env
└── composer.json
```

**Total Pages: 31**

---

## Running the Application

```bash
cd C:\xampp\htdocs\pecos-backend-admin-site
php artisan serve --port=8001
```

Access at: http://localhost:8301/admin

---

## Configuration

### Database (.env)

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pecosriver
DB_USERNAME=root
DB_PASSWORD=
```

### Session

```
SESSION_DRIVER=file
```

---

## Priority Implementation Order

1. **Authentication** - Essential for security
2. **Database models & migrations** - Foundation for all features
3. **Products CRUD** - Core e-commerce functionality
4. **Orders management** - Critical for operations
5. **Inventory tracking** - Stock management
6. **User management** - Admin access control
7. **Reports** - Business intelligence
8. **Marketing features** - Coupons, loyalty, gift cards

---

## Related Projects

- **PRT2** (`C:\xampp\htdocs\PRT2`) - Main customer-facing website
- **pecos-backendadmin-api** (`C:\xampp\htdocs\pecos-backendadmin-api`) - Laravel API backend

---

## Current Status Summary

### Completed ✅
- All 31 UI pages created with consistent styling
- API integration infrastructure (ApiService, AdminController)
- 23 pages connected to real API data
- Filters, pagination, and search working
- Stats cards displaying live data
- **Users management** - Full CRUD with modals and API integration
- **Coupons management** - Full CRUD with modals
- **Inventory** - Product images now display, stock adjustments
- **Dropshippers** - Full CRUD, approve, suspend, regenerate API key
- **Dropship Orders** - List with status updates and tracking
- **API Logs** - Paginated logs with stats, filters, detail modal
- **Tax Settings** - Tax rates with local city/county rates
- **Shipping Settings** - Shipping zones and carriers
- **Settings** - 7 groups with 50 settings, load/save via API
- Loyalty program with members, tiers, stats
- Sales Dashboard with real order/customer stats

### In Progress 🔄
- Inventory Reports with charts
- Categories CRUD operations

### Not Started ❌
- Authentication system
- File uploads (product images, blog images)
- Email notifications
- PDF export functionality
- Print functionality (invoices, packing slips)

---

## Future Enhancements

### High Priority
1. **AJAX Form Submissions** - Connect all create/edit/delete operations to API
2. **Form Validation** - Client and server-side validation
3. **File Uploads** - Product images, blog images
4. **Stock Adjustment** - Connect modal to POST /admin/inventory/adjust-stock

### Medium Priority
1. **Authentication** - Laravel Sanctum for API auth
2. **Email Notifications** - Order updates, password reset, low stock alerts
3. **Export Functionality** - CSV/PDF export for all tables
4. **Rich Text Editor** - TinyMCE or CKEditor for blog posts
5. **Bulk Actions** - Select multiple items for operations

### Low Priority
1. **Two-Factor Authentication**
2. **Activity Logging** - Track admin actions
3. **Dashboard Charts** - Real-time Chart.js with API data
4. **Webhook Management** - Drop shipper webhooks
5. **Report Scheduling** - Automated email reports

---

## Notes

- 23 pages now display real data from pecos-backendadmin-api
- Navigation and UI are fully functional
- API runs on port 8000, Admin site on port 8001
- Consistent with PRT2 admin styling and features
- Users page has full CRUD with edit/delete modals
- Inventory page shows actual product images
- Dropshippers section fully functional with API
- API Logs shows real paginated data with stats
- Settings page loads/saves 7 groups (50 settings) via API
- See `api-integration-tracker.md` for detailed API endpoint documentation
- See `international-tax-guide.md` for tax handling documentation

**Last Updated**: 2025-11-21
