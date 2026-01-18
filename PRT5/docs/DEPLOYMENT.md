# Deployment Guide - Pecos River Traders

Production deployment instructions for the Pecos River Traders website.

## Pre-Deployment Checklist

### Code Review
- [ ] All features tested locally
- [ ] No debug code or `var_dump()` statements
- [ ] Error reporting disabled for production
- [ ] Database credentials secured
- [ ] No test/debug files included
- [ ] All comments reviewed and cleaned
- [ ] Code follows style guidelines

### Security Review
- [ ] SQL injection protection verified
- [ ] XSS prevention implemented
- [ ] File upload validation in place
- [ ] Session management secure
- [ ] HTTPS/SSL configured
- [ ] Strong database passwords
- [ ] Admin areas password protected
- [ ] No sensitive data in version control

### Performance Review
- [ ] Images optimized
- [ ] Database indexed properly
- [ ] Slow queries optimized
- [ ] Caching implemented where needed
- [ ] Asset minification considered

## Production Environment Requirements

### Server Requirements
- **Web Server**: Apache 2.4+ or Nginx 1.18+
- **PHP**: 7.4 or higher (8.0+ recommended)
- **Database**: MySQL 5.7+ or MariaDB 10.3+
- **SSL Certificate**: Required for HTTPS
- **Memory**: Minimum 512MB RAM (1GB+ recommended)
- **Storage**: Minimum 5GB for images and database

### PHP Extensions Required
- PDO
- pdo_mysql
- mbstring
- gd or imagick (for image processing)
- session
- json
- openssl

Verify with:
```bash
php -m
```

## Deployment Methods

### Method 1: FTP/SFTP Upload

1. **Connect to server** via FTP/SFTP client (FileZilla, WinSCP)
2. **Upload files** to web root (usually `/public_html` or `/var/www/html`)
3. **Exclude** development files:
   - `.git/` directory
   - `*.md` documentation (optional)
   - Test/debug PHP files
   - Development utilities

4. **Set permissions**:
```bash
# Files: 644
find . -type f -exec chmod 644 {} \;

# Directories: 755
find . -type d -exec chmod 755 {} \;

# Writable directories: 777 (or 775 with proper ownership)
chmod 777 assets/images/uploads
```

### Method 2: Git Deployment

1. **SSH into server**
2. **Clone repository**:
```bash
cd /var/www/html
git clone <repository-url> pecosriver
cd pecosriver
```

3. **Checkout production branch**:
```bash
git checkout production
```

4. **Set up automated deployments** (optional):
```bash
# Create deploy script
nano deploy.sh

#!/bin/bash
cd /var/www/html/pecosriver
git pull origin production
# Run any post-deployment tasks
```

## Database Setup

### Export from Development

Using phpMyAdmin:
1. Select `pecosriver` database
2. Click "Export"
3. Method: Quick
4. Format: SQL
5. Download file

Using command line:
```bash
mysqldump -u root -p pecosriver > pecosriver_export.sql
```

### Import to Production

Via phpMyAdmin:
1. Create new database `pecosriver`
2. Select database
3. Click "Import"
4. Choose SQL file
5. Execute

Via command line:
```bash
mysql -u username -p pecosriver < pecosriver_export.sql
```

### Create Production Database User

```sql
CREATE USER 'prt_prod'@'localhost' IDENTIFIED BY 'STRONG_PASSWORD_HERE';
GRANT SELECT, INSERT, UPDATE, DELETE ON pecosriver.* TO 'prt_prod'@'localhost';
FLUSH PRIVILEGES;
```

## Configuration Changes

### 1. Update Database Configuration

Edit `config/database.php`:

```php
<?php
// Production database settings
define('DB_HOST', 'localhost');  // Or remote DB host
define('DB_NAME', 'pecosriver');
define('DB_USER', 'prt_prod');
define('DB_PASS', 'your_secure_password');
define('DB_CHARSET', 'utf8mb4');
```

### 2. Disable Error Display

Create or edit `php.ini` or `.htaccess`:

**.htaccess method**:
```apache
php_flag display_errors Off
php_flag display_startup_errors Off
php_value error_reporting 0
php_value log_errors On
php_value error_log /path/to/error.log
```

**Or in your PHP files**:
```php
<?php
// At the very top of critical files
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);
ini_set('log_errors', 1);
ini_set('error_log', '/path/to/error.log');
```

### 3. Configure Session Settings

In `php.ini` or via `.htaccess`:
```ini
session.cookie_httponly = 1
session.cookie_secure = 1  # If using HTTPS
session.use_strict_mode = 1
```

## SSL/HTTPS Setup

### Using Let's Encrypt (Free)

```bash
# Install Certbot
sudo apt-get update
sudo apt-get install certbot python3-certbot-apache

# Obtain certificate
sudo certbot --apache -d yourdomain.com -d www.yourdomain.com

# Auto-renewal is set up automatically
# Test renewal:
sudo certbot renew --dry-run
```

### Force HTTPS

Add to `.htaccess`:
```apache
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

## Apache Configuration

### Virtual Host Example

```apache
<VirtualHost *:80>
    ServerName pecosrivertraders.com
    ServerAlias www.pecosrivertraders.com
    DocumentRoot /var/www/html/pecosriver

    <Directory /var/www/html/pecosriver>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/pecosriver-error.log
    CustomLog ${APACHE_LOG_DIR}/pecosriver-access.log combined
