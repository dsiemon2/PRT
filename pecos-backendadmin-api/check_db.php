<?php
require_once 'C:/xampp/htdocs/PRT2/config/database.php';

echo "=== products3 columns ===\n";
$stmt = $dbConnect->query('DESCRIBE products3');
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo $row['Field'] . ' - ' . $row['Type'] . PHP_EOL;
}

echo "\n=== Sample products ===\n";
$stmt = $dbConnect->query('SELECT UPC, Description, Qty_avail, Sold_out FROM products3 LIMIT 5');
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo $row['UPC'] . ' | ' . $row['Description'] . ' | Qty: ' . ($row['Qty_avail'] ?? 'NULL') . ' | Sold_out: ' . ($row['Sold_out'] ?? 'NULL') . PHP_EOL;
}

echo "\n=== Counts ===\n";
$stmt = $dbConnect->query('SELECT COUNT(*) FROM products3');
echo "Total: " . $stmt->fetchColumn() . PHP_EOL;

$stmt = $dbConnect->query('SELECT COUNT(*) FROM products3 WHERE Qty_avail > 0');
echo "With Qty_avail > 0: " . $stmt->fetchColumn() . PHP_EOL;

$stmt = $dbConnect->query("SELECT COUNT(*) FROM products3 WHERE Sold_out = 'Y'");
echo "Sold out (Y): " . $stmt->fetchColumn() . PHP_EOL;
