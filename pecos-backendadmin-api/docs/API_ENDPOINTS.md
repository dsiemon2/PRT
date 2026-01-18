# Backend API - Endpoints Reference

Last Updated: November 28, 2025

## Base URL
```
http://localhost:8300/api/v1
```

## 🔓 Public Endpoints

### Products

#### List Products
```http
GET /products
```

**Parameters**:
- `page` (int) - Page number (default: 1)
- `limit` (int) - Items per page (default: 20, max: 100)
- `category` (string) - Filter by category code
- `search` (string) - Search query
- `sort` (string) - Sort field (price, name, newest)
- `order` (string) - Sort order (asc, desc)

**Response**:
```json
{
  "success": true,
  "data": [
    {
      "ItemNumber": "0902004000421",
      "ShortDescription": "Men's Boot",
      "SalePrice": 129.99,
      "CategoryCode": 61,
      "Category": "Men's Footwear",
      "Image": "images/Mens/boots/0902004000421.jpg",
      "available": 15
    }
  ],
  "pagination": {
    "current_page": 1,
    "total_pages": 10,
    "total_items": 200
  }
}
```

#### Get Product Detail
```http
GET /products/{itemNumber}
```

**Response**:
```json
{
  "success": true,
  "data": {
    "ItemNumber": "0902004000421",
    "ShortDescription": "Men's Boot",
    "LongDescription": "Premium leather boot...",
    "SalePrice": 129.99,
    "RetailPrice": 159.99,
    "CategoryCode": 61,
    "Image": "images/Mens/boots/0902004000421.jpg",
    "available": 15,
    "sizes": ["8", "9", "10", "11", "12"],
    "colors": ["Brown", "Black"],
    "reviews": [...]
  }
}
```

### Categories

#### List Categories
```http
GET /categories
```

**Parameters**:
- `level` (int) - Filter by level (0=top, 1=sub)
- `parent` (string) - Filter by parent code

**Response**:
```json
{
  "success": true,
  "data": [
    {
      "CategoryCode": 60,
      "Category": "Men's Footwear",
      "ParentCode": null,
      "Level": 0,
      "image": "images/category/mens-footwear.jpg",
      "products_count": 45
    }
  ]
}
```

### Cart

#### Add to Cart
```http
POST /cart/add
```

**Body**:
```json
{
  "item_number": "0902004000421",
  "quantity": 1,
  "size": "10",
  "color": "Brown"
}
```

#### Get Cart
```http
GET /cart
```

### Orders

#### Create Order
```http
POST /orders
```

**Body**:
```json
{
  "customer_id": 123,
  "shipping_address": {
    "name": "John Doe",
    "address_line1": "123 Main St",
    "city": "Dallas",
    "state": "TX",
    "postal_code": "75201"
  },
  "items": [
    {
      "item_number": "0902004000421",
      "quantity": 1,
      "size": "10",
      "price": 129.99
    }
  ],
  "payment_method": "paypal",
  "payment_id": "PAYPAL-12345"
}
```

## 🔐 Admin Endpoints

All require authentication:
```
Authorization: Bearer {token}
```

### Products

```http
GET    /admin/products              # List all
POST   /admin/products              # Create
GET    /admin/products/{id}         # Get one
PUT    /admin/products/{id}         # Update
DELETE /admin/products/{id}         # Delete
```

### Orders

```http
GET    /admin/orders                # List all
GET    /admin/orders/{id}           # Get one
PUT    /admin/orders/{id}/status    # Update status
```

### Customers

```http
GET    /admin/customers             # List all
GET    /admin/customers/{id}        # Get one
```

### Inventory

```http
GET    /admin/inventory/stats           # Get inventory statistics
GET    /admin/inventory/products        # List products with inventory
GET    /admin/inventory/stock-alerts    # Get stock alerts (low/out of stock)
POST   /admin/inventory/adjust-stock    # Adjust product stock
GET    /admin/inventory/product/{id}    # Get single product inventory
```

