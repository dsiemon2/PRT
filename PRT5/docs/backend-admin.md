# Backend Admin Panel - Development Roadmap

## Overview
This document outlines the backend admin panel features for Pecos River Trading Company. The admin panel provides store management capabilities for administrators and managers.

---

## Current Implementation Status

### Authentication & Security ✅ COMPLETE
- [x] Admin authentication system (`admin/common.php`)
- [x] Role-based access control (requireManager/requireAdmin)
- [x] CSRF protection on forms
- [x] Session management

### Inventory Management ✅ COMPLETE
- [x] Inventory dashboard (`inventory-dashboard.php`)
- [x] Product editing (`inventory-edit.php`)
- [x] Stock alerts (`stock-alerts.php`)
- [x] Inventory reports (`inventory-reports.php`)
- [x] Bulk stock update (`inventory-bulk-update.php`)
- [x] Export to CSV (`inventory-export.php`)

### Content Management ✅ COMPLETE
- [x] Blog management with image upload (`blog-management.php`)
- [x] Events management (`Events-Management.php`)
- [x] FAQ statistics (`faq-statistics.php`)
- [x] Reviews management (`reviews-management.php`)

### Gift Cards & Loyalty ✅ UI COMPLETE
- [x] Gift card management (UI in pecos-backend-admin-site)
- [x] Loyalty rewards program (UI in pecos-backend-admin-site)

> **Note**: UI pages for all admin features have been created in the new Laravel Blade admin site at `C:\xampp\htdocs\pecos-backend-admin-site`. See `docs/pecos-backend-admin-site.md` for details.

---

## Features To Implement

> **Admin UI Status**: All admin UI pages with placeholder data have been created in the new Laravel Blade admin site at `C:\xampp\htdocs\pecos-backend-admin-site`. The items below still need backend API integration and database connections.

### 1. Product Management (High Priority) - UI + API Partially Complete
Full CRUD operations for products.

#### Product List View
- [x] Paginated product listing (UI + API)
- [x] Search by name, SKU (UI + API)
- [x] Filter by category, status (UI + API)
- [ ] Filter by price range (API only)
- [ ] Sort by name, price, stock, date added (API only - no UI dropdown)
- [x] Bulk actions dropdown (UI with sample data)

#### Add New Product
- [x] Product name, SKU, description (UI + API)
- [x] Price and sale price (UI + API)
- [x] Category selection (UI + API)
- [x] Stock quantity and low stock threshold (UI + API)
- [x] Single image upload with drag-and-drop (UI)
- [ ] Multiple image upload
- [ ] Image reordering and primary image selection
- [x] Product status (active/draft/discontinued) (UI)
- [ ] SEO fields (meta title, description)
- [ ] Product attributes (size, color, material)

#### Edit Product
- [x] Basic fields (UI buttons + API)
- [ ] View product history/changes
- [x] Duplicate product button (UI)
- [x] Preview product button (UI)

#### Delete Product
- [x] Delete button (UI + API)
- [ ] Soft delete (mark as deleted, keep in database)
- [ ] Confirmation modal
- [ ] Check for active orders before deletion

**API Endpoints (in pecos-backendadmin-api):**
- `GET /api/v1/products` - List with search, filter, sort, pagination
- `GET /api/v1/products/{upc}` - Get single product
- `POST /api/v1/products` - Create product (admin)
- `PUT /api/v1/products/{upc}` - Update product (admin)
- `DELETE /api/v1/products/{upc}` - Delete product (admin)
- `PATCH /api/v1/products/{upc}/stock` - Update stock (admin)

---

### 2. Category Management (High Priority) - UI Complete
Manage product categories and subcategories.

#### Features
- [x] List all categories with product counts (UI with sample data)
- [x] Add new category (UI modal)
  - [x] Name, slug, description
  - [x] Parent category (for subcategories)
  - [x] Category image
  - [x] Display order (drag handle icons in UI)
  - [x] Status (active/inactive)
- [x] Edit category button (UI)
- [x] Delete category button (UI)
- [x] Drag-and-drop reordering icons (UI - not functional)
- [ ] Bulk actions

**API Endpoints (public - in pecos-backendadmin-api):**
- `GET /api/v1/categories` - List categories
- `GET /api/v1/categories/tree` - Category tree
- `GET /api/v1/categories/{categoryCode}` - Get category

**API Needed:**
- `POST /api/admin/categories` - Create
- `PUT /api/admin/categories/{id}` - Update
- `DELETE /api/admin/categories/{id}` - Delete

---

### 3. Order Management (High Priority) - UI + API ✅ COMPLETE
View and manage customer orders.

#### Order List View
- [x] Paginated order listing (UI + API)
- [x] Search by order ID (UI + API)
- [x] Filter by status, date range (UI + API)
- [ ] Filter by payment method (API only)
- [ ] Sort by date, total, status (API only - no UI)
- [x] Quick status update dropdown (UI + API)
- [x] Export button (UI)

#### Order Detail View
- [x] Customer information (UI + API)
- [x] Shipping address (UI - in sidebar)
- [x] Billing address (UI - in sidebar)
- [x] Order items with product details (UI + API)
- [x] Order totals (subtotal, tax, shipping, discounts, total) (UI + API)
- [x] Payment information (UI)
- [x] Order status history/timeline (UI + API)
- [x] Admin notes section (UI + API)

