# Web Services Architecture - Pecos River Trading Company

## Overview

This document outlines the web services layer that will serve as the foundation for:
- Mobile applications (iOS/Android)
- Web frontend (React, Vue, or current PHP)
- Admin backend dashboard
- Third-party integrations (drop shippers, payment gateways)
- Future microservices expansion

Building a robust API layer **before** the admin backend ensures consistent data access patterns and enables multiple client applications.

---

## Framework Comparison

### Option 1: Laravel (Recommended)

**Description:** Full-featured PHP framework with built-in API support, ORM, and extensive ecosystem.

#### Pros
- **Eloquent ORM** - Elegant database abstraction, relationships, migrations
- **Built-in API Resources** - JSON transformation and versioning
- **Authentication** - Laravel Sanctum for API tokens, Passport for OAuth2
- **Validation** - Powerful request validation with custom rules
- **Queue System** - Background jobs for emails, webhooks, reports
- **Scheduling** - Cron replacement for inventory sync, reports
- **Testing** - PHPUnit integration, API testing helpers
- **Documentation** - Excellent docs and large community
- **Ecosystem** - Cashier (payments), Scout (search), Horizon (queues)
- **Migration Path** - Can run alongside existing PHP code

#### Cons
- **Learning Curve** - More complex than raw PHP
- **Overhead** - Heavier than micro-frameworks
- **Hosting** - Requires PHP 8.1+, Composer, may need VPS
- **Migration Effort** - Need to recreate models from existing tables

#### Estimated Setup Time
- Initial setup: 1-2 days
- Core API endpoints: 2-3 weeks
- Full feature parity: 4-6 weeks

---

### Option 2: CodeIgniter 4

**Description:** Lightweight PHP framework with minimal configuration, good for simpler APIs.

#### Pros
- **Lightweight** - Small footprint, fast execution
- **Simple Structure** - Easy to understand MVC
- **Shared Hosting** - Works on basic PHP hosting
- **Quick Setup** - Minimal configuration required
- **Familiar** - Closer to traditional PHP patterns
- **Low Learning Curve** - Easy for PHP developers

#### Cons
- **Limited ORM** - Basic query builder, no Eloquent-like features
- **Smaller Ecosystem** - Fewer packages and extensions
- **Manual Work** - More boilerplate for API resources
- **Authentication** - Must build or use Shield package
- **Less Modern** - Not as feature-rich as Laravel

#### Estimated Setup Time
- Initial setup: 1 day
- Core API endpoints: 3-4 weeks
- Full feature parity: 5-7 weeks

---

### Option 3: Slim Framework + Custom

**Description:** Micro-framework for building APIs with minimal overhead.

#### Pros
- **Ultra Lightweight** - Minimal footprint
- **Full Control** - Build exactly what you need
- **Fast** - Excellent performance
- **PSR Standards** - Uses PSR-7/PSR-15 middleware
- **Flexible** - Mix with any libraries

#### Cons
- **Build Everything** - No ORM, auth, validation included
- **More Code** - Must write more boilerplate
- **No Ecosystem** - Pick and integrate packages manually
- **Documentation** - Less comprehensive than Laravel
- **Maintenance** - More custom code to maintain

#### Estimated Setup Time
- Initial setup: 2-3 days
- Core API endpoints: 4-5 weeks
- Full feature parity: 6-8 weeks

---

### Option 4: Native PHP + Modern Practices

**Description:** Continue with current PHP but add API layer using PSR standards and modern packages.

#### Pros
- **No Framework Learning** - Use existing PHP knowledge
- **Gradual Migration** - Add API endpoints incrementally
- **Full Control** - Complete flexibility
- **No Overhead** - Direct database access
- **Existing Code** - Reuse current PHP functions

#### Cons
- **More Work** - Build routing, middleware, validation
- **Inconsistency Risk** - Without framework conventions
- **Security** - Must handle all security concerns manually
- **Scalability** - Harder to maintain as project grows
- **Testing** - More setup for testing infrastructure

#### Estimated Setup Time
- Initial setup: 3-4 days
- Core API endpoints: 4-6 weeks
- Full feature parity: 7-9 weeks

---

## Recommendation: Laravel

**Why Laravel?**

1. **Best API Support** - API Resources, versioning, rate limiting built-in
2. **Authentication Ready** - Sanctum handles tokens for mobile + SPA
3. **Queue System** - Essential for webhooks, email, inventory sync
4. **Migration Support** - Database versioning for team development
5. **Future Proof** - Can expand to full web app if needed
6. **Community** - Largest PHP community, packages for everything
7. **Modern PHP** - Uses latest PHP features, PSR standards

**Migration Strategy:**
1. Install Laravel alongside existing `/prt2` directory
2. Connect to same MySQL database
3. Build API endpoints that read/write to existing tables
4. Current PHP site continues working during transition
5. Gradually move admin to Laravel or build separate SPA

---

## Proposed API Architecture

