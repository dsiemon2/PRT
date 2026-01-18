<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    /**
     * Get all categories.
     *
     * @OA\Get(
     *     path="/categories",
     *     summary="Get all categories",
     *     tags={"Categories"},
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $search = $request->get('search');
        $perPage = $request->get('per_page', 15);
        $paginate = $request->has('page');

        $query = Category::withCount('products')->ordered();

        if ($search) {
            $query->where('Category', 'like', "%{$search}%");
        }

        // If pagination requested
        if ($paginate) {
            $paginated = $query->paginate($perPage);

            // Calculate aggregate product counts for parent categories
            $categoriesArray = $this->calculateProductCounts($paginated->items());

            return response()->json([
                'success' => true,
                'data' => $categoriesArray,
                'meta' => [
                    'current_page' => $paginated->currentPage(),
                    'last_page' => $paginated->lastPage(),
                    'per_page' => $paginated->perPage(),
                    'total' => $paginated->total(),
                    'from' => $paginated->firstItem(),
                    'to' => $paginated->lastItem(),
                ]
            ]);
        }

        // Non-paginated (original behavior)
        $categories = $query->get();
        $categoriesArray = $this->calculateProductCounts($categories->toArray());

        return response()->json([
            'success' => true,
            'data' => $categoriesArray
        ]);
    }

    /**
     * Calculate aggregate product counts for parent categories.
     */
    private function calculateProductCounts($categories): array
    {
        $categoriesArray = is_array($categories) ? $categories : collect($categories)->toArray();
        $categoryMap = [];

        // Build a map for quick lookup
        foreach ($categoriesArray as $index => $cat) {
            $categoryMap[$cat['CategoryCode']] = $index;
        }

        // For each non-bottom category, sum up child category products
        foreach ($categoriesArray as $index => $cat) {
            if (!$cat['IsBottom']) {
                $totalProducts = 0;
                $parentLevel = $cat['Level'];
                $parentCode = $cat['CategoryCode'];

                // Find all descendant categories (higher level numbers that come after this one)
                foreach ($categoriesArray as $child) {
                    if ($child['CategoryCode'] > $parentCode &&
                        $child['Level'] > $parentLevel &&
                        $child['IsBottom']) {
                        // Check if this is actually a descendant (not from another branch)
                        // by ensuring no category of same or lower level exists between them
                        $isDescendant = true;
                        foreach ($categoriesArray as $between) {
                            if ($between['CategoryCode'] > $parentCode &&
                                $between['CategoryCode'] < $child['CategoryCode'] &&
                                $between['Level'] <= $parentLevel) {
                                $isDescendant = false;
                                break;
                            }
                        }
                        if ($isDescendant) {
                            $totalProducts += $child['products_count'];
                        }
                    }
                }
                $categoriesArray[$index]['products_count'] = $totalProducts;
            }
        }

        return $categoriesArray;
    }

    /**
     * Get a single category with its products.
     *
     * @OA\Get(
     *     path="/categories/{categoryCode}",
     *     summary="Get category by code",
     *     tags={"Categories"},
     *     @OA\Parameter(name="categoryCode", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="Category not found")
     * )
     */
    public function show(int $categoryCode): JsonResponse
    {
        $category = Category::withCount('products')->find($categoryCode);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $category
        ]);
    }

    /**
     * Get category tree (hierarchical).
     *
     * @OA\Get(
     *     path="/categories/tree",
     *     summary="Get category tree",
     *     tags={"Categories"},
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function tree(): JsonResponse
    {
        $categories = Category::withCount('products')
            ->ordered()
            ->get()
            ->groupBy('Level');

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    /**
     * Get bottom-level categories (with products).
     *
     * @OA\Get(
     *     path="/categories/bottom",
     *     summary="Get bottom-level categories",
     *     tags={"Categories"},
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function bottomLevel(): JsonResponse
    {
        $categories = Category::withCount('products')
            ->bottomLevel()
            ->ordered()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    /**
     * Create a new category (Admin).
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'Category' => 'required|string|max:255',
            'ShrtDescription' => 'nullable|string|max:255',
            'lngDescription' => 'nullable|string',
            'image' => 'nullable|string|max:255',
            'Directory' => 'nullable|string|max:255',
            'sOrder' => 'nullable|integer',
            'Level' => 'nullable|integer',
            'IsBottom' => 'nullable|boolean',
        ]);

        // Get next CategoryCode
        $maxCode = Category::max('CategoryCode') ?? 0;
        $newCode = $maxCode + 1;

        $category = Category::create([
            'CategoryCode' => $newCode,
            'Category' => $request->Category,
            'ShrtDescription' => $request->ShrtDescription ?? $request->Category,
            'lngDescription' => $request->lngDescription,
            'image' => $request->image,
            'Directory' => $request->Directory ?? strtolower(str_replace(' ', '-', $request->Category)),
            'sOrder' => $request->sOrder ?? $newCode,
            'Level' => $request->Level ?? 1,
            'IsBottom' => $request->IsBottom ?? true,
            'IsOrdered' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Category created successfully',
            'data' => $category
        ], 201);
    }

    /**
     * Update a category (Admin).
     */
    public function update(Request $request, int $categoryCode): JsonResponse
    {
        $category = Category::find($categoryCode);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        }

        $request->validate([
            'Category' => 'nullable|string|max:255',
            'ShrtDescription' => 'nullable|string|max:255',
            'lngDescription' => 'nullable|string',
            'image' => 'nullable|string|max:255',
            'Directory' => 'nullable|string|max:255',
            'sOrder' => 'nullable|integer',
            'Level' => 'nullable|integer',
            'IsBottom' => 'nullable|boolean',
        ]);

        $category->update($request->only([
            'Category',
            'ShrtDescription',
            'lngDescription',
            'image',
            'Directory',
            'sOrder',
            'Level',
            'IsBottom',
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully',
            'data' => $category->fresh()
        ]);
    }

    /**
     * Delete a category (Admin).
     */
    public function destroy(int $categoryCode): JsonResponse
    {
        $category = Category::withCount('products')->find($categoryCode);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        }

        // Check if category has products
        if ($category->products_count > 0) {
            return response()->json([
                'success' => false,
                'message' => "Cannot delete category with {$category->products_count} products. Reassign products first."
            ], 400);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully'
        ]);
    }

    /**
     * Reorder categories (Admin).
     */
    public function reorder(Request $request): JsonResponse
    {
        $request->validate([
            'categories' => 'required|array',
            'categories.*.CategoryCode' => 'required|integer',
            'categories.*.sOrder' => 'required|integer',
        ]);

        foreach ($request->categories as $item) {
            Category::where('CategoryCode', $item['CategoryCode'])
                ->update(['sOrder' => $item['sOrder']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Categories reordered successfully'
        ]);
    }
}
