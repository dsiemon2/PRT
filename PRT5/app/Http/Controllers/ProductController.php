<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category');

        // Category filter
        $categoryId = $request->get('catid');
        if ($categoryId) {
            $query->where('CategoryCode', $categoryId);
        }

        // Search
        $search = $request->get('search');
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('ShortDescription', 'like', "%{$search}%")
                  ->orWhere('ItemNumber', 'like', "%{$search}%")
                  ->orWhere('UPC', 'like', "%{$search}%");
            });
        }

        // Price filters
        if ($request->filled('min_price')) {
            $query->where('UnitPrice', '>=', $request->get('min_price'));
        }
        if ($request->filled('max_price')) {
            $query->where('UnitPrice', '<=', $request->get('max_price'));
        }

        // Size filter
        if ($request->filled('size')) {
            $query->where('ItemSize', $request->get('size'));
        }

        // Sorting
        $sortBy = $request->get('sort', '');
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
            default:
                $query->orderBy('ID', 'asc');
        }

        // Pagination
        $products = $query->paginate(12)->appends($request->except('page'));

        // Get categories for sidebar
        $categories = Category::withCount(['products' => function($q) {
            $q->whereNotNull('ID');
        }])
        ->where('IsBottom', 1)
        ->having('products_count', '>', 0)
        ->orderBy('Category')
        ->get();

        // Category name
        $categoryName = 'All Products';
        if ($categoryId) {
            $category = Category::where('CategoryCode', $categoryId)->first();
            if ($category) {
                $categoryName = $category->Category;
            }
        }

        // Filters
        $filters = [
            'catid' => $categoryId,
            'search' => $search,
            'min_price' => $request->get('min_price'),
            'max_price' => $request->get('max_price'),
            'size' => $request->get('size'),
            'sort' => $sortBy,
        ];

        // Available sizes
        $sizes = ['6', '6.5', '7', '7.5', '8', '8.5', '9', '9.5', '10', '10.5', '11', '11.5', '12', '12.5', '13', '14'];

        return view('shop.products.index', compact(
            'products', 'categories', 'categoryName', 'filters', 'sizes'
        ));
    }

    public function show($id)
    {
        $product = Product::with(['category', 'images', 'reviews' => function($q) {
            $q->approved()->latest()->limit(10);
        }])->findOrFail($id);

        // Frequently bought together (same category, different product)
        $frequentlyBought = Product::where('CategoryCode', $product->CategoryCode)
            ->where('ID', '!=', $product->ID)
            ->inRandomOrder()
            ->limit(4)
            ->get();

        // Related products (similar price range ±30%, different category allowed)
        $minPrice = $product->UnitPrice * 0.7;
        $maxPrice = $product->UnitPrice * 1.3;
        $relatedProducts = Product::where('ID', '!=', $product->ID)
            ->where(function($q) use ($product, $minPrice, $maxPrice) {
                $q->where('CategoryCode', $product->CategoryCode)
                  ->orWhereBetween('UnitPrice', [$minPrice, $maxPrice]);
            })
            ->whereNotIn('ID', $frequentlyBought->pluck('ID'))
            ->inRandomOrder()
            ->limit(4)
            ->get();

        return view('shop.products.show', compact('product', 'relatedProducts', 'frequentlyBought'));
    }

    public function compare(Request $request)
    {
        $productIds = session('compare_products', []);

        $products = Product::with('category')
            ->whereIn('ID', $productIds)
            ->get();

        return view('shop.products.compare', compact('products'));
    }

    public function compareCount()
    {
        $productIds = session('compare_products', []);
        return response()->json([
            'count' => count($productIds),
            'product_ids' => $productIds,
        ]);
    }

    public function addToCompare(Request $request)
    {
        $productId = $request->input('product_id');
        $compareProducts = session('compare_products', []);

        if (count($compareProducts) >= 4) {
            return response()->json([
                'success' => false,
                'message' => 'You can compare up to 4 products at a time.',
            ]);
        }

        if (!in_array($productId, $compareProducts)) {
            $compareProducts[] = $productId;
            session(['compare_products' => $compareProducts]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Product added to comparison.',
            'count' => count($compareProducts),
        ]);
    }

    public function removeFromCompare(Request $request)
    {
        $isAjax = $request->ajax() || $request->wantsJson() || $request->isJson();

        if ($request->input('clear_all')) {
            session()->forget('compare_products');

            if ($isAjax) {
                return response()->json([
                    'success' => true,
                    'message' => 'All products removed from comparison.',
                    'count' => 0,
                ]);
            }
            return redirect()->route('products.compare')->with('success', 'Comparison cleared.');
        }

        $productId = $request->input('product_id');
        $compareProducts = session('compare_products', []);

        $compareProducts = array_filter($compareProducts, fn($id) => $id != $productId);
        session(['compare_products' => array_values($compareProducts)]);

        if ($isAjax) {
            return response()->json([
                'success' => true,
                'message' => 'Product removed from comparison.',
                'count' => count($compareProducts),
            ]);
        }

        return redirect()->route('products.compare')->with('success', 'Product removed from comparison.');
    }
}
