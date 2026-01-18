# Pecos River Trading Post - Backend Admin Site

**Laravel 11 Administration Panel**

Last Updated: 2026-01-07

## 🌟 Overview

Modern, feature-rich Laravel-based administration panel for managing all aspects of the Pecos River Trading Post e-commerce platform.

## 🚀 Quick Access

| Service | URL |
|---------|-----|
| **Admin Panel** | http://localhost:8301/adminpanel |
| **API** | http://localhost:8300/api/v1 |
| **Storefront** | http://localhost:8300 |
| **phpMyAdmin** | http://localhost:8380 |

- **Technology**: Laravel 11 + PHP 8.2+

## 🛠 Technology Stack

- **Framework**: Laravel 11
- **PHP**: 8.2+
- **Frontend**: Blade Templates, Bootstrap 5, JavaScript
- **Database**: MySQL (shared with frontend)
- **Authentication**: Laravel Breeze
- **Server**: Apache/Nginx

## 📁 Project Structure

```
pecos-backend-admin-site/
├── app/
│   ├── Http/
│   │   ├── Controllers/     # Admin controllers
│   │   └── Middleware/      # Custom middleware
│   └── Models/              # Future Eloquent models
├── resources/
│   └── views/
│       ├── admin/           # Admin views
│       │   ├── categories.blade.php
│       │   ├── products.blade.php
│       │   ├── orders.blade.php
│       │   ├── inventory.blade.php
│       │   └── ...
│       └── layouts/         # Layout templates
├── routes/
│   └── web.php             # Web routes
├── public/
│   ├── css/                # Styles
│   ├── js/                 # Scripts
│   └── assets/             # Static files
├── docs/                   # Documentation
└── maintenance/            # Maintenance scripts
```

## ✨ Key Features

### Dashboard
- Real-time sales metrics
- Recent order activity
- Inventory alerts
- Quick action buttons
- Performance charts

### Product Management
- Complete CRUD operations
- Bulk import/export
- Image management
- Variant handling (sizes/colors)
- Inventory tracking
- Category assignment

### Order Management
- Order listing with filters
- Status updates
- Customer information
- Invoice generation
- Shipping tracking
- **Row highlighting on click** (light blue)

### Inventory Management
- Stock level monitoring
- Low stock alerts
- Stock movement tracking
- Reorder recommendations
- Valuation reports
- **Interactive tables with row selection**

### Admin Profile
- Account information display
- Profile editing modal
- Change password modal (accessibility compliant)
- Notification preferences
- Activity log

### Customer Management
- Customer database
- Order history
- Account management
- Email communication

### Content Management
- Blog posts
- Events calendar
- FAQ management
- Page editing

### Marketing Tools
- Coupon management
- Gift card administration
- Loyalty program
- Newsletter management

### Support Tickets
- Ticket listing with filters (status, priority, category)
- Ticket stats dashboard (open, in progress, pending, urgent)
- Ticket detail with conversation view
- Add replies and status updates
- Canned responses management
- Create ticket modal (enhancement in progress)
- Customer lookup by email

### Branding & Theming
- **Store Info**: Name, tagline, contact details, name styling (font size, color)
- **Logo Settings**: Alignment (Left, Center, Right) with live preview
  - **Left**: Logo far left, nav links far right
  - **Center**: Logo centered, nav links centered below
  - **Right**: Logo far right, nav links far left (mirror of left)
- **Header Styling**: Colors, nav height (50-100px), style (solid/gradient/transparent), sticky, shadow
- **Header Preview**: Real-time preview updates when changing logo alignment or colors
- **Announcement Bar**: Enable/disable, text, colors
- **Theme Colors**: Primary, secondary, accent, text, background with live preview
- **Tooltips**: All color pickers show "Click on color to see Color Wheel"
- **Logo Image**: Uses `PRT-High-Res-Logo.png` from prt4/assets/images

### Drop Shipping
- Partner management
- Order routing
- Commission tracking
- API key management

### Reports & Analytics
- Sales reports
- Inventory reports
- Customer analytics
- Export functionality

## 🎨 UI Enhancements

### Interactive Tables
- **Row Highlighting**: Click any row to highlight in light blue (#e3f2fd)
- Hover effects
- Sortable columns
- Search and filters
- Pagination
- Responsive design

### Implemented On:
- ✅ Orders
- ✅ Products
- ✅ Categories
- ✅ Customers
- ✅ Users
- ✅ Inventory
- ✅ Blog
- ✅ Events
- ✅ Reviews
- ✅ Coupons
- ✅ Gift Cards
- ✅ Loyalty Members
- ✅ Drop Shippers
- ✅ API Logs
- ✅ All Report Tables

## 🔧 Installation

```bash
# Install dependencies
composer install
npm install && npm run build

# Configure environment
cp .env.example .env
php artisan key:generate

# Start server
php artisan serve --port=8001
```

See [docs/SETUP.md](docs/SETUP.md) for detailed instructions.

## 📚 Documentation

- [Setup Guide](docs/SETUP.md)
- [Features](docs/FEATURES.md)
- [UI Enhancements](docs/UI_ENHANCEMENTS.md)
- [API Integration](docs/api-integration-tracker.md)

## 🔗 Related Projects

- **Storefront**: http://localhost:8300/ (Customer site)
- **Backend API**: http://localhost:8300/api/v1 (RESTful API)

## 🔒 Security

- Laravel's built-in security features
- CSRF protection
- XSS prevention
- SQL injection protection
- Session management
- Role-based access control

---

**Part of the Pecos River Trading Post E-Commerce Platform**