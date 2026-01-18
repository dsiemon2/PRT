# Backend Admin Site - Features

Last Updated: November 28, 2025

## 🎯 Complete Feature List

### Top Navigation Bar
- **Stock Alerts Dropdown**
  - Real-time alerts from API (`/api/v1/admin/inventory/stock-alerts`)
  - Shows low stock and out of stock products
  - Badge count for total alerts
  - Links to full alerts page
- **Messages Dropdown**
  - Contact messages from API
  - Sample data fallback when API unavailable
  - Quick preview of recent messages
- **User Profile Dropdown**
  - Profile link
  - Settings link
  - Logout functionality

### Profile Page (`/admin/profile`)
- **Account Information**
  - Display user name, email, role
  - Member since date
  - Last login tracking
- **Profile Editing**
  - Edit name, email, phone
  - Modal-based interface
- **Password Management**
  - Change password modal
  - Current/new password fields
  - Accessibility compliant
- **Notification Preferences**
  - Email notifications toggle
  - New order alerts
  - Low stock alerts
  - Review alerts
- **Activity Log**
  - Recent login history
  - Recent actions taken
  - Timestamped entries

### Dashboard
- **Metrics Display**
  - Total sales (today, week, month)
  - Order statistics
  - Customer count
  - Inventory alerts
- **Quick Actions**
  - Add product
  - Process order
  - View alerts
- **Recent Activity**
  - Latest orders
  - Recent products
  - System notifications

### Product Management
- **Product Listing**
  - Searchable table
  - Category filters
  - Stock status filters
  - **Row highlighting on click**
  - Sortable columns
  - Pagination

- **Product Operations**
  - Create new products
  - Edit existing products
  - Delete products
  - Bulk operations
  - Import/Export (CSV/Excel)

- **Product Details**
  - Basic information
  - Pricing (regular, sale, cost)
  - Inventory tracking
  - Multiple images
  - Variants (sizes, colors)
  - SEO settings

### Order Management
- **Order Listing**
  - **Interactive table with row highlighting**
  - Filter by status, date, customer
  - Search by order number
  - Sort by various fields
  - Export orders

- **Order Operations**
  - View order details
  - Update status
  - Process refunds
  - Generate invoices
  - Add tracking numbers
  - Send notifications

- **Order Statuses**
  - Pending
  - Processing
  - Shipped
  - Delivered
  - Cancelled
  - Refunded

### Inventory Management
### Purchase Order Management
**Location**: `/admin/purchase-orders` and `/admin/inventory-receive`

Complete supplier order management system:
- Create purchase orders with multiple line items
- Product dropdown with auto-fill cost
- Calculate totals (subtotal, shipping, tax)
- Filter by status and supplier
- Track PO status workflow
- Statistics dashboard
- Barcode scanner support for receiving
- Track ordered vs received quantities
- Condition tracking (good/damaged/defective)
- Automatic stock updates on receiving
- Receiving session log
- Row highlighting for navigation


- **Stock Overview**
  - Current levels
  - Stock value
  - Low stock alerts
  - Out of stock items
  - **Interactive table rows**

- **Stock Operations**
  - Add stock
  - Remove stock
  - Adjust quantities
  - Set reorder points
  - Track movements

- **Reports**
  - Inventory valuation
  - Stock status report
  - Movement history
  - Low stock report
  - **All reports have row highlighting**

### Customer Management
- **Customer Database**
  - **Click-to-highlight table rows**
  - Search customers
  - Filter by various criteria
  - View order history
  - Customer segments

- **Customer Operations**
  - View details
  - Edit information
  - Send emails
  - Manage accounts
  - Track activity

### Content Management
- **Blog**
  - **Interactive post listing**
  - Create/edit posts
  - Categories
  - Featured images
  - SEO settings
  - Publish/draft/schedule

- **Events**
  - **Event table with row highlighting**
  - Event calendar
  - Add/edit events
  - RSVP tracking

- **FAQ**
  - **Interactive FAQ management**
  - Question/answer pairs
  - Categories
  - Search indexing

### Marketing Tools
- **Coupons**
  - **Coupon table with row selection**
  - Create discount codes
  - Set usage limits
  - Expiration dates
  - Category restrictions

- **Gift Cards**
  - **Interactive gift card listing**
  - Generate codes
  - Track balances
  - Redemption history

- **Loyalty Program**
  - **Member table with highlighting**
  - Tier configuration
  - Points tracking
  - Rewards management

### Drop Shipping
- **Drop Shipper Management**
  - **Interactive partner table**
  - Add/edit partners
  - API key management
  - Commission rates
  - Status management

- **Drop Ship Orders**
  - **Order table with row highlighting**
  - Order routing
  - Status tracking
  - Commission calculation

### Reports & Analytics
- **All report tables feature row highlighting**
- Sales reports
- Customer analytics
- Product performance
- Inventory reports
- Export to CSV/Excel

### Users & Permissions
- **User Management**
  - **User table with row selection**
  - Add/edit admin users
  - Role assignment
  - Activity logs

- **Permissions**
  - Role-based access control
  - Custom permissions
  - Access levels

### API Logs
- **Log Viewer**
  - **Interactive log table**
  - Filter by endpoint, status
  - Search functionality
  - View request/response details

