# Backend API Documentation

Last Updated: January 7, 2026

## 🆕 Recent Updates

### November 28, 2025
- **Settings Endpoint Enhancements**
  - Notification configuration settings (channels, categories)
  - Live Chat provider settings (Tawk.to, Tidio)
  - Generic key-value storage for feature toggles

## 📖 Documentation Index

### Getting Started
- [Setup Guide](SETUP.md) - Installation and configuration
- [API Endpoints](API_ENDPOINTS.md) - Complete endpoint reference

### Integration
- [API Reference](API_REFERENCE.md) - Quick reference
- [API Suppliers](api-suppliers-and-examples.md) - Third-party integrations
- [Fulfillment Alternatives](fulfillment-alternatives.md) - Drop shipping options

### Drop Shipping
- [Western Australian Survival Gear](dropshippers-western-australian-survival-gear-with-apis.md) - Drop shipper setup
- [Drop Shipper APIs](dropshippers-western-australian-survival-gear-with-apis (1).md) - API details
- [Drop Shipper Examples](dropshippers-western-australian-survival-gear-with-apis (2).md) - Implementation examples

## 🚀 Quick Start

```bash
# Test API
curl http://localhost:8300/api/v1/health

# Get products
curl http://localhost:8300/api/v1/products

# Admin request (with auth)
curl -H "Authorization: Bearer {token}" \
  http://localhost:8300/api/v1/admin/products
```

## 📡 Endpoint Categories

### Public Endpoints
- `GET /api/v1/products` - List products
- `GET /api/v1/products/{id}` - Get product
- `GET /api/v1/categories` - List categories
- `POST /api/v1/cart` - Cart operations
- `POST /api/v1/orders` - Create order

### Admin Endpoints
- `/api/v1/admin/products` - Product management
- `/api/v1/admin/orders` - Order management
- `/api/v1/admin/customers` - Customer management
- `/api/v1/admin/inventory` - Inventory operations
- `/api/v1/admin/reports` - Analytics
- `/api/v1/admin/settings/features` - Feature configuration (notifications, live chat)

### Drop Shipper Endpoints
- `/api/v1/dropship/orders` - Orders
- `/api/v1/dropship/products` - Products
- `/api/v1/dropship/tracking` - Tracking

## 🔐 Authentication

**Headers**:
```
Authorization: Bearer {admin_token}
X-API-Key: {dropshipper_key}
```

## 📝 Response Format

**Success**:
```json
{
  "success": true,
  "data": { ... }
}
```

**Error**:
```json
{
  "success": false,
  "error": "Error message",
  "code": 400
}
```

## 🔗 CORS Configuration

Allowed origins:
- http://localhost:8300 (Storefront)
- http://localhost:8301 (Admin Panel)

## 📋 Status Codes

- `200` - Success
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized
- `404` - Not Found
- `500` - Server Error

---

For detailed endpoint documentation, see [API_ENDPOINTS.md](API_ENDPOINTS.md).