# Frontend Setup Guide

Last Updated: 2025-11-25 16:24:27

- Pecos River Traders

Complete installation and configuration guide for the Pecos River Traders website.

## Prerequisites

### Required Software
- **XAMPP** (Apache + MySQL + PHP 7.4 or higher)
  - Download from: https://www.apachefriends.org/
  - Includes Apache, MySQL, and PHP
- **Web Browser** (Chrome, Firefox, Edge, Safari)
- **Git** (optional, for version control)

### Recommended Tools
- **phpMyAdmin** (included with XAMPP)
- **VS Code** or similar code editor
- **Composer** (optional, for dependency management)

## Installation Steps

### 1. Install XAMPP

1. Download XAMPP for your operating system
2. Run the installer
3. Install to default location: `C:\xampp` (Windows) or `/Applications/XAMPP` (Mac)
4. Select components:
   - Apache
   - MySQL
   - PHP
   - phpMyAdmin

### 2. Clone/Copy Project Files

**Option A: Using Git**
```bash
cd C:\xampp\htdocs
git clone <repository-url> PRT2
```

**Option B: Manual Copy**
1. Copy the `PRT2` folder to `C:\xampp\htdocs\`
2. Ensure the path is: `C:\xampp\htdocs\PRT2\`

### 3. Start XAMPP Services

1. Open XAMPP Control Panel
2. Start **Apache** service
3. Start **MySQL** service
4. Verify both show green "Running" status

### 4. Database Setup

#### Create Database

1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Click "New" to create a new database
3. Database name: `pecosriver`
4. Collation: `utf8mb4_general_ci`
5. Click "Create"

#### Import Database Schema

1. Select the `pecosriver` database
2. Click "Import" tab
3. Choose file: Browse to your SQL dump file (if available)
4. Click "Go"

**If starting fresh**, you'll need to create the tables. See [DATABASE.md](DATABASE.md) for schema details.

### 5. Configure Database Connection

Edit `config/database.php`:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'pecosriver');
define('DB_USER', 'root');
define('DB_PASS', ''); // XAMPP default is blank
```

For production environments, use secure credentials:
```php
define('DB_USER', 'your_username');
define('DB_PASS', 'your_secure_password');
```

### 6. Set File Permissions

**Windows:**
- Ensure the web server has read access to all files
- Write access needed for: `assets/images/` (for uploads)

**Linux/Mac:**
```bash
chmod -R 755 /path/to/PRT2
chmod -R 777 /path/to/PRT2/assets/images
```

### 7. Verify Installation

1. Open browser and navigate to: `http://localhost/PRT2`
2. You should see the homepage
3. Check for:
   - Images loading correctly
   - Categories displaying
   - Navigation working (including Blog, Account dropdown)
   - No PHP errors
   - Footer at bottom of page

### 7b. Test Additional Features

After setup, test these pages:
- Blog: `http://localhost/PRT2/blog/`
- FAQ: `http://localhost/PRT2/pages/faq.php`
- Gift Cards: `http://localhost/PRT2/pages/gift-cards.php`
- Product Comparison: `http://localhost/PRT2/products/compare.php`
- Loyalty Rewards: `http://localhost/PRT2/auth/loyalty-rewards.php` (requires login)

### 8. Test Database Connection

Create a test file `test-db.php`:
```php
<?php
require_once 'config/database.php';
echo "Database connected successfully!";
?>
```

Access: `http://localhost/PRT2/test-db.php`

If successful, delete the test file.

## Common Issues

### Apache Won't Start
- **Port 80 in use**: Change Apache port in XAMPP config
- **Skype conflict**: Disable Skype's port 80 usage
- **IIS running**: Stop IIS service on Windows

### MySQL Won't Start
- **Port 3306 in use**: Check for other MySQL installations
- **Previous crash**: Delete `ibdata1` and restart (backup first!)

### White Screen / PHP Errors
- Enable error reporting in `php.ini`:
  ```ini
  display_errors = On
  error_reporting = E_ALL
  ```
- Check Apache error logs: `C:\xampp\apache\logs\error.log`

