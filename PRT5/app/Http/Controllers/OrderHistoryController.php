<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderHistoryController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = Order::where('user_id', $user->id)
            ->with('items');

        // Filter by status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Search by order number
        if ($request->filled('search')) {
            $query->where('order_number', 'like', '%' . $request->search . '%');
        }

        $orders = $query->orderBy('order_date', 'desc')
            ->paginate(10)
            ->withQueryString();

        $statusCounts = [
            'all' => Order::where('user_id', $user->id)->count(),
            'pending' => Order::where('user_id', $user->id)->pending()->count(),
            'processing' => Order::where('user_id', $user->id)->processing()->count(),
            'shipped' => Order::where('user_id', $user->id)->shipped()->count(),
            'delivered' => Order::where('user_id', $user->id)->delivered()->count(),
            'cancelled' => Order::where('user_id', $user->id)->cancelled()->count(),
        ];

        return view('account.orders.index', compact('orders', 'statusCounts'));
    }

    public function show(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $order->load('items.product');

        return view('account.orders.show', compact('order'));
    }
}
