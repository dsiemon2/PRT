<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WholesaleController extends Controller
{
    /**
     * Get all wholesale accounts with filtering
     */
    public function index(Request $request)
    {
        $query = DB::table('wholesale_accounts')
            ->whereNull('deleted_at');

        // Filters
        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->tier) {
            $query->where('tier', $request->tier);
        }

        if ($request->search) {
            $search = '%' . $request->search . '%';
            $query->where(function($q) use ($search) {
                $q->where('business_name', 'like', $search)
                  ->orWhere('account_number', 'like', $search)
                  ->orWhere('primary_contact_name', 'like', $search)
                  ->orWhere('primary_contact_email', 'like', $search);
            });
        }

        // Sorting
        $sortField = $request->get('sort', 'created_at');
        $sortDir = $request->get('direction', 'desc');
        $query->orderBy($sortField, $sortDir);

        $perPage = $request->get('per_page', 20);
        $accounts = $query->paginate($perPage);

        return response()->json($accounts);
    }

    /**
     * Get wholesale account statistics
     */
    public function stats()
    {
        $stats = [
            'total' => DB::table('wholesale_accounts')->whereNull('deleted_at')->count(),
            'pending' => DB::table('wholesale_accounts')->where('status', 'pending')->whereNull('deleted_at')->count(),
            'approved' => DB::table('wholesale_accounts')->where('status', 'approved')->whereNull('deleted_at')->count(),
            'suspended' => DB::table('wholesale_accounts')->where('status', 'suspended')->whereNull('deleted_at')->count(),
            'total_credit_limit' => DB::table('wholesale_accounts')->where('status', 'approved')->whereNull('deleted_at')->sum('credit_limit'),
        ];

        // By tier
        $stats['by_tier'] = DB::table('wholesale_accounts')
            ->select('tier', DB::raw('COUNT(*) as count'))
            ->where('status', 'approved')
            ->whereNull('deleted_at')
            ->groupBy('tier')
            ->get();

        // Recent orders
        $stats['recent_orders'] = DB::table('wholesale_orders')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'), DB::raw('SUM(total) as total'))
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date', 'desc')
            ->limit(10)
            ->get();

        return response()->json($stats);
    }

    /**
     * Get a single wholesale account
     */
    public function show($id)
    {
        $account = DB::table('wholesale_accounts')
            ->where('id', $id)
            ->first();

        if (!$account) {
            return response()->json(['error' => 'Account not found'], 404);
        }

        // Get orders
        $orders = DB::table('wholesale_orders')
            ->where('account_id', $id)
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        // Get customer info if linked
        $customer = null;
        if ($account->customer_id) {
            $customer = DB::table('customers')
                ->where('ID', $account->customer_id)
                ->first();
        }

        return response()->json([
            'account' => $account,
            'orders' => $orders,
            'customer' => $customer,
        ]);
    }

    /**
     * Create a new wholesale account
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|integer',
            'business_name' => 'required|string|max:255',
        ]);

        // Generate account number
        $lastAccount = DB::table('wholesale_accounts')->orderBy('id', 'desc')->first();
        $nextNum = $lastAccount ? (intval(substr($lastAccount->account_number, 3)) + 1) : 1;
        $accountNumber = 'WS-' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);

        $id = DB::table('wholesale_accounts')->insertGetId([
            'account_number' => $accountNumber,
            'customer_id' => $request->customer_id,
            'business_name' => $request->business_name,
            'business_type' => $request->business_type,
            'tax_id' => $request->tax_id,
            'resale_certificate' => $request->resale_certificate,
            'tier' => $request->tier ?? 'bronze',
            'discount_percentage' => $request->discount_percentage ?? 0,
            'credit_limit' => $request->credit_limit ?? 0,
            'payment_terms_days' => $request->payment_terms_days ?? 30,
            'status' => 'pending',
            'primary_contact_name' => $request->primary_contact_name,
            'primary_contact_email' => $request->primary_contact_email,
            'primary_contact_phone' => $request->primary_contact_phone,
            'billing_address' => $request->billing_address,
            'shipping_address' => $request->shipping_address,
            'notes' => $request->notes,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'message' => 'Wholesale account created successfully',
            'account_id' => $id,
            'account_number' => $accountNumber,
        ], 201);
    }

    /**
     * Update a wholesale account
     */
    public function update(Request $request, $id)
    {
        $account = DB::table('wholesale_accounts')->where('id', $id)->first();
        if (!$account) {
            return response()->json(['error' => 'Account not found'], 404);
        }

        $updateData = ['updated_at' => now()];

        $fields = ['business_name', 'business_type', 'tax_id', 'resale_certificate',
                   'tier', 'discount_percentage', 'credit_limit', 'payment_terms_days',
                   'status', 'primary_contact_name', 'primary_contact_email',
                   'primary_contact_phone', 'billing_address', 'shipping_address', 'notes'];

        foreach ($fields as $field) {
            if ($request->has($field)) {
                $updateData[$field] = $request->$field;
            }
        }

        // Handle status changes
        if (isset($updateData['status'])) {
            if ($updateData['status'] === 'approved' && $account->status !== 'approved') {
                $updateData['approved_at'] = now();
                $updateData['approved_by'] = $request->approved_by ?? 1;
            }
        }

        DB::table('wholesale_accounts')->where('id', $id)->update($updateData);

        return response()->json(['message' => 'Account updated successfully']);
    }

    /**
     * Approve a wholesale account
     */
    public function approve(Request $request, $id)
    {
        $account = DB::table('wholesale_accounts')->where('id', $id)->first();
        if (!$account) {
            return response()->json(['error' => 'Account not found'], 404);
        }

        DB::table('wholesale_accounts')->where('id', $id)->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => $request->approved_by ?? 1,
            'tier' => $request->tier ?? $account->tier,
            'discount_percentage' => $request->discount_percentage ?? $account->discount_percentage,
            'credit_limit' => $request->credit_limit ?? $account->credit_limit,
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Account approved successfully']);
    }

    /**
     * Suspend a wholesale account
     */
    public function suspend(Request $request, $id)
    {
        DB::table('wholesale_accounts')->where('id', $id)->update([
            'status' => 'suspended',
            'notes' => $request->reason ? "Suspended: " . $request->reason : null,
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Account suspended']);
    }

    /**
     * Delete a wholesale account (soft delete)
     */
    public function destroy($id)
    {
        DB::table('wholesale_accounts')->where('id', $id)->update([
            'deleted_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Account deleted successfully']);
    }

    /**
     * Get wholesale orders
     */
    public function orders(Request $request)
    {
        $query = DB::table('wholesale_orders')
            ->leftJoin('wholesale_accounts', 'wholesale_orders.account_id', '=', 'wholesale_accounts.id')
            ->select(
                'wholesale_orders.*',
                'wholesale_accounts.business_name',
                'wholesale_accounts.account_number as account_number'
            );

        if ($request->account_id) {
            $query->where('wholesale_orders.account_id', $request->account_id);
        }

        if ($request->status) {
            $query->where('wholesale_orders.status', $request->status);
        }

        if ($request->payment_status) {
            $query->where('wholesale_orders.payment_status', $request->payment_status);
        }

        $query->orderBy('wholesale_orders.created_at', 'desc');

        $perPage = $request->get('per_page', 20);
        return response()->json($query->paginate($perPage));
    }

    /**
     * Create a wholesale order
     */
    public function createOrder(Request $request)
    {
        $request->validate([
            'account_id' => 'required|exists:wholesale_accounts,id',
            'subtotal' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
        ]);

        $account = DB::table('wholesale_accounts')->where('id', $request->account_id)->first();

        // Generate order number
        $lastOrder = DB::table('wholesale_orders')->orderBy('id', 'desc')->first();
        $nextNum = $lastOrder ? (intval(substr($lastOrder->order_number, 3)) + 1) : 1;
        $orderNumber = 'WO-' . str_pad($nextNum, 5, '0', STR_PAD_LEFT);

        $id = DB::table('wholesale_orders')->insertGetId([
            'order_number' => $orderNumber,
            'account_id' => $request->account_id,
            'order_id' => $request->order_id,
            'subtotal' => $request->subtotal,
            'discount_amount' => $request->discount_amount ?? ($request->subtotal * $account->discount_percentage / 100),
            'tax_amount' => $request->tax_amount ?? 0,
            'total' => $request->total,
            'status' => $request->status ?? 'pending',
            'payment_status' => 'unpaid',
            'due_date' => Carbon::now()->addDays($account->payment_terms_days),
            'notes' => $request->notes,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'message' => 'Wholesale order created',
            'order_id' => $id,
            'order_number' => $orderNumber,
        ], 201);
    }

    /**
     * Update wholesale order
     */
    public function updateOrder(Request $request, $id)
    {
        $order = DB::table('wholesale_orders')->where('id', $id)->first();
        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        $updateData = ['updated_at' => now()];

        $fields = ['status', 'payment_status', 'due_date', 'notes'];
        foreach ($fields as $field) {
            if ($request->has($field)) {
                $updateData[$field] = $request->$field;
            }
        }

        DB::table('wholesale_orders')->where('id', $id)->update($updateData);

        return response()->json(['message' => 'Order updated successfully']);
    }

    /**
     * Get tier pricing info
     */
    public function tiers()
    {
        $tiers = [
            [
                'code' => 'bronze',
                'name' => 'Bronze',
                'discount' => 15,
                'credit_limit' => 10000,
                'requirements' => 'New wholesale accounts',
            ],
            [
                'code' => 'silver',
                'name' => 'Silver',
                'discount' => 20,
                'credit_limit' => 25000,
                'requirements' => '6+ months history, $25k+ annual orders',
            ],
            [
                'code' => 'gold',
                'name' => 'Gold',
                'discount' => 25,
                'credit_limit' => 50000,
                'requirements' => '1+ year history, $50k+ annual orders',
            ],
            [
                'code' => 'platinum',
                'name' => 'Platinum',
                'discount' => 30,
                'credit_limit' => 100000,
                'requirements' => '2+ years history, $100k+ annual orders',
            ],
        ];

        return response()->json($tiers);
    }
}
