<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class ExportController extends Controller
{
    /**
     * Export orders to CSV.
     */
    public function orders(Request $request): Response
    {
        $query = DB::table('orders')
            ->leftJoin('users', 'orders.user_id', '=', 'users.id')
            ->select(
                'orders.id as order_id',
                'orders.order_number',
                'orders.user_id',
                'users.first_name',
                'users.last_name',
                'users.email as customer_email',
                'orders.subtotal',
                'orders.tax',
                'orders.shipping_cost',
                'orders.total',
                'orders.status',
                'orders.payment_method',
                'orders.shipping_method',
                'orders.shipping_address',
                'orders.billing_address',
                'orders.created_at'
            );

        // Apply filters
        if ($request->has('status')) {
            $query->where('orders.status', $request->status);
        }
        if ($request->has('from_date')) {
            $query->whereDate('orders.created_at', '>=', $request->from_date);
        }
        if ($request->has('to_date')) {
            $query->whereDate('orders.created_at', '<=', $request->to_date);
        }

        $orders = $query->orderBy('orders.created_at', 'desc')->get();

        $csv = $this->arrayToCsv($orders->toArray(), [
            'Order ID', 'Order Number', 'User ID', 'First Name', 'Last Name', 'Customer Email',
            'Subtotal', 'Tax', 'Shipping', 'Total', 'Status',
            'Payment Method', 'Shipping Method', 'Shipping Address', 'Billing Address', 'Created At'
        ]);

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="orders_export_' . date('Y-m-d') . '.csv"',
        ]);
    }

    /**
     * Export customers to CSV.
     */
    public function customers(Request $request): Response
    {
        $query = DB::table('users')
            ->leftJoin('loyalty_members', 'users.id', '=', 'loyalty_members.user_id')
            ->leftJoin('loyalty_tiers', 'loyalty_members.tier_id', '=', 'loyalty_tiers.id')
            ->select(
                'users.id',
                'users.first_name',
                'users.last_name',
                'users.email',
                'users.phone',
                'users.role',
                'users.created_at',
                'loyalty_members.available_points as points_balance',
                'loyalty_tiers.tier_name as tier'
            )
            ->selectRaw('(SELECT COUNT(*) FROM orders WHERE orders.user_id = users.id) as order_count')
            ->selectRaw('(SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE orders.user_id = users.id) as total_spent');

        // Apply filters
        if ($request->has('role')) {
            $query->where('users.role', $request->role);
        }
        if ($request->has('from_date')) {
            $query->whereDate('users.created_at', '>=', $request->from_date);
        }
        if ($request->has('to_date')) {
            $query->whereDate('users.created_at', '<=', $request->to_date);
        }

        $customers = $query->orderBy('users.created_at', 'desc')->get();

        $csv = $this->arrayToCsv($customers->toArray(), [
            'User ID', 'First Name', 'Last Name', 'Email', 'Phone', 'Role', 'Created At',
            'Points Balance', 'Loyalty Tier', 'Order Count', 'Total Spent'
        ]);

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="customers_export_' . date('Y-m-d') . '.csv"',
        ]);
    }

    /**
     * Export products to CSV.
     */
    public function products(Request $request): Response
    {
        $query = DB::table('products3')
            ->leftJoin('categories', 'products3.CategoryCode', '=', 'categories.CategoryCode')
            ->select(
                'products3.ItemNumber',
                'products3.UPC',
                'products3.ShortDescription',
                'products3.LngDescription',
                'categories.Category as category_name',
                'products3.UnitPrice',
                'products3.cost_price',
                'products3.stock_quantity',
                'products3.low_stock_threshold',
                'products3.track_inventory'
            );

        // Apply filters
        if ($request->has('category')) {
            $query->where('products3.CategoryCode', $request->category);
        }
        if ($request->has('min_price')) {
            $query->where('products3.UnitPrice', '>=', $request->min_price);
        }
        if ($request->has('max_price')) {
            $query->where('products3.UnitPrice', '<=', $request->max_price);
        }
        if ($request->has('low_stock')) {
            $query->whereRaw('products3.stock_quantity <= products3.low_stock_threshold');
        }

        $products = $query->orderBy('products3.ShortDescription')->get();

        $csv = $this->arrayToCsv($products->toArray(), [
            'Item Number', 'UPC', 'Name', 'Description', 'Category',
            'Price', 'Cost', 'Stock', 'Low Stock Threshold', 'Track Inventory'
        ]);

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="products_export_' . date('Y-m-d') . '.csv"',
        ]);
    }

    /**
     * Convert array to CSV string.
     */
    private function arrayToCsv(array $data, array $headers): string
    {
        $output = fopen('php://temp', 'r+');

        // Write headers
        fputcsv($output, $headers);

        // Write data
        foreach ($data as $row) {
            fputcsv($output, (array) $row);
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }
}
