# Development Guide - Pecos River Traders

Guidelines and best practices for developing the Pecos River Traders website.

## Development Environment

### Required Tools
- **XAMPP** - Local Apache + MySQL + PHP
- **Code Editor** - VS Code, PHPStorm, or similar
- **Browser DevTools** - Chrome DevTools recommended
- **Git** - Version control (optional but recommended)

### Recommended VS Code Extensions
- PHP Intelephense
- PHP Debug
- HTML CSS Support
- Bootstrap 5 Quick Snippets
- GitLens (if using Git)

## Code Style Guidelines

### PHP Standards

Follow PSR-12 coding standards where possible.

**File Structure**:
```php
<?php
/**
 * File description
 *
 * Purpose and usage details
 */

session_start();
require_once(__DIR__ . '/config/database.php');
require_once(__DIR__ . '/includes/common.php');

// Your code here
?>
```

**Naming Conventions**:
- Variables: `$camelCase`
- Functions: `camelCase()`
- Classes: `PascalCase`
- Constants: `UPPER_SNAKE_CASE`
- Database tables: `PascalCase`

**Example**:
```php
// Good
$productName = "Western Boot";
$categoryId = 5;

function getProductById($productId) {
    // function code
}

// Avoid
$product_name = "Western Boot";  // snake_case for variables
$CATEGORYID = 5;                 // all caps for variables
```

### HTML/PHP Templates

Use consistent indentation and separation:

```php
<?php
// PHP logic at top
$products = getProducts();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php echo $pageTitle; ?></title>
    <!-- ... -->
</head>
<body>
    <!-- HTML template -->
    <?php foreach ($products as $product): ?>
        <div class="product-card">
            <h3><?php echo htmlspecialchars($product['ProductName']); ?></h3>
        </div>
    <?php endforeach; ?>
</body>
</html>
```

### CSS Guidelines

**File**: `assets/css/custom.css`

Use the existing CSS variable system:
```css
/* Use defined variables */
.my-element {
    color: var(--prt-red);
    background: var(--prt-tan);
}

/* Add new variables to :root */
:root {
    --prt-new-color: #value;
}
```

**Organization**:
- Group related styles together
- Use section comments
- Mobile-first responsive design

```css
/* ============================================
   Section Name
   ============================================ */
.component {
    /* Desktop styles */
}

@media (max-width: 768px) {
    .component {
        /* Mobile overrides */
    }
}
```

### JavaScript Guidelines

- Use modern ES6+ syntax
- Minimize inline JavaScript
- Comment complex logic
- Handle errors gracefully

```javascript
// Good
document.addEventListener('DOMContentLoaded', function() {
    const cartButton = document.getElementById('add-to-cart');
    cartButton.addEventListener('click', addToCart);
});

function addToCart(event) {
    event.preventDefault();
    // Cart logic
}
```

## Project Structure

```
PRT2/
├── admin/                     # Admin panel and management
│   ├── common.php            # Admin utilities
│   ├── Events.php            # Event management
│   └── ShowEvents.php        # Event display
├── assets/
│   ├── css/
│   │   └── custom.css        # Main stylesheet
│   └── images/               # All images
│       ├── kakadu/          # Product images by brand
│       └── headerCenter.jpg # Site header
├── auth/                     # Authentication pages
│   ├── login.php            # User login
│   ├── register.php         # User registration
│   ├── account.php          # User dashboard
│   └── loyalty-rewards.php  # Loyalty program
├── blog/                     # Blog/news system
│   ├── index.php            # Blog listing
│   └── post.php             # Individual blog post
├── cart/                     # Shopping cart
│   ├── cart.php             # Cart page
│   ├── checkout.php         # Checkout process
│   └── AddToCart.php        # Add to cart handler
├── config/
│   ├── database.php         # DB configuration
│   ├── csrf.php             # CSRF protection
│   └── security-headers.php # Security headers
├── docs/                     # Documentation
│   ├── README.md
│   ├── DEVELOPMENT.md       # This file
│   ├── FEATURE_LOCATIONS.md
│   └── FILE_ORGANIZATION.md
├── includes/                 # Common PHP includes
│   ├── common.php           # Shared functions
│   ├── header.php           # Navigation bar
│   ├── footer.php           # Footer template
│   ├── loyalty-functions.php
│   ├── coupon-functions.php
│   └── faq-handler.php
├── maintenance/              # Maintenance scripts (CLI only)
│   ├── setup_blog.php
│   ├── setup_faq.php
│   ├── check_products_id.php
│   └── .htaccess            # Blocks web access
├── pages/                    # Public-facing pages
│   ├── about-us.php
│   ├── contact-us.php
│   ├── faq.php
│   └── gift-cards.php
├── products/                 # Product catalog
│   ├── products.php         # Product listing
│   ├── product-detail.php   # Product details
│   └── compare.php          # Product comparison
├── public/                   # Public XML feeds
│   ├── sitemap.xml.php
│   └── google-shopping-feed.xml.php
├── index.php                 # Home page
├── 404.php                   # Custom 404 error
└── robots.txt                # SEO crawl instructions
```

