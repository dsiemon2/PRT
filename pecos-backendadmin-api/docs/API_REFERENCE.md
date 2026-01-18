# API Reference

Complete API endpoint documentation for pecos-backendadmin-api.

## Base URL
```
http://localhost:8300/api/v1
```

## Authentication
Currently using session-based authentication. API tokens planned for future.

## Response Format
All responses return JSON:
```json
{
    "success": true,
    "data": [...],
    "meta": {
        "current_page": 1,
        "per_page": 10,
        "total": 100
    }
}
```

## Endpoints

### Products

#### List Products
```
GET /admin/products
```
Query params: `page`, `per_page`, `search`, `category_id`

#### Get Product
```
GET /admin/products/{id}
```

#### Create Product
```
POST /admin/products
```

#### Update Product
```
PUT /admin/products/{id}
```

### Categories

#### List Categories
```
GET /admin/categories
```
Returns categories with `products_count` (aggregated for parent categories)

### Orders

#### List Orders
```
GET /admin/orders
```
Query params: `page`, `per_page`, `status`, `date_from`, `date_to`

Returns orders with `item_count`.

#### Get Order Details
```
GET /admin/orders/{id}
```

### Inventory

#### Stock Levels
```
GET /admin/inventory
```

#### Movement Report
```
GET /admin/inventory/reports?report=movement
```
Returns 30-day inventory movements: `total_added`, `total_removed`, `net_change`, `current_stock`.

### Dropshippers

#### List Dropshippers
```
GET /admin/dropshippers
```
Returns with stats: `total`, `active`, `total_orders`, `total_revenue`.

### API Logs

#### List Logs
```
GET /admin/api-logs
```
Query params: `page`, `per_page`, `dropshipper_id`, `endpoint`, `status_code`, `date`

Includes `country` field (ISO code from IP geolocation).

#### Get Stats
```
GET /admin/api-logs/stats
```
Returns: `total_requests`, `success_rate`, `avg_response_time`, `errors`

### Settings

#### Get Features
```
GET /admin/settings/features
```

#### Update Features
```
PUT /admin/settings/features
```
Body: `{ "faq_enabled": true, "loyalty_enabled": false, ... }`

---
**Updated**: November 26, 2025