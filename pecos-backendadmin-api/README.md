# Pecos River Trading Post - Backend API

**RESTful API Service**

Last Updated: 2026-01-15

## 🌟 Overview

PHP-based RESTful API providing data and business logic for both customer-facing frontend and admin panel. Handles all database operations, business rules, and external integrations.

## 🚀 Quick Access

| Service | URL |
|---------|-----|
| **API Base** | http://localhost:8300/api/v1 |
| **Health Check** | http://localhost:8300/api/v1/health |
| **Storefront** | http://localhost:8300 |
| **Admin Panel** | http://localhost:8301/adminpanel |
| **phpMyAdmin** | http://localhost:8380 |

## 🛠 Technology Stack

- **Language**: PHP 8.x
- **Architecture**: RESTful API
- **Database**: MySQL (shared)
- **Format**: JSON
- **Server**: Apache (XAMPP)

## 📁 Project Structure

```
pecos-backendadmin-api/
├── public/
│   ├── index.php          # Entry point
│   ├── api/
│   │   └── v1/           # API endpoints
│   │       ├── admin/    # Admin endpoints
│   │       ├── products/ # Product endpoints
│   │       ├── orders/   # Order endpoints
│   │       └── ...
│   └── assets/           # Static files
├── config/
│   └── database.php      # DB config
├── classes/
│   ├── Product.php
│   ├── Order.php
│   └── ...
├── middleware/
│   ├── cors.php
│   └── auth.php
└── docs/                 # Documentation
```

## ✨ Key Features

### Core Functionality
- RESTful architecture
- JSON request/response
- CORS support
- Error handling
- Input validation
- Rate limiting
- API versioning

### Endpoint Categories

**Public** (no auth):
- Products
- Categories
- Cart operations
- Order creation

**Admin** (auth required):
- Product management
- Order management
- Customer management
- Inventory operations
- Reports & analytics
- Settings & branding configuration
- Support ticket management
  - List/filter tickets
  - Create tickets (on behalf of customers)
  - Update status and priority
  - Add messages/replies
  - Canned responses CRUD

**Customer** (auth required):
- Support tickets (planned)
  - List own tickets
  - Create support request
  - Reply to tickets
  - Rate resolved tickets

**Drop Shipper** (API key):
- Available products
- Drop ship orders
- Status updates
- Tracking

### Payment Gateway System

Modular payment gateway architecture supporting multiple processors:

| Gateway | SDK Package | Features |
|---------|-------------|----------|
| **Stripe** | stripe/stripe-php v16.x | Cards, Apple Pay, Google Pay, ACH |
| **Braintree** | braintree/braintree_php v6.x | Cards, PayPal, Venmo |
| **PayPal** | (via Braintree) | PayPal Checkout |
| **Square** | square/square v38.x | Cards, Square Wallet, Afterpay |
| **Authorize.net** | authorizenet/authorizenet v2.x | Cards, eCheck/ACH |

**Architecture**:
```
app/
├── Contracts/
│   └── PaymentGatewayInterface.php    # Common interface
├── Services/Payments/
│   ├── PaymentManager.php             # Factory/manager
│   ├── StripeGateway.php
│   ├── BraintreeGateway.php
│   ├── PayPalGateway.php
│   ├── SquareGateway.php
│   └── AuthorizeNetGateway.php
└── Http/Controllers/Api/V1/
    └── PaymentController.php          # API endpoints
```

**Payment API Endpoints**:
```bash
GET  /api/v1/payments/gateways           # List available gateways
POST /api/v1/payments/create             # Create payment
GET  /api/v1/payments/{id}               # Retrieve payment
POST /api/v1/payments/{id}/confirm       # Confirm payment
POST /api/v1/payments/{id}/cancel        # Cancel payment
POST /api/v1/payments/{id}/refund        # Refund payment
POST /api/v1/webhooks/payment/{gateway}  # Webhook handler
```

**Configuration**: Payment gateways are configured via Admin Panel > Features > Payment Gateway Configuration. Settings stored in `settings` table with `setting_group = 'features'`.

## 🔧 Installation

**Docker (Recommended):**
```bash
# From PRT root directory
docker-compose up -d

# API available at: http://localhost:8300/api/v1
```

**XAMPP/Local Development:**
```bash
# Configure database in config/database.php
# Start PHP built-in server
php -S localhost:8300 -t public/
```

See [docs/SETUP.md](docs/SETUP.md) for details.

## 📡 API Examples

**Get Products**:
```bash
GET http://localhost:8300/api/v1/products?limit=20
```

**Get Product Detail**:
```bash
GET http://localhost:8300/api/v1/products/0902004000421
```

**Admin - List Orders**:
```bash
GET http://localhost:8300/api/v1/admin/orders
Authorization: Bearer {token}
```

**Admin - Get All Settings**:
```bash
GET http://localhost:8300/api/v1/admin/settings
```

**Admin - Update Branding**:
```bash
PUT http://localhost:8300/api/v1/admin/settings/branding
Content-Type: application/json
{
  "logo_alignment": "center",
  "nav_height": "70",
  "header_bg_color": "#8B4513",
  "header_text_color": "#FFFFFF",
  "theme_primary": "#8B4513"
}
```

**Response Format**:
```json
{
  "success": true,
  "data": { ... },
  "message": "Operation successful"
}
```

## 📚 Documentation

- [Setup Guide](docs/SETUP.md)
- [API Endpoints](docs/API_ENDPOINTS.md)
- [API Suppliers](docs/api-suppliers-and-examples.md)
- [Drop Shippers](docs/dropshippers-western-australian-survival-gear-with-apis.md)

## 🔐 Authentication

**Methods**:
- Session-based (frontend users)
- API keys (drop shippers)
- Tokens (admin users)

**Headers**:
```
Authorization: Bearer {token}
X-API-Key: {api_key}
```

## 🔒 Security

- SQL injection prevention (PDO)
- XSS protection
- CSRF validation
- Rate limiting
- API key validation
- Password hashing (bcrypt)

## 🔗 Related Projects

- **Storefront**: http://localhost:8300/ (Customer site)
- **Admin Panel**: http://localhost:8301/adminpanel (Admin dashboard)

---

**Part of the Pecos River Trading Post E-Commerce Platform**