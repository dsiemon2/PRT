# Pecos River Traders

E-commerce platform for western/outdoor supplies.

**Production Domain:** www.pecosrivertraders.com

## Tech Stack

### Backend
- **Runtime:** PHP 8.2
- **Framework:** Laravel 12
- **Database:** MySQL 8.0
- **ORM:** Eloquent
- **API Docs:** L5-Swagger

### Frontend
- **Templating:** Blade
- **CSS Framework:** Bootstrap 5

### Payment Gateways
Stripe, PayPal, Braintree, Square, Authorize.net

## Ports

| Service | Port | Description |
|---------|------|-------------|
| Storefront | 8300 | Customer-facing store |
| Admin Panel | 8301 | Admin dashboard |
| API | 8300/api | REST Backend |
| MySQL | 3307 | Database server |
| phpMyAdmin | 8380 | Database management UI |

## Local Development URLs

- **Storefront:** http://localhost:8300/
- **Admin Panel:** http://localhost:8301/adminpanel

## Docker Setup

```bash
# Start all services
docker compose up -d

# Rebuild and start
docker compose up -d --build

# View logs
docker compose logs -f

# Stop all services
docker compose down
```

## Author

Daniel Siemon