#### Order Actions
- [x] Update order status (UI + API)
- [ ] Send order confirmation email
- [ ] Send shipping notification
- [x] Print button (UI)
- [x] Issue refund (API: POST /admin/orders/{id}/refund)
- [ ] Cancel order

**API Endpoints (in pecos-backendadmin-api):**
- `GET /api/v1/admin/orders` - List all orders with filters
- `GET /api/v1/admin/orders/{id}` - Order details with items/history
- `GET /api/v1/admin/orders/stats` - Order statistics
- `PUT /api/v1/admin/orders/{id}/status` - Update status
- `POST /api/v1/admin/orders/{id}/notes` - Add notes
- `POST /api/v1/admin/orders/{id}/refund` - Process refund

#### Order Statuses
- Pending
- Processing
- Shipped
- Delivered
- Cancelled
- Refunded
- On Hold

**Files to create:**
- `admin/orders.php` - Order list
- `admin/order-detail.php` - Single order view

---

### 4. Customer Management (Medium Priority) - UI + API ✅ COMPLETE
View and manage customer accounts.

#### Customer List View
- [x] Paginated customer listing (UI + API)
- [x] Search by name, email, phone (UI + API)
- [x] Filter by status (UI + API)
- [x] Sort by name, date, orders, spent (UI dropdown + API)
- [x] Export button (UI)

#### Customer Detail View
- [x] Account information (name, email, phone) (UI + API)
- [x] Addresses (shipping/billing) (UI + API)
- [x] Order history (UI + API)
- [x] Total orders and lifetime value (UI + API)
- [x] Account status (active/suspended) (UI)
- [ ] Admin notes
- [ ] Wishlist items
- [ ] Reviews written

#### Customer Actions
- [x] Edit customer button (UI + API)
- [ ] Reset password (send reset email)
- [ ] Suspend/activate account
- [ ] Delete account (with data retention options)
- [ ] Add admin notes

**API Endpoints (in pecos-backendadmin-api):**
- `GET /api/v1/admin/customers` - List all customers
- `GET /api/v1/admin/customers/{id}` - Customer details
- `GET /api/v1/admin/customers/{id}/orders` - Customer orders
- `GET /api/v1/admin/customers/stats` - Customer statistics
- `PUT /api/v1/admin/customers/{id}` - Update customer

---

### 5. Sales Reports & Analytics (Medium Priority) - UI Complete
Comprehensive reporting dashboard.

#### Sales Dashboard
- [x] Date range filter (UI with sample data)
- [x] Revenue stats card with comparison (UI)
- [x] Orders stats card (UI)
- [x] Average order value (UI)
- [x] Conversion rate (UI)
- [x] Revenue chart (UI - Chart.js)
- [x] Top selling products (UI)
- [x] Sales by category chart (UI)

#### Reports
- [x] Sales reports page (UI with sample data)
- [x] Sales by date range (UI)
- [x] Sales by product (UI)
- [x] Sales by category (UI)
- [ ] Sales by customer
- [ ] Tax report
- [ ] Discount/coupon usage report
- [ ] Refund report

#### Export Options
- [x] Export buttons (UI)
- [ ] Export to CSV (functionality)
- [ ] Export to PDF (functionality)
- [ ] Print-friendly view

**UI Pages:** `sales-dashboard.blade.php`, `reports.blade.php`

---

### 6. Discount & Coupon Management (Medium Priority) - UI + API Complete
Create and manage promotional codes.

#### Features
- [x] List all coupons (UI + API)
- [x] Add new coupon (UI modal)
  - [x] Code (auto-generate option)
  - [x] Discount type (percentage, fixed amount, free shipping)
  - [x] Discount value
  - [x] Minimum order amount
  - [x] Usage limit
  - [x] Valid date range
  - [ ] Product/category restrictions
  - [ ] Customer restrictions
- [x] Edit coupon button (UI)
- [x] Delete/deactivate coupon button (UI)
- [x] View coupon usage statistics (UI)

**UI Page:** `coupons.blade.php`
**API Endpoints:** GET/POST/PUT/DELETE /api/v1/admin/coupons

---

### 7. Shipping Management (Low Priority) - UI Complete
Configure shipping options and rates.

#### Features
- [x] Shipping zones table (UI with sample data)
- [x] Add shipping zone modal (UI)
- [x] Shipping methods per zone (UI)
- [x] Rate configuration (flat rate, weight-based, price-based) (UI)
- [x] Free shipping thresholds (UI)
- [ ] Shipping class for products

**UI Page:** `shipping.blade.php`

---

### 8. Tax Configuration (Low Priority) - UI Complete
Manage tax rates and rules.

#### Features
- [x] Tax rates table (UI with sample data)
- [x] Tax zones/regions (UI)
- [x] Add tax rate modal (UI)
- [x] Tax classes for products (UI)
- [ ] Tax-exempt customer marking

**UI Page:** `tax-settings.blade.php`

---

### 9. Settings & Configuration (Low Priority) - UI Complete
General store settings.

