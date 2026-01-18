<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CartController extends Controller
{
    /**
     * Get cart items.
     *
     * @OA\Get(
     *     path="/cart",
     *     summary="Get cart items",
     *     tags={"Cart"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $items = Cart::forUser($userId)
            ->with('product:UPC,Description,Price,Qty_avail,Company')
            ->get();

        $subtotal = $items->sum(function ($item) {
            return $item->quantity * ($item->product->Price ?? 0);
        });

        return response()->json([
            'success' => true,
            'data' => $items,
            'summary' => [
                'item_count' => $items->sum('quantity'),
                'subtotal' => round($subtotal, 2),
            ]
        ]);
    }

    /**
     * Add item to cart.
     *
     * @OA\Post(
     *     path="/cart",
     *     summary="Add item to cart",
     *     tags={"Cart"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"product_upc", "quantity"},
     *             @OA\Property(property="product_upc", type="string"),
     *             @OA\Property(property="quantity", type="integer", minimum=1)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Item added"),
     *     @OA\Response(response=400, description="Insufficient stock")
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_upc' => 'required|string|exists:products3,UPC',
            'quantity' => 'required|integer|min:1',
        ]);

        $userId = $request->user()->id;

        // Check stock
        $product = Product::find($validated['product_upc']);
        if ($product->Qty_avail < $validated['quantity']) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient stock',
                'available' => $product->Qty_avail
            ], 400);
        }

        // Check if item already in cart
        $cartItem = Cart::forUser($userId)
            ->where('product_upc', $validated['product_upc'])
            ->first();

        if ($cartItem) {
            $newQty = $cartItem->quantity + $validated['quantity'];
            if ($newQty > $product->Qty_avail) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient stock',
                    'available' => $product->Qty_avail
                ], 400);
            }
            $cartItem->quantity = $newQty;
            $cartItem->save();
        } else {
            $cartItem = Cart::create([
                'user_id' => $userId,
                'product_upc' => $validated['product_upc'],
                'quantity' => $validated['quantity'],
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Item added to cart',
            'data' => $cartItem->load('product')
        ]);
    }

    /**
     * Update cart item quantity.
     *
     * @OA\Put(
     *     path="/cart/{productUpc}",
     *     summary="Update cart item quantity",
     *     tags={"Cart"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="productUpc", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"quantity"},
     *             @OA\Property(property="quantity", type="integer", minimum=1)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Cart updated"),
     *     @OA\Response(response=404, description="Item not found")
     * )
     */
    public function update(Request $request, string $productUpc): JsonResponse
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $userId = $request->user()->id;

        $cartItem = Cart::forUser($userId)
            ->where('product_upc', $productUpc)
            ->first();

        if (!$cartItem) {
            return response()->json([
                'success' => false,
                'message' => 'Item not found in cart'
            ], 404);
        }

        // Check stock
        $product = Product::find($productUpc);
        if ($product->Qty_avail < $validated['quantity']) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient stock',
                'available' => $product->Qty_avail
            ], 400);
        }

        $cartItem->quantity = $validated['quantity'];
        $cartItem->save();

        return response()->json([
            'success' => true,
            'message' => 'Cart updated',
            'data' => $cartItem->load('product')
        ]);
    }

    /**
     * Remove item from cart.
     *
     * @OA\Delete(
     *     path="/cart/{productUpc}",
     *     summary="Remove item from cart",
     *     tags={"Cart"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="productUpc", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Item removed"),
     *     @OA\Response(response=404, description="Item not found")
     * )
     */
    public function destroy(Request $request, string $productUpc): JsonResponse
    {
        $userId = $request->user()->id;

        $deleted = Cart::forUser($userId)
            ->where('product_upc', $productUpc)
            ->delete();

        if (!$deleted) {
            return response()->json([
                'success' => false,
                'message' => 'Item not found in cart'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart'
        ]);
    }

    /**
     * Clear entire cart.
     *
     * @OA\Delete(
     *     path="/cart",
     *     summary="Clear entire cart",
     *     tags={"Cart"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Cart cleared")
     * )
     */
    public function clear(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        Cart::forUser($userId)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cart cleared'
        ]);
    }
}