#### Adjust Stock
```http
POST /admin/inventory/adjust-stock
```

**Body**:
```json
{
  "product_id": 123,
  "adjustment": 10,
  "notes": "Received from supplier"
}
```

- `product_id` (int, required) - Database ID of the product
- `adjustment` (int, required) - Positive to add stock, negative to remove
- `notes` (string, optional) - Reason for adjustment

**Response**:
```json
{
  "success": true,
  "message": "Successfully added 10 units to inventory",
  "data": {
    "quantity_before": 5,
    "quantity_after": 15
  }
}
```

### Reports

```http
GET    /admin/reports/sales         # Sales report
GET    /admin/reports/inventory     # Inventory report
GET    /admin/reports/customers     # Customer analytics
```

### Settings

#### Get Feature Settings
```http
GET /admin/settings/features
```

**Response**:
```json
{
  "success": true,
  "data": {
    "faq_enabled": true,
    "loyalty_enabled": true,
    "blog_enabled": true,
    "notifications_enabled": true,
    "notif_email_enabled": true,
    "notif_sms_enabled": true,
    "notif_push_enabled": true,
    "notif_delivery_enabled": true,
    "notif_promo_enabled": true,
    "notif_payment_enabled": true,
    "notif_security_enabled": true,
    "live_chat_enabled": false,
    "tawkto_enabled": false,
    "tawkto_property_id": "",
    "tawkto_widget_id": "",
    "tidio_enabled": false,
    "tidio_public_key": ""
  }
}
```

#### Save Feature Settings
```http
POST /admin/settings/features
```

**Body**:
```json
{
  "notifications_enabled": true,
  "notif_email_enabled": true,
  "notif_sms_enabled": false,
  "live_chat_enabled": true,
  "tawkto_enabled": true,
  "tawkto_property_id": "abc123",
  "tawkto_widget_id": "def456"
}
```

**Response**:
```json
{
  "success": true,
  "message": "Settings saved successfully"
}
```

## 🚚 Drop Shipper Endpoints

Require API key:
```
X-API-Key: {api_key}
```

### Products

```http
GET /dropship/products              # Available products
```

### Orders

```http
GET  /dropship/orders               # List orders
POST /dropship/orders               # Create order
PUT  /dropship/orders/{id}/status   # Update status
```

## ❌ Error Responses

**Format**:
```json
{
  "success": false,
  "error": "Error message",
  "code": 400
}
```

**Status Codes**:
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `422` - Validation Failed
- `429` - Rate Limit Exceeded
- `500` - Server Error

## 🔄 Rate Limiting

- Public: 100 requests/minute per IP
- Admin: 200 requests/minute
- Drop Shipper: 100 requests/minute per key

**Headers**:
```
X-RateLimit-Limit: 100
X-RateLimit-Remaining: 95
X-RateLimit-Reset: 1640995200
```

## 📄 Pagination

**Request**:
```
GET /api/v1/products?page=2&limit=20
```

**Response**:
```json
{
  "pagination": {
    "current_page": 2,
    "per_page": 20,
    "total_pages": 10,
    "total_items": 200,
    "has_next": true,
    "has_prev": true
  }
}
```

## 🔍 Filtering & Sorting

**Filter by category**:
```
GET /products?category=boots
```

**Price range**:
```
GET /products?price_min=50&price_max=200
```

**Sort**:
```
GET /products?sort=price&order=asc
```

## 🧪 Testing with cURL

```bash
# Get products
curl http://localhost:8300/api/v1/products

# Create order
curl -X POST http://localhost:8300/api/v1/orders \
  -H "Content-Type: application/json" \
  -d '{"customer_id":123,"items":[...]}'

# Admin endpoint
curl http://localhost:8300/api/v1/admin/products \
  -H "Authorization: Bearer {token}"

# Drop shipper endpoint
curl http://localhost:8300/api/v1/dropship/orders \
  -H "X-API-Key: {api_key}"
```

---

For setup instructions, see [SETUP.md](SETUP.md).