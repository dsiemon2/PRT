# Eloquent Models Documentation

Last Updated: December 22, 2025

## Overview

PRT5 uses Eloquent ORM for database access. Models are located in `app/Models/` and correspond to tables in the `pecosriver` database.

## Core Models

### Product

**File:** `app/Models/Product.php`
**Table:** `products3`

Main product catalog model.

#### Columns
| Column | Type | Description |
|--------|------|-------------|
| id | int | Primary key |
| UPC | string | Universal Product Code |
| ItemNumber | string | Internal item number |
| ShortDescription | string | Product name |
| LongDescription | text | Full description |
| UnitPrice | decimal | Price |
| Cost | decimal | Cost price |
| CategoryCode | string | Foreign key to categories |
| Image | string | Product image path |
| stock_quantity | int | Available stock |
| reserved_quantity | int | Reserved for orders |
| low_stock_threshold | int | Low stock alert level |
| track_inventory | bool | Track stock levels |
| allow_backorders | bool | Allow ordering when out of stock |
| Active | bool | Product is active |

#### Relationships
```php
// Category
$product->category;

// Product images
$product->images;

// Reviews
$product->reviews;

// Stock alerts
$product->stockAlerts;

// Inventory transactions
$product->inventoryTransactions;
```

#### Scopes
```php
// In stock products
Product::inStock()->get();

// Out of stock
Product::outOfStock()->get();

// Low stock
Product::lowStock()->get();

// Tracked inventory
Product::tracked()->get();

// Search by term
Product::search('boots')->get();
```

#### Accessors
```php
// Available quantity (stock - reserved)
$product->available_quantity;

// Stock status: 'in_stock', 'low_stock', 'out_of_stock'
$product->stock_status;

// Formatted price: '$29.99'
$product->formatted_price;

// Primary image URL with fallback
$product->primary_image;
```

#### Usage Example
```php
use App\Models\Product;

// Get active products with images
$products = Product::where('Active', 1)
    ->with('images', 'category')
    ->paginate(20);

// Get featured products
$featured = Product::where('is_featured', true)
    ->inStock()
    ->limit(8)
    ->get();

// Search products
$results = Product::search('western')
    ->where('Active', 1)
    ->get();
```

---

### Category

**File:** `app/Models/Category.php`
**Table:** `categories`

Product categories for navigation and filtering.

#### Columns
| Column | Type | Description |
|--------|------|-------------|
| CategoryCode | string | Primary key |
| Category | string | Category name |
| Description | text | Category description |
| image | string | Category image path |
| is_featured | bool | Show on homepage |
| display_order | int | Sort order |

#### Relationships
```php
// Products in category
$category->products;
```

#### Scopes
```php
// Featured categories
Category::featured()->get();

// Ordered by display_order
Category::ordered()->get();
```

#### Accessors
```php
// Product count
$category->product_count;

// Full image URL
$category->image_url;
```

#### Usage Example
```php
use App\Models\Category;

// Get featured categories for homepage
$featuredCategories = Category::featured()
    ->ordered()
    ->withCount('products')
    ->get();

// Get all categories with product counts
$categories = Category::ordered()
    ->withCount('products')
    ->get();
```

---

### User

**File:** `app/Models/User.php`
**Table:** `users`

User accounts with authentication.

#### Columns
| Column | Type | Description |
|--------|------|-------------|
| id | int | Primary key |
| first_name | string | First name |
| last_name | string | Last name |
| email | string | Email (unique) |
| password | string | Hashed password |
| phone | string | Phone number |
| role | string | customer, manager, admin |
| email_verified_at | datetime | Verification timestamp |

#### Relationships
```php
// User's orders
$user->orders;

// Product reviews
$user->reviews;

// Saved addresses
$user->addresses;

// Wishlist items
$user->wishlistItems;

// Loyalty account
$user->loyaltyAccount;
```

#### Role Methods
```php
// Check admin
$user->isAdmin();

// Check manager or admin
$user->isManager();

// Check specific role
$user->hasRole('admin');

// Check minimum role level
$user->hasMinRole('manager');
```

#### Accessors
```php
// Full name
$user->name;
```

#### Usage Example
```php
use App\Models\User;

// Get current user's orders
$orders = auth()->user()->orders()
    ->orderBy('created_at', 'desc')
    ->paginate(10);

// Check permissions in blade
@if(auth()->user()->isManager())
    <a href="{{ route('admin.dashboard') }}">Admin</a>
@endif
```