#### Features
- [x] Store information tabs (UI with API integration)
- [x] General settings tab (timezone, currency, decimal places)
- [x] Store Info tab (name, tagline, address, contact, name styling)
- [x] Branding tab (logo alignment, header styling, colors, announcement bar, theme colors)
- [x] Email settings tab (SMTP configuration)
- [x] Security settings tab (2FA, session timeout, password requirements)
- [x] API settings tab (rate limiting, logging)
- [x] Notifications settings tab (low stock threshold, email alerts)
- [ ] Currency and locale
- [ ] Image upload settings
- [ ] SEO defaults
- [ ] Maintenance mode toggle

#### Settings Consolidation (November 2025)
- Store Name is the single source of truth in **Store Info** tab
- Store Name styling (font size, color) available in Store Info
- Payment settings removed from Settings (available in Features page)
- Branding tab includes logo alignment, nav height, header colors, and theme colors

**UI Page:** `settings.blade.php`

---

### 10. User & Role Management (Low Priority) - UI + API ✅ COMPLETE
Manage admin users and permissions.

#### Features
- [x] List admin users (UI + API)
- [x] Add new admin user (UI + API)
- [x] Edit user with modal (UI + API)
- [x] Delete user with confirmation (UI + API)
- [x] Role assignment dropdown (UI + API)
- [x] Active/inactive status toggle (UI + API)
- [ ] Role management (Admin, Manager, Staff) - separate page
- [ ] Activity log per user
- [ ] Two-factor authentication setup

**UI Page:** `users.blade.php`
**API Endpoints:**
- `GET /api/v1/admin/users` - List all users with filters
- `GET /api/v1/admin/users/{id}` - Get user details
- `POST /api/v1/admin/users` - Create user
- `PUT /api/v1/admin/users/{id}` - Update user
- `DELETE /api/v1/admin/users/{id}` - Delete user

---

### 11. Blog Management ✅ IMPLEMENTED
Manage blog posts and content marketing.

#### Current Features
- [x] Create/edit/delete blog posts
- [x] Featured image upload
- [x] Category assignment
- [x] Status management (draft/published/scheduled)
- [x] SEO fields (meta title, description)
- [x] Author attribution
- [x] View counts

#### Enhancements Needed
- [ ] Rich text editor (TinyMCE/CKEditor)
- [ ] Image gallery within posts
- [ ] Post scheduling with cron job
- [ ] Tags management
- [ ] Related posts
- [ ] Comment moderation
- [ ] Social sharing preview

**Existing file:** `admin/blog-management.php`

---

### 12. Gift Card Management (Medium Priority) - UI Complete
Create and manage digital gift cards.

#### Gift Card Types
- **Digital**: Email delivery with unique code
- **Physical**: Printed cards with activation

#### Features
- [x] List all gift cards with status (UI with sample data)
- [x] Create new gift card modal (UI)
  - [x] Amount (preset values or custom)
  - [x] Recipient email
  - [x] Sender name and message
  - [x] Delivery date
  - [x] Expiration date
  - [ ] Design/template selection
- [ ] Bulk gift card generation
- [x] Activate/deactivate buttons (UI)
- [x] View usage history (UI - detail page)
- [x] Check balance (UI - detail page)
- [x] Void gift card button (UI)
- [ ] Resend delivery email

#### Gift Card Detail View
- [x] Balance and status (UI)
- [x] Transaction history (UI)
- [x] Recipient info (UI)

#### Reporting
- [x] Stats cards (sold, active, redeemed, balance) (UI)
- [ ] Detailed reports

**UI Pages:** `gift-cards.blade.php`, `gift-card-detail.blade.php`
**Database Tables:** `gift_cards`, `gift_card_transactions`

---

### 13. Loyalty Rewards Management (Medium Priority) - UI Complete
Customer loyalty and points program.

#### Program Configuration
- [x] Points earning rules (UI with sample data)
- [x] Tier configuration table (UI)
- [x] Add tier modal (UI)
- [ ] Points redemption rules
- [ ] Tier-specific earning multipliers

#### Member Management
- [x] View all loyalty members (UI with sample data)
- [x] Search/filter members (UI)
- [x] Points balance display (UI)
- [x] Current tier display (UI)
- [x] Manual points adjustment button (UI)
- [ ] Detailed member view
- [ ] Export member list

#### Rewards Catalog
- [x] Rewards table (UI with sample data)
- [x] Add reward button (UI)
- [ ] Full rewards management

#### Reporting
- [x] Stats cards (members, points issued, redeemed) (UI)
- [ ] Detailed reports

**UI Page:** `loyalty.blade.php`
**Database Tables:** `loyalty_members`, `loyalty_transactions`, `loyalty_tiers`, `loyalty_rewards`, `loyalty_settings`

---

### 14. Drop Shipper Management (Medium Priority) - UI + API ✅ COMPLETE
Manage drop shipping partners and API integrations.

#### Drop Shipper Accounts
- [x] List all drop shippers (UI + API)
- [x] Add new drop shipper (UI + API)
  - [x] Company name and contact info
  - [x] API credentials (key, secret)
  - [x] Account status (pending, active, suspended)
  - [x] Commission/markup settings
  - [ ] Shipping preferences
- [x] Drop shipper detail page (UI + API)
- [x] Suspend/activate buttons (UI + API)
- [x] Approve pending accounts (UI + API)
- [x] View performance metrics (UI - stats cards)

#### API Access Management
- [x] API credentials display (UI + API)
- [x] Regenerate API key button (UI + API)
- [ ] Set API rate limits
- [x] Endpoint permissions checkboxes (UI)
- [ ] IP whitelist per drop shipper
- [ ] API key rotation/revocation

