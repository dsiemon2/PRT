<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductHistory;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Get all products with filtering, sorting, and pagination.
     *
     * @OA\Get(
     *     path="/products",
     *     summary="Get all products",
     *     tags={"Products"},
     *     @OA\Parameter(name="category", in="query", @OA\Schema(type="integer"), description="Filter by category code"),
     *     @OA\Parameter(name="search", in="query", @OA\Schema(type="string"), description="Search term"),
     *     @OA\Parameter(name="min_price", in="query", @OA\Schema(type="number"), description="Minimum price"),
     *     @OA\Parameter(name="max_price", in="query", @OA\Schema(type="number"), description="Maximum price"),
     *     @OA\Parameter(name="in_stock", in="query", @OA\Schema(type="boolean"), description="Only show in-stock items"),
     *     @OA\Parameter(name="sort", in="query", @OA\Schema(type="string", enum={"price_low", "price_high", "name_asc", "name_desc", "newest"}), description="Sort order"),
     *     @OA\Parameter(name="size", in="query", @OA\Schema(type="string"), description="Filter by size"),
     *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer", default=20)),
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $query = Product::with(['category', 'images' => function ($q) {
            $q->ordered();
        }]);

        // Exclude soft-deleted products by default (unless include_deleted=true)
        if (!$request->boolean('include_deleted')) {
            $query->where(function ($q) {
                $q->where('is_deleted', false)
                  ->orWhereNull('is_deleted');
            });
        }

        // Filter by category (include child categories for parent categories)
        if ($request->has('category')) {
            $categoryCode = $request->category;

            // Get the category to check if it's a parent
            $category = \App\Models\Category::find($categoryCode);

            if ($category && !$category->IsBottom) {
                // It's a parent category - get all child category codes
                $childCodes = \App\Models\Category::where('CategoryCode', '>', $categoryCode)
                    ->where('CategoryCode', '<', $categoryCode + 100) // Reasonable range for children
                    ->where('Level', '>', $category->Level)
                    ->pluck('CategoryCode')
                    ->toArray();

                $childCodes[] = $categoryCode; // Include the parent itself
                $query->whereIn('CategoryCode', $childCodes);
            } else {
                $query->where('CategoryCode', $categoryCode);
            }
        }

        // Filter by search term
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ShortDescription', 'like', "%{$search}%")
                  ->orWhere('LngDescription', 'like', "%{$search}%")
                  ->orWhere('ItemNumber', 'like', "%{$search}%")
                  ->orWhere('UPC', 'like', "%{$search}%");
            });
        }

        // Filter by price range
        if ($request->has('min_price')) {
            $query->where('UnitPrice', '>=', $request->min_price);
        }
        if ($request->has('max_price')) {
            $query->where('UnitPrice', '<=', $request->max_price);
        }

        // Filter by size (matches products.php)
        if ($request->has('size') && $request->size !== '') {
            $query->where('ItemSize', $request->size);
        }

        // Filter by stock status - default to only in-stock
        if (!$request->has('in_stock') || $request->boolean('in_stock')) {
            $query->inStock();
        }

        // Sorting - support both combined sort values (like products.php) and separate sort/direction
        $sortBy = $request->get('sort', '');
        $sortDirection = $request->get('direction', 'asc');

        // Match products.php sorting options
        switch ($sortBy) {
            case 'price_low':
                $query->orderBy('UnitPrice', 'asc');
                break;
            case 'price_high':
                $query->orderBy('UnitPrice', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('ShortDescription', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('ShortDescription', 'desc');
                break;
            case 'newest':
                $query->orderBy('ID', 'desc');
                break;
            // Legacy API sort options
            case 'Description':
            case 'ShortDescription':
                $query->orderBy('ShortDescription', $sortDirection);
                break;
            case 'Price':
            case 'UnitPrice':
                $query->orderBy('UnitPrice', $sortDirection);
                break;
            case 'Qty_avail':
                $query->orderBy('stock_quantity', $sortDirection);
                break;
            default:
                // Default: ORDER BY ID ASC (matches products.php default)
                $query->orderBy('ID', 'asc');
                break;
        }

        // Pagination - allow higher limit for admin/inventory use
        $maxPerPage = $request->boolean('in_stock') === false ? 500 : 100;
        $perPage = min($request->get('per_page', 20), $maxPerPage);
        $products = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $products->items(),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ]
        ]);
    }

    /**
     * Get a single product by UPC.
     *
     * @OA\Get(
     *     path="/products/{upc}",
     *     summary="Get product by UPC",
     *     tags={"Products"},
     *     @OA\Parameter(name="upc", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="Product not found")
     * )
     */
    public function show(string $upc): JsonResponse
    {
        $product = Product::with([
            'category',
            'images' => function ($q) {
                $q->ordered();
            },
            'reviews' => function ($q) {
                $q->approved()->latest()->limit(10);
            }
        ])->where('UPC', $upc)->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        // Calculate average rating
        $avgRating = $product->reviews()->approved()->avg('rating');
        $reviewCount = $product->reviews()->approved()->count();

        return response()->json([
            'success' => true,
            'data' => array_merge($product->toArray(), [
                'average_rating' => round($avgRating, 1),
                'review_count' => $reviewCount,
                'is_in_stock' => $product->isInStock(),
                'is_low_stock' => $product->isLowStock(),
            ])
        ]);
    }

    /**
     * Get a single product by numeric ID.
     *
     * @OA\Get(
     *     path="/products/by-id/{id}",
     *     summary="Get product by ID",
     *     tags={"Products"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="Product not found")
     * )
     */
    public function showById(int $id): JsonResponse
    {
        $product = Product::with([
            'category',
            'images' => function ($q) {
                $q->ordered();
            },
            'reviews' => function ($q) {
                $q->approved()->latest()->limit(10);
            }
        ])->where('ID', $id)->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        // Calculate average rating
        $avgRating = $product->reviews()->approved()->avg('rating');
        $reviewCount = $product->reviews()->approved()->count();

        return response()->json([
            'success' => true,
            'data' => array_merge($product->toArray(), [
                'average_rating' => round($avgRating ?? 0, 1),
                'review_count' => $reviewCount,
                'is_in_stock' => $product->isInStock(),
                'is_low_stock' => $product->isLowStock(),
            ])
        ]);
    }

    /**
     * Get featured products.
     *
     * @OA\Get(
     *     path="/products/featured",
     *     summary="Get featured products",
     *     tags={"Products"},
     *     @OA\Parameter(name="limit", in="query", @OA\Schema(type="integer", default=8)),
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function featured(Request $request): JsonResponse
    {
        $limit = min($request->get('limit', 8), 50);

        $products = Product::with(['images' => function ($q) {
                $q->primary();
            }])
            ->active()
            ->inStock()
            ->orderBy('UnitPrice', 'desc')
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    /**
     * Get products by category.
     *
     * @OA\Get(
     *     path="/products/category/{categoryCode}",
     *     summary="Get products by category",
     *     tags={"Products"},
     *     @OA\Parameter(name="categoryCode", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer", default=20)),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="Category not found")
     * )
     */
    public function byCategory(int $categoryCode, Request $request): JsonResponse
    {
        $category = Category::find($categoryCode);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        }

        $perPage = min($request->get('per_page', 20), 100);

        $products = Product::with(['images' => function ($q) {
                $q->ordered();
            }])
            ->where('CategoryCode', $categoryCode)
            ->active()
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'category' => $category,
            'data' => $products->items(),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ]
        ]);
    }

    /**
     * Search products.
     *
     * @OA\Get(
     *     path="/products/search",
     *     summary="Search products",
     *     tags={"Products"},
     *     @OA\Parameter(name="q", in="query", required=true, @OA\Schema(type="string", minLength=2)),
     *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer", default=20)),
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'q' => 'required|string|min:2'
        ]);

        $search = $request->q;
        $perPage = min($request->get('per_page', 20), 100);

        // Sorting - match products.php
        $sortBy = $request->get('sort', '');

        $productsQuery = Product::with(['category', 'images' => function ($q) {
                $q->primary();
            }])
            ->where(function ($query) use ($search) {
                // Match products.php search: ShortDescription and ItemNumber
                $query->where('ShortDescription', 'like', "%{$search}%")
                      ->orWhere('ItemNumber', 'like', "%{$search}%");
            })
            ->active()
            ->inStock();

        // Apply sorting (match products.php)
        switch ($sortBy) {
            case 'price_low':
                $productsQuery->orderBy('UnitPrice', 'asc');
                break;
            case 'price_high':
                $productsQuery->orderBy('UnitPrice', 'desc');
                break;
            case 'name_asc':
                $productsQuery->orderBy('ShortDescription', 'asc');
                break;
            case 'name_desc':
                $productsQuery->orderBy('ShortDescription', 'desc');
                break;
            case 'newest':
                $productsQuery->orderBy('ID', 'desc');
                break;
            default:
                $productsQuery->orderBy('ID', 'asc');
                break;
        }

        $products = $productsQuery->paginate($perPage);

        return response()->json([
            'success' => true,
            'query' => $search,
            'data' => $products->items(),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ]
        ]);
    }

    /**
     * Create a new product (Admin only).
     *
     * @OA\Post(
     *     path="/products",
     *     summary="Create a new product",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"UPC", "Description", "Price"},
     *             @OA\Property(property="UPC", type="string"),
     *             @OA\Property(property="Description", type="string"),
     *             @OA\Property(property="Price", type="number"),
     *             @OA\Property(property="Company", type="string"),
     *             @OA\Property(property="CategoryCode", type="integer"),
     *             @OA\Property(property="Qty_avail", type="integer")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Product created"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'UPC' => 'required|string|max:50|unique:products,UPC',
            'Company' => 'nullable|string|max:255',
            'Description' => 'required|string|max:255',
            'Price' => 'required|numeric|min:0',
            'CategoryCode' => 'nullable|integer|exists:categories,CategoryCode',
            'Qty_avail' => 'nullable|integer|min:0',
            'Weight' => 'nullable|string',
            'UOM' => 'nullable|string|max:255',
            'meta_title' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:160',
            'ItemSize' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:50',
            'material' => 'nullable|string|max:100',
        ]);

        $product = Product::create($validated);

        // Log product creation
        ProductHistory::logChange(
            $product->ID,
            $product->UPC,
            'product',
            null,
            $product->ShortDescription ?? $product->UPC,
            'create',
            null,
            'Admin',
            $request->ip()
        );

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully',
            'data' => $product
        ], 201);
    }

    /**
     * Update a product (Admin only).
     *
     * @OA\Put(
     *     path="/products/{upc}",
     *     summary="Update a product",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="upc", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="Description", type="string"),
     *             @OA\Property(property="Price", type="number"),
     *             @OA\Property(property="Qty_avail", type="integer")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Product updated"),
     *     @OA\Response(response=404, description="Product not found")
     * )
     */
    public function update(Request $request, string $upc): JsonResponse
    {
        $product = Product::where('UPC', $upc)->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        $validated = $request->validate([
            'Company' => 'nullable|string|max:255',
            'Description' => 'nullable|string|max:255',
            'Price' => 'nullable|numeric|min:0',
            'CategoryCode' => 'nullable|integer|exists:categories,CategoryCode',
            'Qty_avail' => 'nullable|integer|min:0',
            'Weight' => 'nullable|string',
            'UOM' => 'nullable|string|max:255',
            'Sold_out' => 'nullable|string|max:1',
            'track_inventory' => 'nullable|boolean',
            'allow_backorder' => 'nullable|boolean',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'reorder_point' => 'nullable|integer|min:0',
            'reorder_quantity' => 'nullable|integer|min:0',
            'meta_title' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:160',
            'ItemSize' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:50',
            'material' => 'nullable|string|max:100',
        ]);

        // Capture old data for history logging
        $oldData = $product->toArray();

        $product->update($validated);

        // Log changes
        $this->logProductChanges($product, $oldData, $product->fresh()->toArray(), 'update', $request);

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'data' => $product->fresh()
        ]);
    }

    /**
     * Delete a product (Admin only).
     *
     * @OA\Delete(
     *     path="/products/{upc}",
     *     summary="Delete a product",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="upc", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\Parameter(name="force", in="query", @OA\Schema(type="boolean"), description="Force delete even with active orders"),
     *     @OA\Response(response=200, description="Product deleted"),
     *     @OA\Response(response=404, description="Product not found"),
     *     @OA\Response(response=409, description="Product has active orders")
     * )
     */
    public function destroy(Request $request, string $upc): JsonResponse
    {
        $product = Product::where('UPC', $upc)->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        // Check for active orders containing this product (unless force=true)
        if (!$request->boolean('force')) {
            $activeOrderCount = DB::table('order_items')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->where('order_items.product_id', $product->ID)
                ->whereIn('orders.status', ['pending', 'processing', 'shipped'])
                ->count();

            if ($activeOrderCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Cannot delete product. It is associated with {$activeOrderCount} active order(s). Use soft delete or force delete.",
                    'active_orders' => $activeOrderCount
                ], 409);
            }
        }

        // Soft delete - mark as deleted instead of removing
        $product->update([
            'is_deleted' => true,
            'deleted_at' => now(),
        ]);

        // Log deletion
        ProductHistory::logChange(
            $product->ID,
            $product->UPC,
            'is_deleted',
            false,
            true,
            'delete',
            null,
            'Admin',
            $request->ip(),
            $request->boolean('force') ? 'Force deleted with active orders' : null
        );

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully (soft delete)'
        ]);
    }

    /**
     * Update product stock (Admin only).
     *
     * @OA\Patch(
     *     path="/products/{upc}/stock",
     *     summary="Update product stock",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="upc", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"quantity", "type"},
     *             @OA\Property(property="quantity", type="integer"),
     *             @OA\Property(property="type", type="string", enum={"set", "add", "subtract"})
     *         )
     *     ),
     *     @OA\Response(response=200, description="Stock updated"),
     *     @OA\Response(response=404, description="Product not found")
     * )
     */
    public function updateStock(Request $request, string $upc): JsonResponse
    {
        $product = Product::where('UPC', $upc)->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        $validated = $request->validate([
            'quantity' => 'required|integer',
            'type' => 'required|in:set,add,subtract'
        ]);

        switch ($validated['type']) {
            case 'set':
                $product->Qty_avail = $validated['quantity'];
                break;
            case 'add':
                $product->Qty_avail = ($product->Qty_avail ?? 0) + $validated['quantity'];
                break;
            case 'subtract':
                $product->Qty_avail = max(0, ($product->Qty_avail ?? 0) - $validated['quantity']);
                break;
        }

        $product->save();

        return response()->json([
            'success' => true,
            'message' => 'Stock updated successfully',
            'data' => [
                'upc' => $product->UPC,
                'qty_avail' => $product->Qty_avail
            ]
        ]);
    }

    /**
     * Upload images for a product (Admin only).
     *
     * @OA\Post(
     *     path="/products/{upc}/images",
     *     summary="Upload product images",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="upc", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="images[]", type="array", @OA\Items(type="string", format="binary"))
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Images uploaded"),
     *     @OA\Response(response=404, description="Product not found")
     * )
     */
    public function uploadImages(Request $request, string $upc): JsonResponse
    {
        $product = Product::where('UPC', $upc)->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        $request->validate([
            'images' => 'required|array|max:10',
            'images.*' => 'required|image|mimes:jpeg,png,gif,webp|max:2048'
        ]);

        $uploadedImages = [];
        $currentMaxOrder = ProductImage::where('product_id', $product->ID)->max('display_order') ?? 0;
        $hasPrimary = ProductImage::where('product_id', $product->ID)->where('is_primary', true)->exists();

        foreach ($request->file('images') as $index => $image) {
            // Generate unique filename
            $filename = 'product_' . $product->ID . '_' . time() . '_' . $index . '.' . $image->getClientOriginalExtension();

            // Store in public storage (or move to your assets folder)
            $path = $image->storeAs('product_images', $filename, 'public');

            // Create database record
            $productImage = ProductImage::create([
                'product_id' => $product->ID,
                'image_path' => $path,
                'display_order' => $currentMaxOrder + $index + 1,
                'is_primary' => !$hasPrimary && $index === 0, // First image becomes primary if none exists
                'alt_text' => $product->ShortDescription,
            ]);

            $uploadedImages[] = $productImage;
            if (!$hasPrimary && $index === 0) {
                $hasPrimary = true;
            }
        }

        return response()->json([
            'success' => true,
            'message' => count($uploadedImages) . ' image(s) uploaded successfully',
            'data' => $uploadedImages
        ]);
    }

    /**
     * Reorder product images (Admin only).
     *
     * @OA\Put(
     *     path="/products/{upc}/images/reorder",
     *     summary="Reorder product images",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="upc", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="order", type="array", @OA\Items(type="integer"), description="Array of image IDs in desired order")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Images reordered"),
     *     @OA\Response(response=404, description="Product not found")
     * )
     */
    public function reorderImages(Request $request, string $upc): JsonResponse
    {
        $product = Product::where('UPC', $upc)->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        $request->validate([
            'order' => 'required|array',
            'order.*' => 'required|integer'
        ]);

        DB::transaction(function () use ($request, $product) {
            foreach ($request->order as $index => $imageId) {
                ProductImage::where('id', $imageId)
                    ->where('product_id', $product->ID)
                    ->update(['display_order' => $index + 1]);
            }
        });

        $images = ProductImage::where('product_id', $product->ID)->ordered()->get();

        return response()->json([
            'success' => true,
            'message' => 'Images reordered successfully',
            'data' => $images
        ]);
    }

    /**
     * Set primary product image (Admin only).
     *
     * @OA\Put(
     *     path="/products/{upc}/images/{imageId}/primary",
     *     summary="Set primary product image",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="upc", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\Parameter(name="imageId", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Primary image set"),
     *     @OA\Response(response=404, description="Product or image not found")
     * )
     */
    public function setPrimaryImage(Request $request, string $upc, int $imageId): JsonResponse
    {
        $product = Product::where('UPC', $upc)->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        $image = ProductImage::where('id', $imageId)->where('product_id', $product->ID)->first();

        if (!$image) {
            return response()->json([
                'success' => false,
                'message' => 'Image not found'
            ], 404);
        }

        DB::transaction(function () use ($product, $image) {
            // Remove primary from all other images
            ProductImage::where('product_id', $product->ID)
                ->where('id', '!=', $image->id)
                ->update(['is_primary' => false]);

            // Set this image as primary
            $image->update(['is_primary' => true]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Primary image set successfully',
            'data' => $image->fresh()
        ]);
    }

    /**
     * Delete a product image (Admin only).
     *
     * @OA\Delete(
     *     path="/products/{upc}/images/{imageId}",
     *     summary="Delete a product image",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="upc", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\Parameter(name="imageId", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Image deleted"),
     *     @OA\Response(response=404, description="Product or image not found")
     * )
     */
    public function deleteImage(Request $request, string $upc, int $imageId): JsonResponse
    {
        $product = Product::where('UPC', $upc)->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        $image = ProductImage::where('id', $imageId)->where('product_id', $product->ID)->first();

        if (!$image) {
            return response()->json([
                'success' => false,
                'message' => 'Image not found'
            ], 404);
        }

        $wasPrimary = $image->is_primary;

        // Delete the file from storage
        if (Storage::disk('public')->exists($image->image_path)) {
            Storage::disk('public')->delete($image->image_path);
        }

        // Delete the database record
        $image->delete();

        // If this was the primary image, make the first remaining image primary
        if ($wasPrimary) {
            $firstImage = ProductImage::where('product_id', $product->ID)->ordered()->first();
            if ($firstImage) {
                $firstImage->update(['is_primary' => true]);
            }
        }

        // Get remaining images
        $remainingImages = ProductImage::where('product_id', $product->ID)->ordered()->get();

        return response()->json([
            'success' => true,
            'message' => 'Image deleted successfully',
            'data' => $remainingImages
        ]);
    }

    /**
     * Get all images for a product.
     *
     * @OA\Get(
     *     path="/products/{upc}/images",
     *     summary="Get product images",
     *     tags={"Products"},
     *     @OA\Parameter(name="upc", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="Product not found")
     * )
     */
    public function getImages(string $upc): JsonResponse
    {
        $product = Product::where('UPC', $upc)->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        $images = ProductImage::where('product_id', $product->ID)->ordered()->get();

        return response()->json([
            'success' => true,
            'data' => $images
        ]);
    }

    /**
     * Get product change history.
     *
     * @OA\Get(
     *     path="/products/{upc}/history",
     *     summary="Get product change history",
     *     tags={"Products"},
     *     @OA\Parameter(name="upc", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer", default=20)),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="Product not found")
     * )
     */
    public function getHistory(Request $request, string $upc): JsonResponse
    {
        $product = Product::where('UPC', $upc)->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        $perPage = min($request->get('per_page', 20), 100);

        $history = ProductHistory::where('product_id', $product->ID)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        // Add formatted labels to each record
        $items = collect($history->items())->map(function ($item) {
            $item->action_label = $item->action_label;
            $item->field_label = $item->field_label;
            return $item;
        });

        return response()->json([
            'success' => true,
            'data' => $items,
            'meta' => [
                'current_page' => $history->currentPage(),
                'last_page' => $history->lastPage(),
                'per_page' => $history->perPage(),
                'total' => $history->total(),
            ]
        ]);
    }

    /**
     * Log product changes helper method.
     */
    protected function logProductChanges(
        Product $product,
        array $oldData,
        array $newData,
        string $action = 'update',
        Request $request = null
    ): void {
        $fieldsToTrack = [
            'ShortDescription', 'LngDescription', 'UnitPrice', 'cost_price',
            'stock_quantity', 'CategoryCode', 'low_stock_threshold', 'reorder_point',
            'meta_title', 'meta_description', 'ItemSize', 'color', 'material',
            'track_inventory', 'allow_backorder', 'is_deleted'
        ];

        $changes = [];

        foreach ($fieldsToTrack as $field) {
            $oldVal = $oldData[$field] ?? null;
            $newVal = $newData[$field] ?? null;

            // Normalize values for comparison
            if (is_bool($oldVal)) $oldVal = $oldVal ? '1' : '0';
            if (is_bool($newVal)) $newVal = $newVal ? '1' : '0';
            if (is_numeric($oldVal)) $oldVal = (string) $oldVal;
            if (is_numeric($newVal)) $newVal = (string) $newVal;

            if ($oldVal !== $newVal && ($oldVal !== null || $newVal !== null)) {
                $changes[$field] = [
                    'old' => $oldData[$field] ?? null,
                    'new' => $newData[$field] ?? null
                ];
            }
        }

        if (!empty($changes)) {
            ProductHistory::logMultipleChanges(
                $product->ID,
                $product->UPC,
                $changes,
                $action,
                null, // user_id - implement when auth is added
                'Admin',
                $request ? $request->ip() : null
            );
        }
    }
}
