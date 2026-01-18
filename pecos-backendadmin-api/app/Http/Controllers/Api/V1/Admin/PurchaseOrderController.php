<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PurchaseOrderController extends Controller
{
    /**
     * Get all purchase orders with filtering and pagination
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = min($request->get('per_page', 20), 100);
            $status = $request->get('status');
            $supplier = $request->get('supplier');
            $dateFrom = $request->get('date_from');
            $dateTo = $request->get('date_to');

            $query = DB::connection('mysql')->table('purchase_orders as po')
                ->select(
                    'po.*',
                    's.company_name as supplier_company',
                    's.status as supplier_status',
                    'd.company_name as dropshipper_company',
                    'd.status as dropshipper_status'
                )
                ->leftJoin('suppliers as s', 'po.supplier_id', '=', 's.id')
                ->leftJoin('dropshippers as d', 'po.dropshipper_id', '=', 'd.id');

            if ($status) {
                $query->where('po.status', $status);
            }

            if ($supplier) {
                $query->where(function($q) use ($supplier) {
                    $q->where('po.supplier_name', 'LIKE', "%$supplier%")
                      ->orWhere('s.company_name', 'LIKE', "%$supplier%")
                      ->orWhere('d.company_name', 'LIKE', "%$supplier%");
                });
            }

            if ($dateFrom) {
                $query->where('po.order_date', '>=', $dateFrom);
            }

            if ($dateTo) {
                $query->where('po.order_date', '<=', $dateTo);
            }

            $total = $query->count();
            $currentPage = max(1, (int)$request->get('page', 1));
            $offset = ($currentPage - 1) * $perPage;

            $orders = $query->orderBy('po.created_at', 'desc')
                ->offset($offset)
                ->limit($perPage)
                ->get();

            // Get item counts for each PO
            foreach ($orders as $order) {
                $itemsCount = DB::connection('mysql')->table('purchase_order_items')
                    ->where('purchase_order_id', $order->id)
                    ->count();
                $order->items_count = $itemsCount;

                // Calculate received percentage
                $items = DB::connection('mysql')->table('purchase_order_items')
                    ->where('purchase_order_id', $order->id)
                    ->select('quantity_ordered', 'quantity_received')
                    ->get();

                $totalOrdered = $items->sum('quantity_ordered');
                $totalReceived = $items->sum('quantity_received');
                $order->received_percentage = $totalOrdered > 0 ? round(($totalReceived / $totalOrdered) * 100) : 0;

                // Add supplier type indicator
                if ($order->supplier_id) {
                    $order->supplier_type = 'supplier';
                    $order->supplier_display = $order->supplier_company ?? $order->supplier_name;
                } else if ($order->dropshipper_id) {
                    $order->supplier_type = 'dropshipper';
                    $order->supplier_display = $order->dropshipper_company ?? $order->supplier_name;
                } else {
                    $order->supplier_type = 'one-time';
                    $order->supplier_display = $order->supplier_name;
                }
            }

            return response()->json([
                'success' => true,
                'data' => $orders,
                'meta' => [
                    'current_page' => $currentPage,
                    'per_page' => $perPage,
                    'total' => $total,
                    'last_page' => ceil($total / $perPage),
                    'from' => $offset + 1,
                    'to' => min($offset + $perPage, $total)
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving purchase orders',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get purchase order statistics
     */
    public function stats(): JsonResponse
    {
        try {
            $totalPOs = DB::connection('mysql')->table('purchase_orders')->count();
            $draftPOs = DB::connection('mysql')->table('purchase_orders')->where('status', 'draft')->count();
            $orderedPOs = DB::connection('mysql')->table('purchase_orders')->where('status', 'ordered')->count();
            $shippedPOs = DB::connection('mysql')->table('purchase_orders')->where('status', 'shipped')->count();
            $receivedPOs = DB::connection('mysql')->table('purchase_orders')->where('status', 'received')->count();
            $totalValue = DB::connection('mysql')->table('purchase_orders')->sum('total_cost');

            // Pending orders (ordered + shipped + partially received)
            $pendingPOs = DB::connection('mysql')->table('purchase_orders')
                ->whereIn('status', ['ordered', 'shipped', 'partially_received'])
                ->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'total_purchase_orders' => $totalPOs,
                    'draft' => $draftPOs,
                    'ordered' => $orderedPOs,
                    'shipped' => $shippedPOs,
                    'received' => $receivedPOs,
                    'pending' => $pendingPOs,
                    'total_value' => round($totalValue, 2)
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving stats',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get single purchase order with items
     */
    public function show($id): JsonResponse
    {
        try {
            $po = DB::connection('mysql')->table('purchase_orders')
                ->where('id', $id)
                ->first();

            if (!$po) {
                return response()->json([
                    'success' => false,
                    'message' => 'Purchase order not found'
                ], 404);
            }

            // Get supplier/dropshipper details if linked
            if ($po->supplier_id) {
                $supplier = DB::connection('mysql')->table('suppliers')
                    ->where('id', $po->supplier_id)
                    ->first();
                $po->supplier_details = $supplier;
                $po->supplier_type = 'supplier';
            } else if ($po->dropshipper_id) {
                $dropshipper = DB::connection('mysql')->table('dropshippers')
                    ->where('id', $po->dropshipper_id)
                    ->first();
                $po->supplier_details = $dropshipper;
                $po->supplier_type = 'dropshipper';
            } else {
                $po->supplier_details = null;
                $po->supplier_type = 'one-time';
            }

            // Get PO items with product info
            $items = DB::connection('mysql')->table('purchase_order_items as poi')
                ->leftJoin('products3 as p', 'poi.product_id', '=', 'p.id')
                ->where('poi.purchase_order_id', $id)
                ->select(
                    'poi.*',
                    'p.UPC',
                    'p.stock_quantity as current_stock'
                )
                ->get();

            $po->items = $items;

            return response()->json([
                'success' => true,
                'data' => $po
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving purchase order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create new purchase order
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'supplier_id' => 'nullable|integer|exists:suppliers,id',
            'dropshipper_id' => 'nullable|integer|exists:dropshippers,id',
            'supplier_name' => 'required|string|max:255',
            'supplier_email' => 'nullable|email',
            'order_date' => 'required|date',
            'expected_delivery_date' => 'nullable|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer',
            'items.*.quantity_ordered' => 'required|integer|min:1',
            'items.*.unit_cost' => 'required|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::connection('mysql')->beginTransaction();

            // Generate PO number
            $poNumber = 'PO-' . date('Ymd') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);

            // Calculate totals
            $subtotal = 0;
            foreach ($request->items as $item) {
                $subtotal += $item['quantity_ordered'] * $item['unit_cost'];
            }

            $shippingCost = $request->input('shipping_cost', 0);
            $tax = $request->input('tax', 0);
            $totalCost = $subtotal + $shippingCost + $tax;

            // Create PO
            $poData = [
                'po_number' => $poNumber,
                'supplier_name' => $request->supplier_name,
                'supplier_email' => $request->supplier_email,
                'supplier_phone' => $request->input('supplier_phone'),
                'supplier_address' => $request->input('supplier_address'),
                'order_date' => $request->order_date,
                'expected_delivery_date' => $request->expected_delivery_date,
                'status' => 'draft',
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'tax' => $tax,
                'total_cost' => $totalCost,
                'notes' => $request->input('notes'),
                'created_at' => now(),
                'updated_at' => now()
            ];

            // Add supplier_id or dropshipper_id if provided
            if ($request->has('supplier_id') && $request->supplier_id) {
                $poData['supplier_id'] = $request->supplier_id;
            }
            if ($request->has('dropshipper_id') && $request->dropshipper_id) {
                $poData['dropshipper_id'] = $request->dropshipper_id;
            }

            $poId = DB::connection('mysql')->table('purchase_orders')->insertGetId($poData);

            // Update supplier statistics if supplier_id provided
            if (!empty($poData['supplier_id'])) {
                DB::connection('mysql')->table('suppliers')
                    ->where('id', $poData['supplier_id'])
                    ->increment('total_orders');
                DB::connection('mysql')->table('suppliers')
                    ->where('id', $poData['supplier_id'])
                    ->increment('total_amount', $totalCost);
            }

            // Create PO items
            foreach ($request->items as $item) {
                // Get product info
                $product = DB::connection('mysql')->table('products3')
                    ->where('id', $item['product_id'])
                    ->first();

                if (!$product) {
                    DB::connection('mysql')->rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => "Product ID {$item['product_id']} not found"
                    ], 404);
                }

                $lineTotal = $item['quantity_ordered'] * $item['unit_cost'];

                DB::connection('mysql')->table('purchase_order_items')->insert([
                    'purchase_order_id' => $poId,
                    'product_id' => $item['product_id'],
                    'product_name' => $product->ShortDescription,
                    'sku' => $product->UPC,
                    'quantity_ordered' => $item['quantity_ordered'],
                    'quantity_received' => 0,
                    'unit_cost' => $item['unit_cost'],
                    'line_total' => $lineTotal,
                    'notes' => $item['notes'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            DB::connection('mysql')->commit();

            return response()->json([
                'success' => true,
                'message' => 'Purchase order created successfully',
                'data' => [
                    'id' => $poId,
                    'po_number' => $poNumber
                ]
            ], 201);
        } catch (\Exception $e) {
            DB::connection('mysql')->rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error creating purchase order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update purchase order
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $po = DB::connection('mysql')->table('purchase_orders')
                ->where('id', $id)
                ->first();

            if (!$po) {
                return response()->json([
                    'success' => false,
                    'message' => 'Purchase order not found'
                ], 404);
            }

            $updateData = [];

            if ($request->has('supplier_name')) $updateData['supplier_name'] = $request->supplier_name;
            if ($request->has('supplier_email')) $updateData['supplier_email'] = $request->supplier_email;
            if ($request->has('supplier_phone')) $updateData['supplier_phone'] = $request->supplier_phone;
            if ($request->has('supplier_address')) $updateData['supplier_address'] = $request->supplier_address;
            if ($request->has('expected_delivery_date')) $updateData['expected_delivery_date'] = $request->expected_delivery_date;
            if ($request->has('notes')) $updateData['notes'] = $request->notes;

            if (!empty($updateData)) {
                $updateData['updated_at'] = now();
                DB::connection('mysql')->table('purchase_orders')
                    ->where('id', $id)
                    ->update($updateData);
            }

            return response()->json([
                'success' => true,
                'message' => 'Purchase order updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating purchase order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update purchase order status
     */
    public function updateStatus(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:draft,ordered,shipped,partially_received,received,cancelled'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $updated = DB::connection('mysql')->table('purchase_orders')
                ->where('id', $id)
                ->update([
                    'status' => $request->status,
                    'updated_at' => now()
                ]);

            if (!$updated) {
                return response()->json([
                    'success' => false,
                    'message' => 'Purchase order not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Receive items from purchase order
     */
    public function receive(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'items' => 'required|array|min:1',
            'items.*.purchase_order_item_id' => 'required|integer',
            'items.*.quantity_received' => 'required|integer|min:1',
            'items.*.condition' => 'nullable|in:good,damaged,defective'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::connection('mysql')->beginTransaction();

            $po = DB::connection('mysql')->table('purchase_orders')
                ->where('id', $id)
                ->first();

            if (!$po) {
                return response()->json([
                    'success' => false,
                    'message' => 'Purchase order not found'
                ], 404);
            }

            foreach ($request->items as $item) {
                $poItem = DB::connection('mysql')->table('purchase_order_items')
                    ->where('id', $item['purchase_order_item_id'])
                    ->where('purchase_order_id', $id)
                    ->first();

                if (!$poItem) {
                    DB::connection('mysql')->rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => "Item ID {$item['purchase_order_item_id']} not found"
                    ], 404);
                }

                $qtyToReceive = $item['quantity_received'];
                $condition = $item['condition'] ?? 'good';

                // Update purchase_order_items
                DB::connection('mysql')->table('purchase_order_items')
                    ->where('id', $item['purchase_order_item_id'])
                    ->update([
                        'quantity_received' => DB::raw("quantity_received + $qtyToReceive"),
                        'updated_at' => now()
                    ]);

                // Log in purchase_order_receiving
                DB::connection('mysql')->table('purchase_order_receiving')->insert([
                    'purchase_order_id' => $id,
                    'purchase_order_item_id' => $item['purchase_order_item_id'],
                    'product_id' => $poItem->product_id,
                    'quantity_received' => $qtyToReceive,
                    'received_date' => now(),
                    'condition' => $condition,
                    'notes' => $item['notes'] ?? null,
                    'created_at' => now()
                ]);

                // Update product stock (only if condition is good)
                if ($condition === 'good') {
                    DB::connection('mysql')->table('products3')
                        ->where('id', $poItem->product_id)
                        ->update([
                            'stock_quantity' => DB::raw("stock_quantity + $qtyToReceive"),
                            'updated_at' => now()
                        ]);

                    // Log inventory transaction
                    $product = DB::connection('mysql')->table('products3')
                        ->where('id', $poItem->product_id)
                        ->first();

                    DB::connection('mysql')->table('inventory_transactions')->insert([
                        'product_id' => $poItem->product_id,
                        'transaction_type' => 'purchase',
                        'quantity_change' => $qtyToReceive,
                        'quantity_before' => $product->stock_quantity - $qtyToReceive,
                        'quantity_after' => $product->stock_quantity,
                        'reference_type' => 'purchase_order',
                        'reference_id' => $id,
                        'notes' => "Received from PO {$po->po_number}",
                        'created_at' => now()
                    ]);
                }
            }

            // Check if PO is fully received
            $items = DB::connection('mysql')->table('purchase_order_items')
                ->where('purchase_order_id', $id)
                ->get();

            $fullyReceived = true;
            $partiallyReceived = false;

            foreach ($items as $item) {
                if ($item->quantity_received < $item->quantity_ordered) {
                    $fullyReceived = false;
                }
                if ($item->quantity_received > 0) {
                    $partiallyReceived = true;
                }
            }

            // Update PO status
            $newStatus = $fullyReceived ? 'received' : ($partiallyReceived ? 'partially_received' : $po->status);

            $updateData = ['status' => $newStatus, 'updated_at' => now()];
            if ($fullyReceived) {
                $updateData['actual_delivery_date'] = now()->toDateString();
            }

            DB::connection('mysql')->table('purchase_orders')
                ->where('id', $id)
                ->update($updateData);

            DB::connection('mysql')->commit();

            return response()->json([
                'success' => true,
                'message' => 'Items received successfully',
                'data' => [
                    'status' => $newStatus,
                    'fully_received' => $fullyReceived
                ]
            ]);
        } catch (\Exception $e) {
            DB::connection('mysql')->rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error receiving items',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get pending purchase orders for receiving
     */
    public function pendingForReceiving(): JsonResponse
    {
        try {
            $orders = DB::connection('mysql')->table('purchase_orders')
                ->whereIn('status', ['ordered', 'shipped', 'partially_received'])
                ->orderBy('expected_delivery_date', 'asc')
                ->get();

            // Get items for each order
            foreach ($orders as $order) {
                $items = DB::connection('mysql')->table('purchase_order_items as poi')
                    ->leftJoin('products3 as p', 'poi.product_id', '=', 'p.id')
                    ->where('poi.purchase_order_id', $order->id)
                    ->select(
                        'poi.*',
                        'p.UPC',
                        'p.ShortDescription',
                        DB::raw('(poi.quantity_ordered - poi.quantity_received) as remaining')
                    )
                    ->having('remaining', '>', 0)
                    ->get();

                $order->items = $items;
                $order->pending_items_count = count($items);
            }

            return response()->json([
                'success' => true,
                'data' => $orders
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving pending orders',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete purchase order (only if draft or cancelled)
     */
    public function destroy($id): JsonResponse
    {
        try {
            $po = DB::connection('mysql')->table('purchase_orders')
                ->where('id', $id)
                ->first();

            if (!$po) {
                return response()->json([
                    'success' => false,
                    'message' => 'Purchase order not found'
                ], 404);
            }

            if (!in_array($po->status, ['draft', 'cancelled'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Can only delete draft or cancelled purchase orders'
                ], 400);
            }

            // Cascading delete will handle PO items
            DB::connection('mysql')->table('purchase_orders')
                ->where('id', $id)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Purchase order deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting purchase order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get list of suppliers from existing POs
     */
    public function suppliers(): JsonResponse
    {
        try {
            $suppliers = DB::connection('mysql')->table('purchase_orders')
                ->select('supplier_name', 'supplier_email', 'supplier_phone')
                ->distinct()
                ->orderBy('supplier_name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $suppliers
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving suppliers',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
