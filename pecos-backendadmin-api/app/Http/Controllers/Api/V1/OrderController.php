<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Get all orders (admin) or user's orders.
     *
     * @OA\Get(
     *     path="/orders",
     *     summary="Get orders",
     *     tags={"Orders"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="user_id", in="query", @OA\Schema(type="integer"), description="Filter by user (admin only)"),
     *     @OA\Parameter(name="status", in="query", @OA\Schema(type="string")),
     *     @OA\Parameter(name="date_from", in="query", @OA\Schema(type="string", format="date")),
     *     @OA\Parameter(name="date_to", in="query", @OA\Schema(type="string", format="date")),
     *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer", default=20)),
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = Order::with('items');

        // Non-admin users can only see their own orders
        if (!$user->isManager()) {
            $query->where('user_id', $user->id);
        } else {
            // Admin filters
            if ($request->has('user_id')) {
                $query->where('user_id', $request->user_id);
            }
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }
            if ($request->has('date_from')) {
                $query->where('order_date', '>=', $request->date_from);
            }
            if ($request->has('date_to')) {
                $query->where('order_date', '<=', $request->date_to);
            }
        }

        $perPage = min($request->get('per_page', 20), 100);
        $orders = $query->recent()->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $orders->items(),
            'meta' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
            ]
        ]);
    }

    /**
     * Get a single order.
     *
     * @OA\Get(
     *     path="/orders/{id}",
     *     summary="Get order by ID",
     *     tags={"Orders"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="Order not found"),
     *     @OA\Response(response=403, description="Unauthorized")
     * )
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $order = Order::with(['items', 'user'])->find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }

        // Check authorization
        $user = $request->user();
        if (!$user->isManager() && $order->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $order
        ]);
    }

    /**
     * Create a new order.
     *
     * @OA\Post(
     *     path="/orders",
     *     summary="Create a new order",
     *     tags={"Orders"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"items", "customer_email", "customer_first_name", "customer_last_name", "billing_address1", "billing_city", "billing_state", "billing_zip", "shipping_address1", "shipping_city", "shipping_state", "shipping_zip"},
     *             @OA\Property(property="items", type="array", @OA\Items(
     *                 @OA\Property(property="product_id", type="string"),
     *                 @OA\Property(property="quantity", type="integer")
     *             )),
     *             @OA\Property(property="customer_email", type="string", format="email"),
     *             @OA\Property(property="customer_first_name", type="string"),
     *             @OA\Property(property="customer_last_name", type="string"),
     *             @OA\Property(property="billing_address1", type="string"),
     *             @OA\Property(property="billing_city", type="string"),
     *             @OA\Property(property="billing_state", type="string"),
     *             @OA\Property(property="billing_zip", type="string"),
     *             @OA\Property(property="shipping_address1", type="string"),
     *             @OA\Property(property="shipping_city", type="string"),
     *             @OA\Property(property="shipping_state", type="string"),
     *             @OA\Property(property="shipping_zip", type="string")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Order created"),
     *     @OA\Response(response=400, description="Invalid product")
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'customer_email' => 'required|email',
            'customer_phone' => 'nullable|string|max:20',
            'customer_first_name' => 'required|string|max:100',
            'customer_last_name' => 'required|string|max:100',
            'billing_address1' => 'required|string|max:255',
            'billing_address2' => 'nullable|string|max:255',
            'billing_city' => 'required|string|max:100',
            'billing_state' => 'required|string|max:2',
            'billing_zip' => 'required|string|max:10',
            'shipping_address1' => 'required|string|max:255',
            'shipping_address2' => 'nullable|string|max:255',
            'shipping_city' => 'required|string|max:100',
            'shipping_state' => 'required|string|max:2',
            'shipping_zip' => 'required|string|max:10',
            'order_notes' => 'nullable|string',
        ]);

        // Calculate totals and validate products
        $subtotal = 0;
        $orderItems = [];

        foreach ($validated['items'] as $item) {
            $product = Product::find($item['product_id']);
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => "Product {$item['product_id']} not found"
                ], 400);
            }

            $itemTotal = $product->Price * $item['quantity'];
            $subtotal += $itemTotal;

            $orderItems[] = [
                'product_id' => $product->UPC,
                'product_name' => $product->Description,
                'quantity' => $item['quantity'],
                'unit_price' => $product->Price,
                'total_price' => $itemTotal,
            ];
        }

        // Calculate tax and shipping (simplified)
        $taxRate = 0.0825; // 8.25% Texas tax
        $taxAmount = $subtotal * $taxRate;
        $shippingCost = $subtotal >= 100 ? 0 : 9.99;
        $totalAmount = $subtotal + $taxAmount + $shippingCost;

        // Create order
        $order = Order::create([
            'user_id' => $request->user()->id ?? null,
            'order_number' => 'PRT-' . strtoupper(Str::random(8)),
            'order_date' => now(),
            'customer_email' => $validated['customer_email'],
            'customer_phone' => $validated['customer_phone'] ?? null,
            'customer_first_name' => $validated['customer_first_name'],
            'customer_last_name' => $validated['customer_last_name'],
            'billing_address1' => $validated['billing_address1'],
            'billing_address2' => $validated['billing_address2'] ?? null,
            'billing_city' => $validated['billing_city'],
            'billing_state' => $validated['billing_state'],
            'billing_zip' => $validated['billing_zip'],
            'shipping_address1' => $validated['shipping_address1'],
            'shipping_address2' => $validated['shipping_address2'] ?? null,
            'shipping_city' => $validated['shipping_city'],
            'shipping_state' => $validated['shipping_state'],
            'shipping_zip' => $validated['shipping_zip'],
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'shipping_cost' => $shippingCost,
            'total_amount' => $totalAmount,
            'status' => 'pending',
            'order_notes' => $validated['order_notes'] ?? null,
        ]);

        // Create order items
        foreach ($orderItems as $item) {
            $order->items()->create($item);
        }

        // Deduct inventory
        foreach ($validated['items'] as $item) {
            $product = Product::find($item['product_id']);
            if ($product && $product->track_inventory) {
                $product->Qty_avail = max(0, ($product->Qty_avail ?? 0) - $item['quantity']);
                $product->save();
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Order created successfully',
            'data' => $order->load('items')
        ], 201);
    }

    /**
     * Update order status (admin only).
     *
     * @OA\Put(
     *     path="/orders/{id}",
     *     summary="Update order status (admin)",
     *     tags={"Orders"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"status"},
     *             @OA\Property(property="status", type="string", enum={"pending", "processing", "shipped", "delivered", "cancelled"}),
     *             @OA\Property(property="order_notes", type="string")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Order updated"),
     *     @OA\Response(response=404, description="Order not found")
     * )
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
            'order_notes' => 'nullable|string',
        ]);

        $order->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Order updated successfully',
            'data' => $order->fresh()
        ]);
    }

    /**
     * Cancel an order.
     *
     * @OA\Post(
     *     path="/orders/{id}/cancel",
     *     summary="Cancel an order",
     *     tags={"Orders"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Order cancelled"),
     *     @OA\Response(response=400, description="Order cannot be cancelled"),
     *     @OA\Response(response=404, description="Order not found")
     * )
     */
    public function cancel(Request $request, int $id): JsonResponse
    {
        $order = Order::with('items')->find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }

        // Check authorization
        $user = $request->user();
        if (!$user->isManager() && $order->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        // Can only cancel pending or processing orders
        if (!in_array($order->status, ['pending', 'processing'])) {
            return response()->json([
                'success' => false,
                'message' => 'Order cannot be cancelled'
            ], 400);
        }

        // Restore inventory
        foreach ($order->items as $item) {
            $product = Product::find($item->product_id);
            if ($product && $product->track_inventory) {
                $product->Qty_avail = ($product->Qty_avail ?? 0) + $item->quantity;
                $product->save();
            }
        }

        $order->status = 'cancelled';
        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'Order cancelled successfully',
            'data' => $order
        ]);
    }

    /**
     * Create a guest order (no authentication required).
     *
     * @OA\Post(
     *     path="/orders/guest",
     *     summary="Create a guest order",
     *     tags={"Orders"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"items", "customer_email", "customer_first_name", "customer_last_name", "customer_phone", "billing_address1", "billing_city", "billing_state", "billing_zip", "shipping_address1", "shipping_city", "shipping_state", "shipping_zip", "payment_method", "transaction_id"},
     *             @OA\Property(property="items", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="customer_email", type="string"),
     *             @OA\Property(property="customer_first_name", type="string"),
     *             @OA\Property(property="customer_last_name", type="string"),
     *             @OA\Property(property="customer_phone", type="string"),
     *             @OA\Property(property="billing_address1", type="string"),
     *             @OA\Property(property="billing_city", type="string"),
     *             @OA\Property(property="billing_state", type="string"),
     *             @OA\Property(property="billing_zip", type="string"),
     *             @OA\Property(property="shipping_address1", type="string"),
     *             @OA\Property(property="shipping_city", type="string"),
     *             @OA\Property(property="shipping_state", type="string"),
     *             @OA\Property(property="shipping_zip", type="string"),
     *             @OA\Property(property="payment_method", type="string"),
     *             @OA\Property(property="transaction_id", type="string")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Order created"),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
    public function storeGuest(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.upc' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.description' => 'required|string',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.total' => 'required|numeric|min:0',
            'customer_email' => 'required|email',
            'customer_phone' => 'required|string|max:20',
            'customer_first_name' => 'required|string|max:100',
            'customer_last_name' => 'required|string|max:100',
            'billing_address1' => 'required|string|max:255',
            'billing_address2' => 'nullable|string|max:255',
            'billing_city' => 'required|string|max:100',
            'billing_state' => 'required|string|max:2',
            'billing_zip' => 'required|string|max:10',
            'shipping_address1' => 'required|string|max:255',
            'shipping_address2' => 'nullable|string|max:255',
            'shipping_city' => 'required|string|max:100',
            'shipping_state' => 'required|string|max:2',
            'shipping_zip' => 'required|string|max:10',
            'order_notes' => 'nullable|string',
            'payment_method' => 'required|string',
            'transaction_id' => 'required|string',
            'subtotal' => 'required|numeric',
            'tax_amount' => 'required|numeric',
            'shipping_cost' => 'required|numeric',
            'total_amount' => 'required|numeric',
            'user_id' => 'nullable|integer',
        ]);

        try {
            DB::beginTransaction();

            // Generate order number
            $orderNumber = 'PRT-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));

            // Create order
            $order = Order::create([
                'user_id' => $validated['user_id'] ?? null,
                'order_number' => $orderNumber,
                'order_date' => now(),
                'customer_email' => $validated['customer_email'],
                'customer_phone' => $validated['customer_phone'],
                'customer_first_name' => $validated['customer_first_name'],
                'customer_last_name' => $validated['customer_last_name'],
                'billing_address1' => $validated['billing_address1'],
                'billing_address2' => $validated['billing_address2'] ?? null,
                'billing_city' => $validated['billing_city'],
                'billing_state' => $validated['billing_state'],
                'billing_zip' => $validated['billing_zip'],
                'shipping_address1' => $validated['shipping_address1'],
                'shipping_address2' => $validated['shipping_address2'] ?? null,
                'shipping_city' => $validated['shipping_city'],
                'shipping_state' => $validated['shipping_state'],
                'shipping_zip' => $validated['shipping_zip'],
                'subtotal' => $validated['subtotal'],
                'tax_amount' => $validated['tax_amount'],
                'shipping_cost' => $validated['shipping_cost'],
                'total_amount' => $validated['total_amount'],
                'status' => 'paid',
                'order_notes' => $validated['order_notes'] ?? null,
                'payment_method' => $validated['payment_method'],
                'transaction_id' => $validated['transaction_id'],
            ]);

            // Create order items
            foreach ($validated['items'] as $item) {
                // Try to get product_id from UPC
                $productId = null;
                $product = Product::where('UPC', $item['upc'])->first();
                if ($product) {
                    $productId = $product->ID;
                }

                $order->items()->create([
                    'product_id' => $productId,
                    'product_name' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'total_price' => $item['total'],
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order created successfully',
                'data' => [
                    'order_id' => $order->id,
                    'order_number' => $orderNumber,
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get order by order number (for confirmation page).
     *
     * @OA\Get(
     *     path="/orders/lookup/{orderNumber}",
     *     summary="Get order by order number",
     *     tags={"Orders"},
     *     @OA\Parameter(name="orderNumber", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="Order not found")
     * )
     */
    public function lookup(string $orderNumber): JsonResponse
    {
        $order = Order::with('items')->where('order_number', $orderNumber)->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $order
        ]);
    }

    /**
     * Get orders by user ID (for PHP session-based auth).
     *
     * @OA\Get(
     *     path="/orders/user/{userId}",
     *     summary="Get orders by user ID",
     *     tags={"Orders"},
     *     @OA\Parameter(name="userId", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function userOrders(int $userId): JsonResponse
    {
        $orders = Order::with(['items.product:id,UPC,Image,ShortDescription'])
            ->where('user_id', $userId)
            ->orderBy('order_date', 'desc')
            ->get()
            ->map(function ($order) {
                $order->item_count = $order->items->count();
                // Add product image to each item
                $order->items->transform(function ($item) {
                    if ($item->product) {
                        $item->product_image = $item->product->Image;
                        $item->product_upc = $item->product->UPC;
                    }
                    unset($item->product);
                    return $item;
                });
                return $order;
            });

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    /**
     * Get order by ID (public endpoint for session-based auth).
     */
    public function showById(int $id): JsonResponse
    {
        $order = Order::with(['items.product:id,UPC,Image,ShortDescription'])->find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }

        // Add product image to each item
        $order->items->transform(function ($item) {
            if ($item->product) {
                $item->product_image = $item->product->Image;
                $item->product_upc = $item->product->UPC;
            }
            unset($item->product);
            return $item;
        });

        return response()->json([
            'success' => true,
            'data' => $order
        ]);
    }

    /**
     * Get buy again items for a user.
     */
    public function buyAgainItems(int $userId): JsonResponse
    {
        $items = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->leftJoin('products3', 'order_items.product_id', '=', 'products3.ID')
            ->where('orders.user_id', $userId)
            ->select(
                'order_items.product_id',
                'order_items.product_name',
                'products3.UPC',
                'products3.ShortDescription',
                'products3.UnitPrice',
                'products3.Image',
                'products3.ItemSize',
                DB::raw('COUNT(*) as purchase_count'),
                DB::raw('MAX(orders.order_date) as last_purchased')
            )
            ->groupBy('order_items.product_id', 'order_items.product_name', 'products3.UPC',
                     'products3.ShortDescription', 'products3.UnitPrice', 'products3.Image', 'products3.ItemSize')
            ->orderBy('purchase_count', 'desc')
            ->orderBy('last_purchased', 'desc')
            ->limit(50)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $items
        ]);
    }
}
