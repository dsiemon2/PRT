# Pecos River Traders - Docker Setup Guide

## Project Overview

This document outlines the dockerization setup for the Pecos River Traders Laravel applications.

### Projects Included

| Project | Purpose | Database | Docker URL |
|---------|---------|----------|------------|
| **PRT5** | Customer Storefront (full e-commerce site) | MySQL (pecosriver) | `localhost:8300/` |
| **pecos-backendadmin-api** | REST API backend (Sanctum auth, Swagger docs) | MySQL (pecosriver) | `localhost:8300/api/v1` |
| **pecos-backend-admin-site** | Admin dashboard (Blade/Bootstrap UI) | MySQL (pecosriver) | `localhost:8301/adminpanel` |

### Shared Resources
- All three apps share the same MySQL database: `pecosriver`
- All require **PHP 8.2+** and **Laravel 12.0**
- Docker MySQL runs on port **3307** (to avoid conflict with XAMPP's 3306)

---

## Architecture

```
        Port 8300                    Port 8301
           │                            │
           ▼                            ▼
  ┌────────────────┐           ┌────────────────┐
  │     Nginx      │           │  Nginx Admin   │
  │  (Storefront)  │           │    (Admin)     │
  └───────┬────────┘           └───────┬────────┘
          │                            │
    ┌─────┴─────┐                      │
    ▼           ▼                      ▼
   /         /api/*              /adminpanel/*
    │           │                      │
    ▼           ▼                      ▼
┌─────────┐ ┌─────────┐          ┌───────────┐
│  PRT5   │ │   API   │          │   Admin   │
│Storefront│ │ Backend │          │ Dashboard │
│(PHP-FPM)│ │(PHP-FPM)│          │ (PHP-FPM) │
└────┬────┘ └────┬────┘          └─────┬─────┘
     │           │                     │
     └───────────┼─────────────────────┘
                 ▼
          ┌────────────┐
          │   MySQL    │
          │ (Port 3307)│
          └────────────┘
                 │
          ┌────────────┐
          │ phpMyAdmin │
          │ (Port 8380)│
          └────────────┘
```

---

## Directory Structure

```
PRT/
├── docker/
│   ├── nginx/
│   │   └── default.conf          # Nginx routing configuration
│   ├── php/
│   │   └── Dockerfile            # PHP-FPM 8.2 image
│   └── mysql/
│       └── init.sql              # Database initialization
├── scripts/
│   ├── start-docker.bat          # Start all services
│   ├── stop-docker.bat           # Stop all services
│   └── migrate-from-xampp.bat    # Migrate XAMPP data to Docker
├── docker-compose.yml            # Main orchestration file
├── docs/
│   └── docker-setup-guide.md     # This file
├── PRT5/                         # Customer storefront
│   └── .env.docker               # Docker environment
├── pecos-backendadmin-api/       # REST API
│   └── .env.docker               # Docker environment
├── pecos-backend-admin-site/     # Admin dashboard
│   └── .env.docker               # Docker environment
└── PecosRiverTraders/            # Reserved (empty)
```

---

## Quick Start

### First Time Setup

1. **Start Docker Desktop** (make sure it's running)

2. **Run the startup script:**
   ```cmd
   cd C:\Users\dsiem\Downloads\PRT
   scripts\start-docker.bat
   ```

   This will:
   - Copy `.env.docker` files to `.env`
   - Build Docker images
   - Start all containers
   - Run Laravel migrations

3. **Migrate your XAMPP data (optional):**
   ```cmd
   scripts\migrate-from-xampp.bat
   ```

### Access Your Apps

| App | URL |
|-----|-----|
| **Storefront** | http://localhost:8300/ |
| **API** | http://localhost:8300/api/v1 |
| **Admin Panel** | http://localhost:8301/adminpanel |
| **phpMyAdmin** | http://localhost:8380/ |

---

## Docker Commands

```bash
# Start all services
docker-compose up -d

# Stop all services
docker-compose down

# View logs (all services)
docker-compose logs -f

# View logs (specific service)
docker-compose logs -f prt
docker-compose logs -f api
docker-compose logs -f admin

# Restart services
docker-compose restart

# Rebuild after code changes
docker-compose up -d --build

# Run Laravel commands
docker-compose exec prt php artisan migrate
docker-compose exec api php artisan migrate
docker-compose exec admin php artisan migrate

# Access container shell
docker-compose exec prt bash
docker-compose exec api bash
docker-compose exec admin bash

# Access MySQL
docker-compose exec mysql mysql -u root -psecret pecosriver
```

---

## Services

### Container Details

| Container | Image | Internal Port | External Port |
|-----------|-------|---------------|---------------|
| `prt_nginx` | nginx:alpine | 80 | **8300** |
| `prt_nginx_admin` | nginx:alpine | 80 | **8301** |
| `prt_storefront` | php:8.2-fpm | 9000 | - |
| `prt_api` | php:8.2-fpm | 9000 | - |
| `prt_admin` | php:8.2-fpm | 9000 | - |
| `prt_mysql` | mysql:8.0 | 3306 | **3307** |
| `prt_phpmyadmin` | phpmyadmin | 80 | **8380** |

### Network

All containers are connected via `prt_network` bridge network. Services can communicate using container names:
- `mysql` - MySQL database
- `prt` - Storefront PHP-FPM
- `api` - API PHP-FPM
- `admin` - Admin PHP-FPM

---

## Environment Configuration

### Database Connection (Docker)

```env
DB_CONNECTION=mysql
DB_HOST=mysql           # Docker service name
DB_PORT=3306
DB_DATABASE=pecosriver
DB_USERNAME=root
DB_PASSWORD=secret
```

### App URLs

| App | APP_URL |
|-----|---------|
| PRT5 (Storefront) | `http://localhost:8300` |
| API | `http://localhost:8300/api/v1` |
| Admin Panel | `http://localhost:8301/adminpanel` |

---

## Migrating from XAMPP

### Automatic Migration

Run the migration script (requires XAMPP MySQL running):

```cmd
scripts\migrate-from-xampp.bat
```

### Manual Migration

1. **Export from XAMPP:**
   ```cmd
   C:\xampp\mysql\bin\mysqldump -u root pecosriver > backup.sql
   ```

2. **Import to Docker:**
   ```cmd
   docker exec -i prt_mysql mysql -u root -psecret pecosriver < backup.sql
   ```

---

## Troubleshooting

### Port Conflicts

If port 8300 is in use:
1. Edit `docker-compose.yml`
2. Change `"8300:80"` to another port like `"8400:80"`

If port 3307 conflicts with something:
1. Edit `docker-compose.yml`
2. Change `"3307:3306"` to another port

### MySQL Won't Start

Check if XAMPP MySQL is running on port 3306. Docker MySQL uses 3307 by default to avoid conflict.

### Permission Issues

On Windows, ensure Docker Desktop has access to the drive where your project is located:
1. Open Docker Desktop
2. Settings → Resources → File Sharing
3. Add `C:\` if not present

### Container Won't Start

```bash
# Check logs
docker-compose logs

# Rebuild everything
docker-compose down
docker-compose up -d --build
```

---

## Running XAMPP and Docker Side-by-Side

You can run both XAMPP and Docker simultaneously:

| Service | XAMPP | Docker |
|---------|-------|--------|
| MySQL | Port 3306 | Port 3307 |
| PRT5 (Storefront) | localhost:8110 | localhost:8300 |
| API | localhost:8000 | localhost:8300/api/v1 |
| Admin Panel | localhost:8001 | localhost:8301/adminpanel |
| phpMyAdmin | localhost/phpmyadmin | localhost:8380 |

To switch `.env` files:
- **For Docker:** Copy `.env.docker` to `.env`
- **For XAMPP:** Keep original `.env` (or restore from backup)

---

## Technical Requirements

- **Docker Desktop** 4.x+ (Windows/Mac)
- **Docker Compose** v2+ (included with Docker Desktop)
- **Disk Space:** ~2GB for images
- **RAM:** ~1GB for running containers

---

## Files Created

| File | Purpose |
|------|---------|
| `docker-compose.yml` | Main Docker orchestration |
| `docker/php/Dockerfile` | PHP 8.2 FPM image with extensions |
| `docker/nginx/default.conf` | URL routing configuration |
| `docker/mysql/init.sql` | Database initialization |
| `scripts/start-docker.bat` | Start all services |
| `scripts/stop-docker.bat` | Stop all services |
| `scripts/migrate-from-xampp.bat` | Data migration script |
| `PRT5/.env.docker` | Docker environment for storefront |
| `pecos-backendadmin-api/.env.docker` | Docker environment for API |
| `pecos-backend-admin-site/.env.docker` | Docker environment for admin |

---

---

## New Features (January 2026)

The following features were added to the platform:

1. **Returns/RMA System** - Full return merchandise authorization workflow
2. **Multi-Currency Support** - International currency handling
3. **Multi-Language Support** - Full i18n/l10n support
4. **Email Marketing Campaigns** - Email lists, campaigns, and automation
5. **SMS/Push Notifications** - Multi-channel notification system
6. **Advanced Search with Facets** - Enterprise search capabilities
7. **Product Variants/SKU Management** - Full variant system
8. **Live Chat Support** - Real-time customer support

See `docs/FEATURES.md` for detailed documentation on all features.

### Running New Migrations

After starting Docker, run the migrations to create the new feature tables:

```bash
docker-compose exec api php artisan migrate
docker-compose exec api php artisan db:seed --class=CurrencySeeder
docker-compose exec api php artisan db:seed --class=LanguageSeeder
docker-compose exec api php artisan db:seed --class=VariantsSeeder
docker-compose exec api php artisan db:seed --class=ChatSeeder
```

---

*Document updated: January 15, 2026*
