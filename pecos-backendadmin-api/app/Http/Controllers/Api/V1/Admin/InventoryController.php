<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    /**
     * Get inventory dashboard data.
     *
     * @OA\Get(
     *     path="/admin/inventory/dashboard",
     *     summary="Get inventory dashboard",
     *     tags={"Admin"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function dashboard(): JsonResponse
    {
        $totalProducts = Product::count();
        $inStock = Product::where('Qty_avail', '>', 0)->count();
        $outOfStock = Product::where('Qty_avail', '<=', 0)->orWhereNull('Qty_avail')->count();
        $lowStock = Product::whereNotNull('low_stock_threshold')
            ->whereRaw('Qty_avail <= low_stock_threshold')
            ->count();

        $totalValue = Product::sum(DB::raw('COALESCE(Qty_avail, 0) * COALESCE(cost_price, Price)'));

        return response()->json([
            'success' => true,
            'data' => [
                'total_products' => $totalProducts,
                'in_stock' => $inStock,
                'out_of_stock' => $outOfStock,
                'low_stock' => $lowStock,
                'total_value' => round($totalValue, 2),
            ]
        ]);
    }

    /**
     * Get low stock alerts.
     *
     * @OA\Get(
     *     path="/admin/inventory/alerts",
     *     summary="Get low stock alerts",
     *     tags={"Admin"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer", default=20)),
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function alerts(Request $request): JsonResponse
    {
        // Query products directly with proper column names
        $alerts = DB::table('products3 as p')
            ->leftJoin('categories as c', 'p.CategoryCode', '=', 'c.CategoryCode')
            ->select(
                'p.id',
                'p.UPC',
                'p.ItemNumber',
                'p.ShortDescription',
                'p.stock_quantity',
                'p.reserved_quantity',
                DB::raw('(p.stock_quantity - p.reserved_quantity) as available'),
                'p.low_stock_threshold',
                'p.reorder_point',
                'c.Category as category_name',
                DB::raw("CASE
                    WHEN (p.stock_quantity - p.reserved_quantity) <= 0 THEN 'out_of_stock'
                    WHEN (p.stock_quantity - p.reserved_quantity) <= p.reorder_point THEN 'low_stock'
                    ELSE 'normal'
                END as alert_type")
            )
            ->where('p.track_inventory', 1)
            ->whereRaw('(p.stock_quantity - p.reserved_quantity) <= p.reorder_point')
            ->orderBy(DB::raw('(p.stock_quantity - p.reserved_quantity)'), 'asc')
            ->get();

        // Get summary stats
        $stats = DB::table('products3')
            ->where('track_inventory', 1)
            ->select(
                DB::raw('SUM(CASE WHEN (stock_quantity - reserved_quantity) <= 0 THEN 1 ELSE 0 END) as out_of_stock_count'),
                DB::raw('SUM(CASE WHEN (stock_quantity - reserved_quantity) > 0 AND (stock_quantity - reserved_quantity) <= reorder_point THEN 1 ELSE 0 END) as low_stock_count'),
                DB::raw('SUM(CASE WHEN (stock_quantity - reserved_quantity) <= reorder_point THEN 1 ELSE 0 END) as needs_reorder_count')
            )
            ->first();

        return response()->json([
            'success' => true,
            'data' => $alerts,
            'stats' => $stats
        ]);
    }

    /**
     * Bulk update stock.
     *
     * @OA\Post(
     *     path="/admin/inventory/bulk-update",
     *     summary="Bulk update stock",
     *     tags={"Admin"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"items"},
     *             @OA\Property(property="items", type="array", @OA\Items(
     *                 @OA\Property(property="upc", type="string"),
     *                 @OA\Property(property="quantity", type="integer")
     *             ))
     *         )
     *     ),
     *     @OA\Response(response=200, description="Stock updated")
     * )
     */
    public function bulkUpdate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.upc' => 'required|string',
            'items.*.quantity' => 'required|integer|min:0',
        ]);

        $updated = 0;
        $errors = [];

        foreach ($validated['items'] as $item) {
            $product = Product::find($item['upc']);
            if ($product) {
                $product->Qty_avail = $item['quantity'];
                $product->save();
                $updated++;
            } else {
                $errors[] = "Product {$item['upc']} not found";
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Updated {$updated} products",
            'updated' => $updated,
            'errors' => $errors
        ]);
    }

    /**
     * Get inventory valuation report.
     *
     * @OA\Get(
     *     path="/admin/inventory/valuation",
     *     summary="Get inventory valuation report",
     *     tags={"Admin"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function valuationReport(): JsonResponse
    {
        $byCategory = Product::select(
                'CategoryCode',
                DB::raw('COUNT(*) as product_count'),
                DB::raw('SUM(COALESCE(Qty_avail, 0)) as total_quantity'),
                DB::raw('SUM(COALESCE(Qty_avail, 0) * COALESCE(cost_price, Price)) as total_value')
            )
            ->groupBy('CategoryCode')
            ->get();

        $total = Product::select(
                DB::raw('COUNT(*) as product_count'),
                DB::raw('SUM(COALESCE(Qty_avail, 0)) as total_quantity'),
                DB::raw('SUM(COALESCE(Qty_avail, 0) * COALESCE(cost_price, Price)) as total_value')
            )
            ->first();

        return response()->json([
            'success' => true,
            'data' => [
                'by_category' => $byCategory,
                'totals' => $total
            ]
        ]);
    }

    /**
     * Export inventory to CSV.
     *
     * @OA\Get(
     *     path="/admin/inventory/export",
     *     summary="Export inventory",
     *     tags={"Admin"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function export(Request $request): JsonResponse
    {
        $products = Product::select([
            'UPC',
            'Description',
            'Company',
            'Price',
            'Qty_avail',
            'cost_price',
            'CategoryCode',
            'low_stock_threshold',
            'reorder_point',
            'reorder_quantity'
        ])->get();

        return response()->json([
            'success' => true,
            'data' => $products,
            'count' => $products->count()
        ]);
    }

    /**
     * Get inventory stats for dashboard.
     */
    public function stats(): JsonResponse
    {
        // Use low_stock_threshold for low stock calculation (matches PRT5)
        // low_stock: available > 0 AND available <= low_stock_threshold
        // out_of_stock: available <= 0
        $stats = DB::select("SELECT
            COUNT(*) as total_products,
            SUM(CASE WHEN track_inventory = 1 OR track_inventory IS NULL THEN 1 ELSE 0 END) as tracked_products,
            SUM(CASE WHEN (track_inventory = 1 OR track_inventory IS NULL)
                      AND (stock_quantity - COALESCE(reserved_quantity, 0)) > 0
                      AND (stock_quantity - COALESCE(reserved_quantity, 0)) <= COALESCE(low_stock_threshold, 5) THEN 1 ELSE 0 END) as low_stock_count,
            SUM(CASE WHEN (track_inventory = 1 OR track_inventory IS NULL)
                      AND (stock_quantity - COALESCE(reserved_quantity, 0)) <= 0 THEN 1 ELSE 0 END) as out_of_stock_count,
            SUM(stock_quantity * COALESCE(cost_price, 0)) as total_inventory_value
        FROM products3")[0];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Get inventory products with filters.
     */
    public function products(Request $request): JsonResponse
    {
        $search = $request->get('search', '');
        $category = $request->get('category', '');
        $status = $request->get('status', 'all');

        $query = DB::table('products3 as p')
            ->leftJoin('categories as c', 'p.CategoryCode', '=', 'c.CategoryCode')
            ->select(
                'p.id', 'p.UPC', 'p.ItemNumber', 'p.ShortDescription',
                'p.stock_quantity', 'p.reserved_quantity',
                DB::raw('(p.stock_quantity - p.reserved_quantity) as available'),
                'p.low_stock_threshold', 'p.reorder_point', 'p.cost_price', 'p.UnitPrice',
                'p.track_inventory', 'p.last_restock_date',
                'c.Category as category_name'
            );

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('p.ShortDescription', 'LIKE', "%{$search}%")
                  ->orWhere('p.UPC', 'LIKE', "%{$search}%")
                  ->orWhere('p.ItemNumber', 'LIKE', "%{$search}%");
            });
        }

        if ($category) {
            $query->where('p.CategoryCode', $category);
        }

        if ($status == 'low_stock') {
            $query->whereRaw('(p.stock_quantity - p.reserved_quantity) <= p.low_stock_threshold AND p.track_inventory = 1');
        } elseif ($status == 'out_of_stock') {
            $query->whereRaw('(p.stock_quantity - p.reserved_quantity) <= 0 AND p.track_inventory = 1');
        } elseif ($status == 'in_stock') {
            $query->whereRaw('(p.stock_quantity - p.reserved_quantity) > p.low_stock_threshold AND p.track_inventory = 1');
        }

        $products = $query->orderBy('p.ShortDescription', 'ASC')->limit(50)->get();

        // Get categories for filter
        $categories = DB::table('categories')
            ->select('CategoryCode', 'Category')
            ->distinct()
            ->orderBy('Category')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'products' => $products,
                'categories' => $categories
            ]
        ]);
    }

    /**
     * Get stock alerts from stock_alerts table.
     */
    public function stockAlerts(): JsonResponse
    {
        // Read from stock_alerts table (matches PRT5)
        $alerts = DB::table('stock_alerts as sa')
            ->leftJoin('products3 as p', 'sa.product_id', '=', 'p.ID')
            ->leftJoin('categories as c', 'p.CategoryCode', '=', 'c.CategoryCode')
            ->select(
                'sa.id',
                'sa.product_id',
                'p.ShortDescription',
                'p.UPC',
                'p.ItemNumber',
                'p.stock_quantity',
                'p.reserved_quantity',
                DB::raw('(p.stock_quantity - COALESCE(p.reserved_quantity, 0)) as available'),
                'p.low_stock_threshold',
                'p.reorder_point',
                'c.Category as category_name',
                'sa.alert_type',
                'sa.current_quantity',
                'sa.created_at'
            )
            ->where('sa.is_resolved', 0)
            ->orderByDesc('sa.created_at')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $alerts
        ]);
    }

    /**
     * Get inventory reports data.
     */
    public function reports(Request $request): JsonResponse
    {
        $reportType = $request->get('report', 'valuation');

        switch ($reportType) {
            case 'valuation':
                $data = DB::table('products3 as p')
                    ->leftJoin('categories as c', 'p.CategoryCode', '=', 'c.CategoryCode')
                    ->select(
                        'c.Category as category_name',
                        DB::raw('COUNT(*) as product_count'),
                        DB::raw('SUM(p.stock_quantity) as total_quantity'),
                        DB::raw('SUM(p.stock_quantity * p.cost_price) as total_cost_value'),
                        DB::raw('SUM(p.stock_quantity * p.UnitPrice) as total_retail_value')
                    )
                    ->where('p.track_inventory', 1)
                    ->groupBy('c.Category')
                    ->orderBy('c.Category')
                    ->get();
                break;

            case 'stock_status':
                $data = DB::select("SELECT
                    CASE
                        WHEN (stock_quantity - reserved_quantity) <= 0 THEN 'Out of Stock'
                        WHEN (stock_quantity - reserved_quantity) <= low_stock_threshold THEN 'Low Stock'
                        ELSE 'In Stock'
                    END as status,
                    COUNT(*) as product_count,
                    SUM(stock_quantity) as total_units,
                    SUM(stock_quantity * cost_price) as total_value
                FROM products3
                WHERE track_inventory = 1
                GROUP BY status
                ORDER BY FIELD(status, 'Out of Stock', 'Low Stock', 'In Stock')");
                break;

            case 'low_stock':
                $data = DB::table('products3 as p')
                    ->leftJoin('categories as c', 'p.CategoryCode', '=', 'c.CategoryCode')
                    ->select(
                        'p.id', 'p.UPC', 'p.ItemNumber', 'p.ShortDescription',
                        'p.stock_quantity', 'p.reserved_quantity',
                        DB::raw('(p.stock_quantity - p.reserved_quantity) as available'),
                        'p.low_stock_threshold', 'p.reorder_point', 'p.reorder_quantity',
                        'p.cost_price',
                        DB::raw('(p.reorder_quantity * p.cost_price) as reorder_cost'),
                        'c.Category'
                    )
                    ->whereRaw('(p.stock_quantity - p.reserved_quantity) <= p.reorder_point AND p.track_inventory = 1')
                    ->orderBy(DB::raw('(p.stock_quantity - p.reserved_quantity)'))
                    ->get();
                break;

            case 'movement':
                $data = DB::table('inventory_transactions as it')
                    ->join('products3 as p', 'it.product_id', '=', 'p.id')
                    ->leftJoin('categories as c', 'p.CategoryCode', '=', 'c.CategoryCode')
                    ->select(
                        'p.ShortDescription', 'p.ItemNumber', 'p.UPC',
                        'c.Category',
                        DB::raw('COUNT(it.id) as transaction_count'),
                        DB::raw('SUM(CASE WHEN it.quantity_change > 0 THEN it.quantity_change ELSE 0 END) as total_added'),
                        DB::raw('SUM(CASE WHEN it.quantity_change < 0 THEN ABS(it.quantity_change) ELSE 0 END) as total_removed'),
                        DB::raw('SUM(it.quantity_change) as net_change'),
                        'p.stock_quantity as current_stock'
                    )
                    ->whereRaw('it.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)')
                    ->groupBy('p.id', 'p.ShortDescription', 'p.ItemNumber', 'p.UPC', 'c.Category', 'p.stock_quantity')
                    ->orderByDesc('transaction_count')
                    ->limit(50)
                    ->get();
                break;

            default:
                $data = [];
        }

        return response()->json([
            'success' => true,
            'report_type' => $reportType,
            'data' => $data
        ]);
    }

    /**
     * Get stock alerts with full filters (for admin/stock-alerts.php).
     * Queries products directly instead of stock_alerts table for real-time data.
     */
    public function stockAlertsFull(Request $request): JsonResponse
    {
        $alertType = $request->get('type', 'all');

        // Query products directly for real-time stock status
        $query = DB::table('products3 as p')
            ->leftJoin('categories as c', 'p.CategoryCode', '=', 'c.CategoryCode')
            ->select(
                'p.id',
                'p.UPC as product_id',
                'p.ShortDescription',
                'p.UPC',
                'p.ItemNumber',
                'p.stock_quantity',
                'p.reserved_quantity',
                DB::raw('(p.stock_quantity - p.reserved_quantity) as available'),
                'p.low_stock_threshold',
                'p.reorder_point',
                'c.Category as category_name',
                DB::raw("CASE
                    WHEN (p.stock_quantity - p.reserved_quantity) <= 0 THEN 'out_of_stock'
                    WHEN (p.stock_quantity - p.reserved_quantity) <= p.reorder_point THEN 'low_stock'
                    ELSE 'normal'
                END as alert_type"),
                DB::raw('NOW() as created_at')
            )
            ->where('p.track_inventory', 1);

        // Filter by alert type
        if ($alertType == 'out_of_stock') {
            $query->whereRaw('(p.stock_quantity - p.reserved_quantity) <= 0');
        } elseif ($alertType == 'low_stock') {
            $query->whereRaw('(p.stock_quantity - p.reserved_quantity) > 0')
                  ->whereRaw('(p.stock_quantity - p.reserved_quantity) <= p.reorder_point');
        } else {
            // All alerts: out of stock OR low stock
            $query->whereRaw('(p.stock_quantity - p.reserved_quantity) <= p.reorder_point');
        }

        $alerts = $query->orderBy('available', 'asc')->get();

        // Get summary stats from products directly
        $stats = DB::table('products3')
            ->where('track_inventory', 1)
            ->select(
                DB::raw('SUM(CASE WHEN (stock_quantity - reserved_quantity) <= reorder_point THEN 1 ELSE 0 END) as total_alerts'),
                DB::raw('SUM(CASE WHEN (stock_quantity - reserved_quantity) <= reorder_point THEN 1 ELSE 0 END) as active_alerts'),
                DB::raw('0 as resolved_alerts'),
                DB::raw('SUM(CASE WHEN (stock_quantity - reserved_quantity) <= 0 THEN 1 ELSE 0 END) as out_of_stock_count'),
                DB::raw('SUM(CASE WHEN (stock_quantity - reserved_quantity) > 0 AND (stock_quantity - reserved_quantity) <= reorder_point THEN 1 ELSE 0 END) as low_stock_count')
            )
            ->first();

        return response()->json([
            'success' => true,
            'data' => [
                'alerts' => $alerts,
                'stats' => $stats
            ]
        ]);
    }

    /**
     * Resolve a stock alert.
     */
    public function resolveAlert(Request $request): JsonResponse
    {
        $alertId = $request->input('alert_id');
        $userId = $request->input('user_id');

        DB::table('stock_alerts')
            ->where('id', $alertId)
            ->update([
                'is_resolved' => 1,
                'resolved_at' => now(),
                'resolved_by' => $userId
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Alert marked as resolved'
        ]);
    }

    /**
     * Resolve all stock alerts.
     */
    public function resolveAllAlerts(Request $request): JsonResponse
    {
        $userId = $request->input('user_id');

        $count = DB::table('stock_alerts')
            ->where('is_resolved', 0)
            ->update([
                'is_resolved' => 1,
                'resolved_at' => now(),
                'resolved_by' => $userId
            ]);

        return response()->json([
            'success' => true,
            'message' => "All {$count} alerts marked as resolved"
        ]);
    }

    /**
     * Get inventory reports with full data for exports.
     */
    public function reportsExport(Request $request): JsonResponse
    {
        $reportType = $request->get('report', 'valuation');

        switch ($reportType) {
            case 'valuation':
                $data = DB::table('products3 as p')
                    ->leftJoin('categories as c', 'p.CategoryCode', '=', 'c.CategoryCode')
                    ->select(
                        'c.Category',
                        DB::raw('COUNT(p.id) as product_count'),
                        DB::raw('SUM(p.stock_quantity) as total_units'),
                        DB::raw('SUM(p.stock_quantity * p.cost_price) as cost_value'),
                        DB::raw('SUM(p.stock_quantity * p.UnitPrice) as retail_value'),
                        DB::raw('SUM((p.stock_quantity * p.UnitPrice) - (p.stock_quantity * p.cost_price)) as potential_profit')
                    )
                    ->where('p.track_inventory', 1)
                    ->groupBy('c.Category')
                    ->orderByDesc('cost_value')
                    ->get();
                break;

            case 'stock_status':
                $data = DB::select("SELECT
                    CASE
                        WHEN (stock_quantity - reserved_quantity) <= 0 THEN 'Out of Stock'
                        WHEN (stock_quantity - reserved_quantity) <= low_stock_threshold THEN 'Low Stock'
                        ELSE 'In Stock'
                    END as status,
                    COUNT(*) as product_count,
                    SUM(stock_quantity) as total_units,
                    SUM(stock_quantity * cost_price) as total_value
                FROM products3
                WHERE track_inventory = 1
                GROUP BY status
                ORDER BY FIELD(status, 'Out of Stock', 'Low Stock', 'In Stock')");
                break;

            case 'movement':
                $data = DB::table('inventory_transactions as it')
                    ->join('products3 as p', 'it.product_id', '=', 'p.id')
                    ->leftJoin('categories as c', 'p.CategoryCode', '=', 'c.CategoryCode')
                    ->select(
                        'p.ShortDescription', 'p.ItemNumber', 'p.UPC',
                        'c.Category',
                        DB::raw('COUNT(it.id) as transaction_count'),
                        DB::raw('SUM(CASE WHEN it.quantity_change > 0 THEN it.quantity_change ELSE 0 END) as total_added'),
                        DB::raw('SUM(CASE WHEN it.quantity_change < 0 THEN ABS(it.quantity_change) ELSE 0 END) as total_removed'),
                        DB::raw('SUM(it.quantity_change) as net_change'),
                        'p.stock_quantity as current_stock'
                    )
                    ->whereRaw('it.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)')
                    ->groupBy('p.id', 'p.ShortDescription', 'p.ItemNumber', 'p.UPC', 'c.Category', 'p.stock_quantity')
                    ->orderByDesc('transaction_count')
                    ->limit(50)
                    ->get();
                break;

            case 'low_stock':
                $data = DB::table('products3 as p')
                    ->leftJoin('categories as c', 'p.CategoryCode', '=', 'c.CategoryCode')
                    ->select(
                        'p.ShortDescription', 'p.ItemNumber', 'p.UPC',
                        'c.Category',
                        'p.stock_quantity', 'p.reserved_quantity',
                        DB::raw('(p.stock_quantity - p.reserved_quantity) as available'),
                        'p.low_stock_threshold', 'p.reorder_point', 'p.reorder_quantity',
                        'p.cost_price',
                        DB::raw('(p.reorder_quantity * p.cost_price) as reorder_cost')
                    )
                    ->where('p.track_inventory', 1)
                    ->whereRaw('(p.stock_quantity - p.reserved_quantity) <= p.reorder_point')
                    ->orderBy(DB::raw('(p.stock_quantity - p.reserved_quantity)'))
                    ->get();
                break;

            default:
                $data = [];
        }

        return response()->json([
            'success' => true,
            'report_type' => $reportType,
            'data' => $data
        ]);
    }

    /**
     * Get product for editing.
     */
    public function getProduct(int $id): JsonResponse
    {
        $product = DB::table('products3 as p')
            ->leftJoin('categories as c', 'p.CategoryCode', '=', 'c.CategoryCode')
            ->select('p.*', 'c.Category as category_name')
            ->where('p.id', $id)
            ->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        // Get recent transactions
        $transactions = DB::table('inventory_transactions')
            ->where('product_id', $id)
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'product' => $product,
                'transactions' => $transactions
            ]
        ]);
    }

    /**
     * Get products for bulk update.
     */
    public function bulkUpdateProducts(Request $request): JsonResponse
    {
        $search = $request->get('search', '');
        $category = $request->get('category', '');

        $query = DB::table('products3 as p')
            ->leftJoin('categories as c', 'p.CategoryCode', '=', 'c.CategoryCode')
            ->select('p.id', 'p.ItemNumber', 'p.ShortDescription', 'p.stock_quantity', 'c.Category')
            ->where('p.track_inventory', 1);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('p.ShortDescription', 'LIKE', "%{$search}%")
                  ->orWhere('p.ItemNumber', 'LIKE', "%{$search}%")
                  ->orWhere('p.UPC', 'LIKE', "%{$search}%");
            });
        }

        if ($category) {
            $query->where('p.CategoryCode', $category);
        }

        $products = $query->orderBy('p.ShortDescription')->limit(100)->get();

        // Get categories
        $categories = DB::table('categories')
            ->select('CategoryCode', 'Category')
            ->distinct()
            ->orderBy('Category')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'products' => $products,
                'categories' => $categories
            ]
        ]);
    }

    /**
     * Get inventory export data.
     */
    public function exportData(Request $request): JsonResponse
    {
        $search = $request->get('search', '');
        $category = $request->get('category', '');
        $status = $request->get('status', 'all');

        $query = DB::table('products3 as p')
            ->leftJoin('categories as c', 'p.CategoryCode', '=', 'c.CategoryCode')
            ->select(
                'p.ShortDescription', 'p.ItemNumber', 'p.UPC',
                'c.Category',
                'p.stock_quantity', 'p.reserved_quantity',
                DB::raw('(p.stock_quantity - p.reserved_quantity) as available'),
                'p.low_stock_threshold', 'p.cost_price', 'p.UnitPrice',
                DB::raw('(p.stock_quantity * p.cost_price) as total_value'),
                DB::raw("CASE
                    WHEN (p.stock_quantity - p.reserved_quantity) <= 0 THEN 'Out of Stock'
                    WHEN (p.stock_quantity - p.reserved_quantity) <= p.low_stock_threshold THEN 'Low Stock'
                    ELSE 'In Stock'
                END as status")
            );

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('p.ShortDescription', 'LIKE', "%{$search}%")
                  ->orWhere('p.UPC', 'LIKE', "%{$search}%")
                  ->orWhere('p.ItemNumber', 'LIKE', "%{$search}%");
            });
        }

        if ($category) {
            $query->where('p.CategoryCode', $category);
        }

        if ($status == 'low_stock') {
            $query->whereRaw('(p.stock_quantity - p.reserved_quantity) <= p.low_stock_threshold AND p.track_inventory = 1');
        } elseif ($status == 'out_of_stock') {
            $query->whereRaw('(p.stock_quantity - p.reserved_quantity) <= 0 AND p.track_inventory = 1');
        } elseif ($status == 'in_stock') {
            $query->whereRaw('(p.stock_quantity - p.reserved_quantity) > p.low_stock_threshold AND p.track_inventory = 1');
        }

        $data = $query->orderBy('p.ShortDescription')->get();

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Adjust stock for a product.
     */
    public function adjustStock(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|integer',
            'adjustment' => 'required|integer',
            'notes' => 'nullable|string',
            'user_id' => 'nullable|integer'
        ]);

        $productId = $validated['product_id'];
        $adjustment = $validated['adjustment'];
        $notes = $validated['notes'] ?? '';
        $userId = $validated['user_id'] ?? null;

        // Get current stock
        $product = DB::table('products3')->where('id', $productId)->first();
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        $quantityBefore = $product->stock_quantity;
        $quantityAfter = $quantityBefore + $adjustment;

        // Update stock
        DB::table('products3')
            ->where('id', $productId)
            ->update([
                'stock_quantity' => $quantityAfter,
                'last_restock_date' => $adjustment > 0 ? now() : $product->last_restock_date
            ]);

        // Log transaction
        DB::table('inventory_transactions')->insert([
            'product_id' => $productId,
            'transaction_type' => 'adjustment',
            'quantity_change' => $adjustment,
            'quantity_before' => $quantityBefore,
            'quantity_after' => $quantityAfter,
            'reference_type' => 'manual',
            'notes' => $notes,
            'user_id' => $userId,
            'created_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => $adjustment > 0
                ? "Successfully added {$adjustment} units to inventory"
                : "Successfully removed " . abs($adjustment) . " units from inventory",
            'data' => [
                'quantity_before' => $quantityBefore,
                'quantity_after' => $quantityAfter
            ]
        ]);
    }

    /**
     * Update inventory settings for a product.
     */
    public function updateSettings(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|integer',
            'track_inventory' => 'required|boolean',
            'allow_backorder' => 'required|boolean',
            'reorder_point' => 'required|integer|min:0',
            'reorder_quantity' => 'required|integer|min:0',
            'low_stock_threshold' => 'required|integer|min:0',
            'cost_price' => 'required|numeric|min:0'
        ]);

        $updated = DB::table('products3')
            ->where('id', $validated['product_id'])
            ->update([
                'track_inventory' => $validated['track_inventory'],
                'allow_backorder' => $validated['allow_backorder'],
                'reorder_point' => $validated['reorder_point'],
                'reorder_quantity' => $validated['reorder_quantity'],
                'low_stock_threshold' => $validated['low_stock_threshold'],
                'cost_price' => $validated['cost_price']
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Settings updated successfully'
        ]);
    }

    /**
     * Bulk adjust stock via CSV data.
     */
    public function bulkAdjustCsv(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.item_number' => 'required|string',
            'items.*.adjustment' => 'required|integer',
            'items.*.notes' => 'nullable|string',
            'user_id' => 'nullable|integer'
        ]);

        $userId = $validated['user_id'] ?? null;
        $results = [];
        $successCount = 0;
        $errorCount = 0;

        foreach ($validated['items'] as $item) {
            $product = DB::table('products3')
                ->where('ItemNumber', $item['item_number'])
                ->first();

            if ($product) {
                $quantityBefore = $product->stock_quantity;
                $quantityAfter = $quantityBefore + $item['adjustment'];

                DB::table('products3')
                    ->where('id', $product->id)
                    ->update([
                        'stock_quantity' => $quantityAfter,
                        'last_restock_date' => $item['adjustment'] > 0 ? now() : $product->last_restock_date
                    ]);

                DB::table('inventory_transactions')->insert([
                    'product_id' => $product->id,
                    'transaction_type' => 'adjustment',
                    'quantity_change' => $item['adjustment'],
                    'quantity_before' => $quantityBefore,
                    'quantity_after' => $quantityAfter,
                    'reference_type' => 'bulk_import',
                    'notes' => $item['notes'] ?? 'Bulk update via CSV',
                    'user_id' => $userId,
                    'created_at' => now()
                ]);

                $successCount++;
                $results[] = [
                    'status' => 'success',
                    'item' => $item['item_number'],
                    'adjustment' => $item['adjustment'],
                    'message' => 'Updated successfully'
                ];
            } else {
                $errorCount++;
                $results[] = [
                    'status' => 'error',
                    'item' => $item['item_number'],
                    'adjustment' => $item['adjustment'],
                    'message' => 'Product not found'
                ];
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Bulk update complete: {$successCount} successful, {$errorCount} errors",
            'data' => [
                'results' => $results,
                'success_count' => $successCount,
                'error_count' => $errorCount
            ]
        ]);
    }

    /**
     * Bulk adjust stock for selected products.
     */
    public function bulkAdjustManual(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_ids' => 'required|array',
            'product_ids.*' => 'integer',
            'adjustment' => 'required|integer',
            'notes' => 'nullable|string',
            'user_id' => 'nullable|integer'
        ]);

        $userId = $validated['user_id'] ?? null;
        $adjustment = $validated['adjustment'];
        $notes = $validated['notes'] ?? 'Bulk manual adjustment';
        $successCount = 0;
        $errorCount = 0;

        foreach ($validated['product_ids'] as $productId) {
            $product = DB::table('products3')->where('id', $productId)->first();

            if ($product) {
                $quantityBefore = $product->stock_quantity;
                $quantityAfter = $quantityBefore + $adjustment;

                DB::table('products3')
                    ->where('id', $productId)
                    ->update([
                        'stock_quantity' => $quantityAfter,
                        'last_restock_date' => $adjustment > 0 ? now() : $product->last_restock_date
                    ]);

                DB::table('inventory_transactions')->insert([
                    'product_id' => $productId,
                    'transaction_type' => 'adjustment',
                    'quantity_change' => $adjustment,
                    'quantity_before' => $quantityBefore,
                    'quantity_after' => $quantityAfter,
                    'reference_type' => 'manual',
                    'notes' => $notes,
                    'user_id' => $userId,
                    'created_at' => now()
                ]);

                $successCount++;
            } else {
                $errorCount++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Bulk adjustment complete: {$successCount} successful, {$errorCount} errors",
            'data' => [
                'success_count' => $successCount,
                'error_count' => $errorCount
            ]
        ]);
    }
}
