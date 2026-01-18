# Database Documentation - Pecos River Traders

Database schema, tables, and management guide for the Pecos River Traders e-commerce platform.

## Database Information

- **Database Name**: `pecosriver`
- **Type**: MySQL/MariaDB
- **Charset**: `utf8mb4`
- **Collation**: `utf8mb4_general_ci`

## Database Configuration

Connection settings are in `config/database.php`:

```php
define('DB_TYPE', 'mysql');
define('DB_HOST', 'localhost');
define('DB_NAME', 'pecosriver');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');
```

## Main Tables

### User Account Tables

#### users
Primary user account information.

```sql
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    oauth_provider VARCHAR(50) NULL,
    oauth_uid VARCHAR(255) NULL,
    oauth_token TEXT NULL,
    profile_picture VARCHAR(255) NULL,
    is_active TINYINT(1) DEFAULT 1,
    role ENUM('customer', 'manager', 'admin') DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    INDEX idx_email (email),
    INDEX idx_oauth (oauth_provider, oauth_uid),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**Role Hierarchy:**
- `customer` - Regular users (default)
- `manager` - Can manage inventory, view reports
- `admin` - Full system access

#### user_addresses
Store multiple shipping/billing addresses per user.

```sql
CREATE TABLE user_addresses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    address_type ENUM('shipping', 'billing', 'both') DEFAULT 'shipping',
    is_default TINYINT(1) DEFAULT 0,
    full_name VARCHAR(200) NOT NULL,
    address_line1 VARCHAR(255) NOT NULL,
    address_line2 VARCHAR(255),
    city VARCHAR(100) NOT NULL,
    state VARCHAR(50) NOT NULL,
    postal_code VARCHAR(20) NOT NULL,
    country VARCHAR(50) DEFAULT 'USA',
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

#### user_payment_methods
Store saved payment methods (encrypted).