### Images Not Loading
- Check file paths in database
- Verify images exist in `assets/images/`
- Check case sensitivity (especially on Linux)
- Run `copy_images.php` if migrating from old site

### Database Connection Failed
- Verify MySQL is running
- Check credentials in `config/database.php`
- Ensure database `pecosriver` exists
- Check PHP PDO extension is enabled

## Development Environment Setup

### Enable Error Reporting (Development Only!)

Edit `php.ini` in XAMPP:
```ini
display_errors = On
display_startup_errors = On
error_reporting = E_ALL
```

Restart Apache after changes.

### Virtual Host Setup (Optional)

Create a custom domain like `pecosriver.local`:

1. Edit `C:\xampp\apache\conf\extra\httpd-vhosts.conf`:
```apache
<VirtualHost *:80>
    DocumentRoot "C:/xampp/htdocs/PRT2"
    ServerName pecosriver.local
</VirtualHost>
```

2. Edit `C:\Windows\System32\drivers\etc\hosts`:
```
127.0.0.1 pecosriver.local
```

3. Restart Apache
4. Access: `http://pecosriver.local`

## Optional Feature Setup

The PRT2 site includes optional features that require setup scripts. These scripts are located in the `/maintenance/` folder and must be run via PHP CLI (not web browser).

### Blog System Setup

```bash
php C:\xampp\htdocs\PRT2\maintenance\setup_blog.php
```

Creates blog categories and sample blog posts.

### FAQ System Setup

```bash
php C:\xampp\htdocs\PRT2\maintenance\setup_faq.php
```

Creates FAQ categories and sample questions.

### Loyalty Program Setup

```bash
php C:\xampp\htdocs\PRT2\maintenance\setup_loyalty.php
```

Creates loyalty tiers and rewards.

### Coupon System Setup

```bash
php C:\xampp\htdocs\PRT2\maintenance\setup_coupons.php
```

Creates sample discount coupons.

**Note**: These setup scripts are optional but recommended for full feature set.

## Image Migration

If migrating from the old PRT site:

```bash
php C:\xampp\htdocs\PRT2\maintenance\copy_images.php
```

This copies images from the original site structure to the new location.

## Next Steps

- Review [DATABASE.md](DATABASE.md) for schema details
- Read [DEVELOPMENT.md](DEVELOPMENT.md) for coding guidelines
- See [DEPLOYMENT.md](DEPLOYMENT.md) for production deployment

## Troubleshooting

For additional help:
1. Check XAMPP error logs
2. Review PHP error logs
3. Verify database connection
4. Check file permissions
5. Clear browser cache

## Maintenance Scripts Security

The `/maintenance/` folder contains setup and diagnostic scripts that should NOT be accessible via web browser.

- Scripts are protected by `.htaccess` (Apache)
- Always run via PHP CLI: `php C:\xampp\htdocs\PRT2\maintenance\script.php`
- Never expose this folder in production

**Available Maintenance Scripts:**
- `setup_blog.php` - Initialize blog system
- `setup_faq.php` - Initialize FAQ system
- `setup_coupons.php` - Create sample coupons
- `setup_loyalty.php` - Initialize loyalty program
- `check_products_id.php` - Validate product data
- `check_sizes_structure.php` - Validate size data

## Recent Updates (November 18, 2025)

### File Organization
- Maintenance scripts moved to `/maintenance/` folder (CLI only)
- Public pages moved to `/pages/` folder
- XML feeds moved to `/public/` folder
- Clean root directory with only essential files

### New Features Requiring Setup
- Blog/News system (run `setup_blog.php`)
- FAQ system (run `setup_faq.php`)
- Loyalty rewards (run `setup_loyalty.php`)
- Coupon system (run `setup_coupons.php`)

### Bootstrap Version
- All pages now use Bootstrap 5.3.2
- Consistent styling across all pages
- No duplicate Bootstrap JS loading

## Security Notes

**IMPORTANT for Production:**
- Change default database password
- Disable error display (`display_errors = Off`)
- Use HTTPS/SSL
- Implement proper input validation
- Keep PHP and MySQL updated
- Regular security audits
- Ensure `/maintenance/` folder is not web-accessible
