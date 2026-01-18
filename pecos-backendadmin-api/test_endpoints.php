<?php
/**
 * API Endpoint Test Script
 * Tests all endpoints and reports status
 */

$baseUrl = 'http://localhost:3000/pecos-backendadmin-api/public/api/v1';
$results = [];
$errors = [];

function testEndpoint($url, $method = 'GET', $data = null, $token = null) {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    $headers = ['Accept: application/json'];
    if ($token) {
        $headers[] = "Authorization: Bearer $token";
    }

    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            $headers[] = 'Content-Type: application/json';
        }
    } elseif ($method === 'PUT') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            $headers[] = 'Content-Type: application/json';
        }
    } elseif ($method === 'DELETE') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
    }

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    return [
        'code' => $httpCode,
        'response' => json_decode($response, true),
        'raw' => $response,
        'error' => $error
    ];
}

function printResult($name, $result, $expectedCode = 200, $checkData = false) {
    global $results, $errors;

    $status = '✓';
    $message = '';

    if ($result['error']) {
        $status = '✗';
        $message = "CURL Error: {$result['error']}";
        $errors[] = "$name: $message";
    } elseif ($result['code'] !== $expectedCode) {
        $status = '✗';
        $message = "Expected {$expectedCode}, got {$result['code']}";
        $errors[] = "$name: $message";
    } elseif ($checkData && empty($result['response']['data'])) {
        $status = '⚠';
        $message = "No data returned";
        $errors[] = "$name: $message";
    } elseif ($result['response']['success'] === false) {
        $status = '✗';
        $message = $result['response']['message'] ?? 'Unknown error';
        $errors[] = "$name: $message";
    } else {
        $dataCount = '';
        if (isset($result['response']['data'])) {
            if (is_array($result['response']['data'])) {
                $dataCount = ' (' . count($result['response']['data']) . ' items)';
            }
        }
        $message = "OK$dataCount";
    }

    echo sprintf("%-50s [%s] %s\n", $name, $status, $message);
    $results[$name] = ['status' => $status, 'message' => $message];
}

echo "==============================================\n";
echo "  PECOS RIVER TRADERS API ENDPOINT TESTS\n";
echo "==============================================\n\n";

// ==================
// PUBLIC ENDPOINTS
// ==================
echo "--- PUBLIC ENDPOINTS ---\n\n";

// Products
$result = testEndpoint("$baseUrl/products?per_page=5");
printResult('GET /products', $result, 200, true);

$result = testEndpoint("$baseUrl/products/search?q=story");
printResult('GET /products/search', $result, 200, true);

$result = testEndpoint("$baseUrl/products/featured?limit=3");
printResult('GET /products/featured', $result, 200, true);

// Get first product UPC for testing
$productsResult = testEndpoint("$baseUrl/products?per_page=1");
$firstUpc = $productsResult['response']['data'][0]['UPC'] ?? 'TEST-UPC';

$result = testEndpoint("$baseUrl/products/$firstUpc");
printResult('GET /products/{upc}', $result, 200);

// Categories
$result = testEndpoint("$baseUrl/categories");
printResult('GET /categories', $result, 200, true);

$result = testEndpoint("$baseUrl/categories/tree");
printResult('GET /categories/tree', $result, 200, true);

$result = testEndpoint("$baseUrl/categories/bottom");
printResult('GET /categories/bottom', $result, 200, true);

// Get first category for testing
$categoriesResult = testEndpoint("$baseUrl/categories");
$firstCategory = $categoriesResult['response']['data'][0]['CategoryCode'] ?? 101;

$result = testEndpoint("$baseUrl/categories/$firstCategory");
printResult('GET /categories/{code}', $result, 200);

$result = testEndpoint("$baseUrl/products/category/$firstCategory");
printResult('GET /products/category/{code}', $result, 200);

// Reviews (public)
$result = testEndpoint("$baseUrl/products/$firstUpc/reviews");
printResult('GET /products/{id}/reviews', $result, 200);

// Blog
$result = testEndpoint("$baseUrl/blog");
printResult('GET /blog', $result, 200);

$result = testEndpoint("$baseUrl/blog/categories");
printResult('GET /blog/categories', $result, 200);

$result = testEndpoint("$baseUrl/blog/recent");
printResult('GET /blog/recent', $result, 200);

// Events
$result = testEndpoint("$baseUrl/events");
printResult('GET /events', $result, 200);

$result = testEndpoint("$baseUrl/events/upcoming");
printResult('GET /events/upcoming', $result, 200);

// FAQs
$result = testEndpoint("$baseUrl/faqs");
printResult('GET /faqs', $result, 200);

