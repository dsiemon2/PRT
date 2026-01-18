<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\WishlistItem;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index()
    {
        $wishlistItems = WishlistItem::where('user_id', auth()->id())
            ->with('product')
            ->orderBy('added_at', 'desc')
            ->paginate(12);

        return view('account.wishlist.index', compact('wishlistItems'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products3,ID',
        ]);

        $existing = WishlistItem::where('user_id', auth()->id())
            ->where('product_id', $validated['product_id'])
            ->first();

        if ($existing) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Item is already in your wishlist.',
                ]);
            }
            return back()->with('info', 'Item is already in your wishlist.');
        }

        WishlistItem::create([
            'user_id' => auth()->id(),
            'product_id' => $validated['product_id'],
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Item added to wishlist.',
                'count' => WishlistItem::where('user_id', auth()->id())->count(),
            ]);
        }

        return back()->with('success', 'Item added to wishlist.');
    }

    public function check(Request $request)
    {
        $productIds = $request->get('product_ids', '');

        if (empty($productIds)) {
            return response()->json(['wishlisted' => []]);
        }

        // Handle comma-separated string or array
        if (is_string($productIds)) {
            $productIds = array_filter(explode(',', $productIds));
        }

        if (empty($productIds)) {
            return response()->json(['wishlisted' => []]);
        }

        $wishlisted = WishlistItem::where('user_id', auth()->id())
            ->whereIn('product_id', $productIds)
            ->pluck('product_id')
            ->toArray();

        return response()->json(['wishlisted' => $wishlisted]);
    }

    public function toggle(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products3,ID',
        ]);

        $existing = WishlistItem::where('user_id', auth()->id())
            ->where('product_id', $validated['product_id'])
            ->first();

        if ($existing) {
            $existing->delete();
            return response()->json([
                'success' => true,
                'message' => 'Item removed from wishlist.',
                'in_wishlist' => false,
                'count' => WishlistItem::where('user_id', auth()->id())->count(),
            ]);
        }

        WishlistItem::create([
            'user_id' => auth()->id(),
            'product_id' => $validated['product_id'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Item added to wishlist.',
            'in_wishlist' => true,
            'count' => WishlistItem::where('user_id', auth()->id())->count(),
        ]);
    }

    public function destroy(WishlistItem $wishlist)
    {
        if ($wishlist->user_id !== auth()->id()) {
            abort(403);
        }

        $wishlist->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Item removed from wishlist.',
                'count' => WishlistItem::where('user_id', auth()->id())->count(),
            ]);
        }

        return back()->with('success', 'Item removed from wishlist.');
    }
}
