<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\StockAlert;
use App\Models\InventoryTransaction;
use App\Traits\HasGridFeatures;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    use HasGridFeatures;

    public function index(Request $request)
    {
        $query = Product::with('category')
            ->select('products3.*')
            ->leftJoin('categories', 'products3.CategoryCode', '=', 'categories.CategoryCode');

        // Apply search
        $this->applySearch($query, $request, ['ShortDescription', 'UPC', 'ItemNumber']);

        // Apply category filter
        if ($request->filled('category')) {
            $query->where('products3.CategoryCode', $request->get('category'));
        }

        // Apply stock status filter
        $status = $request->get('status', 'all');
        if ($status === 'low_stock') {
            $query->lowStock();
        } elseif ($status === 'out_of_stock') {
            $query->outOfStock();
        } elseif ($status === 'in_stock') {
            $query->inStock();
        }

        // Default sort: Category, then ItemNumber (matching prt4)
        $sortBy = $request->get('sort', '');
        $sortDir = $request->get('dir', 'asc');

        if ($sortBy) {
            $query->orderBy($sortBy, $sortDir);
        } else {
            // Default: sort by Category name, then ItemNumber
            $query->orderBy('categories.Category', 'asc')
                  ->orderBy('products3.ItemNumber', 'asc');
        }

        // Get paginated results
        $products = $this->getPaginated($query, $request);

        // Get stats
        $stats = [
            'total_products' => Product::count(),
            'tracked_products' => Product::tracked()->count(),
            'low_stock_count' => Product::lowStock()->count(),
            'out_of_stock_count' => Product::outOfStock()->count(),
            'total_inventory_value' => Product::sum(DB::raw('stock_quantity * cost_price')),
        ];

        // Get categories for filter (sorted alphabetically)
        $categories = Category::orderBy('Category')->get();

        // Get recent alerts
        $alerts = StockAlert::with('product')
            ->active()
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        // Get filter options
        $filters = $this->getFilterOptions($request, [
            'category' => $request->get('category', ''),
        ]);

        return view('admin.inventory.index', compact(
            'products', 'stats', 'categories', 'alerts', 'filters'
        ));
    }

    public function edit(Product $product)
    {
        $product->load('inventoryTransactions');
        $transactions = $product->inventoryTransactions()->orderByDesc('created_at')->limit(20)->get();

        return view('admin.inventory.edit', compact('product', 'transactions'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'stock_quantity' => 'required|integer|min:0',
            'reserved_quantity' => 'nullable|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'reorder_point' => 'nullable|integer|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'track_inventory' => 'boolean',
            'adjustment_reason' => 'nullable|string|max:500',
        ]);

        $oldQuantity = $product->stock_quantity;
        $newQuantity = $validated['stock_quantity'];

        // Update product
        $product->update([
            'stock_quantity' => $newQuantity,
            'reserved_quantity' => $validated['reserved_quantity'] ?? $product->reserved_quantity,
            'low_stock_threshold' => $validated['low_stock_threshold'] ?? $product->low_stock_threshold,
            'reorder_point' => $validated['reorder_point'] ?? $product->reorder_point,
            'cost_price' => $validated['cost_price'] ?? $product->cost_price,
            'track_inventory' => $validated['track_inventory'] ?? $product->track_inventory,
            'last_restock_date' => $newQuantity > $oldQuantity ? now() : $product->last_restock_date,
        ]);

        // Create transaction record if quantity changed
        if ($oldQuantity !== $newQuantity) {
            InventoryTransaction::create([
                'product_id' => $product->ID,
                'quantity_change' => $newQuantity - $oldQuantity,
                'transaction_type' => $newQuantity > $oldQuantity ? 'restock' : 'adjustment',
                'notes' => $validated['adjustment_reason'] ?? 'Manual adjustment',
                'previous_quantity' => $oldQuantity,
                'new_quantity' => $newQuantity,
                'user_id' => auth()->id(),
            ]);
        }

        // Auto-resolve stock alerts if product is no longer in alert condition
        $product->refresh();
        $availableQty = $product->availableQuantity;
        $threshold = $product->low_stock_threshold ?? 5;

        // Resolve out_of_stock alerts if now in stock
        if ($availableQty > 0) {
            StockAlert::where('product_id', $product->ID)
                ->where('alert_type', 'out_of_stock')
                ->where('is_resolved', false)
                ->update(['is_resolved' => true, 'resolved_at' => now()]);
        }

        // Resolve low_stock alerts if now above threshold
        if ($availableQty > $threshold) {
            StockAlert::where('product_id', $product->ID)
                ->where('alert_type', 'low_stock')
                ->where('is_resolved', false)
                ->update(['is_resolved' => true, 'resolved_at' => now()]);
        }

        return back()->with('success', 'Inventory updated successfully.');
    }

    public function reports(Request $request)
    {
        $reportType = $request->get('report', 'valuation');

        // Report titles and descriptions (matching prt4)
        $reportInfo = match($reportType) {
            'valuation' => [
                'title' => 'Inventory Valuation Report',
                'description' => 'Total value of inventory at cost and retail prices'
            ],
            'stock_status' => [
                'title' => 'Stock Status Report',
                'description' => 'Summary of products by stock level'
            ],
            'low_stock' => [
                'title' => 'Low Stock & Reorder Report',
                'description' => 'Products that need reordering'
            ],
            'movement' => [
                'title' => 'Stock Movement Report (Last 30 Days)',
                'description' => 'Products with the most inventory transactions'
            ],
            default => [
                'title' => 'Inventory Report',
                'description' => ''
            ],
        };

        $reportTitle = $reportInfo['title'];
        $reportDescription = $reportInfo['description'];

        $data = match($reportType) {
            'valuation' => $this->getValuationReport(),
            'stock_status' => $this->getStockStatusReport(),
            'low_stock' => $this->getLowStockReport(),
            'movement' => $this->getMovementReport($request),
            default => $this->getValuationReport(),
        };

        return view('admin.inventory.reports', compact('reportType', 'data', 'reportTitle', 'reportDescription'));
    }

    public function bulkUpdate(Request $request)
    {
        $categories = Category::orderBy('Category')->get();

        // Get products with filters (matching prt4)
        $query = Product::where('track_inventory', true)
            ->orderBy('ShortDescription');

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('ShortDescription', 'like', "%{$search}%")
                  ->orWhere('ItemNumber', 'like', "%{$search}%")
                  ->orWhere('UPC', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('CategoryCode', $request->get('category'));
        }

        $products = $query->limit(100)->get();

        return view('admin.inventory.bulk-update', compact('categories', 'products'));
    }

    public function bulkUpdateCsv(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt'
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getPathname(), 'r');

        // Skip header row
        fgetcsv($handle);

        $successCount = 0;
        $errorCount = 0;
        $results = [];

        while (($data = fgetcsv($handle)) !== false) {
            if (count($data) >= 2) {
                $itemNumber = trim($data[0]);
                $adjustment = intval($data[1]);
                $notes = isset($data[2]) ? trim($data[2]) : 'Bulk update via CSV';

                $product = Product::where('ItemNumber', $itemNumber)->first();

                if ($product) {
                    $oldQuantity = $product->stock_quantity;
                    $newQuantity = max(0, $oldQuantity + $adjustment);

                    $product->update(['stock_quantity' => $newQuantity]);

                    if ($oldQuantity !== $newQuantity) {
                        InventoryTransaction::create([
                            'product_id' => $product->ID,
                            'quantity_change' => $adjustment,
                            'transaction_type' => 'bulk_update',
                            'notes' => $notes,
                            'previous_quantity' => $oldQuantity,
                            'new_quantity' => $newQuantity,
                            'user_id' => auth()->id(),
                        ]);
                    }

                    $successCount++;
                    $results[] = [
                        'status' => 'success',
                        'item' => $itemNumber,
                        'adjustment' => $adjustment,
                        'message' => 'Updated successfully'
                    ];
                } else {
                    $errorCount++;
                    $results[] = [
                        'status' => 'error',
                        'item' => $itemNumber,
                        'adjustment' => $adjustment,
                        'message' => 'Product not found'
                    ];
                }
            }
        }

        fclose($handle);

        $messageType = $errorCount > 0 ? 'warning' : 'success';
        $message = "Bulk update complete: {$successCount} successful, {$errorCount} errors";

        return back()->with($messageType, $message)->with('upload_results', $results);
    }

    public function bulkUpdateManual(Request $request)
    {
        $request->validate([
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products3,ID',
            'adjustment' => 'required|integer',
            'notes' => 'nullable|string|max:500'
        ]);

        $adjustment = $request->input('adjustment');
        $notes = $request->input('notes', 'Bulk manual adjustment');
        $successCount = 0;
        $errorCount = 0;

        foreach ($request->input('product_ids') as $productId) {
            $product = Product::find($productId);
            if (!$product) {
                $errorCount++;
                continue;
            }

            $oldQuantity = $product->stock_quantity;
            $newQuantity = max(0, $oldQuantity + $adjustment);

            $product->update(['stock_quantity' => $newQuantity]);

            if ($oldQuantity !== $newQuantity) {
                InventoryTransaction::create([
                    'product_id' => $product->ID,
                    'quantity_change' => $adjustment,
                    'transaction_type' => 'bulk_update',
                    'notes' => $notes,
                    'previous_quantity' => $oldQuantity,
                    'new_quantity' => $newQuantity,
                    'user_id' => auth()->id(),
                ]);
                $successCount++;
            }
        }

        $messageType = $errorCount > 0 ? 'warning' : 'success';
        $message = "Bulk adjustment complete: {$successCount} successful, {$errorCount} errors";

        return back()->with($messageType, $message);
    }

    public function processBulkUpdate(Request $request)
    {
        $validated = $request->validate([
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products3,ID',
            'products.*.quantity' => 'required|integer|min:0',
            'products.*.action' => 'required|in:set,add,subtract',
        ]);

        $count = 0;
        foreach ($validated['products'] as $item) {
            $product = Product::find($item['id']);
            if (!$product) continue;

            $oldQuantity = $product->stock_quantity;

            $newQuantity = match($item['action']) {
                'set' => $item['quantity'],
                'add' => $oldQuantity + $item['quantity'],
                'subtract' => max(0, $oldQuantity - $item['quantity']),
            };

            $product->update(['stock_quantity' => $newQuantity]);

            if ($oldQuantity !== $newQuantity) {
                InventoryTransaction::create([
                    'product_id' => $product->ID,
                    'quantity_change' => $newQuantity - $oldQuantity,
                    'transaction_type' => 'bulk_update',
                    'notes' => 'Bulk update',
                    'previous_quantity' => $oldQuantity,
                    'new_quantity' => $newQuantity,
                    'user_id' => auth()->id(),
                ]);
                $count++;
            }
        }

        return back()->with('success', "{$count} products updated successfully.");
    }

    public function export(Request $request)
    {
        $query = Product::with('category')
            ->select('products3.*')
            ->leftJoin('categories', 'products3.CategoryCode', '=', 'categories.CategoryCode');

        // Apply same filters as index
        $this->applySearch($query, $request, ['ShortDescription', 'UPC', 'ItemNumber']);

        if ($request->filled('category')) {
            $query->where('products3.CategoryCode', $request->get('category'));
        }

        $status = $request->get('status', 'all');
        if ($status === 'low_stock') {
            $query->lowStock();
        } elseif ($status === 'out_of_stock') {
            $query->outOfStock();
        } elseif ($status === 'in_stock') {
            $query->inStock();
        }

        // Sort by Category, then ItemNumber (matching prt4)
        $products = $query->orderBy('categories.Category', 'asc')
                          ->orderBy('products3.ItemNumber', 'asc')
                          ->get();

        $filename = 'inventory_export_' . date('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($products) {
            $file = fopen('php://output', 'w');

            // Header row
            fputcsv($file, [
                'UPC', 'Item Number', 'Description', 'Category',
                'Stock Qty', 'Reserved', 'Available', 'Cost Price',
                'Unit Price', 'Stock Value', 'Low Stock Threshold', 'Status'
            ]);

            foreach ($products as $product) {
                fputcsv($file, [
                    $product->UPC,
                    $product->ItemNumber,
                    $product->ShortDescription,
                    $product->category->Category ?? '',
                    $product->stock_quantity,
                    $product->reserved_quantity,
                    $product->availableQuantity,
                    $product->cost_price,
                    $product->UnitPrice,
                    $product->stock_quantity * $product->cost_price,
                    $product->low_stock_threshold,
                    $product->stockStatus,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function getValuationReport(): array
    {
        // Get category-grouped data like prt4
        $categories = DB::select("
            SELECT
                c.Category,
                COUNT(p.id) as product_count,
                SUM(p.stock_quantity) as total_units,
                SUM(p.stock_quantity * p.cost_price) as cost_value,
                SUM(p.stock_quantity * p.UnitPrice) as retail_value,
                SUM((p.stock_quantity * p.UnitPrice) - (p.stock_quantity * p.cost_price)) as potential_profit
            FROM products3 p
            LEFT JOIN categories c ON p.CategoryCode = c.CategoryCode
            WHERE p.track_inventory = 1
            GROUP BY c.Category
            ORDER BY cost_value DESC
        ");

        // Calculate totals
        $totals = [
            'product_count' => collect($categories)->sum('product_count'),
            'total_units' => collect($categories)->sum('total_units'),
            'cost_value' => collect($categories)->sum('cost_value'),
            'retail_value' => collect($categories)->sum('retail_value'),
            'potential_profit' => collect($categories)->sum('potential_profit'),
        ];

        return [
            'categories' => $categories,
            'totals' => $totals,
            'total_value' => $totals['cost_value'],
        ];
    }

    private function getStockStatusReport(): array
    {
        return [
            'in_stock' => Product::inStock()->count(),
            'low_stock' => Product::lowStock()->count(),
            'out_of_stock' => Product::outOfStock()->count(),
            'not_tracked' => Product::where('track_inventory', false)->count(),
            'by_category' => Category::withCount([
                'products as total_products',
                'products as low_stock_products' => function($q) {
                    $q->lowStock();
                },
                'products as out_of_stock_products' => function($q) {
                    $q->outOfStock();
                },
            ])->get(),
        ];
    }

    private function getLowStockReport(): array
    {
        return [
            'products' => Product::with('category')
                ->where(function($q) {
                    $q->lowStock()->orWhere(function($q2) {
                        $q2->outOfStock();
                    });
                })
                ->orderBy(DB::raw('stock_quantity - reserved_quantity'))
                ->get(),
        ];
    }

    private function getMovementReport(Request $request): array
    {
        $days = $request->get('days', 30);
        $from = now()->subDays($days);

        return [
            'transactions' => InventoryTransaction::with(['product', 'user'])
                ->where('created_at', '>=', $from)
                ->orderByDesc('created_at')
                ->get(),
            'days' => $days,
        ];
    }
}
