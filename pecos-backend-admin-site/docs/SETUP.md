# Backend Admin Site - Setup Guide

Last Updated: November 25, 2025

# Admin Site Setup Guide

## Prerequisites

- PHP 8.2+
- Composer
- MySQL/MariaDB
- XAMPP (recommended)
- pecos-backendadmin-api running on port 8000

## Installation

1. Clone repository:
```bash
cd C:\xampp\htdocs
git clone [repository-url] pecos-backend-admin-site
```

2. Install dependencies:
```bash
cd pecos-backend-admin-site
composer install
```

3. Configure environment:
```bash
cp .env.example .env
php artisan key:generate
```

4. Update `.env`:
```env
DB_DATABASE=pecosriver
DB_USERNAME=root
DB_PASSWORD=
```

5. Start server:
```bash
php artisan serve --port=8001
```

## API Connection

The admin site connects to the API at `http://localhost:8300/api/v1`.

To change, update the `API_BASE` constant in blade templates.

## First-Time Setup

1. Ensure API is running: `http://localhost:8300`
2. Access admin: `http://localhost:8301/admin`
3. Default login: Check users table in database

## Troubleshooting

### API Connection Failed
- Verify API server is running on port 8000
- Check CORS configuration in API

### Stats Not Loading
- Check browser console for errors
- Verify API endpoints are accessible

### Feature Toggles Not Working
- Clear session: `php artisan session:clear`
- Features cached for 5 minutes

---
**Updated**: November 23, 2025