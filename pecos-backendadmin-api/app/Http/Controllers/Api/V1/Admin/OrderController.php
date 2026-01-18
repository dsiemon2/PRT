<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Get all orders with filters
     */
    public function index(Request $request)
    {
        $query = DB::table('orders')
            ->leftJoin('users', 'orders.user_id', '=', 'users.id')
            ->leftJoin(DB::raw('(SELECT order_id, COUNT(*) as item_count FROM order_items GROUP BY order_id) as oi'), 'orders.id', '=', 'oi.order_id')
            ->select(
                'orders.*',
                'users.first_name',
                'users.last_name',
                'users.email as user_email',
                DB::raw('COALESCE(oi.item_count, 0) as item_count')
            );

        // Apply filters
        if ($request->filled('status')) {
            $query->where('orders.status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('orders.order_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('orders.order_date', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('orders.order_number', 'like', "%{$search}%")
                  ->orWhere('users.email', 'like', "%{$search}%")
                  ->orWhere('users.first_name', 'like', "%{$search}%")
                  ->orWhere('users.last_name', 'like', "%{$search}%")
                  ->orWhere('orders.customer_first_name', 'like', "%{$search}%")
                  ->orWhere('orders.customer_last_name', 'like', "%{$search}%");
            });
        }

        $perPage = $request->input('per_page', 20);
        $orders = $query->orderBy('orders.order_date', 'desc')
                        ->paginate($perPage);

        return response()->json($orders);
    }

    /**
     * Get order details
     */
    public function show($id)
    {
        $order = DB::table('orders')
            ->leftJoin('users', 'orders.user_id', '=', 'users.id')
            ->select('orders.*', 'users.first_name', 'users.last_name', 'users.email as user_email')
            ->where('orders.id', $id)
            ->first();

        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        // Get order items (product_name is already stored in order_items)
        $items = DB::table('order_items')
            ->where('order_id', $id)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'order' => $order,
                'items' => $items
            ]
        ]);
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string',
            'notes' => 'nullable|string'
        ]);

        $order = DB::table('orders')->where('id', $id)->first();
        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        // Update order status
        DB::table('orders')->where('id', $id)->update([
            'status' => $request->status
        ]);

        // Add to status history
        DB::table('order_status_history')->insert([
            'order_id' => $id,
            'status' => $request->status,
            'notes' => $request->notes,
            'created_by' => $request->user() ? $request->user()->id : null,
            'created_at' => now()
        ]);

        return response()->json(['success' => true, 'message' => 'Order status updated']);
    }

    /**
     * Add note to order
     */
    public function addNote(Request $request, $id)
    {
        $request->validate([
            'notes' => 'required|string'
        ]);

        $order = DB::table('orders')->where('id', $id)->first();
        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        DB::table('order_status_history')->insert([
            'order_id' => $id,
            'status' => $order->status,
            'notes' => $request->notes,
            'created_by' => $request->user() ? $request->user()->id : null,
            'created_at' => now()
        ]);

        return response()->json(['success' => true, 'message' => 'Note added']);
    }

    /**
     * Process refund
     */
    public function refund(Request $request, $id)
    {
        $request->validate([
            'amount' => 'nullable|numeric|min:0',
            'reason' => 'nullable|string'
        ]);

        $order = DB::table('orders')->where('id', $id)->first();
        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        // Calculate refund amount
        $refundAmount = $request->amount ?? $order->total_amount;

        // Update order with refund info
        DB::table('orders')->where('id', $id)->update([
            'status' => 'refunded'
        ]);

        // Add to status history
        DB::table('order_status_history')->insert([
            'order_id' => $id,
            'status' => 'refunded',
            'notes' => "Refund of $" . number_format($refundAmount, 2) .
                       ($request->reason ? ": " . $request->reason : ""),
            'created_by' => $request->user() ? $request->user()->id : null,
            'created_at' => now()
        ]);

        return response()->json(['success' => true, 'message' => 'Refund processed']);
    }

    /**
     * Get order statistics for dashboard
     */
    public function stats()
    {
        $today = now()->startOfDay();
        $thisWeek = now()->subDays(7)->startOfDay();
        $thisMonth = now()->startOfMonth();

        $totalRevenue = DB::table('orders')
            ->whereNotIn('status', ['cancelled'])
            ->sum('total_amount') ?? 0;

        $totalOrders = DB::table('orders')->count();

        return response()->json([
            'total_orders' => $totalOrders,
            'today_orders' => DB::table('orders')->whereDate('order_date', $today)->count(),
            'week_orders' => DB::table('orders')->where('order_date', '>=', $thisWeek)->count(),
            'month_orders' => DB::table('orders')->where('order_date', '>=', $thisMonth)->count(),
            'pending_orders' => DB::table('orders')->where('status', 'pending')->count(),
            'processing_orders' => DB::table('orders')->where('status', 'processing')->count(),
            'total_revenue' => $totalRevenue,
            'avg_order_value' => $totalOrders > 0 ? $totalRevenue / $totalOrders : 0,
            'today_revenue' => DB::table('orders')
                ->whereDate('order_date', $today)
                ->whereNotIn('status', ['cancelled'])
                ->sum('total_amount') ?? 0,
            'week_revenue' => DB::table('orders')
                ->where('order_date', '>=', $thisWeek)
                ->whereNotIn('status', ['cancelled'])
                ->sum('total_amount') ?? 0,
            'month_revenue' => DB::table('orders')
                ->where('order_date', '>=', $thisMonth)
                ->whereNotIn('status', ['cancelled'])
                ->sum('total_amount') ?? 0
        ]);
    }

    /**
     * Cancel an order
     */
    public function cancel($id)
    {
        $order = DB::table('orders')->where('id', $id)->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }

        // Can only cancel pending or processing orders
        if (!in_array($order->status, ['pending', 'processing'])) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot cancel order with status: ' . $order->status
            ], 400);
        }

        DB::table('orders')->where('id', $id)->update([
            'status' => 'cancelled'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Order cancelled successfully'
        ]);
    }
}
