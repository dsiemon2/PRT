<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class FeaturedProductsController extends Controller
{
    /**
     * Get all featured products with product data.
     */
    public function index(): JsonResponse
    {
        $featuredProducts = DB::table('featured_products as fp')
            ->join('products3 as p', 'fp.upc', '=', 'p.UPC')
            ->select(
                'fp.id',
                'fp.upc',
                'fp.label',
                'fp.description',
                'fp.sort_order',
                'p.ShortDescription as product_name',
                'p.LngDescription as full_description',
                'p.Image as product_image',
                'p.UnitPrice as price',
                'p.sale_price',
                'p.QTY as quantity'
            )
            ->orderBy('fp.sort_order')
            ->get();

        // Get visibility setting
        $visibility = DB::table('settings')
            ->where('setting_group', 'featured_products')
            ->where('setting_key', 'featured_products_visible')
            ->value('setting_value');

        // Get section title
        $sectionTitle = DB::table('settings')
            ->where('setting_group', 'featured_products')
            ->where('setting_key', 'featured_products_title')
            ->value('setting_value') ?? 'Featured Products';

        return response()->json([
            'success' => true,
            'data' => [
                'featured_products' => $featuredProducts,
                'is_visible' => $visibility === 'true' || $visibility === '1',
                'section_title' => $sectionTitle,
                'max_allowed' => 9
            ]
        ]);
    }

    /**
     * Get all available products for dropdown.
     */
    public function getProducts(Request $request): JsonResponse
    {
        $search = $request->get('search', '');

        $query = DB::table('products3')
            ->select(
                'UPC as upc',
                'ShortDescription as name',
                'LngDescription as full_description',
                'Image as image',
                'UnitPrice as price',
                'sale_price',
                'QTY as quantity'
            )
            ->where('QTY', '>', 0);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('UPC', 'like', "%{$search}%")
                    ->orWhere('ShortDescription', 'like', "%{$search}%")
                    ->orWhere('LngDescription', 'like', "%{$search}%");
            });
        }

        $products = $query->orderBy('ShortDescription')->limit(50)->get();

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    /**
     * Add a new featured product.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'upc' => 'required|string|max:20',
            'label' => 'required|string|max:100',
            'description' => 'nullable|string',
        ]);

        // Check if max limit reached
        $count = DB::table('featured_products')->count();
        if ($count >= 9) {
            return response()->json([
                'success' => false,
                'message' => 'Maximum of 9 featured products allowed.'
            ], 400);
        }

        // Check if product exists
        $product = DB::table('products3')->where('UPC', $validated['upc'])->first();
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found.'
            ], 404);
        }

        // Check if product already featured
        $exists = DB::table('featured_products')
            ->where('upc', $validated['upc'])
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'This product is already featured.'
            ], 400);
        }

        // Get next sort order
        $maxOrder = DB::table('featured_products')->max('sort_order') ?? 0;

        $id = DB::table('featured_products')->insertGetId([
            'upc' => $validated['upc'],
            'label' => $validated['label'],
            'description' => $validated['description'] ?? '',
            'sort_order' => $maxOrder + 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Featured product added successfully.',
            'data' => ['id' => $id]
        ]);
    }

    /**
     * Update a featured product.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'label' => 'sometimes|string|max:100',
            'description' => 'sometimes|string',
        ]);

        $featured = DB::table('featured_products')->where('id', $id)->first();
        if (!$featured) {
            return response()->json([
                'success' => false,
                'message' => 'Featured product not found.'
            ], 404);
        }

        DB::table('featured_products')
            ->where('id', $id)
            ->update(array_merge($validated, ['updated_at' => now()]));

        return response()->json([
            'success' => true,
            'message' => 'Featured product updated successfully.'
        ]);
    }

    /**
     * Delete a featured product.
     */
    public function destroy(int $id): JsonResponse
    {
        $featured = DB::table('featured_products')->where('id', $id)->first();
        if (!$featured) {
            return response()->json([
                'success' => false,
                'message' => 'Featured product not found.'
            ], 404);
        }

        DB::table('featured_products')->where('id', $id)->delete();

        // Re-order remaining products
        $remaining = DB::table('featured_products')
            ->orderBy('sort_order')
            ->get();

        foreach ($remaining as $index => $fp) {
            DB::table('featured_products')
                ->where('id', $fp->id)
                ->update(['sort_order' => $index + 1]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Featured product removed successfully.'
        ]);
    }

    /**
     * Reorder featured products.
     */
    public function reorder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer',
        ]);

        foreach ($validated['order'] as $index => $id) {
            DB::table('featured_products')
                ->where('id', $id)
                ->update([
                    'sort_order' => $index + 1,
                    'updated_at' => now()
                ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Order updated successfully.'
        ]);
    }

    /**
     * Toggle visibility of featured products section.
     */
    public function toggleVisibility(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'is_visible' => 'required|boolean',
        ]);

        DB::table('settings')
            ->updateOrInsert(
                [
                    'setting_group' => 'featured_products',
                    'setting_key' => 'featured_products_visible'
                ],
                [
                    'setting_value' => $validated['is_visible'] ? 'true' : 'false',
                    'setting_type' => 'boolean'
                ]
            );

        return response()->json([
            'success' => true,
            'message' => 'Visibility updated successfully.'
        ]);
    }

    /**
     * Update section title.
     */
    public function updateTitle(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:100',
        ]);

        DB::table('settings')
            ->updateOrInsert(
                [
                    'setting_group' => 'featured_products',
                    'setting_key' => 'featured_products_title'
                ],
                [
                    'setting_value' => $validated['title'],
                    'setting_type' => 'string'
                ]
            );

        return response()->json([
            'success' => true,
            'message' => 'Section title updated successfully.'
        ]);
    }

    /**
     * Upload/update product image.
     */
    public function uploadImage(Request $request, string $upc): JsonResponse
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        // Check if product exists
        $product = DB::table('products3')
            ->where('UPC', $upc)
            ->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found.'
            ], 404);
        }

        // Store the image
        $file = $request->file('image');
        $filename = 'product_' . $upc . '_' . time() . '.' . $file->getClientOriginalExtension();

        // Save to PRT3 assets folder
        $destinationPath = 'C:/xampp/htdocs/PRT3/assets/images/products';
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        $file->move($destinationPath, $filename);
        $imagePath = 'images/products/' . $filename;

        // Update product image in database
        DB::table('products3')
            ->where('UPC', $upc)
            ->update(['Image' => $imagePath]);

        return response()->json([
            'success' => true,
            'message' => 'Image uploaded successfully.',
            'data' => ['image_path' => $imagePath]
        ]);
    }

    /**
     * Public endpoint for frontend - get featured products.
     */
    public function getPublic(): JsonResponse
    {
        // Check visibility
        $visibility = DB::table('settings')
            ->where('setting_group', 'featured_products')
            ->where('setting_key', 'featured_products_visible')
            ->value('setting_value');

        if ($visibility !== 'true' && $visibility !== '1') {
            return response()->json([
                'success' => true,
                'data' => [
                    'is_visible' => false,
                    'featured_products' => [],
                    'section_title' => ''
                ]
            ]);
        }

        $featuredProducts = DB::table('featured_products as fp')
            ->join('products3 as p', 'fp.upc', '=', 'p.UPC')
            ->select(
                'fp.id',
                'fp.upc',
                'fp.label',
                'fp.description',
                'fp.sort_order',
                'p.ShortDescription as product_name',
                'p.Image as product_image',
                'p.UnitPrice as price',
                'p.sale_price',
                'p.QTY as quantity'
            )
            ->orderBy('fp.sort_order')
            ->get();

        // Get section title
        $sectionTitle = DB::table('settings')
            ->where('setting_group', 'featured_products')
            ->where('setting_key', 'featured_products_title')
            ->value('setting_value') ?? 'Featured Products';

        return response()->json([
            'success' => true,
            'data' => [
                'is_visible' => true,
                'featured_products' => $featuredProducts,
                'section_title' => $sectionTitle
            ]
        ]);
    }
}
