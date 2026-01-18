<?php

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Product;

echo "Total products: " . Product::count() . PHP_EOL;
echo "With Qty_avail > 0: " . Product::where('Qty_avail', '>', 0)->count() . PHP_EOL;
echo "Without track_inventory or null: " . Product::whereNull('track_inventory')->orWhere('track_inventory', 0)->count() . PHP_EOL;

// Show first product
$first = Product::first();
if ($first) {
    echo "\nFirst product:\n";
    echo "UPC: " . $first->UPC . PHP_EOL;
    echo "Description: " . $first->Description . PHP_EOL;
    echo "Qty_avail: " . $first->Qty_avail . PHP_EOL;
    echo "track_inventory: " . ($first->track_inventory ?? 'NULL') . PHP_EOL;
    echo "Sold_out: " . ($first->Sold_out ?? 'NULL') . PHP_EOL;
}