## Common Functions

### Database Queries

Always use prepared statements:

```php
// Good - Protected against SQL injection
$stmt = $dbConnect->prepare("SELECT * FROM Products WHERE ProductID = ?");
$stmt->execute([$productId]);
$product = $stmt->fetch();

// Bad - Vulnerable to SQL injection
$query = "SELECT * FROM Products WHERE ProductID = $productId";
$result = $dbConnect->query($query);
```

### Output Escaping

Always escape output:

```php
// Good
echo htmlspecialchars($product['ProductName']);
echo htmlspecialchars($product['Description'], ENT_QUOTES, 'UTF-8');

// Bad - XSS vulnerability
echo $product['ProductName'];
```

### Session Management

```php
// Start session at top of file
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Cart management
$_SESSION['ItemsInBasket'] = count($_SESSION['cart'] ?? []);
```

## Bootstrap 5 Components

The site uses **Bootstrap 5.3.2**. Common patterns:

### Cards
```html
<div class="card product-card">
    <img src="..." class="card-img-top" alt="...">
    <div class="card-body product-card-body">
        <h5 class="card-title product-card-title">Product Name</h5>
        <p class="card-text product-card-text">Description</p>
        <p class="product-price">$99.99</p>
        <div class="d-grid">
            <a href="#" class="btn btn-primary">View Details</a>
        </div>
    </div>
</div>
```

### Navigation
```html
<nav class="navbar navbar-expand-lg navbar-dark navbar-custom sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">
            <i class="bi bi-shop"></i> PECOS RIVER TRADERS
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Home</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
```

## Adding New Features

### Creating a New Page

1. **Determine location** based on type:
   - Public pages → `/pages/`
   - Authentication → `/auth/`
   - Blog posts → `/blog/`
   - Products → `/products/`
2. **Create PHP file** using template below
3. **Update title and content**
4. **Add to navigation** in `includes/header.php`
5. **Test thoroughly**

**Important**: Always start session and use consistent Bootstrap version (5.3.2)

Template:
```php
<?php
session_start(); // ALWAYS start session first!
require_once(__DIR__ . '/../config/database.php');
require_once(__DIR__ . '/../includes/common.php');

$pageTitle = "Page Title";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?> - Pecos River Traders</title>

    <!-- Bootstrap 5.3.2 (consistent version) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/custom.css">
</head>
<body>
    <?php include(__DIR__ . '/../includes/header.php'); ?>

    <!-- Page Header (standard styling) -->
    <div class="page-header">
        <div class="container text-center">
            <h1 class="display-4 fw-bold mb-3" style="color: var(--prt-brown);">
                <i class="bi bi-icon-name"></i> Page Title
            </h1>
            <p class="lead" style="color: var(--prt-brown);">Page subtitle</p>
        </div>
    </div>

    <!-- Main Content (min-height prevents footer issues) -->
    <div class="container my-5" style="min-height: 60vh;">
        <!-- Your content here -->
    </div>

    <?php include(__DIR__ . '/../includes/footer.php'); ?>

    <!-- Footer already includes Bootstrap JS - DO NOT add duplicate script here -->
</body>
</html>
```

### Adding a New Product Category

1. Insert into database:
```sql
INSERT INTO Categories (CategoryName, CategoryImage, Description, DisplayOrder)
VALUES ('New Category', 'category-image.jpg', 'Description', 10);
```

2. Add category image to `assets/images/`
3. Verify in category listing

### Adding Product Images

1. Place images in `assets/images/[brand]/`
2. Use consistent naming: `productcode.jpg`
3. Update Products table:
```php
UPDATE Products
SET ImagePath = 'kakadu/productcode.jpg'
WHERE ProductCode = 'CODE';
```

## Testing

### Manual Testing Checklist

- [ ] Homepage loads correctly
- [ ] All navigation links work
- [ ] Products display with images
- [ ] Category filtering works
- [ ] Shopping cart adds/removes items
- [ ] Responsive design on mobile
- [ ] No PHP errors in logs
- [ ] No JavaScript console errors

### Database Testing

Run validation scripts:
```bash
php check_products_data.php
php check_categories.php
php verify_fixes.php
```

### Cross-Browser Testing

Test in:
- Chrome
- Firefox
- Safari
- Edge
- Mobile browsers (iOS Safari, Chrome Mobile)

## Debugging

### Enable PHP Error Reporting

Add to top of page during development:
```php
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
```

### Check Logs