</VirtualHost>
```

### Enable Required Modules

```bash
sudo a2enmod rewrite
sudo a2enmod ssl
sudo systemctl restart apache2
```

## .htaccess Security

Create/update `.htaccess` in root:

```apache
# Disable directory browsing
Options -Indexes

# Prevent access to sensitive files
<FilesMatch "\.(htaccess|htpasswd|ini|log|sql|md)$">
    Require all denied
</FilesMatch>

# Prevent access to config directory
<Directory "/var/www/html/pecosriver/config">
    Require all denied
</Directory>

# PHP security settings
php_flag display_errors Off
php_flag log_errors On

# Force HTTPS
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# Custom error pages
ErrorDocument 404 /404.php
ErrorDocument 500 /500.php
```

## Post-Deployment Tasks

### 1. Verify Installation

- [ ] Visit homepage: `https://yourdomain.com`
- [ ] Check all navigation links
- [ ] Test product pages
- [ ] Verify images load
- [ ] Test shopping cart
- [ ] Check admin panel
- [ ] Review error logs

### 2. Test Database Connection

Create temporary `test-db.php`:
```php
<?php
require_once 'config/database.php';
try {
    $dbConnect = getDbConnection();
    echo "Database connected successfully!";
} catch (Exception $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
```

Access via browser, then **DELETE** immediately.

### 3. Set Up Monitoring

**Error Monitoring**:
- Monitor error logs daily
- Set up log rotation
- Configure email alerts for critical errors

**Uptime Monitoring**:
- Use services like UptimeRobot
- Monitor website availability
- Alert on downtime

**Performance Monitoring**:
- Check page load times
- Monitor database query performance
- Review server resource usage

### 4. Backup Setup

**Database Backups**:
```bash
# Create backup script
nano /root/backup-db.sh

#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
mysqldump -u prt_prod -p'password' pecosriver > /backups/pecosriver_$DATE.sql
# Keep only last 30 days
find /backups -name "pecosriver_*.sql" -mtime +30 -delete

# Make executable
chmod +x /root/backup-db.sh

# Add to cron (daily at 2 AM)
crontab -e
0 2 * * * /root/backup-db.sh
```

**File Backups**:
```bash
# Backup entire site
tar -czf /backups/pecosriver_files_$(date +%Y%m%d).tar.gz /var/www/html/pecosriver
```

## Performance Optimization

### 1. Enable Gzip Compression

Add to `.htaccess`:
```apache
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript
</IfModule>
```

### 2. Browser Caching

```apache
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpg "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/gif "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
</IfModule>
```

### 3. PHP OpCache

Enable in `php.ini`:
```ini
opcache.enable=1
opcache.memory_consumption=128
opcache.max_accelerated_files=10000
opcache.revalidate_freq=60
```

### 4. Database Optimization

```sql
-- Optimize tables
OPTIMIZE TABLE Products, Categories, Events;

-- Add indexes
CREATE INDEX idx_category ON Products(CategoryID);
CREATE INDEX idx_active ON Products(Active);
```

## Maintenance

### Regular Tasks

**Daily**:
- Check error logs
- Monitor site performance
- Review backup status

**Weekly**:
- Test backups (restore to test environment)
- Review security logs
- Check for PHP/MySQL updates

**Monthly**:
- Update dependencies
- Review and optimize database
- Security audit
- Performance review

### Update Deployment

When deploying updates:

1. **Backup current site**:
```bash
tar -czf backup_before_update_$(date +%Y%m%d).tar.gz /var/www/html/pecosriver
```

2. **Backup database**:
```bash
mysqldump -u prt_prod -p pecosriver > backup_before_update.sql
```

3. **Deploy update** (Git method):
```bash
cd /var/www/html/pecosriver
git pull origin production
```

4. **Clear cache** if applicable
5. **Test site thoroughly**
6. **Monitor error logs** for issues

## Troubleshooting

### Site Not Loading
- Check Apache status: `systemctl status apache2`
- Review error logs: `/var/log/apache2/error.log`
- Verify DNS settings
- Check SSL certificate

### Database Connection Errors
- Verify MySQL is running: `systemctl status mysql`
- Check credentials in `config/database.php`
- Test connection from command line
- Review MySQL error log

### Permission Errors
```bash
# Reset permissions
sudo chown -R www-data:www-data /var/www/html/pecosriver
sudo find /var/www/html/pecosriver -type d -exec chmod 755 {} \;
sudo find /var/www/html/pecosriver -type f -exec chmod 644 {} \;
```

### Images Not Displaying
- Check file permissions
- Verify image paths in database
- Check .htaccess rules
- Review Apache error log

## Rollback Procedure

If deployment fails:

1. **Stop Apache**:
```bash
sudo systemctl stop apache2
```

2. **Restore files**:
```bash
cd /var/www/html
rm -rf pecosriver
tar -xzf backup_before_update_YYYYMMDD.tar.gz
```

3. **Restore database**:
```bash
mysql -u prt_prod -p pecosriver < backup_before_update.sql
```

4. **Restart Apache**:
```bash
sudo systemctl start apache2
```

5. **Verify site is working**

## Support Contacts

- **Hosting Support**: [Provider contact]
- **Database Admin**: [DBA contact]
- **Development Team**: [Dev team contact]

## Additional Resources

- [Apache Documentation](https://httpd.apache.org/docs/)
- [PHP Production Best Practices](https://www.php.net/manual/en/security.php)
- [MySQL Performance Tuning](https://dev.mysql.com/doc/refman/8.0/en/optimization.html)