### Directory Structure (Laravel)
```
prt2-api/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Api/
│   │   │       └── V1/
│   │   │           ├── ProductController.php
│   │   │           ├── CategoryController.php
│   │   │           ├── OrderController.php
│   │   │           ├── CustomerController.php
│   │   │           ├── CartController.php
│   │   │           ├── AuthController.php
│   │   │           ├── GiftCardController.php
│   │   │           ├── LoyaltyController.php
│   │   │           └── DropshipController.php
│   │   ├── Middleware/
│   │   │   ├── ApiKeyAuth.php
│   │   │   └── RateLimit.php
│   │   └── Resources/
│   │       └── V1/
│   │           ├── ProductResource.php
│   │           ├── OrderResource.php
│   │           └── CustomerResource.php
│   ├── Models/
│   │   ├── Product.php
│   │   ├── Category.php
│   │   ├── Order.php
│   │   ├── Customer.php
│   │   ├── GiftCard.php
│   │   └── LoyaltyMember.php
│   └── Services/
│       ├── InventoryService.php
│       ├── PaymentService.php
│       └── WebhookService.php
├── routes/
│   └── api.php
├── database/
│   └── migrations/
└── tests/
    └── Feature/
        └── Api/
```

---

## API Endpoints Specification

### Authentication
```
POST   /api/v1/auth/register         - Customer registration
POST   /api/v1/auth/login             - Customer login (returns token)
POST   /api/v1/auth/logout            - Revoke token
POST   /api/v1/auth/forgot-password   - Send reset email
POST   /api/v1/auth/reset-password    - Reset with token
GET    /api/v1/auth/user              - Get current user
```

### Products
```
GET    /api/v1/products               - List products (paginated, filterable)
GET    /api/v1/products/{id}          - Single product details
GET    /api/v1/products/{id}/reviews  - Product reviews
GET    /api/v1/products/featured      - Featured products
GET    /api/v1/products/search        - Search products
```

### Categories
```
GET    /api/v1/categories             - List all categories
GET    /api/v1/categories/{id}        - Category with products
GET    /api/v1/categories/tree        - Nested category tree
```

### Cart
```
GET    /api/v1/cart                   - Get cart contents
POST   /api/v1/cart/items             - Add item to cart
PUT    /api/v1/cart/items/{id}        - Update quantity
DELETE /api/v1/cart/items/{id}        - Remove item
POST   /api/v1/cart/coupon            - Apply coupon code
DELETE /api/v1/cart/coupon            - Remove coupon
```

### Orders
```
GET    /api/v1/orders                 - Customer's orders
GET    /api/v1/orders/{id}            - Order details
POST   /api/v1/orders                 - Create order (checkout)
POST   /api/v1/orders/{id}/cancel     - Cancel order
```

### Customer Profile
```
GET    /api/v1/profile                - Get profile
PUT    /api/v1/profile                - Update profile
GET    /api/v1/profile/addresses      - List addresses
POST   /api/v1/profile/addresses      - Add address
PUT    /api/v1/profile/addresses/{id} - Update address
DELETE /api/v1/profile/addresses/{id} - Delete address
GET    /api/v1/profile/wishlist       - Get wishlist
POST   /api/v1/profile/wishlist       - Add to wishlist
DELETE /api/v1/profile/wishlist/{id}  - Remove from wishlist
```

### Reviews
```
GET    /api/v1/reviews                - Customer's reviews
POST   /api/v1/reviews                - Submit review
PUT    /api/v1/reviews/{id}           - Update review
DELETE /api/v1/reviews/{id}           - Delete review
```

### Gift Cards
```
GET    /api/v1/gift-cards/balance     - Check balance
POST   /api/v1/gift-cards/purchase    - Purchase gift card
POST   /api/v1/gift-cards/redeem      - Redeem at checkout
```

### Loyalty Program
```
GET    /api/v1/loyalty                - Member status & points
GET    /api/v1/loyalty/history        - Points history
GET    /api/v1/loyalty/rewards        - Available rewards
POST   /api/v1/loyalty/redeem         - Redeem reward
```

### Content
```
GET    /api/v1/blog                   - Blog posts
GET    /api/v1/blog/{slug}            - Single post
GET    /api/v1/events                 - Upcoming events
GET    /api/v1/faqs                   - FAQ list
```

### Admin Endpoints (Separate Auth)
```
# Products
GET    /api/v1/admin/products         - List all products
POST   /api/v1/admin/products         - Create product
PUT    /api/v1/admin/products/{id}    - Update product
DELETE /api/v1/admin/products/{id}    - Delete product
POST   /api/v1/admin/products/import  - Bulk import

# Orders
GET    /api/v1/admin/orders           - All orders
PUT    /api/v1/admin/orders/{id}      - Update order status
GET    /api/v1/admin/orders/export    - Export orders

# Customers
GET    /api/v1/admin/customers        - All customers
GET    /api/v1/admin/customers/{id}   - Customer details

# Reports
GET    /api/v1/admin/reports/sales    - Sales report
GET    /api/v1/admin/reports/inventory - Inventory report

# And more...
```