```sql
CREATE TABLE user_payment_methods (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    payment_type ENUM('credit_card', 'debit_card', 'paypal', 'other') DEFAULT 'credit_card',
    is_default TINYINT(1) DEFAULT 0,
    card_last_four VARCHAR(4),
    card_brand VARCHAR(50),
    cardholder_name VARCHAR(200),
    expiry_month INT,
    expiry_year INT,
    billing_address_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (billing_address_id) REFERENCES user_addresses(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

#### user_wishlists
Product wishlist/favorites.

```sql
CREATE TABLE user_wishlists (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_product (user_id, product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

#### orders
Customer orders.

```sql
CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    total_amount DECIMAL(10,2) NOT NULL,
    shipping_address_id INT,
    billing_address_id INT,
    payment_method_id INT,
    shipping_method VARCHAR(100),
    tracking_number VARCHAR(100),
    notes TEXT,
    ordered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    shipped_at TIMESTAMP NULL,
    delivered_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (shipping_address_id) REFERENCES user_addresses(id) ON DELETE SET NULL,
    FOREIGN KEY (billing_address_id) REFERENCES user_addresses(id) ON DELETE SET NULL,
    INDEX idx_user_orders (user_id),
    INDEX idx_order_number (order_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

#### order_items
Individual items within orders.

```sql
CREATE TABLE order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    product_size VARCHAR(50),
    quantity INT NOT NULL DEFAULT 1,
    unit_price DECIMAL(10,2) NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    INDEX idx_product (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### Product Tables

### Categories Table
Stores product categories and subcategories.

```sql
CREATE TABLE Categories (
    CategoryID INT PRIMARY KEY AUTO_INCREMENT,
    CategoryName VARCHAR(255) NOT NULL,
    ParentCategoryID INT DEFAULT NULL,
    CategoryImage VARCHAR(255),
    Description TEXT,
    DisplayOrder INT DEFAULT 0,
    Active TINYINT(1) DEFAULT 1,
    FOREIGN KEY (ParentCategoryID) REFERENCES Categories(CategoryID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**Key Fields:**
- `CategoryID` - Primary key
- `CategoryName` - Display name
- `ParentCategoryID` - For nested categories (NULL = top level)
- `CategoryImage` - Image filename
- `DisplayOrder` - Sort order

### Products Table
Main product catalog.

```sql
CREATE TABLE Products (
    ProductID INT PRIMARY KEY AUTO_INCREMENT,
    ProductCode VARCHAR(50) UNIQUE,
    ProductName VARCHAR(255) NOT NULL,
    Description TEXT,
    CategoryID INT NOT NULL,
    Price DECIMAL(10,2) NOT NULL,
    ImagePath VARCHAR(255),
    ImagePath2 VARCHAR(255),
    ImagePath3 VARCHAR(255),
    StockQuantity INT DEFAULT 0,
    reserved_quantity INT DEFAULT 0,
    reorder_point INT DEFAULT 10,
    reorder_quantity INT DEFAULT 50,
    cost_price DECIMAL(10,2),
    last_restock_date DATETIME,
    track_inventory TINYINT(1) DEFAULT 1,
    allow_backorder TINYINT(1) DEFAULT 0,
    low_stock_threshold INT DEFAULT 5,
    Featured TINYINT(1) DEFAULT 0,
    Active TINYINT(1) DEFAULT 1,
    DateAdded DATETIME DEFAULT CURRENT_TIMESTAMP,
    LastModified DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (CategoryID) REFERENCES Categories(CategoryID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**Key Fields:**
- `ProductID` - Primary key
- `ProductCode` - Unique SKU/product code
- `ProductName` - Display name
- `CategoryID` - Link to Categories table
- `Price` - Product price
- `ImagePath`, `ImagePath2`, `ImagePath3` - Product images
- `Featured` - Show on homepage

**Inventory Fields:**
- `StockQuantity` - Current stock level
- `reserved_quantity` - Reserved for pending orders
- `reorder_point` - Quantity trigger for reordering
- `reorder_quantity` - Amount to reorder
- `cost_price` - Product cost
- `last_restock_date` - Last inventory addition
- `track_inventory` - Enable/disable tracking
- `allow_backorder` - Allow selling when out of stock
- `low_stock_threshold` - Low stock warning level

### Events Table
For event management system.

```sql
CREATE TABLE Events (
    EventID INT PRIMARY KEY AUTO_INCREMENT,
    EventName VARCHAR(255) NOT NULL,
    EventDate DATE NOT NULL,
    Location VARCHAR(255),
    Description TEXT,
    ImagePath VARCHAR(255),
    Active TINYINT(1) DEFAULT 1,
    DateCreated DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### ShoppingCart Table (Session-based)
Temporary cart storage.

```sql
CREATE TABLE ShoppingCart (
    CartID INT PRIMARY KEY AUTO_INCREMENT,
    SessionID VARCHAR(255) NOT NULL,
    ProductID INT NOT NULL,
    Quantity INT DEFAULT 1,
    DateAdded DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ProductID) REFERENCES Products(ProductID),
    INDEX idx_session (SessionID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### inventory_transactions Table
Audit trail for all inventory movements.

```sql
CREATE TABLE inventory_transactions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    transaction_type ENUM('purchase', 'sale', 'return', 'adjustment', 'damaged', 'transfer') NOT NULL,
    quantity_change INT NOT NULL COMMENT 'Positive = increase, Negative = decrease',
    quantity_before INT NOT NULL,
    quantity_after INT NOT NULL,
    reference_type VARCHAR(50) COMMENT 'order, purchase_order, manual, etc.',
    reference_id INT COMMENT 'ID of order/PO/etc.',
    notes TEXT,
    user_id INT COMMENT 'Who made the change',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_product (product_id),
    INDEX idx_date (created_at),
    INDEX idx_type (transaction_type),
    FOREIGN KEY (product_id) REFERENCES Products(ProductID) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**Purpose:** Complete audit trail of all inventory movements for compliance and reporting.

### stock_alerts Table
Stock level alerts and notifications.

```sql
CREATE TABLE stock_alerts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    alert_type ENUM('low_stock', 'out_of_stock', 'overstock') NOT NULL,
    current_quantity INT NOT NULL,
    threshold_quantity INT NOT NULL,
    is_resolved BOOLEAN DEFAULT 0,
    resolved_at DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_product (product_id),
    INDEX idx_resolved (is_resolved),
    FOREIGN KEY (product_id) REFERENCES Products(ProductID) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**Purpose:** Automatic alerts when products fall below thresholds or go out of stock.

## Database Relationships

```
Categories (1) ----< (M) Products
Products (1) ----< (M) ShoppingCart
Products (1) ----< (M) inventory_transactions
Products (1) ----< (M) stock_alerts
Categories (1) ----< (M) Categories (Self-referencing for parent/child)
```

## Common Queries

### Get All Products with Category Names
```sql
SELECT
    p.ProductID,
    p.ProductName,
    p.Price,
    p.ImagePath,
    c.CategoryName
FROM Products p
INNER JOIN Categories c ON p.CategoryID = c.CategoryID
WHERE p.Active = 1
ORDER BY p.ProductName;
```

### Get Products by Category
```sql
SELECT
    ProductID,
    ProductName,
    Price,
    ImagePath,
    Description
FROM Products
WHERE CategoryID = ?
AND Active = 1
ORDER BY ProductName;
```

### Get Category Hierarchy
```sql
SELECT
    c1.CategoryID,
    c1.CategoryName,
    c2.CategoryName AS ParentCategory
FROM Categories c1
LEFT JOIN Categories c2 ON c1.ParentCategoryID = c2.CategoryID
WHERE c1.Active = 1
ORDER BY c1.DisplayOrder;
```

### Count Products per Category
```sql
SELECT
    c.CategoryID,
    c.CategoryName,
    COUNT(p.ProductID) AS ProductCount
FROM Categories c
LEFT JOIN Products p ON c.CategoryID = p.CategoryID AND p.Active = 1
WHERE c.Active = 1
GROUP BY c.CategoryID, c.CategoryName
ORDER BY c.DisplayOrder;
```

## Database Maintenance

### Check for Orphaned Products
Products without valid category:

```sql
SELECT p.*
FROM Products p
LEFT JOIN Categories c ON p.CategoryID = c.CategoryID
WHERE c.CategoryID IS NULL;
```

Or use the provided utility:
```bash
php fix_orphaned_products.php
```

### Check for Missing Images
```sql
SELECT ProductID, ProductName, ImagePath
FROM Products
WHERE (ImagePath IS NULL OR ImagePath = '')
AND Active = 1;
```

### Update Product Prices
```sql
UPDATE Products
SET Price = Price * 1.10  -- 10% increase
WHERE CategoryID = 5;
```

### Backup Database
```bash
# Using mysqldump
mysqldump -u root -p pecosriver > backup_$(date +%Y%m%d).sql

# Via phpMyAdmin: Export tab -> Quick export -> Go
```

### Restore Database
```bash
mysql -u root -p pecosriver < backup_20251108.sql
```

## Index Optimization

Recommended indexes for performance:

```sql
-- Products table
CREATE INDEX idx_category ON Products(CategoryID);
CREATE INDEX idx_active ON Products(Active);
CREATE INDEX idx_featured ON Products(Featured);
CREATE INDEX idx_code ON Products(ProductCode);

-- Categories table
CREATE INDEX idx_parent ON Categories(ParentCategoryID);
CREATE INDEX idx_active_cat ON Categories(Active);
CREATE INDEX idx_order ON Categories(DisplayOrder);

-- Shopping cart
CREATE INDEX idx_session ON ShoppingCart(SessionID);
CREATE INDEX idx_product ON ShoppingCart(ProductID);
```

## Data Validation Scripts

The project includes several validation utilities:

- `check_products_data.php` - Validates product data integrity
- `check_categories.php` - Checks category structure
- `check_product_images.php` - Verifies image files exist
- `verify_fixes.php` - Comprehensive data validation

## Migration Notes

### From Old Access Database

Original connection string:
```
Provider=SQLOLEDB; Data Source=srv04; Initial Catalog=Pecosriver; Uid=sa; Pwd=yourmom42;
```

Migration steps:
1. Export Access database to CSV
2. Import to MySQL via phpMyAdmin
3. Run data cleanup scripts
4. Update image paths
5. Verify data integrity

### Legacy Table Structure

The site was migrated from:
- **Database Type**: Microsoft Access (.mdb)
- **Server**: SQL Server (srv04)
- **Catalog**: Pecosriver

## Sample Data

### Insert Sample Category
```sql
INSERT INTO Categories (CategoryName, CategoryImage, Description, DisplayOrder)
VALUES ('Boots', 'boots-category.jpg', 'Quality Western Boots', 1);
```

### Insert Sample Product
```sql
INSERT INTO Products (
    ProductCode,
    ProductName,
    Description,
    CategoryID,
    Price,
    ImagePath,
    Featured
)
VALUES (
    'BOOT-001',
    'Classic Western Boot',
    'Genuine leather western boot',
    1,
    149.99,
    'kakadu/boot001.jpg',
    1
);
```

## Database Security

### Production Recommendations

1. **Use strong passwords**:
```php
define('DB_PASS', 'strong_random_password_here');
```

2. **Create dedicated database user**:
```sql
CREATE USER 'prt_user'@'localhost' IDENTIFIED BY 'secure_password';
GRANT SELECT, INSERT, UPDATE, DELETE ON pecosriver.* TO 'prt_user'@'localhost';
FLUSH PRIVILEGES;
```

3. **Regular backups**:
- Daily automated backups
- Store offsite
- Test restoration regularly

4. **Monitor for issues**:
- Check error logs
- Monitor slow queries
- Review access logs

## Troubleshooting

### Connection Issues
- Verify MySQL service is running
- Check credentials in `config/database.php`
- Test connection: `php test_db_connection.php`

### Slow Queries
- Check indexes are present
- Review EXPLAIN for queries
- Optimize large tables

### Data Inconsistencies
- Run validation scripts
- Check foreign key constraints
- Review recent changes

## Resources

- [MySQL Documentation](https://dev.mysql.com/doc/)
- [phpMyAdmin Guide](https://docs.phpmyadmin.net/)
- [Database Design Best Practices](https://www.mysqltutorial.org/)
