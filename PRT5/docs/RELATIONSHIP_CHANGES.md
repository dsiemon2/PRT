# Database Relationship Changes Summary

**Date:** 2025-11-25
**Purpose:** Fix and establish proper relationships between all database entities

## Overview

This document summarizes all changes made to establish correct relationships between products, inventory, suppliers, shipping, tax, reports, and related entities across:
- **Frontend (PRT2)**: C:\xampp\htdocs\PRT2
- **Backend (pecos-backend-admin-site)**: C:\xampp\htdocs\pecos-backend-admin-site
- **API (pecos-backendadmin-api)**: C:\xampp\htdocs\pecos-backendadmin-api

---

## 1. Models Created (API)

### Supplier System
- **Supplier.php** - Manages supplier companies
  - `hasMany: purchaseOrders`
  - `hasMany: preferredProducts` (products preferring this supplier)
  - `hasMany: lastOrderedProducts`

- **PurchaseOrder.php** - Purchase orders from suppliers
  - `belongsTo: supplier`
  - `belongsTo: dropshipper`
  - `hasMany: items` (PurchaseOrderItem)
  - `hasMany: receivings`
  - `belongsTo: createdBy` (User)
  - `belongsTo: updatedBy` (User)

- **PurchaseOrderItem.php** - Line items on purchase orders
  - `belongsTo: purchaseOrder`
  - `belongsTo: product` (products3.ID)
  - `hasMany: receivings`

- **PurchaseOrderReceiving.php** - Receiving records
  - `belongsTo: purchaseOrder`
  - `belongsTo: purchaseOrderItem`
  - `belongsTo: product`
  - `belongsTo: receivedBy` (User)

### Shipping System
- **ShippingZone.php** - Shipping regions
  - `hasMany: methods` (ShippingMethod)

- **ShippingMethod.php** - Shipping options per zone
  - `belongsTo: zone` (ShippingZone)

- **ShippingClass.php** - Product shipping classes

- **CarrierIntegration.php** - External carrier connections

### Tax System
- **TaxRate.php** - Tax rates by location
- **TaxClass.php** - Product tax classes
- **TaxExemption.php** - Customer tax exemptions
  - `belongsTo: user`

### Inventory System
- **InventoryTransaction.php** - Stock movement logs
  - `belongsTo: product` (products3.ID)
  - `belongsTo: user`

- **StockAlert.php** - Low stock notifications
  - `belongsTo: product` (products3.ID)

### Gift Cards
- **GiftCard.php** - Gift card records
  - `hasMany: transactions`

- **GiftCardTransaction.php** - Gift card usage
  - `belongsTo: giftCard`
  - `belongsTo: order`

### Dropshippers
- **Dropshipper.php** - Dropshipper accounts
  - `hasMany: orders` (DropshipOrder)
  - `hasMany: purchaseOrders`
  - `hasMany: apiLogs`
  - `hasMany: permissions`
  - `hasMany: webhooks`

- **DropshipOrder.php** - Dropship orders
  - `belongsTo: dropshipper`
  - `hasMany: items`

- **DropshipOrderItem.php** - Dropship line items
  - `belongsTo: order`
  - `belongsTo: product` (products3.UPC)

- **DropshipperPermission.php** - Dropshipper access
- **DropshipWebhook.php** - Webhook configurations

### Loyalty System
- **LoyaltyMember.php** - Member enrollment
  - `belongsTo: user`
  - `belongsTo: tier`
  - `hasMany: transactions` (via user_id)

- **LoyaltyTier.php** - Membership tiers
  - `hasMany: members`
  - `hasMany: rewards`

- **LoyaltyReward.php** - Available rewards
  - `belongsTo: tier`

### User System
- **UserAddress.php** - Customer addresses
  - `belongsTo: user`

- **UserPaymentMethod.php** - Saved payment methods
  - `belongsTo: user`
  - `belongsTo: billingAddress`

- **UserGiftCard.php** - User's gift card wallet
  - `belongsTo: user`

- **UserDeliveryPreference.php** - Delivery preferences
  - `belongsTo: user`

