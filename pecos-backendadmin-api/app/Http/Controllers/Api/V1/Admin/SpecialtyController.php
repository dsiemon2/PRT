<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SpecialtyController extends Controller
{
    /**
     * Get all specialty categories with product counts
     */
    public function index()
    {
        $categories = DB::table('specialty_categories')
            ->select('specialty_categories.*')
            ->orderBy('sort_order')
            ->get();

        // Add product counts
        foreach ($categories as $cat) {
            $cat->products_count = DB::table('specialty_products')
                ->where('specialty_category_id', $cat->id)
                ->where('is_visible', true)
                ->count();
        }

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    /**
     * Get a single specialty category with its products
     */
    public function show($id)
    {
        $category = DB::table('specialty_categories')->where('id', $id)->first();

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        }

        // Get products with linked product data
        $products = DB::table('specialty_products')
            ->where('specialty_category_id', $id)
            ->orderBy('sort_order')
            ->get();

        // Enrich products with data from products3 table
        foreach ($products as $product) {
            if ($product->upc) {
                $linkedProduct = DB::table('products3')
                    ->where('UPC', $product->upc)
                    ->first();

                if ($linkedProduct) {
                    $product->product_image = $linkedProduct->Image;
                    $product->product_price = $product->price ?? $linkedProduct->UnitPrice;
                    $product->product_sale_price = $linkedProduct->sale_price;
                    $product->quantity = $linkedProduct->QTY;
                }
            } else {
                // Custom product without linked UPC
                $product->product_image = null;
                $product->product_price = $product->price;
                $product->product_sale_price = null;
                $product->quantity = 100; // Assume in stock
            }
        }

        $category->products = $products;
        $category->products_count = count($products);

        return response()->json([
            'success' => true,
            'data' => $category
        ]);
    }

    /**
     * Create a new specialty category
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'label' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|integer',
            'image' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Get max sort order
        $maxOrder = DB::table('specialty_categories')->max('sort_order') ?? 0;

        $id = DB::table('specialty_categories')->insertGetId([
            'category_id' => $request->category_id,
            'label' => $request->label,
            'description' => $request->description,
            'image' => $request->image,
            'sort_order' => $maxOrder + 1,
            'is_visible' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $category = DB::table('specialty_categories')->where('id', $id)->first();

        return response()->json([
            'success' => true,
            'message' => 'Category created successfully',
            'data' => $category
        ], 201);
    }

    /**
     * Update a specialty category
     */
    public function update(Request $request, $id)
    {
        $category = DB::table('specialty_categories')->where('id', $id)->first();

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'label' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|integer',
            'image' => 'nullable|string|max:500',
            'is_visible' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $updateData = ['updated_at' => now()];

        if ($request->has('label')) $updateData['label'] = $request->label;
        if ($request->has('description')) $updateData['description'] = $request->description;
        if ($request->has('category_id')) $updateData['category_id'] = $request->category_id;
        if ($request->has('image')) $updateData['image'] = $request->image;
        if ($request->has('is_visible')) $updateData['is_visible'] = $request->is_visible;

        DB::table('specialty_categories')->where('id', $id)->update($updateData);

        $category = DB::table('specialty_categories')->where('id', $id)->first();

        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully',
            'data' => $category
        ]);
    }

    /**
     * Delete a specialty category
     */
    public function destroy($id)
    {
        $category = DB::table('specialty_categories')->where('id', $id)->first();

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        }

        // Products are deleted via cascade
        DB::table('specialty_categories')->where('id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully'
        ]);
    }

    /**
     * Reorder specialty categories
     */
    public function reorderCategories(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order' => 'required|array',
            'order.*' => 'integer|exists:specialty_categories,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        foreach ($request->order as $index => $id) {
            DB::table('specialty_categories')
                ->where('id', $id)
                ->update(['sort_order' => $index + 1, 'updated_at' => now()]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Categories reordered successfully'
        ]);
    }

    /**
     * Upload category image
     */
    public function uploadCategoryImage(Request $request, $id)
    {
        $category = DB::table('specialty_categories')->where('id', $id)->first();

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $file = $request->file('image');
        $filename = 'specialty-cat-' . $id . '-' . time() . '.' . $file->getClientOriginalExtension();
        $path = 'images/categories/' . $filename;

        // Store file
        $file->move(public_path('images/categories'), $filename);

        // Update database
        DB::table('specialty_categories')
            ->where('id', $id)
            ->update(['image' => $path, 'updated_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'Image uploaded successfully',
            'data' => ['image' => $path]
        ]);
    }

    // ==================== PRODUCTS ====================

    /**
     * Get products for a category
     */
    public function getProducts($categoryId)
    {
        $category = DB::table('specialty_categories')->where('id', $categoryId)->first();

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        }

        $products = DB::table('specialty_products')
            ->where('specialty_category_id', $categoryId)
            ->orderBy('sort_order')
            ->get();

        // Enrich products with data from products3 table
        foreach ($products as $product) {
            if ($product->upc) {
                $linkedProduct = DB::table('products3')
                    ->where('UPC', $product->upc)
                    ->first();

                if ($linkedProduct) {
                    $product->product_image = $linkedProduct->Image;
                    $product->product_price = $product->price ?? $linkedProduct->UnitPrice;
                    $product->product_sale_price = $linkedProduct->sale_price;
                    $product->quantity = $linkedProduct->QTY;
                    $product->item_number = $linkedProduct->ItemNumber;
                }
            } else {
                $product->product_image = null;
                $product->product_price = $product->price;
                $product->product_sale_price = null;
                $product->quantity = 100;
                $product->item_number = null;
            }
        }

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    /**
     * Add product to category
     */
    public function addProduct(Request $request, $categoryId)
    {
        $category = DB::table('specialty_categories')->where('id', $categoryId)->first();

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'upc' => 'nullable|string|max:50',
            'label' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sizes' => 'nullable|string|max:500',
            'colors' => 'nullable|string|max:500',
            'price' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if UPC is already in this category
        if ($request->upc) {
            $existing = DB::table('specialty_products')
                ->where('specialty_category_id', $categoryId)
                ->where('upc', $request->upc)
                ->first();

            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product already exists in this category'
                ], 422);
            }

            // Ensure product has stock
            DB::table('products3')
                ->where('UPC', $request->upc)
                ->update(['QTY' => 100]);
        }

        $maxOrder = DB::table('specialty_products')
            ->where('specialty_category_id', $categoryId)
            ->max('sort_order') ?? 0;

        $id = DB::table('specialty_products')->insertGetId([
            'specialty_category_id' => $categoryId,
            'upc' => $request->upc,
            'label' => $request->label,
            'description' => $request->description,
            'sizes' => $request->sizes,
            'colors' => $request->colors,
            'price' => $request->price,
            'sort_order' => $maxOrder + 1,
            'is_visible' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $product = DB::table('specialty_products')->where('id', $id)->first();

        // Enrich with linked product data
        if ($product->upc) {
            $linkedProduct = DB::table('products3')
                ->where('UPC', $product->upc)
                ->first();

            if ($linkedProduct) {
                $product->product_image = $linkedProduct->Image;
                $product->product_price = $product->price ?? $linkedProduct->UnitPrice;
                $product->quantity = $linkedProduct->QTY;
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Product added successfully',
            'data' => $product
        ], 201);
    }

    /**
     * Update a specialty product
     */
    public function updateProduct(Request $request, $id)
    {
        $product = DB::table('specialty_products')->where('id', $id)->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'label' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'sizes' => 'nullable|string|max:500',
            'colors' => 'nullable|string|max:500',
            'price' => 'nullable|numeric|min:0',
            'is_visible' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $updateData = ['updated_at' => now()];

        if ($request->has('label')) $updateData['label'] = $request->label;
        if ($request->has('description')) $updateData['description'] = $request->description;
        if ($request->has('sizes')) $updateData['sizes'] = $request->sizes;
        if ($request->has('colors')) $updateData['colors'] = $request->colors;
        if ($request->has('price')) $updateData['price'] = $request->price;
        if ($request->has('is_visible')) $updateData['is_visible'] = $request->is_visible;

        DB::table('specialty_products')->where('id', $id)->update($updateData);

        $product = DB::table('specialty_products')->where('id', $id)->first();

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'data' => $product
        ]);
    }

    /**
     * Delete a specialty product
     */
    public function destroyProduct($id)
    {
        $product = DB::table('specialty_products')->where('id', $id)->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        DB::table('specialty_products')->where('id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product removed successfully'
        ]);
    }

    /**
     * Reorder products within a category
     */
    public function reorderProducts(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order' => 'required|array',
            'order.*' => 'integer|exists:specialty_products,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        foreach ($request->order as $index => $id) {
            DB::table('specialty_products')
                ->where('id', $id)
                ->update(['sort_order' => $index + 1, 'updated_at' => now()]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Products reordered successfully'
        ]);
    }

    // ==================== PUBLIC ENDPOINTS ====================

    /**
     * Public: Get all visible specialty categories with products
     */
    public function getPublicCategories()
    {
        $categories = DB::table('specialty_categories')
            ->where('is_visible', true)
            ->orderBy('sort_order')
            ->get();

        foreach ($categories as $cat) {
            // Get visible products count
            $cat->products_count = DB::table('specialty_products')
                ->where('specialty_category_id', $cat->id)
                ->where('is_visible', true)
                ->count();
        }

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    /**
     * Public: Get a single category with its products
     */
    public function getPublicCategory($id)
    {
        $category = DB::table('specialty_categories')
            ->where('id', $id)
            ->where('is_visible', true)
            ->first();

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        }

        // Get visible products
        $products = DB::table('specialty_products')
            ->where('specialty_category_id', $id)
            ->where('is_visible', true)
            ->orderBy('sort_order')
            ->get();

        // Enrich products
        foreach ($products as $product) {
            if ($product->upc) {
                $linkedProduct = DB::table('products3')
                    ->where('UPC', $product->upc)
                    ->first();

                if ($linkedProduct) {
                    $product->product_image = $linkedProduct->Image;
                    $product->product_price = $product->price ?? $linkedProduct->UnitPrice;
                    $product->product_sale_price = $linkedProduct->sale_price;
                    $product->quantity = $linkedProduct->QTY;
                    $product->item_number = $linkedProduct->ItemNumber;
                    $product->product_id = $linkedProduct->ID;
                }
            } else {
                $product->product_image = null;
                $product->product_price = $product->price;
                $product->product_sale_price = null;
                $product->quantity = 100;
                $product->item_number = null;
                $product->product_id = null;
            }
        }

        $category->products = $products;
        $category->products_count = count($products);

        return response()->json([
            'success' => true,
            'data' => $category
        ]);
    }

    /**
     * Search products for adding to specialty categories
     */
    public function searchProducts(Request $request)
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json([
                'success' => true,
                'data' => []
            ]);
        }

        $products = DB::table('products3')
            ->where(function ($q) use ($query) {
                $q->where('UPC', 'like', "%{$query}%")
                  ->orWhere('ShortDescription', 'like', "%{$query}%")
                  ->orWhere('ItemNumber', 'like', "%{$query}%");
            })
            ->limit(20)
            ->get(['ID', 'UPC', 'ItemNumber', 'ShortDescription', 'LngDescription', 'UnitPrice', 'Image', 'QTY', 'ItemSize']);

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }
}