### Feature Configuration (`/admin/features`)
- **Feature Toggles**
  - FAQ, Loyalty, Blog, Events, Reviews, etc.
  - Enable/disable features across entire system
  - Quick Enable All / Disable All buttons
- **Live Chat Configuration**
  - Master Live Chat toggle
  - Tawk.to provider support
    - Property ID and Widget ID fields
    - Auto-disable other providers
  - Tidio provider support
    - Public Key field
    - Auto-disable other providers
  - Only one chat provider active at a time
  - Settings saved to API and loaded by frontend
- **Notification Configuration**
  - Master notification toggle (enable/disable all notifications)
  - Channel toggles:
    - Email notifications
    - SMS notifications (Twilio)
    - Push notifications (Web Push)
  - Category toggles:
    - Delivery updates (shipped, delivered, tracking)
    - Promotional (sales, back-in-stock alerts)
    - Payment notifications (received, failed, refunds)
    - Security alerts (login, password changes)
  - Frontend respects admin settings (hides disabled options)
  - Settings saved to API and loaded by NotificationService
- **Tax Calculation Provider** (NEW)
  - Master tax calculation toggle
  - **Custom Tax Table** (default):
    - Use admin-configured rates in Settings > Tax
    - Free, full control, offline capable
    - Best for: Simple US-only sales
  - **Stripe Tax** integration:
    - Automatic tax calculation via Stripe
    - 0.5% per transaction, global coverage, nexus tracking
    - Best for: Stripe users wanting automated compliance
    - Tax shown as "Calculated at payment"
  - **TaxJar** integration:
    - Professional tax automation service
    - $19-99/month, multi-channel, auto filing
    - Best for: High volume, multi-channel sellers
    - API Token and Sandbox Mode configuration
  - Comparison table showing feature differences
  - Automatic fallback to Custom Tax Table if API fails
- **Payment Gateway Configuration**
  - Master payment processing toggle
  - **Stripe** integration:
    - Supports all major cards + Apple Pay + Google Pay
    - Publishable Key and Secret Key configuration
    - Test Mode toggle for development
    - **ACH Bank Transfers** toggle for low-cost US bank payments (0.8% capped at $5)
  - **Braintree (PayPal)** integration:
    - Supports cards + PayPal + Venmo + digital wallets
    - Merchant ID, Public Key, Private Key configuration
    - Sandbox Mode toggle for testing
  - **PayPal Checkout** integration:
    - PayPal Express Checkout
    - Client ID and Client Secret configuration
    - Sandbox Mode toggle
  - Multiple gateways can be enabled simultaneously
  - Settings saved to API for frontend checkout use
  - **Frontend Integration**: PRT2 checkout page dynamically loads enabled gateways

### Stock Alerts (`/admin/inventory/alerts`)
- **Out of Stock Items**
  - List products with zero available stock
  - Quick Add Stock button
  - Create Reorder button
- **Low Stock Items**
  - List products below reorder point
  - Shows available quantity vs reorder threshold
  - Quick Add Stock with correct available quantity display
- **Alert Settings**
  - Default low stock threshold configuration
  - Email alert frequency (Daily, Immediate, Weekly, Disabled)
- **Add Stock Modal**
  - Shows product name, SKU, current available stock
  - Quantity to add input
  - Notes field for tracking
  - Calls `/admin/inventory/adjust-stock` API

## 🎨 UI/UX Features

### Interactive Tables
**Implemented across all admin pages:**
- Click any row to highlight (light blue #e3f2fd)
- Hover effects for better visibility
- Doesn't interfere with action buttons
- Clear visual feedback
- Consistent across all pages

### Table Features
- Search functionality
- Column sorting
- Filtering options
- Pagination
- Responsive design
- Mobile-friendly
- Export capabilities

### Pages with Row Highlighting
1. ✅ Dashboard - Recent activity
2. ✅ Orders - All orders
3. ✅ Products - Product listing
4. ✅ Categories - Category management
5. ✅ Customers - Customer database
6. ✅ Users - User management
7. ✅ Inventory - Stock levels
8. ✅ Inventory Alerts - Low stock
9. ✅ Inventory Reports - All report tables
10. ✅ Blog - Post management
11. ✅ Events - Event listing
12. ✅ Reviews - Review management
13. ✅ Coupons - Coupon management
14. ✅ Gift Cards - Gift card management
15. ✅ Loyalty - Member and tier tables
16. ✅ Drop Shippers - Partner management
17. ✅ Drop Ship Orders - Order management
18. ✅ API Logs - Request logs
19. ✅ FAQ Stats - Performance tables

### Responsive Design
- Mobile-optimized
- Tablet-friendly
- Desktop enhanced
- Touch-friendly buttons

### Notifications
- Success messages
- Error alerts
- Warning prompts
- Confirmation dialogs

## 🔒 Security Features

- Laravel's built-in security
- CSRF protection
- XSS prevention
- SQL injection protection
- Session management
- Role-based access control
- Activity logging
- IP tracking

## 🚀 Performance

- Lazy loading
- Pagination
- Cached queries
- Optimized assets
- Database indexing

---

For setup instructions, see [SETUP.md](SETUP.md).
For UI enhancement details, see [UI_ENHANCEMENTS.md](UI_ENHANCEMENTS.md).