$result = testEndpoint("$baseUrl/faqs/categories");
printResult('GET /faqs/categories', $result, 200);

// ==================
// AUTH ENDPOINTS
// ==================
echo "\n--- AUTH ENDPOINTS ---\n\n";

// Register a test user
$testEmail = 'test_' . time() . '@example.com';
$result = testEndpoint("$baseUrl/auth/register", 'POST', [
    'email' => $testEmail,
    'password' => 'password123',
    'password_confirmation' => 'password123',
    'first_name' => 'Test',
    'last_name' => 'User'
]);
printResult('POST /auth/register', $result, 201);
$token = $result['response']['data']['token'] ?? null;

// Login
$result = testEndpoint("$baseUrl/auth/login", 'POST', [
    'email' => $testEmail,
    'password' => 'password123'
]);
printResult('POST /auth/login', $result, 200);
$token = $result['response']['data']['token'] ?? $token;

if (!$token) {
    echo "\n⚠ No auth token available. Skipping protected endpoints.\n";
} else {
    // ==================
    // PROTECTED ENDPOINTS
    // ==================
    echo "\n--- PROTECTED ENDPOINTS (require auth) ---\n\n";

    // User
    $result = testEndpoint("$baseUrl/auth/user", 'GET', null, $token);
    printResult('GET /auth/user', $result, 200);

    // Cart
    $result = testEndpoint("$baseUrl/cart", 'GET', null, $token);
    printResult('GET /cart', $result, 200);

    $result = testEndpoint("$baseUrl/cart", 'POST', [
        'product_upc' => $firstUpc,
        'quantity' => 1
    ], $token);
    printResult('POST /cart', $result, 200);

    $result = testEndpoint("$baseUrl/cart/$firstUpc", 'PUT', [
        'quantity' => 2
    ], $token);
    printResult('PUT /cart/{upc}', $result, 200);

    // Wishlist
    $result = testEndpoint("$baseUrl/wishlist", 'GET', null, $token);
    printResult('GET /wishlist', $result, 200);

    $result = testEndpoint("$baseUrl/wishlist", 'POST', [
        'product_id' => $firstUpc
    ], $token);
    printResult('POST /wishlist', $result, 201);

    $result = testEndpoint("$baseUrl/wishlist/check/$firstUpc", 'GET', null, $token);
    printResult('GET /wishlist/check/{id}', $result, 200);

    $result = testEndpoint("$baseUrl/wishlist/toggle/$firstUpc", 'POST', null, $token);
    printResult('POST /wishlist/toggle/{id}', $result, 200);

    // Orders
    $result = testEndpoint("$baseUrl/orders", 'GET', null, $token);
    printResult('GET /orders', $result, 200);

    // Loyalty
    $result = testEndpoint("$baseUrl/loyalty", 'GET', null, $token);
    printResult('GET /loyalty', $result, 200);

    $result = testEndpoint("$baseUrl/loyalty/rewards", 'GET', null, $token);
    printResult('GET /loyalty/rewards', $result, 200);

    // Reviews
    $result = testEndpoint("$baseUrl/user/reviews", 'GET', null, $token);
    printResult('GET /user/reviews', $result, 200);

    // Coupons - test with non-existent code (404 expected)
    $result = testEndpoint("$baseUrl/coupons/validate", 'POST', [
        'code' => 'TESTCODE',
        'subtotal' => 100
    ], $token);
    // Expect 404 for non-existent coupon - this is correct behavior
    // Custom check since success:false is expected
    if ($result['code'] === 404) {
        echo sprintf("%-50s [%s] %s\n", 'POST /coupons/validate', '✓', 'OK (correctly returns 404 for invalid coupon)');
        $results['POST /coupons/validate'] = ['status' => '✓', 'message' => 'OK'];
    } else {
        printResult('POST /coupons/validate', $result, 404);
    }

    // Logout
    $result = testEndpoint("$baseUrl/auth/logout", 'POST', null, $token);
    printResult('POST /auth/logout', $result, 200);
}

// ==================
// SUMMARY
// ==================
echo "\n==============================================\n";
echo "  SUMMARY\n";
echo "==============================================\n\n";

$total = count($results);
$passed = count(array_filter($results, fn($r) => $r['status'] === '✓'));
$warnings = count(array_filter($results, fn($r) => $r['status'] === '⚠'));
$failed = count(array_filter($results, fn($r) => $r['status'] === '✗'));

echo "Total: $total | Passed: $passed | Warnings: $warnings | Failed: $failed\n\n";

if (!empty($errors)) {
    echo "ERRORS:\n";
    foreach ($errors as $error) {
        echo "  - $error\n";
    }
}

echo "\n";
