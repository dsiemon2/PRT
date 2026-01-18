<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    /**
     * Get all customers with filters
     */
    public function index(Request $request)
    {
        $query = DB::table('users')
            ->select(
                'users.id',
                'users.first_name',
                'users.last_name',
                'users.email',
                'users.phone',
                'users.created_at',
                DB::raw('(SELECT COUNT(*) FROM orders WHERE orders.user_id = users.id) as order_count'),
                DB::raw('(SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE orders.user_id = users.id AND status NOT IN ("cancelled", "refunded")) as total_spent')
            )
            ->where('role', 'customer');

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $perPage = $request->input('per_page', 20);
        $customers = $query->orderBy('created_at', 'desc')
                          ->paginate($perPage);

        return response()->json($customers);
    }

    /**
     * Get customer details
     */
    public function show($id)
    {
        $customer = DB::table('users')
            ->select('id', 'first_name', 'last_name', 'email', 'phone', 'created_at')
            ->where('id', $id)
            ->first();

        if (!$customer) {
            return response()->json(['error' => 'Customer not found'], 404);
        }

        // Get customer stats
        $stats = [
            'total_orders' => DB::table('orders')->where('user_id', $id)->count(),
            'total_spent' => DB::table('orders')
                ->where('user_id', $id)
                ->whereNotIn('status', ['cancelled', 'refunded'])
                ->sum('total_amount') ?? 0,
            'avg_order_value' => DB::table('orders')
                ->where('user_id', $id)
                ->whereNotIn('status', ['cancelled', 'refunded'])
                ->avg('total_amount') ?? 0
        ];

        // Get addresses
        $addresses = DB::table('user_addresses')
            ->where('user_id', $id)
            ->get();

        // Get loyalty info
        $loyalty = DB::table('loyalty_members')
            ->leftJoin('loyalty_tiers', 'loyalty_members.tier_id', '=', 'loyalty_tiers.id')
            ->select('loyalty_members.*', 'loyalty_tiers.tier_name')
            ->where('loyalty_members.user_id', $id)
            ->first();

        return response()->json([
            'customer' => $customer,
            'stats' => $stats,
            'addresses' => $addresses,
            'loyalty' => $loyalty
        ]);
    }

    /**
     * Get customer orders
     */
    public function orders($id)
    {
        $orders = DB::table('orders')
            ->where('user_id', $id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($orders);
    }

    /**
     * Update customer
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'FirstName' => 'nullable|string|max:255',
            'LastName' => 'nullable|string|max:255',
            'Phone' => 'nullable|string|max:50'
        ]);

        $customer = DB::table('users')->where('id', $id)->first();
        if (!$customer) {
            return response()->json(['error' => 'Customer not found'], 404);
        }

        $updateData = [];
        if ($request->filled('FirstName')) $updateData['FirstName'] = $request->FirstName;
        if ($request->filled('LastName')) $updateData['LastName'] = $request->LastName;
        if ($request->filled('Phone')) $updateData['Phone'] = $request->Phone;

        if (!empty($updateData)) {
            DB::table('users')->where('id', $id)->update($updateData);
        }

        return response()->json(['message' => 'Customer updated']);
    }

    /**
     * Get customer statistics
     */
    public function stats()
    {
        $today = now()->startOfDay();
        $thisMonth = now()->startOfMonth();

        return response()->json([
            'total_customers' => DB::table('users')->count(),
            'new_today' => DB::table('users')->whereDate('created_at', $today)->count(),
            'new_this_month' => DB::table('users')->where('created_at', '>=', $thisMonth)->count(),
            'with_orders' => DB::table('users')
                ->whereExists(function ($query) {
                    $query->select(DB::raw(1))
                          ->from('orders')
                          ->whereColumn('orders.user_id', 'users.id');
                })
                ->count()
        ]);
    }
}
