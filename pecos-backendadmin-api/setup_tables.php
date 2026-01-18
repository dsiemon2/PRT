<?php
/**
 * Setup required database tables for API endpoints
 */

require_once 'C:/xampp/htdocs/PRT2/config/database.php';

$tables = [
    // Cart table
    'cart' => "CREATE TABLE IF NOT EXISTS cart (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        session_id VARCHAR(255) NULL,
        product_upc VARCHAR(50) NOT NULL,
        quantity INT NOT NULL DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_user_id (user_id),
        INDEX idx_session_id (session_id),
        INDEX idx_product_upc (product_upc)
    )",

    // User wishlists table
    'user_wishlists' => "CREATE TABLE IF NOT EXISTS user_wishlists (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        product_id VARCHAR(50) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_user_product (user_id, product_id),
        INDEX idx_user_id (user_id)
    )",

    // Loyalty transactions table
    'loyalty_transactions' => "CREATE TABLE IF NOT EXISTS loyalty_transactions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        points INT NOT NULL,
        type VARCHAR(50) NOT NULL,
        description VARCHAR(255) NULL,
        order_id INT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_user_id (user_id),
        INDEX idx_type (type)
    )",

    // Coupons table
    'coupons' => "CREATE TABLE IF NOT EXISTS coupons (
        id INT AUTO_INCREMENT PRIMARY KEY,
        code VARCHAR(50) NOT NULL UNIQUE,
        discount_type ENUM('percentage', 'fixed') NOT NULL,
        discount_value DECIMAL(10,2) NOT NULL,
        min_order_amount DECIMAL(10,2) DEFAULT 0,
        max_discount DECIMAL(10,2) NULL,
        usage_limit INT NULL,
        used_count INT DEFAULT 0,
        starts_at DATETIME NULL,
        expires_at DATETIME NULL,
        is_active TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )",

    // Coupon usage tracking
    'coupon_usages' => "CREATE TABLE IF NOT EXISTS coupon_usages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        coupon_id INT NOT NULL,
        user_id INT NOT NULL,
        order_id INT NULL,
        discount_amount DECIMAL(10,2) NOT NULL,
        used_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_coupon_id (coupon_id),
        INDEX idx_user_id (user_id)
    )",

    // FAQ categories
    'faq_categories' => "CREATE TABLE IF NOT EXISTS faq_categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        slug VARCHAR(100) NOT NULL,
        description TEXT NULL,
        display_order INT DEFAULT 0,
        is_active TINYINT(1) DEFAULT 1
    )",

    // FAQs table
    'faqs' => "CREATE TABLE IF NOT EXISTS faqs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        question VARCHAR(500) NOT NULL,
        answer TEXT NOT NULL,
        category_id INT NULL,
        display_order INT DEFAULT 0,
        views INT DEFAULT 0,
        helpful_count INT DEFAULT 0,
        not_helpful_count INT DEFAULT 0,
        is_active TINYINT(1) DEFAULT 1,
        INDEX idx_category_id (category_id)
    )"
];

echo "=== Setting up database tables ===\n\n";

foreach ($tables as $name => $sql) {
    try {
        $dbConnect->exec($sql);
        echo "✓ $name created/exists\n";
    } catch (PDOException $e) {
        echo "✗ $name failed: " . $e->getMessage() . "\n";
    }
}

// Insert sample FAQ categories if empty
$stmt = $dbConnect->query("SELECT COUNT(*) FROM faq_categories");
if ($stmt->fetchColumn() == 0) {
    echo "\nInserting sample FAQ categories...\n";
    $categories = [
        ['Shipping & Delivery', 'shipping-delivery', 'Questions about shipping and delivery', 1],
        ['Returns & Refunds', 'returns-refunds', 'Questions about returns and refunds', 2],
        ['Products & Inventory', 'products-inventory', 'Questions about our products', 3],
        ['Account & Orders', 'account-orders', 'Questions about accounts and orders', 4],
        ['General', 'general', 'General questions', 5]
    ];

    $stmt = $dbConnect->prepare("INSERT INTO faq_categories (name, slug, description, display_order) VALUES (?, ?, ?, ?)");
    foreach ($categories as $cat) {
        $stmt->execute($cat);
    }
    echo "✓ FAQ categories inserted\n";
}

// Insert sample FAQs if empty
$stmt = $dbConnect->query("SELECT COUNT(*) FROM faqs");
if ($stmt->fetchColumn() == 0) {
    echo "\nInserting sample FAQs...\n";
    $faqs = [
        ['How long does shipping take?', 'Standard shipping takes 5-7 business days. Express shipping takes 2-3 business days.', 1, 1],
        ['What is your return policy?', 'We accept returns within 30 days of purchase. Items must be unused and in original packaging.', 2, 1],
        ['Do you ship internationally?', 'Yes, we ship to most countries. International shipping typically takes 10-14 business days.', 1, 2],
        ['How can I track my order?', 'Once shipped, you will receive a tracking number via email. You can also track orders in your account.', 1, 3],
    ];

    $stmt = $dbConnect->prepare("INSERT INTO faqs (question, answer, category_id, display_order) VALUES (?, ?, ?, ?)");
    foreach ($faqs as $faq) {
        $stmt->execute($faq);
    }
    echo "✓ FAQs inserted\n";
}

echo "\n=== Setup complete ===\n";