#### Order Management
- [x] View drop ship orders page (UI + API)
- [x] Order status display (UI + API)
- [x] Tracking number display (UI + API)
- [x] Update order status with tracking (UI + API)
- [ ] Auto-forward orders to fulfillment
- [ ] Order status sync

#### API Logs
- [x] API request logs page (UI + API)
- [x] Endpoint, method, response code (UI + API)
- [x] Filter by date, endpoint, status, dropshipper (UI + API)
- [x] Pagination with real data (UI + API)
- [x] Stats (total requests, success rate, avg response time, errors) (UI + API)
- [x] Log detail modal (UI + API)

**UI Pages:** `dropshippers.blade.php`, `dropshipper-add.blade.php`, `dropshipper-detail.blade.php`, `dropship-orders.blade.php`, `api-logs.blade.php`
**Database Tables:** `dropshippers`, `dropship_orders`, `dropship_order_items`, `api_logs`

**API Endpoints (in pecos-backendadmin-api):**
- `GET /api/v1/admin/dropshippers` - List all dropshippers with stats
- `GET /api/v1/admin/dropshippers/{id}` - Get dropshipper details
- `POST /api/v1/admin/dropshippers` - Create dropshipper
- `PUT /api/v1/admin/dropshippers/{id}` - Update dropshipper
- `DELETE /api/v1/admin/dropshippers/{id}` - Delete dropshipper
- `POST /api/v1/admin/dropshippers/{id}/approve` - Approve pending dropshipper
- `POST /api/v1/admin/dropshippers/{id}/toggle-suspend` - Toggle suspend status
- `POST /api/v1/admin/dropshippers/{id}/regenerate-key` - Regenerate API key
- `GET /api/v1/admin/dropship/orders` - List dropship orders with filters
- `GET /api/v1/admin/dropship/orders/{id}` - Order details
- `PUT /api/v1/admin/dropship/orders/{id}/status` - Update order status
- `GET /api/v1/admin/api-logs` - Paginated API logs with filters
- `GET /api/v1/admin/api-logs/{id}` - Single log detail
- `GET /api/v1/admin/api-logs/stats` - 24h API statistics
- `GET /api/v1/admin/api-logs/dropshippers` - Dropshippers for filter
- `GET /api/v1/admin/api-logs/endpoints` - Unique endpoints for filter

#### Still Needed
- [ ] Returns handling
- [ ] Invoice generation for drop shippers
- [ ] Real-time inventory updates via webhook
- [ ] Batch inventory sync schedule
- [ ] API endpoints for drop shippers

#### Reporting & Analytics (Not Implemented)
- [ ] Orders by drop shipper
- [ ] Revenue and commission reports
- [ ] Top selling products per partner
- [ ] API usage statistics
- [ ] Error logs and failed requests

#### API Endpoints to Create
```
GET    /api/v1/dropship/products          - Product catalog
GET    /api/v1/dropship/products/{id}     - Single product
GET    /api/v1/dropship/inventory         - Stock levels
GET    /api/v1/dropship/categories        - Category list
POST   /api/v1/dropship/orders            - Create order
GET    /api/v1/dropship/orders/{id}       - Order status
GET    /api/v1/dropship/shipping/rates    - Calculate shipping
POST   /api/v1/dropship/webhooks          - Register webhooks
```

#### Authentication
- API key in header: `X-API-Key: {key}`
- HMAC signature for sensitive operations
- OAuth 2.0 support (future)

#### Webhook Events
- `inventory.updated` - Stock level changes
- `product.created` - New product added
- `product.updated` - Product details changed
- `order.shipped` - Tracking available
- `order.delivered` - Delivery confirmed

---

### Coupons
```sql
CREATE TABLE coupons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) UNIQUE,
    description TEXT,
    discount_type ENUM('percentage','fixed','free_shipping'),
    discount_value DECIMAL(10,2),
    min_order_amount DECIMAL(10,2),
    max_discount DECIMAL(10,2),
    usage_limit INT,
    usage_count INT DEFAULT 0,
    per_customer_limit INT,
    start_date DATETIME,
    end_date DATETIME,
    status ENUM('active','inactive','expired'),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Customer Addresses
```sql
CREATE TABLE customer_addresses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT,
    type ENUM('shipping','billing'),
    is_default BOOLEAN DEFAULT FALSE,
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    company VARCHAR(255),
    address_line1 VARCHAR(255),
    address_line2 VARCHAR(255),
    city VARCHAR(100),
    state VARCHAR(100),
    postal_code VARCHAR(20),
    country VARCHAR(100),
    phone VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Gift Cards
```sql
CREATE TABLE gift_cards (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) UNIQUE NOT NULL,
    initial_balance DECIMAL(10,2) NOT NULL,
    current_balance DECIMAL(10,2) NOT NULL,
    status ENUM('pending','active','used','expired','voided') DEFAULT 'pending',
    purchaser_id INT,
    purchaser_email VARCHAR(255),
    recipient_email VARCHAR(255),
    recipient_name VARCHAR(255),
    sender_name VARCHAR(255),
    message TEXT,
    design_template VARCHAR(100),
    delivery_date DATE,
    expiration_date DATE,
    activated_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE gift_card_transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    gift_card_id INT,
    order_id INT,
    amount DECIMAL(10,2),
    type ENUM('purchase','redemption','refund','adjustment'),
    balance_after DECIMAL(10,2),
    note TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (gift_card_id) REFERENCES gift_cards(id)
);
```

