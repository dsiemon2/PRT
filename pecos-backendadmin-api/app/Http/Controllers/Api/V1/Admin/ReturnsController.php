<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReturnRequest;
use App\Models\ReturnItem;
use App\Models\ReturnReason;
use App\Models\ReturnPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReturnsController extends Controller
{
    /**
     * Get all returns with filters
     */
    public function index(Request $request)
    {
        $query = DB::table('returns')
            ->leftJoin('orders', 'returns.order_id', '=', 'orders.id')
            ->leftJoin('customers', 'returns.customer_id', '=', 'customers.id')
            ->leftJoin('return_reasons', 'returns.reason_id', '=', 'return_reasons.id')
            ->leftJoin(DB::raw('(SELECT return_id, COUNT(*) as item_count FROM return_items GROUP BY return_id) as ri'), 'returns.id', '=', 'ri.return_id')
            ->select(
                'returns.*',
                'orders.order_number',
                'customers.first_name',
                'customers.last_name',
                'customers.email as customer_email',
                'return_reasons.name as reason_name',
                DB::raw('COALESCE(ri.item_count, 0) as item_count')
            )
            ->whereNull('returns.deleted_at');

        // Apply filters
        if ($request->filled('status')) {
            $query->where('returns.status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('returns.type', $request->type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('returns.created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('returns.created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('returns.rma_number', 'like', "%{$search}%")
                  ->orWhere('orders.order_number', 'like', "%{$search}%")
                  ->orWhere('customers.email', 'like', "%{$search}%")
                  ->orWhere('customers.first_name', 'like', "%{$search}%")
                  ->orWhere('customers.last_name', 'like', "%{$search}%");
            });
        }

        $perPage = $request->input('per_page', 20);
        $returns = $query->orderBy('returns.created_at', 'desc')
                         ->paginate($perPage);

        return response()->json($returns);
    }

    /**
     * Get return details
     */
    public function show($id)
    {
        $return = DB::table('returns')
            ->leftJoin('orders', 'returns.order_id', '=', 'orders.id')
            ->leftJoin('customers', 'returns.customer_id', '=', 'customers.id')
            ->leftJoin('return_reasons', 'returns.reason_id', '=', 'return_reasons.id')
            ->leftJoin('users as processor', 'returns.processed_by', '=', 'processor.id')
            ->select(
                'returns.*',
                'orders.order_number',
                'orders.total_amount as order_total',
                'customers.first_name',
                'customers.last_name',
                'customers.email as customer_email',
                'customers.phone as customer_phone',
                'return_reasons.name as reason_name',
                'return_reasons.code as reason_code',
                'processor.name as processed_by_name'
            )
            ->where('returns.id', $id)
            ->whereNull('returns.deleted_at')
            ->first();

        if (!$return) {
            return response()->json(['error' => 'Return not found'], 404);
        }

        // Get return items
        $items = DB::table('return_items')
            ->where('return_id', $id)
            ->get();

        // Get return photos
        $photos = DB::table('return_photos')
            ->where('return_id', $id)
            ->get();

        // Get status history
        $history = DB::table('return_status_history')
            ->leftJoin('users', 'return_status_history.changed_by', '=', 'users.id')
            ->where('return_id', $id)
            ->select('return_status_history.*', 'users.name as changed_by_name')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'return' => $return,
                'items' => $items,
                'photos' => $photos,
                'history' => $history
            ]
        ]);
    }

    /**
     * Create a new return request
     */
    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|integer|exists:orders,id',
            'reason_id' => 'required|integer|exists:return_reasons,id',
            'type' => 'required|in:refund,exchange,store_credit',
            'customer_notes' => 'nullable|string',
            'admin_notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_upc' => 'required|string',
            'items.*.product_name' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.condition' => 'required|in:unopened,like_new,good,fair,damaged,defective',
        ]);

        $order = DB::table('orders')->where('id', $request->order_id)->first();
        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        DB::beginTransaction();
        try {
            // Create return
            $returnId = DB::table('returns')->insertGetId([
                'rma_number' => ReturnRequest::generateRmaNumber(),
                'order_id' => $request->order_id,
                'customer_id' => $order->user_id ?? $order->customer_id,
                'reason_id' => $request->reason_id,
                'status' => 'pending',
                'type' => $request->type,
                'customer_notes' => $request->customer_notes,
                'admin_notes' => $request->admin_notes,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Create return items
            $totalRefund = 0;
            foreach ($request->items as $item) {
                $refundAmount = $item['unit_price'] * $item['quantity'];
                $totalRefund += $refundAmount;

                DB::table('return_items')->insert([
                    'return_id' => $returnId,
                    'order_item_id' => $item['order_item_id'] ?? null,
                    'product_upc' => $item['product_upc'],
                    'product_name' => $item['product_name'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'refund_amount' => $refundAmount,
                    'condition' => $item['condition'],
                    'condition_notes' => $item['condition_notes'] ?? null,
                    'restock' => $item['restock'] ?? true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Update return with total refund amount
            DB::table('returns')->where('id', $returnId)->update([
                'refund_amount' => $totalRefund
            ]);

            // Add status history
            DB::table('return_status_history')->insert([
                'return_id' => $returnId,
                'old_status' => null,
                'new_status' => 'pending',
                'notes' => 'Return request created',
                'changed_by' => $request->user() ? $request->user()->id : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Return request created',
                'data' => ['id' => $returnId]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to create return: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update return status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected,received,inspecting,processed,refunded,exchanged,closed',
            'notes' => 'nullable|string'
        ]);

        $return = DB::table('returns')->where('id', $id)->whereNull('deleted_at')->first();
        if (!$return) {
            return response()->json(['error' => 'Return not found'], 404);
        }

        $updates = [
            'status' => $request->status,
            'updated_at' => now()
        ];

        // Set timestamps based on status
        if ($request->status === 'approved' && !$return->approved_at) {
            $updates['approved_at'] = now();
        }
        if ($request->status === 'received' && !$return->received_at) {
            $updates['received_at'] = now();
        }
        if (in_array($request->status, ['processed', 'refunded', 'exchanged']) && !$return->processed_at) {
            $updates['processed_at'] = now();
            $updates['processed_by'] = $request->user() ? $request->user()->id : null;
        }

        DB::table('returns')->where('id', $id)->update($updates);

        // Add to status history
        DB::table('return_status_history')->insert([
            'return_id' => $id,
            'old_status' => $return->status,
            'new_status' => $request->status,
            'notes' => $request->notes,
            'changed_by' => $request->user() ? $request->user()->id : null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Return status updated']);
    }

    /**
     * Add note to return
     */
    public function addNote(Request $request, $id)
    {
        $request->validate([
            'notes' => 'required|string'
        ]);

        $return = DB::table('returns')->where('id', $id)->whereNull('deleted_at')->first();
        if (!$return) {
            return response()->json(['error' => 'Return not found'], 404);
        }

        DB::table('return_status_history')->insert([
            'return_id' => $id,
            'old_status' => $return->status,
            'new_status' => $return->status,
            'notes' => $request->notes,
            'changed_by' => $request->user() ? $request->user()->id : null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Note added']);
    }

    /**
     * Update refund details
     */
    public function updateRefund(Request $request, $id)
    {
        $request->validate([
            'refund_amount' => 'nullable|numeric|min:0',
            'restocking_fee' => 'nullable|numeric|min:0',
            'refund_method' => 'nullable|string'
        ]);

        $return = DB::table('returns')->where('id', $id)->whereNull('deleted_at')->first();
        if (!$return) {
            return response()->json(['error' => 'Return not found'], 404);
        }

        DB::table('returns')->where('id', $id)->update([
            'refund_amount' => $request->refund_amount ?? $return->refund_amount,
            'restocking_fee' => $request->restocking_fee ?? $return->restocking_fee,
            'refund_method' => $request->refund_method ?? $return->refund_method,
            'updated_at' => now()
        ]);

        return response()->json(['success' => true, 'message' => 'Refund details updated']);
    }

    /**
     * Add tracking number
     */
    public function addTracking(Request $request, $id)
    {
        $request->validate([
            'tracking_number' => 'required|string',
            'return_label_url' => 'nullable|string|url'
        ]);

        $return = DB::table('returns')->where('id', $id)->whereNull('deleted_at')->first();
        if (!$return) {
            return response()->json(['error' => 'Return not found'], 404);
        }

        DB::table('returns')->where('id', $id)->update([
            'tracking_number' => $request->tracking_number,
            'return_label_url' => $request->return_label_url,
            'updated_at' => now()
        ]);

        return response()->json(['success' => true, 'message' => 'Tracking information updated']);
    }

    /**
     * Get return statistics for dashboard
     */
    public function stats()
    {
        $today = now()->startOfDay();
        $thisWeek = now()->subDays(7)->startOfDay();
        $thisMonth = now()->startOfMonth();

        $totalRefunds = DB::table('returns')
            ->whereIn('status', ['refunded', 'processed'])
            ->whereNull('deleted_at')
            ->sum('refund_amount') ?? 0;

        $totalReturns = DB::table('returns')->whereNull('deleted_at')->count();

        return response()->json([
            'total_returns' => $totalReturns,
            'today_returns' => DB::table('returns')->whereDate('created_at', $today)->whereNull('deleted_at')->count(),
            'week_returns' => DB::table('returns')->where('created_at', '>=', $thisWeek)->whereNull('deleted_at')->count(),
            'month_returns' => DB::table('returns')->where('created_at', '>=', $thisMonth)->whereNull('deleted_at')->count(),
            'pending_returns' => DB::table('returns')->where('status', 'pending')->whereNull('deleted_at')->count(),
            'approved_returns' => DB::table('returns')->where('status', 'approved')->whereNull('deleted_at')->count(),
            'processing_returns' => DB::table('returns')->whereIn('status', ['received', 'inspecting'])->whereNull('deleted_at')->count(),
            'total_refunds' => $totalRefunds,
            'avg_refund_amount' => $totalReturns > 0 ? $totalRefunds / $totalReturns : 0,
            'by_status' => DB::table('returns')
                ->select('status', DB::raw('COUNT(*) as count'))
                ->whereNull('deleted_at')
                ->groupBy('status')
                ->get(),
            'by_type' => DB::table('returns')
                ->select('type', DB::raw('COUNT(*) as count'))
                ->whereNull('deleted_at')
                ->groupBy('type')
                ->get(),
            'by_reason' => DB::table('returns')
                ->leftJoin('return_reasons', 'returns.reason_id', '=', 'return_reasons.id')
                ->select('return_reasons.name', DB::raw('COUNT(*) as count'))
                ->whereNull('returns.deleted_at')
                ->groupBy('returns.reason_id', 'return_reasons.name')
                ->get()
        ]);
    }

    /**
     * Get all return reasons
     */
    public function reasons()
    {
        $reasons = DB::table('return_reasons')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $reasons
        ]);
    }

    /**
     * Create a new return reason
     */
    public function storeReason(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:return_reasons,code',
            'description' => 'nullable|string',
            'requires_photo' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer'
        ]);

        $id = DB::table('return_reasons')->insertGetId([
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
            'requires_photo' => $request->requires_photo ?? false,
            'is_active' => $request->is_active ?? true,
            'sort_order' => $request->sort_order ?? 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Return reason created',
            'data' => ['id' => $id]
        ], 201);
    }

    /**
     * Update a return reason
     */
    public function updateReason(Request $request, $id)
    {
        $request->validate([
            'name' => 'string|max:255',
            'code' => 'string|max:50|unique:return_reasons,code,' . $id,
            'description' => 'nullable|string',
            'requires_photo' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer'
        ]);

        $reason = DB::table('return_reasons')->where('id', $id)->first();
        if (!$reason) {
            return response()->json(['error' => 'Return reason not found'], 404);
        }

        DB::table('return_reasons')->where('id', $id)->update([
            'name' => $request->name ?? $reason->name,
            'code' => $request->code ?? $reason->code,
            'description' => $request->description ?? $reason->description,
            'requires_photo' => $request->requires_photo ?? $reason->requires_photo,
            'is_active' => $request->is_active ?? $reason->is_active,
            'sort_order' => $request->sort_order ?? $reason->sort_order,
            'updated_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Return reason updated']);
    }

    /**
     * Delete a return reason
     */
    public function destroyReason($id)
    {
        $reason = DB::table('return_reasons')->where('id', $id)->first();
        if (!$reason) {
            return response()->json(['error' => 'Return reason not found'], 404);
        }

        // Check if reason is in use
        $inUse = DB::table('returns')->where('reason_id', $id)->exists();
        if ($inUse) {
            // Soft deactivate instead of delete
            DB::table('return_reasons')->where('id', $id)->update([
                'is_active' => false,
                'updated_at' => now()
            ]);
            return response()->json(['success' => true, 'message' => 'Return reason deactivated (in use by existing returns)']);
        }

        DB::table('return_reasons')->where('id', $id)->delete();
        return response()->json(['success' => true, 'message' => 'Return reason deleted']);
    }

    /**
     * Soft delete a return
     */
    public function destroy($id)
    {
        $return = DB::table('returns')->where('id', $id)->whereNull('deleted_at')->first();
        if (!$return) {
            return response()->json(['error' => 'Return not found'], 404);
        }

        DB::table('returns')->where('id', $id)->update([
            'deleted_at' => now()
        ]);

        return response()->json(['success' => true, 'message' => 'Return deleted']);
    }
}
