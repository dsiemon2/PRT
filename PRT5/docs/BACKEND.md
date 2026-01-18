# Backend Architecture Guide - Pecos River Traders

Comprehensive guide for building a robust backend system for the Pecos River Traders e-commerce platform.

## Table of Contents

1. [Current Architecture](#current-architecture)
2. [Recommended Backend Architecture](#recommended-backend-architecture)
3. [API Design](#api-design)
4. [Authentication & Authorization](#authentication--authorization)
5. [Database Architecture](#database-architecture)
6. [Payment Integration](#payment-integration)
7. [Email System](#email-system)
8. [File Storage](#file-storage)
9. [Caching Strategy](#caching-strategy)
10. [Security](#security)
11. [Scalability](#scalability)
12. [DevOps & Deployment](#devops--deployment)

---

## Current Architecture

### Technology Stack
- **Language**: PHP 7.4+
- **Database**: MySQL/MariaDB
- **Server**: Apache (XAMPP)
- **Frontend**: Bootstrap 5 + Vanilla JavaScript
- **Session**: PHP Sessions

### Current Structure
```
PRT2/
├── *.php                    # Mixed view/logic pages
├── config/
│   └── database.php        # Database connection
├── includes/
│   ├── header.php          # Header template
│   ├── footer.php          # Footer template
│   └── common.php          # Shared functions
├── admin/                  # Admin pages
└── assets/                 # Static files
```

### Limitations
- No separation of concerns (MVC)
- No API for frontend communication
- Limited error handling
- No automated testing
- Difficult to scale
- Security concerns (direct database access in views)

---

## Recommended Backend Architecture

### Modern Architecture: API-First Approach

```
Backend/
├── api/                    # RESTful API endpoints
│   ├── v1/
│   │   ├── auth/          # Authentication endpoints
│   │   ├── products/      # Product CRUD
│   │   ├── cart/          # Shopping cart
│   │   ├── orders/        # Order management
│   │   ├── users/         # User management
│   │   └── wishlist/      # Wishlist operations
├── src/
│   ├── Controllers/       # Request handling
│   ├── Models/            # Data models
│   ├── Services/          # Business logic
│   ├── Repositories/      # Database access layer
│   ├── Middleware/        # Request/response processing
│   ├── Validators/        # Input validation
│   └── Utilities/         # Helper functions
├── config/
│   ├── database.php       # Database configuration
│   ├── app.php            # Application settings
│   ├── mail.php           # Email configuration
│   └── payment.php        # Payment gateway config
├── storage/
│   ├── logs/              # Application logs
│   ├── cache/             # Cache files
│   └── uploads/           # User uploads
├── tests/
│   ├── Unit/              # Unit tests
│   └── Integration/       # Integration tests
├── vendor/                # Composer dependencies
├── .env                   # Environment variables
└── composer.json          # Dependencies
```

### MVC Pattern Implementation

**Model** (Data Layer):
```php
// src/Models/Product.php
<?php
namespace App\Models;

class Product {
    private $id;
    private $itemNumber;
    private $shortDescription;
    private $longDescription;
    private $unitPrice;
    private $categoryCode;
    private $image;

    // Getters and setters
    public function getId(): int {
        return $this->id;
    }

    public function setId(int $id): void {
        $this->id = $id;
    }

    // ... more getters/setters
}
?>
```

**Repository** (Database Access):
```php
// src/Repositories/ProductRepository.php
<?php
namespace App\Repositories;

use App\Models\Product;
use PDO;

class ProductRepository {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function findById(int $id): ?Product {
        $stmt = $this->db->prepare("SELECT * FROM products3 WHERE ID = :id");
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        return $this->hydrate($data);
    }

    public function findAll(int $limit = 20, int $offset = 0): array {
        $stmt = $this->db->prepare("SELECT * FROM products3 LIMIT :limit OFFSET :offset");
        $stmt->execute(['limit' => $limit, 'offset' => $offset]);

        $products = [];
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $products[] = $this->hydrate($data);
        }

        return $products;
    }

    public function findByCategory(int $categoryId): array {
        $stmt = $this->db->prepare("SELECT * FROM products3 WHERE CategoryCode = :catId");
        $stmt->execute(['catId' => $categoryId]);

        $products = [];
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $products[] = $this->hydrate($data);
        }

        return $products;
    }

    public function save(Product $product): bool {
        if ($product->getId()) {
            return $this->update($product);
        }
        return $this->create($product);
    }

    private function create(Product $product): bool {
        $stmt = $this->db->prepare("
            INSERT INTO products3 (ItemNumber, ShortDescription, LngDescription, UnitPrice, CategoryCode, Image)
            VALUES (:itemNumber, :shortDesc, :longDesc, :price, :category, :image)
        ");

        return $stmt->execute([
            'itemNumber' => $product->getItemNumber(),
            'shortDesc' => $product->getShortDescription(),
            'longDesc' => $product->getLongDescription(),
            'price' => $product->getUnitPrice(),
            'category' => $product->getCategoryCode(),
            'image' => $product->getImage()
        ]);
    }

    private function update(Product $product): bool {
        $stmt = $this->db->prepare("
            UPDATE products3
            SET ItemNumber = :itemNumber,
                ShortDescription = :shortDesc,
                LngDescription = :longDesc,
                UnitPrice = :price,
                CategoryCode = :category,
                Image = :image
            WHERE ID = :id
        ");

        return $stmt->execute([
            'id' => $product->getId(),
            'itemNumber' => $product->getItemNumber(),
            'shortDesc' => $product->getShortDescription(),
            'longDesc' => $product->getLongDescription(),
            'price' => $product->getUnitPrice(),
            'category' => $product->getCategoryCode(),
            'image' => $product->getImage()
        ]);
    }

    private function hydrate(array $data): Product {
        $product = new Product();
        $product->setId($data['ID']);
        $product->setItemNumber($data['ItemNumber']);
        $product->setShortDescription($data['ShortDescription']);
        $product->setLongDescription($data['LngDescription']);
        $product->setUnitPrice($data['UnitPrice']);
        $product->setCategoryCode($data['CategoryCode']);
        $product->setImage($data['Image']);
        return $product;
    }
}
?>
```

**Service** (Business Logic):
```php
// src/Services/ProductService.php
<?php
namespace App\Services;

use App\Repositories\ProductRepository;
use App\Models\Product;

class ProductService {
    private $productRepository;

    public function __construct(ProductRepository $productRepository) {
        $this->productRepository = $productRepository;
    }

    public function getProduct(int $id): ?Product {
        return $this->productRepository->findById($id);
    }

    public function getProductsByCategory(int $categoryId): array {
        return $this->productRepository->findByCategory($categoryId);
    }

    public function searchProducts(string $query): array {
        // Implement search logic
        return $this->productRepository->search($query);
    }

    public function getFeaturedProducts(int $limit = 10): array {
        return $this->productRepository->findFeatured($limit);
    }

    public function isInStock(int $productId): bool {
        $product = $this->getProduct($productId);
        return $product && $product->getQuantity() > 0;
    }

    public function updateStock(int $productId, int $quantity): bool {
        $product = $this->getProduct($productId);
        if (!$product) {
            return false;
        }

        $product->setQuantity($product->getQuantity() - $quantity);
        return $this->productRepository->save($product);
    }
}
?>
```

**Controller** (Request Handling):
```php
// src/Controllers/ProductController.php
<?php
namespace App\Controllers;

use App\Services\ProductService;

class ProductController {
    private $productService;

    public function __construct(ProductService $productService) {
        $this->productService = $productService;
    }

    public function index() {
        // GET /api/v1/products
        $page = $_GET['page'] ?? 1;
        $limit = $_GET['limit'] ?? 20;
        $offset = ($page - 1) * $limit;

        $products = $this->productService->getAllProducts($limit, $offset);

        return $this->jsonResponse([
            'success' => true,
            'data' => $products,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $this->productService->getTotalCount()
            ]
        ]);
    }

    public function show(int $id) {
        // GET /api/v1/products/{id}
        $product = $this->productService->getProduct($id);

        if (!$product) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        return $this->jsonResponse([
            'success' => true,
            'data' => $product
        ]);
    }

    public function store() {
        // POST /api/v1/products
        $data = json_decode(file_get_contents('php://input'), true);

        // Validate input
        $errors = $this->validate($data, [
            'itemNumber' => 'required|string',
            'shortDescription' => 'required|string',
            'unitPrice' => 'required|numeric',
            'categoryCode' => 'required|integer'
        ]);

        if (!empty($errors)) {
            return $this->jsonResponse([
                'success' => false,
                'errors' => $errors
            ], 422);
        }

        $product = $this->productService->createProduct($data);

        return $this->jsonResponse([
            'success' => true,
            'data' => $product
        ], 201);
    }

    public function update(int $id) {
        // PUT /api/v1/products/{id}
        $data = json_decode(file_get_contents('php://input'), true);

        $product = $this->productService->updateProduct($id, $data);

        if (!$product) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        return $this->jsonResponse([
            'success' => true,
            'data' => $product
        ]);
    }

    public function destroy(int $id) {
        // DELETE /api/v1/products/{id}
        $success = $this->productService->deleteProduct($id);

        if (!$success) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        return $this->jsonResponse([
            'success' => true,
            'message' => 'Product deleted successfully'
        ]);
    }

    private function jsonResponse(array $data, int $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    private function validate(array $data, array $rules): array {
        // Simple validation logic
        $errors = [];
        foreach ($rules as $field => $rule) {
            $rulesParts = explode('|', $rule);
            foreach ($rulesParts as $rulePart) {
                // Implement validation rules
            }
        }
        return $errors;
    }
}
?>
```

---

## API Design

### RESTful API Endpoints

#### Authentication
```
POST   /api/v1/auth/register          # Register new user
POST   /api/v1/auth/login             # Login
POST   /api/v1/auth/logout            # Logout
POST   /api/v1/auth/refresh           # Refresh token
POST   /api/v1/auth/forgot-password   # Request password reset
POST   /api/v1/auth/reset-password    # Reset password
GET    /api/v1/auth/me                # Get current user
```

#### Products
```
GET    /api/v1/products               # List products
GET    /api/v1/products/{id}          # Get product details
POST   /api/v1/products               # Create product (admin)
PUT    /api/v1/products/{id}          # Update product (admin)
DELETE /api/v1/products/{id}          # Delete product (admin)
GET    /api/v1/products/search        # Search products
GET    /api/v1/products/featured      # Get featured products
```

#### Categories
```
GET    /api/v1/categories             # List categories
GET    /api/v1/categories/{id}        # Get category details
GET    /api/v1/categories/{id}/products # Get products in category
POST   /api/v1/categories             # Create category (admin)
PUT    /api/v1/categories/{id}        # Update category (admin)
DELETE /api/v1/categories/{id}        # Delete category (admin)
```

#### Cart
```
GET    /api/v1/cart                   # Get cart contents
POST   /api/v1/cart/items             # Add item to cart
PUT    /api/v1/cart/items/{id}        # Update cart item
DELETE /api/v1/cart/items/{id}        # Remove cart item
DELETE /api/v1/cart                   # Clear cart
```

#### Orders
```
GET    /api/v1/orders                 # List user orders
GET    /api/v1/orders/{id}            # Get order details
POST   /api/v1/orders                 # Create order (checkout)
PUT    /api/v1/orders/{id}/cancel     # Cancel order
GET    /api/v1/orders/{id}/track      # Track order
```

#### Wishlist
```
GET    /api/v1/wishlist               # Get wishlist
POST   /api/v1/wishlist               # Add to wishlist
DELETE /api/v1/wishlist/{productId}   # Remove from wishlist
POST   /api/v1/wishlist/toggle        # Toggle wishlist item
```

#### User Profile
```
GET    /api/v1/user/profile           # Get user profile
PUT    /api/v1/user/profile           # Update profile
GET    /api/v1/user/addresses         # List addresses
POST   /api/v1/user/addresses         # Add address
PUT    /api/v1/user/addresses/{id}    # Update address
DELETE /api/v1/user/addresses/{id}    # Delete address
GET    /api/v1/user/payment-methods   # List payment methods
POST   /api/v1/user/payment-methods   # Add payment method
DELETE /api/v1/user/payment-methods/{id} # Delete payment method
```

### API Response Format

**Success Response:**
```json
{
  "success": true,
  "data": {
    "id": 123,
    "name": "Product Name",
    "price": 99.99
  },
  "message": "Operation successful" // optional
}
```

**Error Response:**
```json
{
  "success": false,
  "message": "Error message",
  "errors": {
    "field1": ["Error detail 1"],
    "field2": ["Error detail 2"]
  }
}
```

**Paginated Response:**
```json
{
  "success": true,
  "data": [...],
  "pagination": {
    "current_page": 1,
    "per_page": 20,
    "total": 150,
    "total_pages": 8,
    "next_page": 2,
    "prev_page": null
  }
}
```

### API Versioning

Use URL versioning:
```
/api/v1/products
/api/v2/products
```

---

## Authentication & Authorization

### JWT (JSON Web Token) Implementation

**Generate Token:**
```php
// src/Services/AuthService.php
<?php
namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthService {
    private $secretKey;
    private $userRepository;

    public function __construct($secretKey, $userRepository) {
        $this->secretKey = $secretKey;
        $this->userRepository = $userRepository;
    }

    public function login(string $email, string $password): ?array {
        $user = $this->userRepository->findByEmail($email);

        if (!$user || !password_verify($password, $user->getPassword())) {
            return null;
        }

        $token = $this->generateToken($user);
        $refreshToken = $this->generateRefreshToken($user);

        // Update last login
        $user->setLastLogin(new \DateTime());
        $this->userRepository->save($user);

        return [
            'access_token' => $token,
            'refresh_token' => $refreshToken,
            'token_type' => 'Bearer',
            'expires_in' => 3600,
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'name' => $user->getFirstName() . ' ' . $user->getLastName()
            ]
        ];
    }

    public function register(array $data): ?array {
        // Validate email doesn't exist
        if ($this->userRepository->findByEmail($data['email'])) {
            throw new \Exception('Email already exists');
        }

        $user = new User();
        $user->setEmail($data['email']);
        $user->setPassword(password_hash($data['password'], PASSWORD_DEFAULT));
        $user->setFirstName($data['first_name']);
        $user->setLastName($data['last_name']);
        $user->setPhone($data['phone'] ?? null);

        $this->userRepository->save($user);

        return $this->login($data['email'], $data['password']);
    }

    private function generateToken($user): string {
        $payload = [
            'iss' => 'https://pecosrivertraders.com',
            'iat' => time(),
            'exp' => time() + 3600, // 1 hour
            'sub' => $user->getId(),
            'email' => $user->getEmail(),
            'role' => $user->getRole()
        ];

        return JWT::encode($payload, $this->secretKey, 'HS256');
    }

    private function generateRefreshToken($user): string {
        $payload = [
            'iss' => 'https://pecosrivertraders.com',
            'iat' => time(),
            'exp' => time() + 2592000, // 30 days
            'sub' => $user->getId(),
            'type' => 'refresh'
        ];

        return JWT::encode($payload, $this->secretKey, 'HS256');
    }

    public function validateToken(string $token): ?object {
        try {
            return JWT::decode($token, new Key($this->secretKey, 'HS256'));
        } catch (\Exception $e) {
            return null;
        }
    }
}
?>
```

**Authentication Middleware:**
```php
// src/Middleware/AuthMiddleware.php
<?php
namespace App\Middleware;

use App\Services\AuthService;

class AuthMiddleware {
    private $authService;

    public function __construct(AuthService $authService) {
        $this->authService = $authService;
    }

    public function handle() {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? '';

        if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $this->unauthorized('Missing or invalid authorization header');
        }

        $token = $matches[1];
        $payload = $this->authService->validateToken($token);

        if (!$payload) {
            $this->unauthorized('Invalid or expired token');
        }

        // Store user info in request context
        $_REQUEST['auth_user'] = $payload;
    }

    private function unauthorized(string $message) {
        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => $message
        ]);
        exit;
    }
}
?>
```

### OAuth 2.0 (Social Login)

**Google OAuth Implementation:**
```php
// src/Services/OAuthService.php
<?php
namespace App\Services;

class OAuthService {
    private $providers = [
        'google' => [
            'client_id' => 'GOOGLE_CLIENT_ID',
            'client_secret' => 'GOOGLE_CLIENT_SECRET',
            'redirect_uri' => 'https://pecosrivertraders.com/api/v1/auth/callback/google',
            'auth_url' => 'https://accounts.google.com/o/oauth2/v2/auth',
            'token_url' => 'https://oauth2.googleapis.com/token',
            'user_info_url' => 'https://www.googleapis.com/oauth2/v2/userinfo'
        ],
        'facebook' => [
            'client_id' => 'FACEBOOK_APP_ID',
            'client_secret' => 'FACEBOOK_APP_SECRET',
            'redirect_uri' => 'https://pecosrivertraders.com/api/v1/auth/callback/facebook',
            'auth_url' => 'https://www.facebook.com/v12.0/dialog/oauth',
            'token_url' => 'https://graph.facebook.com/v12.0/oauth/access_token',
            'user_info_url' => 'https://graph.facebook.com/me'
        ]
    ];

    public function getAuthorizationUrl(string $provider): string {
        $config = $this->providers[$provider];

        $state = bin2hex(random_bytes(16));
        $_SESSION['oauth_state'] = $state;

        $params = [
            'client_id' => $config['client_id'],
            'redirect_uri' => $config['redirect_uri'],
            'response_type' => 'code',
            'scope' => $this->getScope($provider),
            'state' => $state
        ];

        return $config['auth_url'] . '?' . http_build_query($params);
    }

    public function handleCallback(string $provider, string $code, string $state): array {
        // Verify state
        if ($state !== $_SESSION['oauth_state']) {
            throw new \Exception('Invalid state parameter');
        }

        $config = $this->providers[$provider];

        // Exchange code for access token
        $tokenData = $this->exchangeCodeForToken($provider, $code);

        // Get user info
        $userInfo = $this->getUserInfo($provider, $tokenData['access_token']);

        // Create or update user
        $user = $this->findOrCreateUser($provider, $userInfo, $tokenData['access_token']);

        return $user;
    }

    private function exchangeCodeForToken(string $provider, string $code): array {
        $config = $this->providers[$provider];

        $data = [
            'client_id' => $config['client_id'],
            'client_secret' => $config['client_secret'],
            'code' => $code,
            'grant_type' => 'authorization_code',
            'redirect_uri' => $config['redirect_uri']
        ];

        $ch = curl_init($config['token_url']);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    private function getUserInfo(string $provider, string $accessToken): array {
        $config = $this->providers[$provider];

        $ch = curl_init($config['user_info_url']);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $accessToken
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    private function getScope(string $provider): string {
        $scopes = [
            'google' => 'https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile',
            'facebook' => 'email,public_profile'
        ];

        return $scopes[$provider];
    }
}
?>
```

---

## Database Architecture

### Connection Pool

**PDO Connection with Pooling:**
```php
// src/Database/ConnectionPool.php
<?php
namespace App\Database;

class ConnectionPool {
    private static $instance = null;
    private $connections = [];
    private $config;
    private $maxConnections = 10;

    private function __construct(array $config) {
        $this->config = $config;
    }

    public static function getInstance(array $config): self {
        if (self::$instance === null) {
            self::$instance = new self($config);
        }
        return self::$instance;
    }

    public function getConnection(): \PDO {
        // Return available connection or create new one
        foreach ($this->connections as $conn) {
            if (!$conn['in_use']) {
                $conn['in_use'] = true;
                return $conn['connection'];
            }
        }

        if (count($this->connections) < $this->maxConnections) {
            return $this->createConnection();
        }

        // Wait for available connection
        throw new \Exception('Connection pool exhausted');
    }

    public function releaseConnection(\PDO $connection): void {
        foreach ($this->connections as &$conn) {
            if ($conn['connection'] === $connection) {
                $conn['in_use'] = false;
                break;
            }
        }
    }

    private function createConnection(): \PDO {
        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=utf8mb4',
            $this->config['host'],
            $this->config['database']
        );

        $options = [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES => false,
            \PDO::ATTR_PERSISTENT => true
        ];

        $pdo = new \PDO($dsn, $this->config['username'], $this->config['password'], $options);

        $this->connections[] = [
            'connection' => $pdo,
            'in_use' => true
        ];

        return $pdo;
    }
}
?>
```

### Query Builder

**Simple Query Builder:**
```php
// src/Database/QueryBuilder.php
<?php
namespace App\Database;

class QueryBuilder {
    private $pdo;
    private $table;
    private $select = ['*'];
    private $where = [];
    private $orderBy = [];
    private $limit;
    private $offset;

    public function __construct(\PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function table(string $table): self {
        $this->table = $table;
        return $this;
    }

    public function select(array $columns): self {
        $this->select = $columns;
        return $this;
    }

    public function where(string $column, string $operator, $value): self {
        $this->where[] = [
            'column' => $column,
            'operator' => $operator,
            'value' => $value,
            'type' => 'AND'
        ];
        return $this;
    }

    public function orWhere(string $column, string $operator, $value): self {
        $this->where[] = [
            'column' => $column,
            'operator' => $operator,
            'value' => $value,
            'type' => 'OR'
        ];
        return $this;
    }

    public function orderBy(string $column, string $direction = 'ASC'): self {
        $this->orderBy[] = [$column, $direction];
        return $this;
    }

    public function limit(int $limit): self {
        $this->limit = $limit;
        return $this;
    }

    public function offset(int $offset): self {
        $this->offset = $offset;
        return $this;
    }

    public function get(): array {
        $sql = $this->buildSelectQuery();
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($this->getBindValues());
        return $stmt->fetchAll();
    }

    public function first(): ?array {
        $this->limit(1);
        $results = $this->get();
        return $results[0] ?? null;
    }

    public function count(): int {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}";
        $sql .= $this->buildWhereClause();

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($this->getBindValues());
        $result = $stmt->fetch();
        return (int) $result['count'];
    }

    public function insert(array $data): int {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array_values($data));

        return (int) $this->pdo->lastInsertId();
    }

    public function update(array $data): int {
        $setParts = [];
        foreach (array_keys($data) as $column) {
            $setParts[] = "{$column} = ?";
        }
        $setClause = implode(', ', $setParts);

        $sql = "UPDATE {$this->table} SET {$setClause}";
        $sql .= $this->buildWhereClause();

        $stmt = $this->pdo->prepare($sql);
        $values = array_merge(array_values($data), $this->getBindValues());
        $stmt->execute($values);

        return $stmt->rowCount();
    }

    public function delete(): int {
        $sql = "DELETE FROM {$this->table}";
        $sql .= $this->buildWhereClause();

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($this->getBindValues());

        return $stmt->rowCount();
    }

    private function buildSelectQuery(): string {
        $columns = implode(', ', $this->select);
        $sql = "SELECT {$columns} FROM {$this->table}";

        $sql .= $this->buildWhereClause();
        $sql .= $this->buildOrderByClause();
        $sql .= $this->buildLimitClause();

        return $sql;
    }

    private function buildWhereClause(): string {
        if (empty($this->where)) {
            return '';
        }

        $clauses = [];
        foreach ($this->where as $index => $condition) {
            $prefix = $index === 0 ? 'WHERE' : $condition['type'];
            $clauses[] = "{$prefix} {$condition['column']} {$condition['operator']} ?";
        }

        return ' ' . implode(' ', $clauses);
    }

    private function buildOrderByClause(): string {
        if (empty($this->orderBy)) {
            return '';
        }

        $parts = [];
        foreach ($this->orderBy as list($column, $direction)) {
            $parts[] = "{$column} {$direction}";
        }

        return ' ORDER BY ' . implode(', ', $parts);
    }

    private function buildLimitClause(): string {
        $clause = '';
        if ($this->limit) {
            $clause .= " LIMIT {$this->limit}";
        }
        if ($this->offset) {
            $clause .= " OFFSET {$this->offset}";
        }
        return $clause;
    }

    private function getBindValues(): array {
        $values = [];
        foreach ($this->where as $condition) {
            $values[] = $condition['value'];
        }
        return $values;
    }
}
?>
```

**Usage:**
```php
$products = $queryBuilder
    ->table('products3')
    ->select(['ID', 'ShortDescription', 'UnitPrice'])
    ->where('CategoryCode', '=', 5)
    ->where('UnitPrice', '>', 50)
    ->orderBy('UnitPrice', 'DESC')
    ->limit(20)
    ->offset(0)
    ->get();
```

---

## Payment Integration

### Stripe Integration

**Install Stripe SDK:**
```bash
composer require stripe/stripe-php
```

**Payment Service:**
```php
// src/Services/PaymentService.php
<?php
namespace App\Services;

use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Customer;

class PaymentService {
    private $stripeKey;

    public function __construct(string $stripeKey) {
        $this->stripeKey = $stripeKey;
        Stripe::setApiKey($stripeKey);
    }

    public function createPaymentIntent(float $amount, string $currency = 'usd', array $metadata = []): array {
        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => (int) ($amount * 100), // Convert to cents
                'currency' => $currency,
                'metadata' => $metadata,
                'automatic_payment_methods' => [
                    'enabled' => true
                ]
            ]);

            return [
                'success' => true,
                'client_secret' => $paymentIntent->client_secret,
                'payment_intent_id' => $paymentIntent->id
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function createCustomer(string $email, array $data = []): ?string {
        try {
            $customer = Customer::create([
                'email' => $email,
                'name' => $data['name'] ?? null,
                'phone' => $data['phone'] ?? null,
                'address' => [
                    'line1' => $data['address_line1'] ?? null,
                    'line2' => $data['address_line2'] ?? null,
                    'city' => $data['city'] ?? null,
                    'state' => $data['state'] ?? null,
                    'postal_code' => $data['postal_code'] ?? null,
                    'country' => $data['country'] ?? 'US'
                ]
            ]);

            return $customer->id;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function confirmPayment(string $paymentIntentId): bool {
        try {
            $paymentIntent = PaymentIntent::retrieve($paymentIntentId);
            return $paymentIntent->status === 'succeeded';
        } catch (\Exception $e) {
            return false;
        }
    }

    public function refund(string $paymentIntentId, float $amount = null): array {
        try {
            $params = ['payment_intent' => $paymentIntentId];
            if ($amount) {
                $params['amount'] = (int) ($amount * 100);
            }

            $refund = \Stripe\Refund::create($params);

            return [
                'success' => true,
                'refund_id' => $refund->id,
                'status' => $refund->status
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}
?>
```

**Checkout Controller:**
```php
// src/Controllers/CheckoutController.php
<?php
namespace App\Controllers;

use App\Services\PaymentService;
use App\Services\OrderService;

class CheckoutController {
    private $paymentService;
    private $orderService;

    public function __construct(PaymentService $paymentService, OrderService $orderService) {
        $this->paymentService = $paymentService;
        $this->orderService = $orderService;
    }

    public function createPaymentIntent() {
        // POST /api/v1/checkout/payment-intent
        $data = json_decode(file_get_contents('php://input'), true);

        $userId = $_REQUEST['auth_user']->sub;
        $cart = $this->getCart($userId);
        $total = $this->calculateTotal($cart);

        $result = $this->paymentService->createPaymentIntent($total, 'usd', [
            'user_id' => $userId,
            'cart_items' => count($cart)
        ]);

        return $this->jsonResponse($result);
    }

    public function processOrder() {
        // POST /api/v1/checkout/process
        $data = json_decode(file_get_contents('php://input'), true);

        $userId = $_REQUEST['auth_user']->sub;

        // Verify payment
        $paymentConfirmed = $this->paymentService->confirmPayment($data['payment_intent_id']);

        if (!$paymentConfirmed) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Payment verification failed'
            ], 400);
        }

        // Create order
        $order = $this->orderService->createOrder($userId, [
            'payment_intent_id' => $data['payment_intent_id'],
            'shipping_address_id' => $data['shipping_address_id'],
            'billing_address_id' => $data['billing_address_id'],
            'shipping_method' => $data['shipping_method']
        ]);

        // Clear cart
        $this->clearCart($userId);

        // Send confirmation email
        $this->sendOrderConfirmation($order);

        return $this->jsonResponse([
            'success' => true,
            'order_id' => $order->getId(),
            'order_number' => $order->getOrderNumber()
        ]);
    }

    private function jsonResponse(array $data, int $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
?>
```

---

## Email System

### PHPMailer Setup

**Install PHPMailer:**
```bash
composer require phpmailer/phpmailer
```

**Email Service:**
```php
// src/Services/EmailService.php
<?php
namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailService {
    private $mailer;
    private $config;

    public function __construct(array $config) {
        $this->config = $config;
        $this->mailer = new PHPMailer(true);
        $this->configure();
    }

    private function configure(): void {
        $this->mailer->isSMTP();
        $this->mailer->Host = $this->config['host'];
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = $this->config['username'];
        $this->mailer->Password = $this->config['password'];
        $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mailer->Port = $this->config['port'];
        $this->mailer->setFrom($this->config['from_address'], $this->config['from_name']);
    }

    public function sendOrderConfirmation($order, $user): bool {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($user->getEmail(), $user->getFirstName() . ' ' . $user->getLastName());

            $this->mailer->isHTML(true);
            $this->mailer->Subject = 'Order Confirmation - ' . $order->getOrderNumber();
            $this->mailer->Body = $this->renderOrderConfirmationEmail($order, $user);
            $this->mailer->AltBody = $this->renderOrderConfirmationText($order, $user);

            $this->mailer->send();
            return true;
        } catch (Exception $e) {
            error_log('Email send failed: ' . $this->mailer->ErrorInfo);
            return false;
        }
    }

    public function sendPasswordReset($user, $resetToken): bool {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($user->getEmail());

            $resetLink = "https://pecosrivertraders.com/reset-password?token=" . $resetToken;

            $this->mailer->isHTML(true);
            $this->mailer->Subject = 'Password Reset Request';
            $this->mailer->Body = $this->renderPasswordResetEmail($user, $resetLink);

            $this->mailer->send();
            return true;
        } catch (Exception $e) {
            error_log('Email send failed: ' . $this->mailer->ErrorInfo);
            return false;
        }
    }

    public function sendShippingNotification($order, $user): bool {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($user->getEmail());

            $trackingLink = $this->getTrackingLink($order);

            $this->mailer->isHTML(true);
            $this->mailer->Subject = 'Your Order Has Shipped - ' . $order->getOrderNumber();
            $this->mailer->Body = $this->renderShippingEmail($order, $user, $trackingLink);

            $this->mailer->send();
            return true;
        } catch (Exception $e) {
            error_log('Email send failed: ' . $this->mailer->ErrorInfo);
            return false;
        }
    }

    private function renderOrderConfirmationEmail($order, $user): string {
        // Load template and populate with order data
        ob_start();
        include __DIR__ . '/../../templates/emails/order-confirmation.php';
        return ob_get_clean();
    }

    private function getTrackingLink($order): string {
        $carrier = $order->getShippingMethod();
        $trackingNumber = $order->getTrackingNumber();

        $trackingUrls = [
            'usps' => "https://tools.usps.com/go/TrackConfirmAction?tLabels={$trackingNumber}",
            'ups' => "https://www.ups.com/track?tracknum={$trackingNumber}",
            'fedex' => "https://www.fedex.com/fedextrack/?trknbr={$trackingNumber}"
        ];

        return $trackingUrls[strtolower($carrier)] ?? '#';
    }
}
?>
```

**Email Template:**
```php
// templates/emails/order-confirmation.php
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #990000; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f5f5f5; }
        .order-details { background: white; padding: 15px; margin: 15px 0; }
        .footer { text-align: center; padding: 20px; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Thank You for Your Order!</h1>
        </div>
        <div class="content">
            <p>Hi <?php echo htmlspecialchars($user->getFirstName()); ?>,</p>
            <p>Your order has been confirmed and will be shipped soon.</p>

            <div class="order-details">
                <h2>Order Details</h2>
                <p><strong>Order Number:</strong> <?php echo htmlspecialchars($order->getOrderNumber()); ?></p>
                <p><strong>Order Date:</strong> <?php echo $order->getOrderedAt()->format('F j, Y'); ?></p>
                <p><strong>Total:</strong> $<?php echo number_format($order->getTotalAmount(), 2); ?></p>

                <h3>Items:</h3>
                <?php foreach ($order->getItems() as $item): ?>
                    <p><?php echo htmlspecialchars($item->getProductName()); ?> -
                       Qty: <?php echo $item->getQuantity(); ?> -
                       $<?php echo number_format($item->getTotalPrice(), 2); ?></p>
                <?php endforeach; ?>
            </div>

            <p>You can track your order status at:
               <a href="https://pecosrivertraders.com/orders/<?php echo $order->getId(); ?>">View Order</a>
            </p>
        </div>
        <div class="footer">
            <p>Pecos River Traders<br>
               717-914-8124<br>
               contact@pecosrivertraders.com</p>
        </div>
    </div>
</body>
</html>
```

---

## File Storage

### Local Storage

**File Upload Service:**
```php
// src/Services/FileStorageService.php
<?php
namespace App\Services;

class FileStorageService {
    private $uploadPath;
    private $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    private $maxFileSize = 5242880; // 5MB

    public function __construct(string $uploadPath) {
        $this->uploadPath = rtrim($uploadPath, '/');
    }

    public function uploadProductImage(array $file, string $productId): ?string {
        // Validate file
        if (!$this->validateFile($file)) {
            return null;
        }

        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = "product_{$productId}_" . uniqid() . ".{$extension}";
        $destination = $this->uploadPath . '/products/' . $filename;

        // Create directory if doesn't exist
        $directory = dirname($destination);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            // Optimize image
            $this->optimizeImage($destination);

            return 'images/products/' . $filename;
        }

        return null;
    }

    public function deleteFile(string $path): bool {
        $fullPath = $this->uploadPath . '/' . $path;

        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }

        return false;
    }

    private function validateFile(array $file): bool {
        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        // Check file size
        if ($file['size'] > $this->maxFileSize) {
            return false;
        }

        // Check MIME type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $this->allowedTypes)) {
            return false;
        }

        return true;
    }

    private function optimizeImage(string $path): void {
        $info = getimagesize($path);
        $mime = $info['mime'];

        switch ($mime) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($path);
                imagejpeg($image, $path, 85); // 85% quality
                break;
            case 'image/png':
                $image = imagecreatefrompng($path);
                imagepng($image, $path, 8); // Compression level 8
                break;
        }

        if (isset($image)) {
            imagedestroy($image);
        }
    }

    public function resizeImage(string $path, int $maxWidth, int $maxHeight): bool {
        list($width, $height, $type) = getimagesize($path);

        $ratio = min($maxWidth / $width, $maxHeight / $height);
        $newWidth = (int) ($width * $ratio);
        $newHeight = (int) ($height * $ratio);

        $newImage = imagecreatetruecolor($newWidth, $newHeight);

        switch ($type) {
            case IMAGETYPE_JPEG:
                $source = imagecreatefromjpeg($path);
                break;
            case IMAGETYPE_PNG:
                $source = imagecreatefrompng($path);
                imagealphablending($newImage, false);
                imagesavealpha($newImage, true);
                break;
            case IMAGETYPE_GIF:
                $source = imagecreatefromgif($path);
                break;
            default:
                return false;
        }

        imagecopyresampled($newImage, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        switch ($type) {
            case IMAGETYPE_JPEG:
                imagejpeg($newImage, $path, 90);
                break;
            case IMAGETYPE_PNG:
                imagepng($newImage, $path, 9);
                break;
            case IMAGETYPE_GIF:
                imagegif($newImage, $path);
                break;
        }

        imagedestroy($source);
        imagedestroy($newImage);

        return true;
    }
}
?>
```

### Cloud Storage (AWS S3)

**Install AWS SDK:**
```bash
composer require aws/aws-sdk-php
```

**S3 Storage Service:**
```php
// src/Services/S3StorageService.php
<?php
namespace App\Services;

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

class S3StorageService {
    private $s3Client;
    private $bucket;

    public function __construct(array $config) {
        $this->s3Client = new S3Client([
            'version' => 'latest',
            'region' => $config['region'],
            'credentials' => [
                'key' => $config['key'],
                'secret' => $config['secret']
            ]
        ]);

        $this->bucket = $config['bucket'];
    }

    public function upload(string $filePath, string $key, array $options = []): ?string {
        try {
            $result = $this->s3Client->putObject([
                'Bucket' => $this->bucket,
                'Key' => $key,
                'SourceFile' => $filePath,
                'ACL' => 'public-read',
                'ContentType' => $options['content_type'] ?? 'application/octet-stream'
            ]);

            return $result['ObjectURL'];
        } catch (AwsException $e) {
            error_log('S3 upload failed: ' . $e->getMessage());
            return null;
        }
    }

    public function delete(string $key): bool {
        try {
            $this->s3Client->deleteObject([
                'Bucket' => $this->bucket,
                'Key' => $key
            ]);
            return true;
        } catch (AwsException $e) {
            error_log('S3 delete failed: ' . $e->getMessage());
            return false;
        }
    }

    public function getUrl(string $key, int $expirationMinutes = 60): string {
        $cmd = $this->s3Client->getCommand('GetObject', [
            'Bucket' => $this->bucket,
            'Key' => $key
        ]);

        $request = $this->s3Client->createPresignedRequest($cmd, "+{$expirationMinutes} minutes");
        return (string) $request->getUri();
    }
}
?>
```

---

## Caching Strategy

### Redis Caching

**Install Redis Extension:**
```bash
composer require predis/predis
```

**Cache Service:**
```php
// src/Services/CacheService.php
<?php
namespace App\Services;

use Predis\Client;

class CacheService {
    private $redis;
    private $defaultTtl = 3600; // 1 hour

    public function __construct(array $config) {
        $this->redis = new Client([
            'scheme' => 'tcp',
            'host' => $config['host'],
            'port' => $config['port'],
            'password' => $config['password'] ?? null
        ]);
    }

    public function get(string $key) {
        $value = $this->redis->get($key);

        if ($value === null) {
            return null;
        }

        return json_decode($value, true);
    }

    public function set(string $key, $value, int $ttl = null): bool {
        $ttl = $ttl ?? $this->defaultTtl;
        $jsonValue = json_encode($value);

        return $this->redis->setex($key, $ttl, $jsonValue) === 'OK';
    }

    public function delete(string $key): bool {
        return $this->redis->del([$key]) > 0;
    }

    public function remember(string $key, callable $callback, int $ttl = null) {
        $value = $this->get($key);

        if ($value !== null) {
            return $value;
        }

        $value = $callback();
        $this->set($key, $value, $ttl);

        return $value;
    }

    public function flush(): bool {
        return $this->redis->flushdb();
    }

    public function tags(array $tags): self {
        // Implement tag-based caching
        return $this;
    }
}
?>
```

**Usage in Product Service:**
```php
public function getProduct(int $id): ?Product {
    $cacheKey = "product:{$id}";

    return $this->cache->remember($cacheKey, function() use ($id) {
        return $this->productRepository->findById($id);
    }, 3600);
}

public function updateProduct(int $id, array $data): ?Product {
    $product = $this->productRepository->update($id, $data);

    // Invalidate cache
    $this->cache->delete("product:{$id}");

    return $product;
}
```

---

## Security

### Input Validation

**Validator Class:**
```php
// src/Validators/Validator.php
<?php
namespace App\Validators;

class Validator {
    private $data;
    private $rules;
    private $errors = [];

    public function validate(array $data, array $rules): array {
        $this->data = $data;
        $this->rules = $rules;
        $this->errors = [];

        foreach ($rules as $field => $ruleset) {
            $this->validateField($field, $ruleset);
        }

        return $this->errors;
    }

    private function validateField(string $field, string $ruleset): void {
        $rules = explode('|', $ruleset);
        $value = $this->data[$field] ?? null;

        foreach ($rules as $rule) {
            $this->applyRule($field, $value, $rule);
        }
    }

    private function applyRule(string $field, $value, string $rule): void {
        if (strpos($rule, ':') !== false) {
            list($rule, $parameter) = explode(':', $rule, 2);
        } else {
            $parameter = null;
        }

        switch ($rule) {
            case 'required':
                if (empty($value) && $value !== '0') {
                    $this->addError($field, "The {$field} field is required.");
                }
                break;

            case 'email':
                if ($value && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, "The {$field} must be a valid email address.");
                }
                break;

            case 'min':
                if (strlen($value) < (int)$parameter) {
                    $this->addError($field, "The {$field} must be at least {$parameter} characters.");
                }
                break;

            case 'max':
                if (strlen($value) > (int)$parameter) {
                    $this->addError($field, "The {$field} may not be greater than {$parameter} characters.");
                }
                break;

            case 'numeric':
                if ($value && !is_numeric($value)) {
                    $this->addError($field, "The {$field} must be a number.");
                }
                break;

            case 'integer':
                if ($value && filter_var($value, FILTER_VALIDATE_INT) === false) {
                    $this->addError($field, "The {$field} must be an integer.");
                }
                break;

            case 'url':
                if ($value && !filter_var($value, FILTER_VALIDATE_URL)) {
                    $this->addError($field, "The {$field} must be a valid URL.");
                }
                break;

            case 'confirmed':
                $confirmField = $field . '_confirmation';
                if ($value !== ($this->data[$confirmField] ?? null)) {
                    $this->addError($field, "The {$field} confirmation does not match.");
                }
                break;

            case 'unique':
                // Check database for uniqueness
                list($table, $column) = explode(',', $parameter);
                if ($this->checkUnique($table, $column, $value)) {
                    $this->addError($field, "The {$field} has already been taken.");
                }
                break;
        }
    }

    private function addError(string $field, string $message): void {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = [];
        }
        $this->errors[$field][] = $message;
    }

    private function checkUnique(string $table, string $column, $value): bool {
        // Check if value exists in database
        // Implementation depends on database access layer
        return false;
    }
}
?>
```

### CSRF Protection

**CSRF Middleware:**
```php
// src/Middleware/CsrfMiddleware.php
<?php
namespace App\Middleware;

class CsrfMiddleware {
    public function handle(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' ||
            $_SERVER['REQUEST_METHOD'] === 'PUT' ||
            $_SERVER['REQUEST_METHOD'] === 'DELETE') {

            $token = $_POST['_csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';

            if (!$this->validateToken($token)) {
                http_response_code(403);
                echo json_encode([
                    'success' => false,
                    'message' => 'CSRF token mismatch'
                ]);
                exit;
            }
        }
    }

    public static function generateToken(): string {
        if (!isset($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_csrf_token'];
    }

    private function validateToken(string $token): bool {
        return isset($_SESSION['_csrf_token']) &&
               hash_equals($_SESSION['_csrf_token'], $token);
    }
}
?>
```

### Rate Limiting

**Rate Limiter:**
```php
// src/Middleware/RateLimitMiddleware.php
<?php
namespace App\Middleware;

use Predis\Client;

class RateLimitMiddleware {
    private $redis;
    private $maxRequests = 60;
    private $windowSeconds = 60;

    public function __construct(Client $redis) {
        $this->redis = $redis;
    }

    public function handle(): void {
        $identifier = $this->getIdentifier();
        $key = "rate_limit:{$identifier}";

        $current = (int) $this->redis->get($key);

        if ($current >= $this->maxRequests) {
            $ttl = $this->redis->ttl($key);

            http_response_code(429);
            header('X-RateLimit-Limit: ' . $this->maxRequests);
            header('X-RateLimit-Remaining: 0');
            header('X-RateLimit-Reset: ' . (time() + $ttl));
            header('Retry-After: ' . $ttl);

            echo json_encode([
                'success' => false,
                'message' => 'Too many requests. Please try again later.',
                'retry_after' => $ttl
            ]);
            exit;
        }

        if ($current === 0) {
            $this->redis->setex($key, $this->windowSeconds, 1);
        } else {
            $this->redis->incr($key);
        }

        $remaining = $this->maxRequests - ($current + 1);
        header('X-RateLimit-Limit: ' . $this->maxRequests);
        header('X-RateLimit-Remaining: ' . max(0, $remaining));
    }

    private function getIdentifier(): string {
        // Use user ID if authenticated, otherwise IP address
        if (isset($_REQUEST['auth_user'])) {
            return 'user_' . $_REQUEST['auth_user']->sub;
        }

        return 'ip_' . $_SERVER['REMOTE_ADDR'];
    }
}
?>
```

### SQL Injection Prevention

**Always use prepared statements:**
```php
// BAD - Vulnerable to SQL injection
$id = $_GET['id'];
$query = "SELECT * FROM products WHERE id = $id";
$result = $db->query($query);

// GOOD - Safe from SQL injection
$id = $_GET['id'];
$stmt = $db->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$result = $stmt->fetch();

// ALSO GOOD - Named parameters
$stmt = $db->prepare("SELECT * FROM products WHERE id = :id");
$stmt->execute(['id' => $id]);
```

### XSS Prevention

**Always escape output:**
```php
// BAD - Vulnerable to XSS
echo $user_input;

// GOOD - Safe from XSS
echo htmlspecialchars($user_input, ENT_QUOTES, 'UTF-8');

// For JSON output
echo json_encode($data, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
```

---

## Scalability

### Horizontal Scaling

**Load Balancer Configuration (Nginx):**
```nginx
upstream backend {
    least_conn;
    server backend1.example.com:80 weight=3;
    server backend2.example.com:80 weight=2;
    server backend3.example.com:80;
}

server {
    listen 80;
    server_name pecosrivertraders.com;

    location / {
        proxy_pass http://backend;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
    }
}
```

### Database Read Replicas

**Master-Slave Configuration:**
```php
// src/Database/ConnectionManager.php
<?php
namespace App\Database;

class ConnectionManager {
    private $master;
    private $slaves = [];
    private $currentSlave = 0;

    public function __construct(array $masterConfig, array $slaveConfigs) {
        $this->master = $this->createConnection($masterConfig);

        foreach ($slaveConfigs as $config) {
            $this->slaves[] = $this->createConnection($config);
        }
    }

    public function getMaster(): \PDO {
        return $this->master;
    }

    public function getSlave(): \PDO {
        if (empty($this->slaves)) {
            return $this->master;
        }

        // Round-robin load balancing
        $slave = $this->slaves[$this->currentSlave];
        $this->currentSlave = ($this->currentSlave + 1) % count($this->slaves);

        return $slave;
    }

    public function read(string $query, array $params = []): array {
        $stmt = $this->getSlave()->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function write(string $query, array $params = []): bool {
        $stmt = $this->getMaster()->prepare($query);
        return $stmt->execute($params);
    }

    private function createConnection(array $config): \PDO {
        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=utf8mb4',
            $config['host'],
            $config['database']
        );

        return new \PDO($dsn, $config['username'], $config['password'], [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_PERSISTENT => true
        ]);
    }
}
?>
```

### Queue System (Background Jobs)

**Install Queue Package:**
```bash
composer require bernard/bernard
```

**Queue Service:**
```php
// src/Services/QueueService.php
<?php
namespace App\Services;

use Bernard\Queue;
use Bernard\Producer;

class QueueService {
    private $producer;

    public function __construct(Producer $producer) {
        $this->producer = $producer;
    }

    public function dispatch(string $jobName, array $data): void {
        $message = new \Bernard\Message($jobName, $data);
        $this->producer->produce($message);
    }

    public function sendOrderEmail(int $orderId): void {
        $this->dispatch('SendOrderEmail', ['order_id' => $orderId]);
    }

    public function processImageOptimization(string $imagePath): void {
        $this->dispatch('OptimizeImage', ['path' => $imagePath]);
    }
}
?>
```

**Queue Worker:**
```php
// worker.php
<?php
require 'vendor/autoload.php';

$consumer = new Bernard\Consumer($router, $eventDispatcher);
$consumer->consume($queue);
```

---

## DevOps & Deployment

### Docker Configuration

**Dockerfile:**
```dockerfile
FROM php:8.1-apache

# Install dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    && docker-php-ext-configure gd --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql mysqli zip gd

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . /var/www/html/

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage

EXPOSE 80

CMD ["apache2-foreground"]
```

**docker-compose.yml:**
```yaml
version: '3.8'

services:
  web:
    build: .
    ports:
      - "80:80"
    volumes:
      - ./:/var/www/html
    environment:
      - DB_HOST=db
      - DB_NAME=pecosriver
      - DB_USER=root
      - DB_PASS=root_password
    depends_on:
      - db
      - redis

  db:
    image: mysql:8.0
    environment:
      MYSQL_ROOT_PASSWORD: root_password
      MYSQL_DATABASE: pecosriver
    volumes:
      - db_data:/var/lib/mysql
    ports:
      - "3306:3306"

  redis:
    image: redis:7-alpine
    ports:
      - "6379:6379"

  phpmyadmin:
    image: phpmyadmin/phpmyadmin
    ports:
      - "8080:80"
    environment:
      PMA_HOST: db
      MYSQL_ROOT_PASSWORD: root_password

volumes:
  db_data:
```

### CI/CD Pipeline (GitHub Actions)

**.github/workflows/deploy.yml:**
```yaml
name: Deploy to Production

on:
  push:
    branches: [main]

jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3

      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.1'
          extensions: pdo, pdo_mysql, zip, gd

      - name: Install dependencies
        run: composer install --prefer-dist

      - name: Run tests
        run: vendor/bin/phpunit

  deploy:
    needs: test
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3

      - name: Deploy to server
        uses: appleboy/ssh-action@master
        with:
          host: ${{ secrets.HOST }}
          username: ${{ secrets.USERNAME }}
          key: ${{ secrets.SSH_KEY }}
          script: |
            cd /var/www/pecosrivertraders.com
            git pull origin main
            composer install --no-dev --optimize-autoloader
            php artisan migrate --force
            php artisan cache:clear
            php artisan config:cache
```

### Monitoring & Logging

**Log Service:**
```php
// src/Services/LogService.php
<?php
namespace App\Services;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SlackWebhookHandler;

class LogService {
    private $logger;

    public function __construct() {
        $this->logger = new Logger('app');

        // Log to file
        $this->logger->pushHandler(
            new StreamHandler(__DIR__ . '/../../storage/logs/app.log', Logger::DEBUG)
        );

        // Log critical errors to Slack
        $this->logger->pushHandler(
            new SlackWebhookHandler(
                'https://hooks.slack.com/services/YOUR/SLACK/WEBHOOK',
                null,
                'PRT Errors',
                true,
                null,
                false,
                false,
                Logger::ERROR
            )
        );
    }

    public function info(string $message, array $context = []): void {
        $this->logger->info($message, $context);
    }

    public function error(string $message, array $context = []): void {
        $this->logger->error($message, $context);
    }

    public function warning(string $message, array $context = []): void {
        $this->logger->warning($message, $context);
    }

    public function debug(string $message, array $context = []): void {
        $this->logger->debug($message, $context);
    }
}
?>
```

---

## Summary

This backend architecture guide provides:

1. **Modern PHP structure** with MVC pattern
2. **RESTful API design** with proper endpoints
3. **JWT authentication** for secure user sessions
4. **Repository pattern** for database abstraction
5. **Payment integration** with Stripe
6. **Email system** with PHPMailer
7. **File storage** (local and S3)
8. **Redis caching** for performance
9. **Security measures** (CSRF, rate limiting, validation)
10. **Scalability** (load balancing, read replicas, queues)
11. **DevOps** (Docker, CI/CD, monitoring)

This provides a solid foundation for building a modern, scalable e-commerce backend that can grow with the business needs.

---

**Last Updated**: November 2025
**Version**: 1.0