### Loyalty Program
```sql
CREATE TABLE loyalty_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE,
    setting_value TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE loyalty_members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT UNIQUE,
    points_balance INT DEFAULT 0,
    lifetime_points INT DEFAULT 0,
    tier ENUM('bronze','silver','gold','platinum') DEFAULT 'bronze',
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_activity TIMESTAMP NULL
);

CREATE TABLE loyalty_transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    member_id INT,
    points INT,
    type ENUM('earned','redeemed','adjusted','expired'),
    source VARCHAR(100),
    reference_id INT,
    description TEXT,
    balance_after INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (member_id) REFERENCES loyalty_members(id)
);

CREATE TABLE loyalty_tiers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50),
    min_points INT,
    earning_multiplier DECIMAL(3,2) DEFAULT 1.00,
    benefits TEXT,
    display_order INT
);

CREATE TABLE loyalty_rewards (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255),
    description TEXT,
    points_cost INT,
    reward_type ENUM('discount_percent','discount_fixed','free_shipping','free_product'),
    reward_value DECIMAL(10,2),
    product_id INT,
    status ENUM('active','inactive'),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Drop Shippers
```sql
CREATE TABLE dropshippers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_name VARCHAR(255) NOT NULL,
    contact_name VARCHAR(255),
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50),
    address TEXT,
    api_key VARCHAR(64) UNIQUE,
    api_secret VARCHAR(128),
    status ENUM('pending','active','suspended') DEFAULT 'pending',
    commission_rate DECIMAL(5,2) DEFAULT 0.00,
    markup_percentage DECIMAL(5,2) DEFAULT 0.00,
    ip_whitelist TEXT,
    rate_limit INT DEFAULT 1000,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE dropshipper_permissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    dropshipper_id INT,
    endpoint VARCHAR(100),
    can_read BOOLEAN DEFAULT FALSE,
    can_write BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (dropshipper_id) REFERENCES dropshippers(id) ON DELETE CASCADE
);

CREATE TABLE dropship_orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    dropshipper_id INT,
    external_order_id VARCHAR(100),
    status ENUM('pending','processing','shipped','delivered','cancelled'),
    commission_amount DECIMAL(10,2),
    tracking_number VARCHAR(100),
    shipped_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (dropshipper_id) REFERENCES dropshippers(id)
);

CREATE TABLE api_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    dropshipper_id INT,
    endpoint VARCHAR(255),
    method VARCHAR(10),
    request_body TEXT,
    response_code INT,
    response_body TEXT,
    ip_address VARCHAR(45),
    execution_time INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_dropshipper (dropshipper_id),
    INDEX idx_created (created_at)
);

