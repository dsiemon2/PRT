<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\WishlistItem;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Recent orders
        $recentOrders = Order::where('user_id', $user->id)
            ->orderBy('order_date', 'desc')
            ->take(5)
            ->get();

        // Order statistics
        $orderStats = [
            'total_orders' => Order::where('user_id', $user->id)->count(),
            'pending_orders' => Order::where('user_id', $user->id)->pending()->count(),
            'total_spent' => Order::where('user_id', $user->id)->sum('total_amount'),
        ];

        // Wishlist count
        $wishlistCount = WishlistItem::where('user_id', $user->id)->count();

        return view('account.index', compact(
            'user', 'recentOrders', 'orderStats', 'wishlistCount'
        ));
    }
}
