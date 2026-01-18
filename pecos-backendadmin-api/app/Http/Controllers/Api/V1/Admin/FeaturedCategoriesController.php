<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class FeaturedCategoriesController extends Controller
{
    /**
     * Get all featured categories with category data.
     */
    public function index(): JsonResponse
    {
        $featuredCategories = DB::table('featured_categories as fc')
            ->join('categories as c', 'fc.category_id', '=', 'c.CategoryCode')
            ->select(
                'fc.id',
                'fc.category_id',
                'fc.label',
                'fc.description',
                'fc.sort_order',
                'c.Category as category_name',
                'c.image as category_image'
            )
            ->orderBy('fc.sort_order')
            ->get();

        // Get product counts for each category
        foreach ($featuredCategories as $fc) {
            $fc->products_count = DB::table('products3')
                ->where('CategoryCode', $fc->category_id)
                ->count();
        }

        // Get visibility setting
        $visibility = DB::table('settings')
            ->where('setting_group', 'featured_categories')
            ->where('setting_key', 'featured_categories_visible')
            ->value('setting_value');

        return response()->json([
            'success' => true,
            'data' => [
                'featured_categories' => $featuredCategories,
                'is_visible' => $visibility === 'true' || $visibility === '1',
                'max_allowed' => 9
            ]
        ]);
    }

    /**
     * Get all available categories for dropdown.
     */
    public function getCategories(): JsonResponse
    {
        $categories = DB::table('categories')
            ->where('IsBottom', 1)
            ->select('CategoryCode as id', 'Category as name', 'image')
            ->orderBy('Category')
            ->get();

        // Add product counts
        foreach ($categories as $cat) {
            $cat->products_count = DB::table('products3')
                ->where('CategoryCode', $cat->id)
                ->count();
        }

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    /**
     * Add a new featured category.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category_id' => 'required|integer',
            'label' => 'required|string|max:100',
            'description' => 'nullable|string',
        ]);

        // Check if max limit reached
        $count = DB::table('featured_categories')->count();
        if ($count >= 9) {
            return response()->json([
                'success' => false,
                'message' => 'Maximum of 9 featured categories allowed.'
            ], 400);
        }

        // Check if category already featured
        $exists = DB::table('featured_categories')
            ->where('category_id', $validated['category_id'])
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'This category is already featured.'
            ], 400);
        }

        // Get next sort order
        $maxOrder = DB::table('featured_categories')->max('sort_order') ?? 0;

        $id = DB::table('featured_categories')->insertGetId([
            'category_id' => $validated['category_id'],
            'label' => $validated['label'],
            'description' => $validated['description'] ?? '',
            'sort_order' => $maxOrder + 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Featured category added successfully.',
            'data' => ['id' => $id]
        ]);
    }

    /**
     * Update a featured category.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'category_id' => 'sometimes|integer',
            'label' => 'sometimes|string|max:100',
            'description' => 'sometimes|string',
        ]);

        $featured = DB::table('featured_categories')->where('id', $id)->first();
        if (!$featured) {
            return response()->json([
                'success' => false,
                'message' => 'Featured category not found.'
            ], 404);
        }

        // If changing category, check if new category is already featured
        if (isset($validated['category_id']) && $validated['category_id'] != $featured->category_id) {
            $exists = DB::table('featured_categories')
                ->where('category_id', $validated['category_id'])
                ->where('id', '!=', $id)
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'This category is already featured.'
                ], 400);
            }
        }

        DB::table('featured_categories')
            ->where('id', $id)
            ->update(array_merge($validated, ['updated_at' => now()]));

        return response()->json([
            'success' => true,
            'message' => 'Featured category updated successfully.'
        ]);
    }

    /**
     * Delete a featured category.
     */
    public function destroy(int $id): JsonResponse
    {
        $featured = DB::table('featured_categories')->where('id', $id)->first();
        if (!$featured) {
            return response()->json([
                'success' => false,
                'message' => 'Featured category not found.'
            ], 404);
        }

        DB::table('featured_categories')->where('id', $id)->delete();

        // Re-order remaining categories
        $remaining = DB::table('featured_categories')
            ->orderBy('sort_order')
            ->get();

        foreach ($remaining as $index => $fc) {
            DB::table('featured_categories')
                ->where('id', $fc->id)
                ->update(['sort_order' => $index + 1]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Featured category removed successfully.'
        ]);
    }

    /**
     * Reorder featured categories.
     */
    public function reorder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer',
        ]);

        foreach ($validated['order'] as $index => $id) {
            DB::table('featured_categories')
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
     * Toggle visibility of featured categories section.
     */
    public function toggleVisibility(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'is_visible' => 'required|boolean',
        ]);

        DB::table('settings')
            ->where('setting_group', 'featured_categories')
            ->where('setting_key', 'featured_categories_visible')
            ->update([
                'setting_value' => $validated['is_visible'] ? 'true' : 'false',
                'updated_at' => now()
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Visibility updated successfully.'
        ]);
    }

    /**
     * Upload/update category image.
     */
    public function uploadImage(Request $request, int $categoryId): JsonResponse
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        // Check if category exists
        $category = DB::table('categories')
            ->where('CategoryCode', $categoryId)
            ->first();

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found.'
            ], 404);
        }

        // Store the image
        $file = $request->file('image');
        $filename = 'category_' . $categoryId . '_' . time() . '.' . $file->getClientOriginalExtension();

        // Save to PRT3 assets folder
        $destinationPath = 'C:/xampp/htdocs/PRT3/assets/images/categories';
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        $file->move($destinationPath, $filename);
        $imagePath = 'images/categories/' . $filename;

        // Update category image in database
        DB::table('categories')
            ->where('CategoryCode', $categoryId)
            ->update(['image' => $imagePath]);

        return response()->json([
            'success' => true,
            'message' => 'Image uploaded successfully.',
            'data' => ['image_path' => $imagePath]
        ]);
    }

    /**
     * Public endpoint for frontend - get featured categories.
     */
    public function getPublic(): JsonResponse
    {
        // Check visibility
        $visibility = DB::table('settings')
            ->where('setting_group', 'featured_categories')
            ->where('setting_key', 'featured_categories_visible')
            ->value('setting_value');

        if ($visibility !== 'true' && $visibility !== '1') {
            return response()->json([
                'success' => true,
                'data' => [
                    'is_visible' => false,
                    'featured_categories' => []
                ]
            ]);
        }

        $featuredCategories = DB::table('featured_categories as fc')
            ->join('categories as c', 'fc.category_id', '=', 'c.CategoryCode')
            ->select(
                'fc.id',
                'fc.category_id',
                'fc.label',
                'fc.description',
                'fc.sort_order',
                'c.Category as category_name',
                'c.image as category_image'
            )
            ->orderBy('fc.sort_order')
            ->get();

        // Get product counts
        foreach ($featuredCategories as $fc) {
            $fc->products_count = DB::table('products3')
                ->where('CategoryCode', $fc->category_id)
                ->count();
        }

        return response()->json([
            'success' => true,
            'data' => [
                'is_visible' => true,
                'featured_categories' => $featuredCategories
            ]
        ]);
    }
}