---

### Order

**File:** `app/Models/Order.php`
**Table:** `orders`

Customer orders.

#### Columns
| Column | Type | Description |
|--------|------|-------------|
| id | int | Primary key |
| user_id | int | Foreign key to users |
| order_number | string | Public order number |
| status | string | pending, processing, shipped, delivered, cancelled |
| subtotal | decimal | Order subtotal |
| tax | decimal | Tax amount |
| shipping | decimal | Shipping cost |
| discount | decimal | Discount amount |
| total | decimal | Order total |
| shipping_* | string | Shipping address fields |
| billing_* | string | Billing address fields |
| payment_method | string | Payment type |
| payment_status | string | paid, pending, failed |
| coupon_code | string | Applied coupon |

#### Relationships
```php
// Order owner
$order->user;

// Order items
$order->items;
```

#### Scopes
```php
Order::pending()->get();
Order::processing()->get();
Order::shipped()->get();
Order::delivered()->get();
Order::cancelled()->get();
```

#### Accessors
```php
// Formatted total
$order->formatted_total;

// Total item count
$order->item_count;
```

---

### OrderItem

**File:** `app/Models/OrderItem.php`
**Table:** `order_items`

Individual items within an order.

#### Relationships
```php
$orderItem->order;
$orderItem->product;
```

---

## Supporting Models

### ProductImage

**File:** `app/Models/ProductImage.php`
**Table:** `product_images`

Additional product images.

```php
// Get primary image
$primary = $product->images()->where('is_primary', true)->first();

// Get all images ordered
$images = $product->images()->orderBy('display_order')->get();
```

### ProductReview

**File:** `app/Models/ProductReview.php`
**Table:** `product_reviews`

Customer product reviews.

```php
// Get approved reviews
$reviews = $product->reviews()
    ->where('status', 'approved')
    ->orderBy('created_at', 'desc')
    ->get();
```

### UserAddress

**File:** `app/Models/UserAddress.php`
**Table:** `user_addresses`

Saved customer addresses.

```php
// Get user's default address
$default = $user->addresses()
    ->where('is_default', true)
    ->first();
```

### WishlistItem

**File:** `app/Models/WishlistItem.php`
**Table:** `wishlist_items`

User wishlist items.

```php
// Check if product in wishlist
$inWishlist = $user->wishlistItems()
    ->where('product_id', $productId)
    ->exists();
```

### ContactMessage

**File:** `app/Models/ContactMessage.php`
**Table:** `contact_messages`

Contact form submissions.

```php
// Get unread messages
$unread = ContactMessage::where('status', 'unread')
    ->orderBy('created_at', 'desc')
    ->get();
```

### StockAlert

**File:** `app/Models/StockAlert.php`
**Table:** `stock_alerts`

Low stock notifications.

### BlogPost

**File:** `app/Models/BlogPost.php`
**Table:** `blog_posts`

Blog content.

### Event

**File:** `app/Models/Event.php`
**Table:** `events`

Store events calendar.

### Faq / FaqCategory

**Files:** `app/Models/Faq.php`, `app/Models/FaqCategory.php`
**Tables:** `faqs`, `faq_categories`

FAQ content organized by category.

### Coupon

**File:** `app/Models/Coupon.php`
**Table:** `coupons`

Discount coupons.

---

## Database Schema Notes

### Legacy Table Names

The database uses legacy naming conventions from prt4:

- Products table is `products3` (not `products`)
- Primary key for Category is `CategoryCode` (not `id`)
- Product foreign key is `CategoryCode` (string, not int)

### No Timestamps

Many legacy tables don't have `created_at`/`updated_at` columns:

```php
class Product extends Model
{
    public $timestamps = false;
}
```

### Mixed Case Columns

Legacy tables use PascalCase column names:

- `ShortDescription` instead of `short_description`
- `UnitPrice` instead of `unit_price`
- `CategoryCode` instead of `category_code`

---

## Creating New Models

```bash
php artisan make:model ModelName
```

For models matching legacy tables:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegacyModel extends Model
{
    protected $table = 'legacy_table_name';
    protected $primaryKey = 'custom_id';
    public $timestamps = false;

    protected $fillable = [
        'Column1',
        'Column2',
    ];
}
```
