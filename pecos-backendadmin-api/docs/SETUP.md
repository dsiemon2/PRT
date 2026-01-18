# Backend API - Setup Guide

Last Updated: January 7, 2026

## 📋 Prerequisites

- PHP >= 8.0
- MySQL
- Apache with mod_rewrite
- cURL extension
- JSON extension
- PDO MySQL extension

## 🔧 Installation

### 1. Configure Database

Edit `config/database.php`:

```php
<?php
$host = "localhost";
$dbname = "pecos_db";
$username = "root";
$password = "";

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database connection failed'
    ]);
    exit;
}
?>
```

### 2. Configure Apache Virtual Host (XAMPP)

Edit `httpd-vhosts.conf`:

```apache
Listen 8300

<VirtualHost *:8300>
    ServerAdmin admin@localhost
    DocumentRoot "C:/xampp/htdocs/pecos-backendadmin-api/public"
    ServerName localhost

    <Directory "C:/xampp/htdocs/pecos-backendadmin-api/public">
        Options Indexes FollowSymLinks MultiViews
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog "logs/api-error.log"
    CustomLog "logs/api-access.log" common
</VirtualHost>
```

> **Note:** For Docker setup, the API runs on port 8300 automatically via nginx.

### 3. Configure CORS

Edit `middleware/cors.php`:

```php
<?php
$allowedOrigins = [
    'http://localhost:8300',
    'http://localhost:8301'
];

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

if (in_array($origin, $allowedOrigins)) {
    header("Access-Control-Allow-Origin: $origin");
}

header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-API-Key');
header('Access-Control-Allow-Credentials: true');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}
?>
```

### 4. Set Up .htaccess

Ensure `public/.htaccess` exists:

```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^api/(.*)$ api/$1 [L]

Header always set Access-Control-Allow-Origin "*"
Header always set Access-Control-Allow-Methods "GET, POST, PUT, DELETE, OPTIONS"
Header always set Access-Control-Allow-Headers "Content-Type, Authorization, X-API-Key"
```

### 5. Start Server

**Option A: Apache** (recommended for production)
```bash
# Restart Apache via XAMPP Control Panel
```

**Option B: PHP Built-in Server** (development only)
```bash
cd public
php -S localhost:8300
```

### 6. Test Installation

```bash
# Health check
curl http://localhost:8300/api/v1/health

# Expected response:
# {"success":true,"message":"API is running","version":"1.0"}

# Test products endpoint
curl http://localhost:8300/api/v1/products?limit=5
```

## 🔍 Troubleshooting

### Port Already in Use
```bash
# Find process using port 8300
netstat -ano | findstr :8300

# Kill process
taskkill /PID <process_id> /F

# Or use different port
php -S localhost:8400 -t public/
```

### Database Connection Fails
1. Verify MySQL is running
2. Check credentials in config/database.php
3. Ensure database exists:
```bash
mysql -u root -e "CREATE DATABASE IF NOT EXISTS pecos_db;"
```

### CORS Errors
1. Check browser console for specific error
2. Verify origin is in allowed list
3. Check preflight OPTIONS requests
4. Ensure headers are set correctly

### 404 on All Endpoints
1. Enable mod_rewrite in Apache:
```apache
LoadModule rewrite_module modules/mod_rewrite.so
```
2. Verify .htaccess exists in public/
3. Check AllowOverride is set to All

### 500 Internal Server Error
1. Enable error reporting:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```
2. Check PHP error log
3. Check Apache error log:
```bash
tail -f C:/xampp/apache/logs/error.log
```

## 🚀 Performance Optimization

### Enable OPcache
In `php.ini`:
```ini
opcache.enable=1
opcache.memory_consumption=128
opcache.max_accelerated_files=10000
```

### Database Indexes
```sql
CREATE INDEX idx_category ON products3(CategoryCode);
CREATE INDEX idx_status ON orders3(status);
CREATE INDEX idx_customer ON orders3(customer_id);
```

## 📊 Monitoring

### Enable Logging
Create `logs/` directory:
```bash
mkdir logs
chmod 775 logs
```

### API Request Logging
Add to endpoints:
```php
file_put_contents(
    'logs/api_requests.log',
    date('Y-m-d H:i:s') . " - " . $_SERVER['REQUEST_URI'] . PHP_EOL,
    FILE_APPEND
);
```

---

For endpoint documentation, see [API_ENDPOINTS.md](API_ENDPOINTS.md).