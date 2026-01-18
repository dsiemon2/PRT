# Pecos River Trading Company - Coding Standards

**Version**: 1.0.0
**Last Updated**: November 18, 2025
**Status**: Official Standard for All New Code

---

## Table of Contents

1. [Overview](#overview)
2. [PHP Coding Standards](#php-coding-standards)
3. [File Naming Conventions](#file-naming-conventions)
4. [Function Naming Conventions](#function-naming-conventions)
5. [Variable Naming Conventions](#variable-naming-conventions)
6. [Database Conventions](#database-conventions)
7. [Documentation Standards](#documentation-standards)
8. [HTML/CSS Standards](#htmlcss-standards)
9. [JavaScript Standards](#javascript-standards)
10. [Security Standards](#security-standards)
11. [Error Handling Standards](#error-handling-standards)
12. [Page Structure Template](#page-structure-template)

---

## Overview

These coding standards ensure consistency, maintainability, and quality across the Pecos River Trading Company codebase. All new code MUST follow these standards. Existing code SHOULD be refactored to meet these standards when modified.

### Guiding Principles

1. **Consistency**: Code should look like it was written by one person
2. **Clarity**: Code should be self-documenting where possible
3. **Security**: Security should be built-in, not bolted-on
4. **Maintainability**: Future developers should easily understand the code
5. **Performance**: Write efficient code, but prioritize readability

---

## PHP Coding Standards

### General PHP Rules

1. **PHP Version**: Target PHP 7.4+ (current XAMPP version)
2. **Encoding**: All files MUST use UTF-8 without BOM
3. **Line Endings**: Use Unix line endings (LF, not CRLF)
4. **Indentation**: 4 spaces (no tabs)
5. **Line Length**: Limit lines to 120 characters when practical

### PHP Tags

```php
// CORRECT: Full PHP tags
<?php

// INCORRECT: Short tags
<?
```

### Type Hints

Use type hints for all new functions (PHP 7.0+):

```php
// CORRECT: With type hints
function calculateDiscount(float $price, int $percentage): float {
    return $price * ($percentage / 100);
}

// INCORRECT: Without type hints
function calculateDiscount($price, $percentage) {
    return $price * ($percentage / 100);
}
```

### Return Type Declarations

Always specify return types:

```php
// CORRECT
function getUserById(int $userId): ?array {
    // Returns array or null
}

function isValidEmail(string $email): bool {
    // Returns boolean
}

function processOrder(int $orderId): void {
    // Returns nothing
}
```

### Strict Types

Use strict types at the top of all new PHP files:

```php
<?php
declare(strict_types=1);

// Rest of file
```

---

## File Naming Conventions

### PHP Files

| File Type | Convention | Example |
|-----------|------------|---------|
| Public pages | kebab-case.php | `about-us.php`, `contact-us.php` |
| Include files | kebab-case.php | `product-image-functions.php` |
| Class files | PascalCase.php | `ProductManager.php`, `OrderProcessor.php` |
| Config files | lowercase.php | `database.php`, `tracking.php` |
| Handler files | kebab-case-handler.php | `auth-handler.php`, `wishlist-handler.php` |

### Directories

| Directory Type | Convention | Example |
|----------------|------------|---------|
| Feature folders | PascalCase | `Products/`, `Cart/` |
| Utility folders | lowercase | `includes/`, `config/`, `assets/` |
| User folders | lowercase | `auth/`, `pages/` |

---

## Function Naming Conventions

### Standard Functions

Use **camelCase** for all function names:

```php
// CORRECT
function getUserById(int $id): ?array { }
function calculateOrderTotal(array $items): float { }
function sendConfirmationEmail(string $to, array $orderData): bool { }

// INCORRECT
function GetUserById($id) { }  // PascalCase
function calculate_order_total($items) { }  // snake_case
```

### Boolean Functions

Prefix with `is`, `has`, `should`, `can`:

```php
function isAuthenticated(): bool { }
function hasPermission(string $permission): bool { }
function shouldShowDiscount(float $total): bool { }
function canCheckout(): bool { }
```

### Action Functions

Use clear verb-noun combinations:

```php
function createOrder(array $data): int { }
function updateInventory(int $productId, int $quantity): bool { }
function deleteUser(int $userId): void { }
function fetchProducts(array $filters): array { }
```

### CRUD Operations

Follow consistent naming:

```php
// Create
function createProduct(array $data): int { }

// Read
function getProduct(int $id): ?array { }
function getProducts(array $filters = []): array { }

// Update
function updateProduct(int $id, array $data): bool { }

// Delete
function deleteProduct(int $id): bool { }
```

---

## Variable Naming Conventions

### Variables

Use **camelCase** for variable names:

```php
// CORRECT
$userId = 123;
$orderTotal = 99.99;
$shippingAddress = [];

// INCORRECT
$user_id = 123;  // snake_case
$UserID = 123;   // PascalCase
```

### Constants

Use **SCREAMING_SNAKE_CASE** for constants:

```php
// CORRECT
define('MAX_LOGIN_ATTEMPTS', 5);
define('SESSION_TIMEOUT', 3600);
const TAX_RATE = 0.0825;

// INCORRECT
define('maxLoginAttempts', 5);
define('SessionTimeout', 3600);
```

### Arrays

Use descriptive plural names:

```php
// CORRECT
$products = [];
$orderItems = [];
$shippingOptions = [];

// INCORRECT
$product = [];  // Confusing - is it one or many?
$items = [];    // Too generic
```

### Booleans

Prefix with `is`, `has`, `should`:

```php
$isLoggedIn = true;
$hasPermission = false;
$shouldRedirect = true;
```

---

## Database Conventions

### Table Names

- Use **snake_case** for table names
- Use plural form for data tables
- Use singular for junction/pivot tables

```sql
-- CORRECT
products3
orders
order_items
users
product_images
blog_posts

-- INCORRECT
Products  -- Not lowercase
product  -- Should be plural
orderitems  -- Needs underscore
```

### Column Names

- Use **snake_case** for column names
- Be descriptive but concise
- Use consistent suffixes

```sql
-- CORRECT
user_id
email_address
created_at
is_active
order_total
shipping_method

-- INCORRECT
UserID  -- Not snake_case
email  -- Could be confused with email address vs email message
createdAt  -- Not snake_case
active  -- Use is_active for boolean
```

### Foreign Keys

Format: `{referenced_table}_id`

```sql
user_id  -- References users table
product_id  -- References products table
category_id  -- References categories table
```

### Timestamps

Use these standard names:

```sql
created_at DATETIME DEFAULT CURRENT_TIMESTAMP
updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
deleted_at DATETIME NULL  -- For soft deletes
```

---

## Documentation Standards

### File-Level Documentation

Every PHP file should start with a doc block:

```php
<?php
/**
 * Product Management Functions
 *
 * Handles all product-related operations including CRUD,
 * inventory management, and product relationships.
 *
 * @package PRT2
 * @subpackage Products
 * @version 1.0.0
 * @since November 18, 2025
 */
```

### Function Documentation

Use PHPDoc format for all functions:

```php
/**
 * Calculate discount amount based on coupon
 *
 * Applies the coupon discount rules and returns the discount amount.
 * Handles both percentage and fixed amount coupons with max discount caps.
 *
 * @param array $coupon Coupon data array with 'type', 'value', 'max_discount'
 * @param float $orderTotal Total order amount before discount
 *
 * @return float Discount amount to apply
 *
 * @throws InvalidArgumentException If coupon type is invalid
 *
 * @example
 * $discount = calculateDiscount(['type' => 'percentage', 'value' => 20, 'max_discount' => 50], 300);
 * // Returns 50 (20% of 300 = 60, but capped at 50)
 */
function calculateDiscount(array $coupon, float $orderTotal): float {
    // Implementation
}
```

### Inline Comments

- Use comments to explain **WHY**, not **WHAT**
- Avoid obvious comments
- Keep comments updated with code

```php
// GOOD COMMENTS
// Apply tier multiplier only for non-promotional items
$points = $basePoints * $tierMultiplier;

// Prevent race condition in concurrent cart updates
$stmt->execute();

// POOR COMMENTS
// Set $i to 0
$i = 0;

// Loop through products
foreach ($products as $product) {
```

---

## HTML/CSS Standards

### HTML

1. Use semantic HTML5 elements
2. Close all tags
3. Use lowercase for elements and attributes
4. Use double quotes for attribute values

```html
<!-- CORRECT -->
<nav class="navbar">
    <ul class="nav-list">
        <li class="nav-item"><a href="/home">Home</a></li>
    </ul>
</nav>

<!-- INCORRECT -->
<NAV class='navbar'>
    <UL>
        <LI><A href=/home>Home</A>
</NAV>
```

### CSS Classes

Use Bootstrap 5 utility classes where possible:

```html
<!-- CORRECT -->
<div class="container mt-4 mb-3">
    <div class="row">
        <div class="col-md-6">Content</div>
    </div>
</div>

<!-- Custom classes: Use kebab-case -->
<div class="product-card featured-item">
```

---

## JavaScript Standards

### Variable Declaration

Use `const` and `let`, never `var`:

```javascript
// CORRECT
const TAX_RATE = 0.0825;
let cartTotal = 0;

// INCORRECT
var cartTotal = 0;
```

### Function Names

Use camelCase:

```javascript
// CORRECT
function calculateTotal() { }
async function fetchProducts() { }

// INCORRECT
function CalculateTotal() { }
function fetch_products() { }
```

### Modern JavaScript

Use modern ES6+ features:

```javascript
// CORRECT: Arrow functions
const addToCart = (productId, quantity) => {
    // ...
};

// CORRECT: Template literals
const message = `Added ${quantity} items to cart`;

// CORRECT: Destructuring
const { name, price } = product;

// CORRECT: Async/await
async function loadProducts() {
    try {
        const response = await fetch('/api/products');
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Failed to load products:', error);
    }
}
```

---

## Security Standards

### Input Validation

**ALWAYS** validate and sanitize user input:

```php
// CORRECT
$email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
if (!$email) {
    throw new InvalidArgumentException('Invalid email address');
}

$quantity = filter_var($_POST['quantity'], FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 1, 'max_range' => 100]
]);
```

### SQL Injection Prevention

**ALWAYS** use prepared statements:

```php
// CORRECT
$stmt = $dbConnect->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);

// INCORRECT - NEVER DO THIS
$result = $dbConnect->query("SELECT * FROM users WHERE email = '$email'");
```

### XSS Prevention

**ALWAYS** escape output:

```php
// CORRECT
echo htmlspecialchars($userInput, ENT_QUOTES, 'UTF-8');

// INCORRECT
echo $userInput;
```

### CSRF Protection

**ALWAYS** use CSRF tokens on forms:

```php
// In form
<?php csrfTokenField(); ?>

// In handler
if (!csrfTokenValid()) {
    http_response_code(403);
    die('Invalid CSRF token');
}
```

---

## Error Handling Standards

### Try-Catch Blocks

Use try-catch for all database operations and external calls:

```php
try {
    $stmt = $dbConnect->prepare("INSERT INTO orders (user_id, total) VALUES (?, ?)");
    $stmt->execute([$userId, $total]);
    $orderId = $dbConnect->lastInsertId();

    return $orderId;
} catch (PDOException $e) {
    error_log("Order creation failed: " . $e->getMessage());
    throw new Exception("Failed to create order. Please try again.");
}
```

### Error Logging

Log errors, never display them to users:

```php
// CORRECT
try {
    // Code
} catch (Exception $e) {
    error_log("Error in processOrder: " . $e->getMessage());
    return false;  // or throw user-friendly exception
}

// INCORRECT
catch (Exception $e) {
    echo $e->getMessage();  // Exposes system details
}
```

### User-Friendly Messages

Show helpful messages to users:

```php
// CORRECT
if (!$result) {
    return [
        'success' => false,
        'message' => 'Unable to process your order. Please try again or contact support.'
    ];
}

// INCORRECT
if (!$result) {
    return [
        'success' => false,
        'message' => 'PDOException in line 42: Duplicate entry for key PRIMARY'
    ];
}
```

---

## Page Structure Template

### Standard Page Template

Use the new layout functions for all pages:

```php
<?php
/**
 * Page Title - Description
 *
 * @package PRT2
 * @since Date
 */

// Initialize
require_once(__DIR__ . '/config/database.php');
require_once(__DIR__ . '/includes/layout.php');

// Authentication check (if needed)
requireAuth();

// Page logic here
$pageData = getPageData();

// Start page output
startPage([
    'title' => 'Page Title',
    'description' => 'SEO description',
    'keywords' => 'keyword1, keyword2'
]);

// Breadcrumb
echo generateBreadcrumb([
    ['label' => 'Section', 'url' => '/section'],
    ['label' => 'Current Page']
]);

// Main content
startContainer();
?>

<h1>Page Heading</h1>

<!-- Page content here -->

<?php
endContainer();

// End page
endPage();
?>
```

---

## Grid & Table Standards

### Overview

All data tables and grids must follow the standard patterns for consistency. See [GRID_STANDARDS.md](GRID_STANDARDS.md) for complete implementation details.

### Key Requirements

1. **Row Selection**
   - Light blue background (#e3f2fd) on click
   - Only one row selected at a time
   - Clicking buttons/links does NOT trigger selection
   - Cursor changes to pointer on hover

2. **Action Buttons**
   - Use icon buttons with `title` attribute for tooltips
   - Group multiple actions in `btn-group`
   - Follow standard icon conventions (eye for view, pencil for edit, trash for delete)

3. **Pagination**
   - Show "Showing X to Y of Z entries" info text
   - Include Previous/Next navigation
   - Display page numbers
   - Position at bottom of card in `card-footer`

### Quick Implementation

```php
<!-- Row with onclick handler -->
<tr onclick="highlightRow(event)" style="cursor: pointer;">
    <td>Content</td>
    <td>
        <button class="btn btn-sm btn-outline-primary" title="View details">
            <i class="bi bi-eye"></i>
        </button>
    </td>
</tr>
```

For complete code examples and JavaScript functions, see [GRID_STANDARDS.md](GRID_STANDARDS.md).

---

## Migration Guide

### Migrating Existing Pages

1. **Replace header code** with `startPage()` function
2. **Replace breadcrumbs** with `generateBreadcrumb()` function
3. **Replace auth checks** with `requireAuth()` function
4. **Replace footer code** with `endPage()` function
5. **Add doc blocks** to all functions
6. **Add type hints** to function parameters and returns

### Example Migration

**Before:**
```php
<?php
session_start();
require_once('../config/database.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Account - Pecos River Trading Company</title>
    <!-- ... 20 lines of meta tags and CSS ... -->
</head>
<body>
<?php include('../includes/header.php'); ?>
<!-- Breadcrumb HTML ... -->
```

**After:**
```php
<?php
require_once(__DIR__ . '/../config/database.php');
require_once(__DIR__ . '/../includes/layout.php');

requireAuth();

startPage(['title' => 'My Account']);
echo generateBreadcrumb([['label' => 'My Account']]);
startContainer();
```

---

## Enforcement

### Code Reviews

All code changes MUST be reviewed for compliance with these standards.

### Automated Tools

Consider using:
- PHP_CodeSniffer for PHP standards
- ESLint for JavaScript standards
- Prettier for formatting

### Legacy Code

When modifying existing code:
1. Bring the modified function up to standards
2. If time permits, refactor the entire file
3. Document any deviations with TODO comments

---

**Questions?** Contact the development team.

**Updates?** This document is version-controlled. Propose changes via the standard process.