- Apache error log: `C:\xampp\apache\logs\error.log`
- PHP error log: Check `php.ini` for location

### Database Debugging

```php
// Enable PDO error mode
$dbConnect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Debug query
try {
    $stmt = $dbConnect->prepare($query);
    $stmt->execute($params);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
```

## Performance Optimization

### Database
- Use indexes on frequently queried columns
- Limit SELECT to needed columns only
- Use pagination for large result sets
- Cache repeated queries

### Images
- Optimize image sizes (max 800px width for products)
- Use WebP format where supported
- Implement lazy loading

### CSS/JS
- Minimize use of custom JavaScript
- Leverage Bootstrap classes
- Avoid inline styles

## Security Best Practices

### Input Validation
```php
// Validate and sanitize input
$productId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$productId) {
    die("Invalid product ID");
}
```

### SQL Injection Prevention
```php
// Always use prepared statements
$stmt = $dbConnect->prepare("SELECT * FROM Products WHERE CategoryID = ?");
$stmt->execute([$categoryId]);
```

### XSS Prevention
```php
// Escape all output
echo htmlspecialchars($userInput, ENT_QUOTES, 'UTF-8');
```

### File Upload Security
```php
// Validate file types
$allowed = ['jpg', 'jpeg', 'png', 'gif'];
$ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
if (!in_array($ext, $allowed)) {
    die("Invalid file type");
}
```

## Git Workflow (If Using Git)

```bash
# Create feature branch
git checkout -b feature/new-feature

# Make changes and commit
git add .
git commit -m "Add: new feature description"

# Push to remote
git push origin feature/new-feature

# Merge to main after review
git checkout main
git merge feature/new-feature
```

## Common Tasks

### Update Product Price
```php
$stmt = $dbConnect->prepare("UPDATE Products SET Price = ? WHERE ProductID = ?");
$stmt->execute([$newPrice, $productId]);
```

### Add Product to Featured
```php
$stmt = $dbConnect->prepare("UPDATE Products SET Featured = 1 WHERE ProductID = ?");
$stmt->execute([$productId]);
```

### Clear Shopping Cart
```php
unset($_SESSION['cart']);
$_SESSION['ItemsInBasket'] = 0;
```

## Resources

- [PHP Manual](https://www.php.net/manual/en/)
- [Bootstrap 5 Docs](https://getbootstrap.com/docs/5.3/)
- [MySQL Reference](https://dev.mysql.com/doc/)
- [MDN Web Docs](https://developer.mozilla.org/)

## Common Issues & Solutions

### Dropdown Menus Not Working
**Problem**: Account dropdown doesn't work on certain pages
**Cause**: Duplicate Bootstrap JS loading
**Solution**: Remove `<script src="bootstrap.bundle.min.js">` from page - footer already includes it

### Loyalty Rewards Redirect Loop
**Problem**: Loyalty rewards page redirects to account.php
**Cause**: Missing `session_start()` before session check
**Solution**: Always call `session_start()` at the very top of the file

### Footer Appearing Mid-Page
**Problem**: Footer shows halfway up the page on empty content
**Cause**: Content container too short
**Solution**: Add `style="min-height: 60vh;"` to main container

### Hero Section Wrong Colors
**Problem**: Hero has wrong gradient or text colors
**Cause**: Using custom class instead of `.page-header`
**Solution**: Use standard `.page-header` class with brown text (`color: var(--prt-brown)`)

### Link URL Has Double Slashes
**Problem**: URLs show as `/prt2//pages/faq.php`
**Cause**: Incorrect path construction
**Solution**: Use `makeLink()` helper function from header.php

## Recent Changes (November 18, 2025)

### Bootstrap Updates
- All pages now use Bootstrap 5.3.2 (consistent version)
- Removed duplicate Bootstrap JS from individual pages
- Footer now contains single Bootstrap JS include

### Styling Standards
- Standard hero section: `.page-header` class
- Hero gradient: Brown to green (`rgba(139, 108, 66, 0.95)` to `rgba(0, 64, 0, 0.95)`)
- Hero text color: Brown (`var(--prt-brown)`)
- Minimum container height: `60vh` to prevent footer issues

### File Organization
- Maintenance scripts moved to `/maintenance/` (CLI only, .htaccess protected)
- Public pages moved to `/pages/`
- XML feeds moved to `/public/`
- Clean root directory with only essential files

### Navigation Updates
- Blog added to main navigation
- Loyalty Rewards, Gift Cards, FAQ added to account dropdown
- All features now accessible through navigation

## Getting Help

1. Check existing documentation (especially FEATURE_LOCATIONS.md)
2. Review error logs (`C:\xampp\apache\logs\error.log`)
3. Check FILE_ORGANIZATION.md for file locations
4. Search for similar issues
5. Contact development team