- **UserNotificationPreference.php** - Notification settings
  - `belongsTo: user`

- **OrderStatusHistory.php** - Order status changes
  - `belongsTo: order`
  - `belongsTo: createdBy` (User)

- **ApiLog.php** - API request logs
  - `belongsTo: dropshipper`

---

## 2. Models Updated (API)

### Product.php
- **Changed primary key** from `UPC` to `ID`
  - Reason: `order_items.product_id` and `user_wishlists.product_id` reference integer IDs
- **Added relationships:**
  - `belongsTo: preferredSupplier`
  - `belongsTo: lastSupplier`
  - `hasMany: inventoryTransactions`
  - `hasMany: stockAlerts`
  - `hasMany: orderItems`
  - `hasMany: wishlists`
  - `hasMany: purchaseOrderItems`
- **Fixed images relationship:** Uses `ID` (was incorrectly using `UPC`)
- **Fixed reviews relationship:** Uses `UPC` (correct)
- **Fixed inStock scope:** Cast `QTY` column properly (varchar to integer)

### OrderItem.php
- **Fixed product relationship:** Changed from `id` to `ID`

### Wishlist.php
- **Fixed product relationship:** Comment updated to clarify ID reference

### LoyaltyTransaction.php
- **Fixed field name:** Changed `type` to `transaction_type` (matches database)
- **Fixed scopes:** Use correct field `transaction_type`

### User.php
- **Added relationships:**
  - `hasMany: wishlists`
  - `hasMany: cartItems`
  - `hasOne: loyaltyMember`
  - `hasMany: loyaltyTransactions`
  - `hasMany: giftCards`
  - `hasMany: taxExemptions`
  - `hasOne: deliveryPreferences`
  - `hasOne: notificationPreferences`
  - `hasMany: couponUsages`

### Order.php
- **Added relationships:**
  - `belongsTo: shippingAddress`
  - `belongsTo: billingAddress`
  - `belongsTo: paymentMethod`
  - `hasMany: statusHistory`
  - `hasMany: giftCardTransactions`
  - `hasMany: loyaltyTransaction`
  - `hasMany: couponUsage`

### ProductImage.php
- **Fixed product relationship:** Changed from `UPC` to `ID`

---

## 3. Controllers Updated (API)

### ProductController.php
- Changed `find($upc)` to `where('UPC', $upc)->first()` for all methods
  - Affected methods: `show`, `update`, `destroy`, `updateStock`

### SupplierController.php
- Converted from raw `DB::table()` queries to Eloquent model usage
- Now uses `Supplier::with(['purchaseOrders'])` for eager loading

---

## 4. Migration Created

**File:** `2025_11_25_000001_add_foreign_key_constraints.php`

Adds foreign key constraints for:
- `user_addresses.user_id` -> `users.id`
- `user_payment_methods.user_id` -> `users.id`
- `user_payment_methods.billing_address_id` -> `user_addresses.id`
- `user_delivery_preferences.user_id` -> `users.id`
- `user_notification_preferences.user_id` -> `users.id`
- `user_wishlists.user_id` -> `users.id`
- `user_gift_cards.user_id` -> `users.id`
- `orders.user_id` -> `users.id`
- `order_items.order_id` -> `orders.id`
- `order_status_history.order_id` -> `orders.id`
- `product_reviews.user_id` -> `users.id`
- `purchase_orders.supplier_id` -> `suppliers.id`
- `purchase_orders.dropshipper_id` -> `dropshippers.id`
- `purchase_order_items.purchase_order_id` -> `purchase_orders.id`
- `shipping_methods.zone_id` -> `shipping_zones.id`
- `tax_exemptions.user_id` -> `users.id`
- `gift_card_transactions.gift_card_id` -> `gift_cards.id`
- `loyalty_members.user_id` -> `users.id`
- `loyalty_members.tier_id` -> `loyalty_tiers.id`
- `loyalty_transactions.user_id` -> `users.id`
- `dropship_orders.dropshipper_id` -> `dropshippers.id`
- `dropship_order_items.order_id` -> `dropship_orders.id`
- `api_logs.dropshipper_id` -> `dropshippers.id`
- `coupon_usage.coupon_id` -> `coupons.id`
- `coupon_usage.user_id` -> `users.id`
- `blog_posts.category_id` -> `blog_categories.id`
- `blog_post_tags.post_id` -> `blog_posts.id`
- `blog_post_tags.tag_id` -> `blog_tags.id`
- `faqs.category_id` -> `faq_categories.id`
- `review_votes.review_id` -> `product_reviews.id`

