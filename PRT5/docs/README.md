# PRT5 - Pecos River Traders (Laravel)

Last Updated: January 7, 2026

## Overview

PRT5 is the Laravel 12 conversion of the prt4 PHP frontend for Pecos River Trading Company e-commerce platform. This project maintains full compatibility with the existing backend APIs and database while providing the benefits of Laravel's MVC architecture.

## Project Structure

```
PRT5/
├── app/
│   ├── Http/
│   │   └── Controllers/         # Request handlers
│   ├── Models/                  # Eloquent models
│   └── Services/                # API integration services
├── resources/
│   ├── views/
│   │   ├── components/          # Blade components (header, footer)
│   │   ├── layouts/             # Layout templates
│   │   ├── shop/                # Shop pages (products, cart, checkout)
│   │   ├── account/             # User account pages
│   │   └── auth/                # Authentication pages
├── public/
│   └── assets/                  # Static files (CSS, JS, images)
├── routes/
│   └── web.php                  # Route definitions
└── docs/                        # Documentation
```

## Backend Integration

PRT5 connects to the same backend systems:

### Admin API (http://localhost:8300/api/v1)
- **Branding Settings**: `/api/v1/admin/settings`
- **Feature Flags**: `/api/v1/admin/settings/features`
- **Featured Categories**: `/api/v1/featured-categories`
- **Featured Products**: `/api/v1/featured-products`
- **Category Display**: `/api/v1/admin/settings/category_display`
- **Blog Posts**: `/api/v1/blog`
- **Events**: `/api/v1/events`

### Database (pecosriver)
- Products: `products3` table
- Categories: `categories` table
- Users: `users` table
- Orders: `orders`, `order_items` tables
- Loyalty: `loyalty_members`, `loyalty_transactions` tables

## Services

### BrandingService
Fetches branding/theme settings from the admin API.

```php
use App\Services\BrandingService;

$brandingService = new BrandingService();
$settings = $brandingService->getSettings();
$navbarClasses = $brandingService->getNavbarClasses();
$themeCSS = $brandingService->getThemeCSS();
$navbarCSS = $brandingService->getNavbarCSS();
$announcementBar = $brandingService->getAnnouncementBar();
```

**Settings included:**
- Logo alignment (left, center, right)
- Header colors (background, text, hover)
- Header style (solid, gradient, transparent)
- Sticky header, shadow
- Announcement bar
- Theme colors (primary, secondary, accent)

### FeaturesService
Fetches feature flags from the admin API.

```php
use App\Services\FeaturesService;

$featuresService = new FeaturesService();
$isEnabled = $featuresService->isEnabled('loyalty');
$config = $featuresService->getConfig('digital_download_categories');
```

**Features controlled:**
- FAQ, Loyalty, Gift Cards, Wishlists
- Blog, Events, Reviews
- Newsletter, Admin Link

### HomepageService
Fetches homepage configuration from the admin API.

```php
use App\Services\HomepageService;

$homepageService = new HomepageService();
$categories = $homepageService->getFeaturedCategoriesData();
$products = $homepageService->getFeaturedProductsData();
$displayStyle = $homepageService->getCategoryDisplayStyle();
```

## Running the Application

### Development Server
```bash
cd C:\xampp\htdocs\PRT5
php artisan serve --port=8005
```

### Required Services (Docker)

| Service | URL | Description |
|---------|-----|-------------|
| **Storefront** | http://localhost:8300 | PRT5 frontend |
| **API** | http://localhost:8300/api/v1 | Backend API |
| **Admin Panel** | http://localhost:8301/adminpanel | Admin dashboard |
| **MySQL** | localhost:3307 | Database server |
| **phpMyAdmin** | http://localhost:8380 | Database management |

### Environment Configuration
Copy `.env.example` to `.env` and configure:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pecosriver
DB_USERNAME=root
DB_PASSWORD=
```

## Documentation Index

- [API Integration](API_INTEGRATION.md) - Backend API integration details
- [Services](SERVICES.md) - Service class documentation
- [Models](MODELS.md) - Eloquent model documentation

## Comparison with prt4

| Feature | prt4 | PRT5 |
|---------|------|------|
| Framework | Plain PHP | Laravel 12 |
| Template Engine | PHP includes | Blade |
| Routing | File-based | Route definitions |
| Database | PDO | Eloquent ORM |
| Sessions | PHP sessions | Laravel sessions |
| Caching | File/Session | Laravel Cache |
| Assets | Direct | asset() helper |

## Related Projects (Docker)

| Project | URL | Description |
|---------|-----|-------------|
| PRT5 (Storefront) | http://localhost:8300 | Customer-facing site |
| Backend API | http://localhost:8300/api/v1 | REST API |
| Admin Panel | http://localhost:8301/adminpanel | Admin dashboard |
| phpMyAdmin | http://localhost:8380 | Database management |

## Documentation Locations

| Project | File | Description |
|---------|------|-------------|
| API | README.md | Project overview |
| API | docs/BRANDING_SYSTEM.md | Branding API reference |
| Admin | README.md | Project overview |
| Admin | docs/SETTINGS_PAGE.md | Settings page reference |
| prt4 | docs/README.md | Documentation index |
| prt4 | docs/backend-admin.md | Admin system roadmap |
| PRT5 | docs/README.md | This file |
| PRT5 | docs/API_INTEGRATION.md | API integration guide |