CREATE TABLE dropship_webhooks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    dropshipper_id INT,
    event_type VARCHAR(50),
    url VARCHAR(500),
    secret VARCHAR(128),
    status ENUM('active','inactive') DEFAULT 'active',
    last_triggered TIMESTAMP NULL,
    failure_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (dropshipper_id) REFERENCES dropshippers(id) ON DELETE CASCADE
);
```

---

## Implementation Priority

### Phase 1 - Core E-commerce (High Priority)
1. Product Management (full CRUD)
2. Category Management
3. Order Management

### Phase 2 - Customer & Marketing (Medium Priority)
4. Customer Management
5. Sales Reports & Analytics
6. Discount/Coupon Management
7. Gift Card Management
8. Loyalty Rewards Program
9. Drop Shipper Management & API

### Phase 3 - Configuration (Low Priority)
10. Shipping Management
11. Tax Configuration
12. Settings & Configuration
13. User & Role Management
14. Blog Enhancements (rich editor, scheduling)

---

## UI/UX Guidelines

### Consistent Design
- Use existing admin card styles from `admin/index.php`
- Brown header backgrounds for cards
- Red accents for primary actions
- Bootstrap 5 components
- Bootstrap Icons

### Common Elements
- Breadcrumb navigation
- Alert messages (success/error/warning)
- Confirmation modals for destructive actions
- Loading indicators for async operations
- Responsive tables with horizontal scroll on mobile

### Form Standards
- CSRF token on all forms
- Client-side validation
- Server-side validation with clear error messages
- Required field indicators (red asterisk)
- Help text for complex fields

---

## Security Considerations

- All admin pages must include `admin/common.php`
- Use prepared statements for all database queries
- Validate and sanitize all user input
- CSRF protection on all forms
- Rate limiting on sensitive operations
- Audit logging for important actions
- File upload validation (type, size, malware scanning)

---

## File Structure

```
admin/
├── index.php                  # Dashboard
├── common.php                 # Auth & DB connection
├──
├── # Product Management
├── products.php               # Product list
├── product-add.php            # Add product
├── product-edit.php           # Edit product
├──
├── # Category Management
├── categories.php             # Category management
├──
├── # Order Management
├── orders.php                 # Order list
├── order-detail.php           # Order details
├──
├── # Customer Management
├── customers.php              # Customer list
├── customer-detail.php        # Customer details
├──
├── # Reports
├── sales-dashboard.php        # Analytics
├── reports.php                # Detailed reports
├──
├── # Existing Files
├── inventory-dashboard.php
├── inventory-edit.php
├── inventory-reports.php
├── inventory-bulk-update.php
├── inventory-export.php
├── stock-alerts.php
├── blog-management.php
├── Events-Management.php
├── faq-statistics.php
├── reviews-management.php
├──
├── # Gift Cards & Loyalty
├── gift-cards.php
├── gift-card-detail.php
├── loyalty-settings.php
├── loyalty-members.php
├── loyalty-rewards.php
├── loyalty-reports.php
├──
├── # Drop Shipping
├── dropshippers.php
├── dropshipper-detail.php
├── dropshipper-add.php
├── dropship-orders.php
├── dropship-settings.php
├── api-logs.php
├──
├── # Future/Configuration
├── coupons.php
├── shipping.php
├── tax-settings.php
├── settings.php
├── users.php
```

### API File Structure
```
api/
├── v1/
│   ├── dropship/
│   │   ├── products.php
│   │   ├── inventory.php
│   │   ├── orders.php
│   │   ├── categories.php
│   │   ├── shipping.php
│   │   └── webhooks.php
│   └── middleware/
│       ├── auth.php
│       ├── rate-limit.php
│       └── logging.php
```

---

## Related Documentation

- [CODING_STANDARDS.md](./CODING_STANDARDS.md) - PHP coding standards
- [API Documentation](./api/) - REST API endpoints (future)

---

## Admin UI Implementation Status

**All admin UI pages have been created** in the new Laravel Blade admin site at `C:\xampp\htdocs\pecos-backend-admin-site`.

### Phase 1 - Core Pages with Real Data ✅ COMPLETE
- Dashboard with real stats from API
- Orders list and detail with real data
- Customers list and detail with real data
- Products list with filters and pagination
- Inventory management with stock adjustment

### Phase 2 - Content & Features with Real Data ✅ COMPLETE
- Stock Alerts with categorized tables
- Blog management with filters
- Events management with search
- Reviews management with star ratings
- Gift Cards list with stats

### UI Pages Completed (31 total):
- Dashboard with stats and charts
- Inventory management (dashboard, reports, alerts, bulk update, export)
- Product & category management
- Order management with detail views
- Customer management with detail views
- User/role management
- Content management (blog, events, reviews, FAQ stats)
- Marketing (coupons, loyalty program, gift cards with details)
- Drop shipper management (partners, orders, API logs)
- System settings (general, shipping, tax)
- Sales dashboard with analytics
- Reports

### Blade Pages Hooked to Real API Data:
| Page | Status | Notes |
|------|--------|-------|
| Dashboard | ✅ Complete | Stats cards with real data |
| Products | ✅ Complete | Filters, pagination, categories from API |
| Orders | ✅ Complete | List + detail with status history |
| Order Detail | ✅ Complete | Items, totals, timeline, customer info |
| Customers | ✅ Complete | List + stats with filters |
| Customer Detail | ✅ Complete | Orders, wishlist, loyalty |
| Users | ✅ Complete | List + CRUD with modals, API integration |
| Inventory | ✅ Complete | Stock levels, product images, adjustment modal |
| Stock Alerts | ✅ Complete | Out of stock and low stock tables |
| Blog | ✅ Complete | Posts with filters |
| Events | ✅ Complete | Events with search |
| Reviews | ✅ Complete | Star ratings, moderation status |
| Gift Cards | ✅ Complete | Cards with balance/status |
| Coupons | ✅ Complete | List + CRUD with modals |
| Dropshippers | ✅ Complete | List + CRUD, approve, suspend, regenerate key |
| Dropship Orders | ✅ Complete | Orders with status updates, tracking |
| API Logs | ✅ Complete | Paginated logs, stats, filters, detail modal |
| Tax Settings | ✅ Complete | Tax rates with local city rates |
| Shipping | ✅ Complete | Shipping zones and carriers |
| Settings | ✅ Complete | 7 groups, 50 settings, API load/save |
| Feature Config | ✅ Complete | Enable/disable features site-wide, grayed sidebar |
| Categories | 🔄 In Progress | Controller ready |
| Inventory Reports | 🔄 In Progress | Controller ready |
| FAQ Statistics | 🔄 In Progress | Controller ready |
| Gift Card Detail | 🔄 In Progress | Controller ready |

### What Still Needs Implementation:
- Categories blade with CRUD operations
- Inventory Reports with charts
- FAQ Statistics display
- Gift Card Detail with transactions
- Form validation on all forms
- AJAX form submissions
- File uploads (product images, blog images)
- Email notifications
- Export to CSV/PDF features
- Print invoices/packing slips

See `C:\xampp\htdocs\pecos-backend-admin-site\docs\api-integration-tracker.md` for detailed API integration status.

---

## Database Tables Status

### Core Tables ✅ EXISTING:
- products, categories, users
- orders, order_items
- cart, coupons
- blog_posts, events, faqs
- loyalty_transactions, loyalty_tiers, loyalty_rewards
- product_reviews, user_wishlists, user_addresses

### Newly Created Tables ✅ COMPLETE:
- order_status_history - Track order status changes
- gift_cards - Gift card records
- gift_card_transactions - Gift card usage history
- loyalty_members - User loyalty membership
- loyalty_settings - Loyalty program configuration
- inventory_movements - Stock change history
- dropshippers - Drop ship partner records
- dropshipper_permissions - Partner API permissions
- dropship_orders - Orders from drop shippers
- api_logs - API request logging
- dropship_webhooks - Webhook configurations
- settings - System settings storage

---

## API Endpoints Status

### Completed Admin APIs:
- **Orders**: GET /admin/orders, GET /admin/orders/{id}, PUT /admin/orders/{id}/status, POST /admin/orders/{id}/notes, POST /admin/orders/{id}/refund, GET /admin/orders/stats
- **Customers**: GET /admin/customers, GET /admin/customers/{id}, GET /admin/customers/{id}/orders, PUT /admin/customers/{id}, GET /admin/customers/stats
- **Users**: GET /admin/users, GET /admin/users/{id}, POST /admin/users, PUT /admin/users/{id}, DELETE /admin/users/{id}
- **Inventory**: GET /admin/inventory/products, GET /admin/inventory/stats, GET /admin/inventory/stock-alerts, POST /admin/inventory/adjust-stock
- **Products**: GET /products, POST /products, PUT /products/{upc}, DELETE /products/{upc}
- **Categories**: GET /categories, POST /admin/categories, PUT /admin/categories/{code}, DELETE /admin/categories/{code}
- **Gift Cards**: GET /admin/gift-cards, GET /admin/gift-cards/{id}, GET /admin/gift-cards/stats, POST /admin/gift-cards, PUT /admin/gift-cards/{id}/void
- **Dropshippers**: GET /admin/dropshippers, GET /admin/dropshippers/{id}, POST /admin/dropshippers, PUT /admin/dropshippers/{id}, DELETE /admin/dropshippers/{id}, POST /admin/dropshippers/{id}/approve, POST /admin/dropshippers/{id}/toggle-suspend, POST /admin/dropshippers/{id}/regenerate-key
- **Dropship Orders**: GET /admin/dropship/orders, GET /admin/dropship/orders/{id}, PUT /admin/dropship/orders/{id}/status
- **API Logs**: GET /admin/api-logs, GET /admin/api-logs/{id}, GET /admin/api-logs/stats, GET /admin/api-logs/dropshippers, GET /admin/api-logs/endpoints
- **Blog**: GET /admin/blog
- **Events**: GET /admin/events
- **Reviews**: GET /admin/reviews
- **FAQ Stats**: GET /admin/faq-stats
- **Coupons**: GET /admin/coupons, POST /admin/coupons, PUT /admin/coupons/{id}, DELETE /admin/coupons/{id}
- **Loyalty**: GET /admin/loyalty/account/{userId}, GET /admin/loyalty/stats
- **Tax**: GET /admin/tax-rates, POST /admin/tax-rates, DELETE /admin/tax-rates/{id}, GET /admin/tax-exemptions
- **Shipping**: GET /admin/shipping-zones, POST /admin/shipping-zones, DELETE /admin/shipping-zones/{id}
- **Settings**: GET /admin/settings, GET /admin/settings/{group}, PUT /admin/settings/{group}
- **Export**: GET /admin/export/orders, GET /admin/export/customers, GET /admin/export/products

### API Integration Infrastructure:
- **ApiService.php**: Centralized HTTP client for all API calls
- **AdminController.php**: Controller that fetches data from API and passes to views
- **Routes**: All admin routes configured in web.php

---

## Future Enhancements

### High Priority
1. AJAX form submissions for all CRUD operations
2. Real-time form validation
3. File upload handling (product images, blog images)
4. Stock adjustment API integration in modal

### Medium Priority
1. Email notifications (order confirmation, shipping, password reset)
2. Export to CSV functionality
3. Print invoice/packing slip
4. Bulk actions (delete, status update)
5. Rich text editor for blog posts

### Low Priority
1. Two-factor authentication
2. Activity logging
3. Dashboard analytics with charts
4. Webhook management for drop shippers
5. PDF export for reports

---

## Branding & Theme System ✅ COMPLETE

### Admin Location
`http://localhost:8301/admin/settings` → Branding tab