### Drop Shipper Endpoints
```
GET    /api/v1/dropship/products      - Product catalog
GET    /api/v1/dropship/inventory     - Stock levels
POST   /api/v1/dropship/orders        - Create order
GET    /api/v1/dropship/orders/{id}   - Order status
```

---

## Authentication Strategy

### Customer Authentication (Sanctum)
- Token-based authentication for mobile/SPA
- Cookie-based for traditional web
- Token abilities for permission scoping

### Admin Authentication
- Same Sanctum tokens with admin abilities
- IP restrictions optional
- 2FA support via packages

### Drop Shipper Authentication
- API key + secret in headers
- HMAC signature for sensitive operations
- Rate limiting per key

---

## Implementation Phases

### Phase 1: Foundation (Week 1-2)
- [ ] Laravel installation and configuration
- [ ] Database connection to existing MySQL
- [ ] Create Eloquent models for existing tables
- [ ] Setup authentication (Sanctum)
- [ ] API versioning structure
- [ ] Error handling and responses
- [ ] Rate limiting
- [ ] CORS configuration
- [ ] Basic documentation setup

### Phase 2: Core E-commerce (Week 3-4)
- [ ] Products API (list, detail, search, filter)
- [ ] Categories API
- [ ] Cart API (add, update, remove)
- [ ] Customer auth (register, login, profile)
- [ ] Reviews API
- [ ] Wishlist API

### Phase 3: Orders & Checkout (Week 5-6)
- [ ] Order creation
- [ ] Order history
- [ ] Address management
- [ ] Payment integration (Stripe/PayPal)
- [ ] Order status updates
- [ ] Email notifications

### Phase 4: Admin API (Week 7-8)
- [ ] Product management
- [ ] Order management
- [ ] Customer management
- [ ] Inventory management
- [ ] Reports

### Phase 5: Advanced Features (Week 9-10)
- [ ] Gift cards
- [ ] Loyalty program
- [ ] Drop shipper API
- [ ] Webhooks
- [ ] Blog/content API

### Phase 6: Testing & Documentation (Week 11-12)
- [ ] Unit tests
- [ ] Feature tests
- [ ] API documentation (Swagger/OpenAPI)
- [ ] Postman collection
- [ ] Performance optimization

---

## Technical Requirements

### Server Requirements (Laravel 10)
- PHP >= 8.1
- Composer
- MySQL 5.7+ (existing)
- BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML extensions

### Development Tools
- Laravel Telescope (debugging)
- Laravel Sanctum (API auth)
- Laravel Horizon (queues - optional)
- Swagger/OpenAPI (documentation)

### Deployment Options
1. **Subdomain:** `api.pecosrivertraders.com`
2. **Subdirectory:** `pecosrivertraders.com/api`
3. **Same server** as current site initially

---

## Database Considerations

### Using Existing Tables
Laravel can work with existing table structures:

```php
// Model example for existing Products table
class Product extends Model
{
    protected $table = 'Products';
    protected $primaryKey = 'ProductID';
    public $timestamps = false; // If no created_at/updated_at

    protected $fillable = [
        'ProductName', 'SKU', 'Price', 'Description', 'CategoryID', 'Stock'
    ];
}
```

### New Tables
Use Laravel migrations for new tables (orders, gift_cards, loyalty, etc.) to maintain version control.

---

## Security Considerations

- **HTTPS Only** - All API endpoints
- **Rate Limiting** - Per user/IP
- **Input Validation** - Laravel Form Requests
- **SQL Injection** - Eloquent prevents this
- **XSS** - JSON responses, no HTML rendering
- **CSRF** - Not needed for token auth
- **Authentication** - Sanctum tokens with expiration
- **Authorization** - Policies and Gates
- **Logging** - All requests logged
- **Secrets** - Environment variables

---

## Estimated Total Effort

| Approach | Setup | Core API | Full Features | Total |
|----------|-------|----------|---------------|-------|
| **Laravel** | 1-2 days | 2-3 weeks | 4-6 weeks | **6-8 weeks** |
| CodeIgniter | 1 day | 3-4 weeks | 5-7 weeks | 8-10 weeks |
| Slim | 2-3 days | 4-5 weeks | 6-8 weeks | 10-12 weeks |
| Native PHP | 3-4 days | 4-6 weeks | 7-9 weeks | 11-14 weeks |

**Recommendation:** Laravel provides the best balance of development speed, features, and maintainability. The initial learning investment pays off quickly with built-in solutions for authentication, validation, queues, and more.

---

## Next Steps

1. **Decision:** Confirm Laravel as the framework choice
2. **Environment:** Set up development environment (Laravel Herd, Valet, or Docker)
3. **Installation:** Create new Laravel project
4. **Database:** Connect to existing MySQL, create models
5. **Auth:** Implement Sanctum authentication
6. **First Endpoint:** Build products API as proof of concept
7. **Documentation:** Set up Swagger for API docs

---

## Related Documentation

- [Backend Admin Roadmap](./backend-admin.md) - Admin panel features
- [Coding Standards](./CODING_STANDARDS.md) - PHP coding conventions
- [Laravel Documentation](https://laravel.com/docs) - Official Laravel docs
