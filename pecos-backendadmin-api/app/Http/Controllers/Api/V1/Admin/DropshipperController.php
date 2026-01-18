<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DropshipperController extends Controller
{
    /**
     * Get all dropshippers with stats.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 20);
        $page = $request->get('page', 1);

        $query = DB::table('dropshippers')
            ->orderByDesc('created_at');

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Search by name or email
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Get total count before pagination
        $total = $query->count();

        // Apply pagination
        $dropshippers = $query
            ->offset(($page - 1) * $perPage)
            ->limit($perPage)
            ->get();

        // Get stats
        $stats = [
            'total' => DB::table('dropshippers')->count(),
            'active' => DB::table('dropshippers')->where('status', 'active')->count(),
            'total_orders' => DB::table('dropship_orders')->count(),
            'total_revenue' => DB::table('dropshippers')->sum('total_revenue')
        ];

        // Calculate pagination meta
        $lastPage = ceil($total / $perPage);
        $from = $total > 0 ? (($page - 1) * $perPage) + 1 : 0;
        $to = min($page * $perPage, $total);

        return response()->json([
            'success' => true,
            'data' => $dropshippers,
            'stats' => $stats,
            'meta' => [
                'current_page' => (int) $page,
                'per_page' => (int) $perPage,
                'total' => $total,
                'last_page' => (int) $lastPage,
                'from' => $from,
                'to' => $to
            ]
        ]);
    }

    /**
     * Get a single dropshipper.
     */
    public function show(int $id): JsonResponse
    {
        $dropshipper = DB::table('dropshippers')->find($id);

        if (!$dropshipper) {
            return response()->json([
                'success' => false,
                'message' => 'Dropshipper not found'
            ], 404);
        }

        // Get recent orders for this dropshipper
        $orders = DB::table('dropship_orders')
            ->where('dropshipper_id', $id)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $dropshipper,
            'orders' => $orders
        ]);
    }

    /**
     * Create a new dropshipper.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'contact_name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:dropshippers,email',
            'phone' => 'nullable|string|max:50',
            'commission_rate' => 'required|numeric|min:0|max:100',
            'address_line1' => 'nullable|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        // Generate API key
        $apiKey = substr($request->company_name, 0, 2) . '_' . bin2hex(random_bytes(16));

        $id = DB::table('dropshippers')->insertGetId([
            'company_name' => $validated['company_name'],
            'contact_name' => $validated['contact_name'] ?? null,
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'api_key' => $apiKey,
            'commission_rate' => $validated['commission_rate'],
            'status' => 'pending',
            'address_line1' => $validated['address_line1'] ?? null,
            'address_line2' => $validated['address_line2'] ?? null,
            'city' => $validated['city'] ?? null,
            'state' => $validated['state'] ?? null,
            'postal_code' => $validated['postal_code'] ?? null,
            'country' => $validated['country'] ?? 'USA',
            'notes' => $validated['notes'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $dropshipper = DB::table('dropshippers')->find($id);

        return response()->json([
            'success' => true,
            'message' => 'Dropshipper created successfully',
            'data' => $dropshipper
        ], 201);
    }

    /**
     * Update a dropshipper.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $dropshipper = DB::table('dropshippers')->find($id);

        if (!$dropshipper) {
            return response()->json([
                'success' => false,
                'message' => 'Dropshipper not found'
            ], 404);
        }

        $validated = $request->validate([
            'company_name' => 'sometimes|string|max:255',
            'contact_name' => 'sometimes|nullable|string|max:255',
            'email' => 'sometimes|email|unique:dropshippers,email,' . $id,
            'phone' => 'sometimes|nullable|string|max:50',
            'commission_rate' => 'sometimes|numeric|min:0|max:100',
            'status' => 'sometimes|in:active,pending,suspended',
            'address_line1' => 'sometimes|nullable|string|max:255',
            'address_line2' => 'sometimes|nullable|string|max:255',
            'city' => 'sometimes|nullable|string|max:100',
            'state' => 'sometimes|nullable|string|max:100',
            'postal_code' => 'sometimes|nullable|string|max:20',
            'country' => 'sometimes|nullable|string|max:100',
            'notes' => 'sometimes|nullable|string',
        ]);

        $validated['updated_at'] = now();

        DB::table('dropshippers')
            ->where('id', $id)
            ->update($validated);

        $dropshipper = DB::table('dropshippers')->find($id);

        return response()->json([
            'success' => true,
            'message' => 'Dropshipper updated successfully',
            'data' => $dropshipper
        ]);
    }

    /**
     * Approve a pending dropshipper.
     */
    public function approve(int $id): JsonResponse
    {
        $dropshipper = DB::table('dropshippers')->find($id);

        if (!$dropshipper) {
            return response()->json([
                'success' => false,
                'message' => 'Dropshipper not found'
            ], 404);
        }

        if ($dropshipper->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Only pending dropshippers can be approved'
            ], 400);
        }

        DB::table('dropshippers')
            ->where('id', $id)
            ->update([
                'status' => 'active',
                'updated_at' => now()
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Dropshipper approved successfully'
        ]);
    }

    /**
     * Suspend/unsuspend a dropshipper.
     */
    public function toggleSuspend(int $id): JsonResponse
    {
        $dropshipper = DB::table('dropshippers')->find($id);

        if (!$dropshipper) {
            return response()->json([
                'success' => false,
                'message' => 'Dropshipper not found'
            ], 404);
        }

        $newStatus = $dropshipper->status === 'suspended' ? 'active' : 'suspended';

        DB::table('dropshippers')
            ->where('id', $id)
            ->update([
                'status' => $newStatus,
                'updated_at' => now()
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Dropshipper ' . ($newStatus === 'suspended' ? 'suspended' : 'reactivated') . ' successfully'
        ]);
    }

    /**
     * Regenerate API key for a dropshipper.
     */
    public function regenerateKey(int $id): JsonResponse
    {
        $dropshipper = DB::table('dropshippers')->find($id);

        if (!$dropshipper) {
            return response()->json([
                'success' => false,
                'message' => 'Dropshipper not found'
            ], 404);
        }

        $newApiKey = substr($dropshipper->company_name, 0, 2) . '_' . bin2hex(random_bytes(16));

        DB::table('dropshippers')
            ->where('id', $id)
            ->update([
                'api_key' => $newApiKey,
                'updated_at' => now()
            ]);

        return response()->json([
            'success' => true,
            'message' => 'API key regenerated successfully',
            'api_key' => $newApiKey
        ]);
    }

    /**
     * Delete a dropshipper.
     */
    public function destroy(int $id): JsonResponse
    {
        $dropshipper = DB::table('dropshippers')->find($id);

        if (!$dropshipper) {
            return response()->json([
                'success' => false,
                'message' => 'Dropshipper not found'
            ], 404);
        }

        // Delete related orders first
        DB::table('dropship_orders')->where('dropshipper_id', $id)->delete();
        DB::table('dropshippers')->where('id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Dropshipper deleted successfully'
        ]);
    }

    /**
     * Get all dropship orders.
     */
    public function orders(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 20);
        $page = $request->get('page', 1);

        $query = DB::table('dropship_orders')
            ->join('dropshippers', 'dropship_orders.dropshipper_id', '=', 'dropshippers.id')
            ->select(
                'dropship_orders.*',
                'dropshippers.company_name as dropshipper_name'
            )
            ->orderByDesc('dropship_orders.created_at');

        // Filter by dropshipper
        if ($request->has('dropshipper_id') && $request->dropshipper_id) {
            $query->where('dropship_orders.dropshipper_id', $request->dropshipper_id);
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('dropship_orders.status', $request->status);
        }

        // Search by order number
        if ($request->has('search') && $request->search) {
            $query->where('dropship_orders.order_number', 'like', "%{$request->search}%");
        }

        // Filter by date
        if ($request->has('date') && $request->date) {
            $query->whereDate('dropship_orders.created_at', $request->date);
        }

        // Get total count before pagination
        $total = $query->count();

        // Apply pagination
        $orders = $query
            ->offset(($page - 1) * $perPage)
            ->limit($perPage)
            ->get();

        // Get stats
        $stats = [
            'total' => DB::table('dropship_orders')->count(),
            'pending' => DB::table('dropship_orders')->where('status', 'pending')->count(),
            'in_transit' => DB::table('dropship_orders')->whereIn('status', ['processing', 'shipped'])->count(),
            'delivered' => DB::table('dropship_orders')->where('status', 'delivered')->count()
        ];

        // Get dropshippers for filter
        $dropshippers = DB::table('dropshippers')
            ->select('id', 'company_name')
            ->orderBy('company_name')
            ->get();

        // Calculate pagination meta
        $lastPage = ceil($total / $perPage);
        $from = $total > 0 ? (($page - 1) * $perPage) + 1 : 0;
        $to = min($page * $perPage, $total);

        return response()->json([
            'success' => true,
            'data' => $orders,
            'stats' => $stats,
            'dropshippers' => $dropshippers,
            'meta' => [
                'current_page' => (int) $page,
                'per_page' => (int) $perPage,
                'total' => $total,
                'last_page' => (int) $lastPage,
                'from' => $from,
                'to' => $to
            ]
        ]);
    }

    /**
     * Get a single dropship order.
     */
    public function showOrder(int $id): JsonResponse
    {
        $order = DB::table('dropship_orders')
            ->join('dropshippers', 'dropship_orders.dropshipper_id', '=', 'dropshippers.id')
            ->select(
                'dropship_orders.*',
                'dropshippers.company_name as dropshipper_name',
                'dropshippers.email as dropshipper_email'
            )
            ->where('dropship_orders.id', $id)
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }

        // Get order items
        $items = DB::table('dropship_order_items')
            ->where('order_id', $id)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $order,
            'items' => $items
        ]);
    }

    /**
     * Update order status.
     */
    public function updateOrderStatus(Request $request, int $id): JsonResponse
    {
        $order = DB::table('dropship_orders')->find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
            'tracking_number' => 'nullable|string|max:100',
            'carrier' => 'nullable|string|max:50',
        ]);

        $updateData = [
            'status' => $validated['status'],
            'updated_at' => now()
        ];

        if (isset($validated['tracking_number'])) {
            $updateData['tracking_number'] = $validated['tracking_number'];
        }

        if (isset($validated['carrier'])) {
            $updateData['carrier'] = $validated['carrier'];
        }

        if ($validated['status'] === 'shipped') {
            $updateData['shipped_at'] = now();
        } elseif ($validated['status'] === 'delivered') {
            $updateData['delivered_at'] = now();
        }

        DB::table('dropship_orders')
            ->where('id', $id)
            ->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully'
        ]);
    }
}