---

## 5. Key Database Relationships

### Product-Related Tables
```
products3 (ID, UPC)
├── product_images.product_id -> products3.ID
├── product_reviews.product_id -> products3.UPC
├── order_items.product_id -> products3.ID
├── user_wishlists.product_id -> products3.ID
├── inventory_transactions.product_id -> products3.ID
├── stock_alerts.product_id -> products3.ID
├── purchase_order_items.product_id -> products3.ID
└── categories.CategoryCode <- products3.CategoryCode
```

### User-Related Tables
```
users
├── user_addresses.user_id
├── user_payment_methods.user_id
├── user_delivery_preferences.user_id
├── user_notification_preferences.user_id
├── user_wishlists.user_id
├── user_gift_cards.user_id
├── orders.user_id
├── product_reviews.user_id
├── loyalty_members.user_id
├── loyalty_transactions.user_id
├── tax_exemptions.user_id
└── coupon_usage.user_id
```

### Supplier System
```
suppliers
├── purchase_orders.supplier_id
└── products3.preferred_supplier_id

purchase_orders
├── purchase_order_items.purchase_order_id
└── purchase_order_receiving.purchase_order_id
```

---

## 6. Additional Fixes (2025-11-25)

### Frontend Functions Fixed

**reviews-functions.php**
- Fixed `getProductReviews()`: Now correctly looks up UPC from product ID before querying `product_reviews` (which uses UPC as product_id)
- Fixed `getProductRating()`: Same UPC lookup fix

### Important Relationship Notes

| Table | product_id Type | Links To |
|-------|----------------|----------|
| `product_reviews` | varchar(50) | `products3.UPC` |
| `product_images` | double | `products3.ID` |
| `order_items` | int(11) | `products3.ID` |
| `user_wishlists` | int(11) | `products3.ID` |
| `inventory_transactions` | int(11) | `products3.ID` |
| `dropship_order_items` | varchar(50) | `products3.UPC` |

---

## 7. Testing

### Basic Verification (10 tests)
```bash
php C:\xampp\htdocs\PRT2\maintenance\verify_relationships.php
```

### Comprehensive Verification (27 tests)
```bash
php C:\xampp\htdocs\PRT2\maintenance\verify_all_relationships.php
```

All relationship tests pass successfully.

---

## 8. Running the System

### Start API Server (Port 8000)
```bash
cd C:\xampp\htdocs\pecos-backendadmin-api
php artisan serve --host=localhost --port=8000
```

### Start Backend Admin Site (Port 8001)
```bash
cd C:\xampp\htdocs\pecos-backend-admin-site
php artisan serve --host=localhost --port=8001
```

### Frontend (PRT2)
Access via Apache: `http://localhost/PRT2/`

---

## 9. Notes

1. **Product Primary Key:** The `products3` table has both `ID` (double) and `UPC` (varchar). The ID is used as the primary key for relationships with `order_items`, `wishlists`, `inventory_transactions`, etc.

2. **Product Reviews:** The `product_reviews.product_id` is varchar(50) and stores the UPC, NOT the integer ID. Frontend functions have been updated to handle this.

3. **QTY Column:** The `QTY` column in `products3` is varchar, so the `scopeInStock` uses `CAST(QTY AS SIGNED)` for comparison.

4. **API Port:** The Laravel API runs on port 8000 (`php artisan serve`). Ensure it's running for frontend to fetch products.

5. **SQL Fallback:** The frontend `products.php` has a SQL fallback that works even when the API is unavailable.

6. **Migration Execution:** Run `php artisan migrate` in the API directory to apply foreign key constraints.