### Features
Comprehensive visual customization from admin panel:

#### Logo Settings
- Logo alignment: Left, Center, Right
- Logo size controlled by Navigation Bar Height
- Logo image: `PRT-High-Res-Logo.png` from prt4/assets/images

#### Logo Alignment Behavior (December 2025 Fix)
| Position | Logo | Nav Links | CSS Technique |
|----------|------|-----------|---------------|
| **Left** | Far left | Far right | `margin-left: auto` on nav |
| **Center** | Centered top | Centered below | `flex-direction: column` |
| **Right** | Far right | Far left | `order` swap + `margin-left: auto` on logo |

#### Header Styling
- Background color picker with hex input
- Text/Link color picker
- Hover/Active color picker
- Navigation Bar Height (50px - 100px)
- Header style (Solid, Gradient, Transparent)
- Sticky header toggle
- Drop shadow toggle

#### Announcement Bar
- Enable/disable toggle
- Custom announcement text
- Background color
- Text color

#### Site Theme Colors
- Primary color (buttons, links)
- Secondary color (sale badges, CTAs)
- Accent color (stars, highlights)
- Text Dark color
- Text Light color
- Page Background color
- Live color preview panel
- Live header preview

### Frontend Integration (PRT2)
- `/includes/header-helpers.php` - Fetches branding from API
- `getHeaderSettings()` - Returns all header/theme settings
- `getThemeCSS()` - Generates CSS variables
- `getNavbarCSS()` - Generates navbar-specific CSS
- `getAnnouncementBar()` - Returns announcement HTML
- `getLogoAlignment()` - Returns logo position class
- CSS variables applied to buttons, links, badges, stars

