<?php
require_once 'C:/xampp/htdocs/PRT2/config/database.php';

$tables = ['loyalty_transactions', 'faqs', 'faq_categories', 'user_wishlists'];

foreach ($tables as $table) {
    echo "=== $table ===\n";
    try {
        $stmt = $dbConnect->query("DESCRIBE $table");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo $row['Field'] . ' - ' . $row['Type'] . PHP_EOL;
        }
    } catch (Exception $e) {
        echo "ERROR: " . $e->getMessage() . "\n";
    }
    echo "\n";
}
