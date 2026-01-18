<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{
    /**
     * Get all suppliers with stats.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Supplier::query()
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
                  ->orWhere('contact_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Pagination
        $perPage = $request->get('per_page', 20);
        $suppliers = $query->paginate($perPage);

        // Get stats
        $stats = [
            'total' => Supplier::count(),
            'active' => Supplier::active()->count(),
            'inactive' => Supplier::inactive()->count(),
            'pending' => Supplier::pending()->count(),
            'total_orders' => PurchaseOrder::whereNotNull('supplier_id')->count(),
            'total_amount' => Supplier::sum('total_amount')
        ];

        return response()->json([
            'success' => true,
            'data' => $suppliers->items(),
            'stats' => $stats,
            'meta' => [
                'current_page' => $suppliers->currentPage(),
                'per_page' => $suppliers->perPage(),
                'total' => $suppliers->total(),
                'last_page' => $suppliers->lastPage(),
                'from' => $suppliers->firstItem(),
                'to' => $suppliers->lastItem()
            ]
        ]);
    }

    /**
     * Get a single supplier with purchase orders.
     */
    public function show(int $id): JsonResponse
    {
        $supplier = Supplier::with(['purchaseOrders' => function ($q) {
            $q->orderByDesc('created_at')->limit(10);
        }])->find($id);

        if (!$supplier) {
            return response()->json([
                'success' => false,
                'message' => 'Supplier not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $supplier,
            'purchase_orders' => $supplier->purchaseOrders
        ]);
    }

    /**
     * Create a new supplier.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'contact_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'tax_id' => 'nullable|string|max:50',
            'payment_terms' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'status' => 'nullable|in:active,inactive,pending',
        ]);

        $validated['status'] = $validated['status'] ?? 'active';

        $supplier = Supplier::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Supplier created successfully',
            'data' => $supplier
        ], 201);
    }

    /**
     * Update a supplier.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $supplier = Supplier::find($id);

        if (!$supplier) {
            return response()->json([
                'success' => false,
                'message' => 'Supplier not found'
            ], 404);
        }

        $validated = $request->validate([
            'company_name' => 'sometimes|required|string|max:255',
            'contact_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'tax_id' => 'nullable|string|max:50',
            'payment_terms' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'status' => 'nullable|in:active,inactive,pending',
        ]);

        $supplier->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Supplier updated successfully',
            'data' => $supplier->fresh()
        ]);
    }

    /**
     * Update supplier status.
     */
    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $supplier = Supplier::find($id);

        if (!$supplier) {
            return response()->json([
                'success' => false,
                'message' => 'Supplier not found'
            ], 404);
        }

        $validated = $request->validate([
            'status' => 'required|in:active,inactive,pending',
        ]);

        $supplier->update(['status' => $validated['status']]);

        return response()->json([
            'success' => true,
            'message' => 'Supplier status updated successfully',
            'data' => $supplier->fresh()
        ]);
    }

    /**
     * Delete a supplier.
     */
    public function destroy(int $id): JsonResponse
    {
        $supplier = Supplier::find($id);

        if (!$supplier) {
            return response()->json([
                'success' => false,
                'message' => 'Supplier not found'
            ], 404);
        }

        // Check if supplier has purchase orders using relationship
        if ($supplier->purchaseOrders()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete supplier with existing purchase orders. Set status to inactive instead.'
            ], 400);
        }

        $supplier->delete();

        return response()->json([
            'success' => true,
            'message' => 'Supplier deleted successfully'
        ]);
    }

    /**
     * Get supplier statistics.
     */
    public function stats(): JsonResponse
    {
        $totalOrders = PurchaseOrder::whereNotNull('supplier_id')->count();
        $totalAmount = (float) Supplier::sum('total_amount');

        $stats = [
            'total' => Supplier::count(),
            'active' => Supplier::active()->count(),
            'inactive' => Supplier::inactive()->count(),
            'pending' => Supplier::pending()->count(),
            'total_orders' => $totalOrders,
            'total_amount' => $totalAmount,
            'average_order_value' => $totalOrders > 0 ? $totalAmount / $totalOrders : 0
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}
