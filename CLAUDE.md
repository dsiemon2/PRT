# PRT (Pecos River Traders) - Project Documentation

## Project Overview

PRT is a multi-service e-commerce platform for western/outdoor supplies consisting of three Laravel applications communicating via APIs, all containerized with Docker.

## Architecture

```
┌─────────────────────────────────────────────────────────────────────┐
│                         Docker Network                               │
├─────────────────────────────────────────────────────────────────────┤
│  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐     │
│  │   Storefront    │  │   Admin Site    │  │   Backend API   │     │
│  │   (Laravel)     │  │   (Laravel)     │  │   (Laravel)     │     │
│  │   Port: 8450    │  │   Port: 8451    │  │  8450/api/v1    │     │
│  │   PHP-FPM 8.2   │  │   PHP-FPM 8.2   │  │   PHP-FPM 8.2   │     │
│  └────────┬────────┘  └────────┬────────┘  └────────┬────────┘     │
│           │                    │                    │               │
│           └────────────────────┴────────────────────┘               │
│                                │                                     │
│                    ┌───────────┴───────────┐                        │
│                    │      MySQL 8.0        │                        │
│                    │      Port: 3350       │                        │
│                    └───────────────────────┘                        │
└─────────────────────────────────────────────────────────────────────┘
```

## Services & Ports

| Service | Port | URL | Description |
|---------|------|-----|-------------|
| Storefront | 8450 | http://localhost:8450 | Customer-facing store |
| Admin Panel | 8451 | http://localhost:8451/adminpanel | Admin dashboard |
| API | 8450 | http://localhost:8450/api/v1 | Backend REST API |
| MySQL | 3350 | localhost:3350 | Database server |

## Payment Gateways

All 5 payment gateways are fully integrated in the API service:

| Gateway | Status | Location |
|---------|--------|----------|
| **Stripe** | Full integration | `app/Services/Payments/StripeGateway.php` |
| **PayPal** | Full integration | `app/Services/Payments/PayPalGateway.php` |
| **Braintree** | Full integration | `app/Services/Payments/BraintreeGateway.php` |
| **Square** | Full integration | `app/Services/Payments/SquareGateway.php` |
| **Authorize.net** | Full integration | `app/Services/Payments/AuthorizeNetGateway.php` |

### Payment Services Location
```
pecos-backendadmin-api/app/Services/Payments/
├── StripeGateway.php       # Stripe payment processing
├── PayPalGateway.php       # PayPal order management
├── BraintreeGateway.php    # Braintree transactions
├── SquareGateway.php       # Square payment processing
├── AuthorizeNetGateway.php # Authorize.net processing
└── PaymentManager.php      # Unified payment orchestrator
```

## Directory Structure

```
PRT/
├── docker-compose.yml          # Docker orchestration
├── CLAUDE.md                   # This file
├── docker/
│   ├── nginx/
│   └── mysql/
├── PecosRiverTraders/          # Storefront Laravel app
│   ├── app/
│   │   └── Services/
│   └── resources/views/
├── pecos-backend-admin-site/   # Admin Laravel app
│   ├── app/Http/Controllers/
│   └── resources/views/admin/
└── pecos-backendadmin-api/     # API Laravel app
    ├── app/
    │   ├── Http/Controllers/Api/V1/
    │   └── Services/Payments/
    └── database/
```

## Test Credentials

### Admin Panel
- URL: http://localhost:8451/adminpanel
- Email: `admin@pecos.com`
- Password: `Test1234`

## Docker Commands

```bash
# Start all services
docker-compose up -d

# Stop all services
docker-compose down

# Rebuild and start
docker-compose up -d --build

# View logs
docker-compose logs -f storefront
docker-compose logs -f admin
docker-compose logs -f api
```

## Related Documentation

See `/docs/` folder for:
- `docker-setup-guide.md` - Docker setup instructions
- `FEATURES.md` - Feature documentation
