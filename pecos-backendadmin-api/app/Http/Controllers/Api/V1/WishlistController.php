<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class WishlistController extends Controller
{
    /**
     * Get user's wishlist.
     *
     * @OA\Get(
     *     path="/wishlist",
     *     summary="Get user's wishlist",
     *     tags={"Wishlist"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $wishlistItems = Wishlist::where('user_id', $user->id)
            ->with(['product' => function ($q) {
                $q->with('images');
            }])
            ->get();

        return response()->json([
            'success' => true,
            'data' => $wishlistItems,
            'count' => $wishlistItems->count()
        ]);
    }

    /**
     * Add item to wishlist.
     *
     * @OA\Post(
     *     path="/wishlist",
     *     summary="Add item to wishlist",
     *     tags={"Wishlist"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"product_id"},
     *             @OA\Property(property="product_id", type="string")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Added to wishlist"),
     *     @OA\Response(response=400, description="Already in wishlist"),
     *     @OA\Response(response=404, description="Product not found")
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|string'
        ]);

        $user = $request->user();

        // Check if product exists - find by UPC
        $product = Product::find($validated['product_id']);
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        // Get numeric ID for wishlist
        $numericId = $product->ID;

        // Check if already in wishlist
        $exists = Wishlist::where('user_id', $user->id)
            ->where('product_id', $numericId)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Product already in wishlist'
            ], 400);
        }

        $wishlist = Wishlist::create([
            'user_id' => $user->id,
            'product_id' => $numericId
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Added to wishlist',
            'data' => $wishlist->load('product')
        ], 201);
    }

    /**
     * Remove item from wishlist.
     *
     * @OA\Delete(
     *     path="/wishlist/{productId}",
     *     summary="Remove item from wishlist",
     *     tags={"Wishlist"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="productId", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Removed from wishlist"),
     *     @OA\Response(response=404, description="Item not found")
     * )
     */
    public function destroy(Request $request, string $productId): JsonResponse
    {
        $user = $request->user();

        // Find product by UPC to get numeric ID
        $product = Product::find($productId);
        $numericId = $product ? $product->ID : $productId;

        $deleted = Wishlist::where('user_id', $user->id)
            ->where('product_id', $numericId)
            ->delete();

        if (!$deleted) {
            return response()->json([
                'success' => false,
                'message' => 'Item not found in wishlist'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Removed from wishlist'
        ]);
    }

    /**
     * Toggle wishlist item.
     *
     * @OA\Post(
     *     path="/wishlist/toggle/{productId}",
     *     summary="Toggle wishlist item",
     *     tags={"Wishlist"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="productId", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Toggled"),
     *     @OA\Response(response=404, description="Product not found")
     * )
     */
    public function toggle(Request $request, string $productId): JsonResponse
    {
        $user = $request->user();

        // Find product by UPC to get numeric ID
        $product = Product::find($productId);
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        $numericId = $product->ID;

        $existing = Wishlist::where('user_id', $user->id)
            ->where('product_id', $numericId)
            ->first();

        if ($existing) {
            $existing->delete();
            return response()->json([
                'success' => true,
                'message' => 'Removed from wishlist',
                'in_wishlist' => false
            ]);
        }

        Wishlist::create([
            'user_id' => $user->id,
            'product_id' => $numericId
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Added to wishlist',
            'in_wishlist' => true
        ]);
    }

    /**
     * Check if product is in wishlist.
     *
     * @OA\Get(
     *     path="/wishlist/check/{productId}",
     *     summary="Check if product is in wishlist",
     *     tags={"Wishlist"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="productId", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function check(Request $request, string $productId): JsonResponse
    {
        $user = $request->user();

        // Find product by UPC to get numeric ID
        $product = Product::find($productId);
        $numericId = $product ? $product->ID : $productId;

        $inWishlist = Wishlist::where('user_id', $user->id)
            ->where('product_id', $numericId)
            ->exists();

        return response()->json([
            'success' => true,
            'in_wishlist' => $inWishlist
        ]);
    }

    /**
     * Clear entire wishlist.
     *
     * @OA\Delete(
     *     path="/wishlist",
     *     summary="Clear entire wishlist",
     *     tags={"Wishlist"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Wishlist cleared")
     * )
     */
    public function clear(Request $request): JsonResponse
    {
        $user = $request->user();

        Wishlist::where('user_id', $user->id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Wishlist cleared'
        ]);
    }

    /**
     * Get wishlist by user ID (for PHP session-based auth).
     *
     * @OA\Get(
     *     path="/wishlist/user/{userId}",
     *     summary="Get wishlist by user ID",
     *     tags={"Wishlist"},
     *     @OA\Parameter(name="userId", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function userWishlist(int $userId): JsonResponse
    {
        $wishlistItems = Wishlist::where('user_id', $userId)
            ->join('products3', 'user_wishlists.product_id', '=', 'products3.ID')
            ->select(
                'user_wishlists.id as wishlist_id',
                'user_wishlists.added_at',
                'products3.ID as product_id',
                'products3.UPC',
                'products3.ShortDescription',
                'products3.UnitPrice',
                'products3.Image',
                'products3.ItemSize',
                'products3.CategoryCode'
            )
            ->orderBy('user_wishlists.added_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $wishlistItems,
            'count' => $wishlistItems->count()
        ]);
    }

    /**
     * Remove item from wishlist by user ID (for PHP session-based auth).
     *
     * @OA\Delete(
     *     path="/wishlist/user/{userId}/{productId}",
     *     summary="Remove item from wishlist by user ID",
     *     tags={"Wishlist"},
     *     @OA\Parameter(name="userId", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="productId", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Removed from wishlist"),
     *     @OA\Response(response=404, description="Item not found")
     * )
     */
    public function removeByUserId(int $userId, int $productId): JsonResponse
    {
        $deleted = Wishlist::where('user_id', $userId)
            ->where('product_id', $productId)
            ->delete();

        if (!$deleted) {
            return response()->json([
                'success' => false,
                'message' => 'Item not found in wishlist'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Removed from wishlist'
        ]);
    }

    /**
     * Add item to wishlist by user ID.
     */
    public function addByUserId(int $userId, int $productId): JsonResponse
    {
        $exists = Wishlist::where('user_id', $userId)
            ->where('product_id', $productId)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Item is already in your wishlist',
                'inWishlist' => true
            ]);
        }

        Wishlist::create([
            'user_id' => $userId,
            'product_id' => $productId
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Added to wishlist',
            'inWishlist' => true
        ]);
    }

    /**
     * Toggle wishlist item by user ID.
     */
    public function toggleByUserId(int $userId, int $productId): JsonResponse
    {
        $existing = Wishlist::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($existing) {
            $existing->delete();
            return response()->json([
                'success' => true,
                'message' => 'Removed from wishlist',
                'inWishlist' => false
            ]);
        }

        Wishlist::create([
            'user_id' => $userId,
            'product_id' => $productId
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Added to wishlist',
            'inWishlist' => true
        ]);
    }

    /**
     * Check if item is in wishlist by user ID.
     */
    public function checkByUserId(int $userId, int $productId): JsonResponse
    {
        $inWishlist = Wishlist::where('user_id', $userId)
            ->where('product_id', $productId)
            ->exists();

        return response()->json([
            'success' => true,
            'inWishlist' => $inWishlist
        ]);
    }

    /**
     * Clear all wishlist items by user ID.
     */
    public function clearByUserId(int $userId): JsonResponse
    {
        $count = Wishlist::where('user_id', $userId)->delete();

        return response()->json([
            'success' => true,
            'message' => 'All items cleared from wishlist',
            'count' => $count
        ]);
    }
}