### Admin Backend Integration
- Store name centered in top navigation bar
- Font size and color customizable in Store Info
- Dynamic styling via inline CSS

---

## Feature Configuration System ✅ COMPLETE

### Admin Location
`http://localhost:8301/admin/features`

### Features
Enable/disable features site-wide from admin panel:
- FAQ, Loyalty Points, Digital Downloads, Specialty Products
- Gift Cards, Wishlists, Blog, Events, Reviews, Admin Link

### Frontend Integration (PRT2)
- `/config/features.php` - Loads features from API, caches in session
- `isFeatureEnabled($name)` - Helper function to check feature status
- Header nav items conditionally hidden
- Account dropdown items conditionally hidden
- Wishlist heart icons hidden when disabled
- Cart/checkout loyalty points calculation skipped when disabled

### Admin Sidebar Integration
- Disabled features grayed out with "(off)" indicator
- Features loaded from API and cached in session (5 min)
- CSS class: `.feature-disabled` with opacity and ::after pseudo-element

### API Endpoints
- `GET /api/v1/admin/settings/features` - Get all feature flags
- `PUT /api/v1/admin/settings/features` - Update feature flags

### Database
Settings stored in `settings` table with `setting_group = 'features'`

---

---

### 15. Support Ticket System (High Priority) - UI Partial, API Complete

#### Admin Support Tickets ✅ UI Complete
- [x] Support tickets list page (`/admin/support`)
- [x] Ticket stats cards (open, in progress, pending, urgent, avg response, satisfaction)
- [x] Filter by status, priority, category
- [x] Search by ticket #, subject, customer
- [x] Ticket detail page with conversation view
- [x] Add reply functionality
- [x] Status update dropdown
- [x] Canned responses
- [x] "New Ticket" modal (UI only)
- [x] **Create Ticket functionality** - modal submit with API integration ✅

#### Create Ticket Enhancement ✅ COMPLETE
- [x] Customer lookup by email in modal (with debounce)
- [x] Auto-populate customer info when found
- [x] Call `POST /admin/support/tickets` API on submit
- [x] Success/error feedback with toast notifications
- [x] Redirect to ticket detail on success
- [x] Form reset on modal close

#### Admin Use Cases for Creating Tickets
- Phone calls - customer calls, agent creates ticket on their behalf
- Proactive outreach - admin notices issue, creates ticket to track resolution
- Internal escalation - staff creates ticket for complex issues
- Order follow-ups - create ticket linked to specific order

#### API Endpoints (in pecos-backendadmin-api) ✅ COMPLETE:
- `GET /api/v1/admin/support/tickets` - List tickets with filters
- `GET /api/v1/admin/support/tickets/stats` - Ticket statistics
- `GET /api/v1/admin/support/tickets/{id}` - Ticket details with messages
- `POST /api/v1/admin/support/tickets` - Create ticket
- `PUT /api/v1/admin/support/tickets/{id}` - Update ticket status/priority
- `POST /api/v1/admin/support/tickets/{id}/messages` - Add message/reply
- `POST /api/v1/admin/support/tickets/{id}/rating` - Add customer rating
- `GET /api/v1/admin/support/canned-responses` - List canned responses
- `POST /api/v1/admin/support/canned-responses` - Create canned response
- `PUT /api/v1/admin/support/canned-responses/{id}` - Update canned response
- `DELETE /api/v1/admin/support/canned-responses/{id}` - Delete canned response

**UI Page:** `support-tickets.blade.php`, `support-ticket-detail.blade.php`

---

### 16. Customer Support Requests (Frontend) - PLANNED

Customer-facing support request system under account section.

#### Features to Implement
- [ ] "Support Requests" page in customer account
- [ ] View list of existing tickets
  - Ticket number, subject, status, date
  - Filter by status (open, resolved, closed)
- [ ] View ticket details with conversation history
- [ ] Create new support request
  - Subject field
  - Category dropdown (Order Issue, Return/Exchange, Product Question, Shipping, Billing, Other)
  - Order selection (optional - link to specific order)
  - Message textarea
  - File attachment (images for product issues)
- [ ] Reply to existing tickets
- [ ] Satisfaction rating after ticket resolved

#### Customer API Endpoints Needed:
```
GET    /api/v1/customer/support/tickets           - List customer's tickets
GET    /api/v1/customer/support/tickets/{id}      - Ticket details
POST   /api/v1/customer/support/tickets           - Create ticket
POST   /api/v1/customer/support/tickets/{id}/reply - Add reply
POST   /api/v1/customer/support/tickets/{id}/rate  - Rate resolved ticket
```

#### Database Tables ✅ EXISTING:
- `support_tickets` - Main ticket records
- `ticket_messages` - Conversation messages
- `ticket_attachments` - File attachments (if implemented)

#### Navigation Integration
- Add "Support Requests" to account dropdown
- Add "Submit a Support Request" link on Contact page (for logged-in users)

---

**Last Updated**: 2025-12-22
