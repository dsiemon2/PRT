<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GiftCardController extends Controller
{
    /**
     * List all gift cards with filtering and pagination.
     */
    public function index(Request $request): JsonResponse
    {
        $query = DB::table('gift_cards');

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by search (code or recipient email)
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('recipient_email', 'like', "%{$search}%")
                  ->orWhere('recipient_name', 'like', "%{$search}%");
            });
        }

        // Filter by date range
        if ($request->has('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->has('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDir = $request->get('sort_dir', 'desc');
        $query->orderBy($sortBy, $sortDir);

        // Paginate
        $perPage = $request->get('per_page', 20);
        $giftCards = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $giftCards
        ]);
    }

    /**
     * Get a single gift card with transactions.
     */
    public function show($id): JsonResponse
    {
        $giftCard = DB::table('gift_cards')->find($id);

        if (!$giftCard) {
            return response()->json([
                'success' => false,
                'message' => 'Gift card not found'
            ], 404);
        }

        // Get transactions
        $transactions = DB::table('gift_card_transactions')
            ->where('gift_card_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        $giftCard->transactions = $transactions;

        return response()->json([
            'success' => true,
            'data' => $giftCard
        ]);
    }

    /**
     * Create a new gift card.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'initial_balance' => 'required|numeric|min:1|max:1000',
            'recipient_email' => 'nullable|email',
            'recipient_name' => 'nullable|string|max:255',
            'purchaser_email' => 'nullable|email|max:255',
            'message' => 'nullable|string|max:500',
            'expires_at' => 'nullable|date|after:today',
        ]);

        // Generate unique code
        do {
            $code = strtoupper(Str::random(4) . '-' . Str::random(4) . '-' . Str::random(4));
        } while (DB::table('gift_cards')->where('code', $code)->exists());

        $id = DB::table('gift_cards')->insertGetId([
            'code' => $code,
            'initial_balance' => $request->initial_balance,
            'current_balance' => $request->initial_balance,
            'purchaser_email' => $request->purchaser_email ?? 'admin@pecos.com',
            'recipient_email' => $request->recipient_email,
            'recipient_name' => $request->recipient_name,
            'message' => $request->message,
            'status' => 'active',
            'expires_at' => $request->expires_at,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $giftCard = DB::table('gift_cards')->find($id);

        return response()->json([
            'success' => true,
            'message' => 'Gift card created successfully',
            'data' => $giftCard
        ], 201);
    }

    /**
     * Check gift card balance by code.
     */
    public function checkBalance($code): JsonResponse
    {
        $giftCard = DB::table('gift_cards')
            ->where('code', $code)
            ->first();

        if (!$giftCard) {
            return response()->json([
                'success' => false,
                'message' => 'Gift card not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'code' => $giftCard->code,
                'current_balance' => $giftCard->current_balance,
                'status' => $giftCard->status,
                'expires_at' => $giftCard->expires_at
            ]
        ]);
    }

    /**
     * Void a gift card.
     */
    public function void($id): JsonResponse
    {
        $giftCard = DB::table('gift_cards')->find($id);

        if (!$giftCard) {
            return response()->json([
                'success' => false,
                'message' => 'Gift card not found'
            ], 404);
        }

        if ($giftCard->status === 'voided') {
            return response()->json([
                'success' => false,
                'message' => 'Gift card is already voided'
            ], 400);
        }

        DB::table('gift_cards')
            ->where('id', $id)
            ->update([
                'status' => 'voided',
                'updated_at' => now()
            ]);

        // Log the void transaction
        DB::table('gift_card_transactions')->insert([
            'gift_card_id' => $id,
            'type' => 'void',
            'amount' => $giftCard->current_balance,
            'balance_after' => 0,
            'order_id' => null,
            'created_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Gift card voided successfully'
        ]);
    }

    /**
     * Adjust gift card balance.
     */
    public function adjustBalance(Request $request, $id): JsonResponse
    {
        $request->validate([
            'amount' => 'required|numeric',
            'reason' => 'required|string|max:255',
        ]);

        $giftCard = DB::table('gift_cards')->find($id);

        if (!$giftCard) {
            return response()->json([
                'success' => false,
                'message' => 'Gift card not found'
            ], 404);
        }

        if ($giftCard->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot adjust balance of inactive gift card'
            ], 400);
        }

        $newBalance = $giftCard->current_balance + $request->amount;

        if ($newBalance < 0) {
            return response()->json([
                'success' => false,
                'message' => 'Adjustment would result in negative balance'
            ], 400);
        }

        DB::table('gift_cards')
            ->where('id', $id)
            ->update([
                'current_balance' => $newBalance,
                'updated_at' => now()
            ]);

        // Log the adjustment (use 'refund' for credit, 'redemption' for debit since those are the allowed enums)
        DB::table('gift_card_transactions')->insert([
            'gift_card_id' => $id,
            'type' => $request->amount > 0 ? 'refund' : 'redemption',
            'amount' => abs($request->amount),
            'balance_after' => $newBalance,
            'order_id' => null,
            'created_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Balance adjusted successfully',
            'data' => [
                'new_balance' => $newBalance
            ]
        ]);
    }

    /**
     * Get gift card statistics.
     */
    public function stats(): JsonResponse
    {
        $stats = [
            'total_cards' => DB::table('gift_cards')->count(),
            'active_cards' => DB::table('gift_cards')->where('status', 'active')->count(),
            'total_issued' => DB::table('gift_cards')->sum('initial_balance'),
            'total_redeemed' => DB::table('gift_cards')->sum('initial_balance') - DB::table('gift_cards')->sum('current_balance'),
            'total_balance' => DB::table('gift_cards')->sum('current_balance'),
            'outstanding_balance' => DB::table('gift_cards')->where('status', 'active')->sum('current_balance'),
            'expired_cards' => DB::table('gift_cards')
                ->whereNotNull('expires_at')
                ->where('expires_at', '<', now())
                ->where('status', 'active')
                ->count(),
        ];

        // Recent activity
        $recentTransactions = DB::table('gift_card_transactions')
            ->join('gift_cards', 'gift_card_transactions.gift_card_id', '=', 'gift_cards.id')
            ->select('gift_card_transactions.*', 'gift_cards.code')
            ->orderBy('gift_card_transactions.created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'stats' => $stats,
                'recent_transactions' => $recentTransactions
            ]
        ]);
    }

    /**
     * Export gift cards to CSV.
     */
    public function export(Request $request)
    {
        $query = DB::table('gift_cards');

        // Apply same filters as index
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('recipient_email', 'like', "%{$search}%")
                  ->orWhere('recipient_name', 'like', "%{$search}%");
            });
        }

        $giftCards = $query->orderBy('created_at', 'desc')->get();

        // Build CSV
        $csv = "Code,Initial Balance,Current Balance,Recipient Name,Recipient Email,Status,Expires At,Created At\n";

        foreach ($giftCards as $card) {
            $csv .= sprintf(
                "%s,%.2f,%.2f,%s,%s,%s,%s,%s\n",
                $card->code,
                $card->initial_balance,
                $card->current_balance,
                str_replace(',', ' ', $card->recipient_name ?? ''),
                $card->recipient_email ?? '',
                $card->status,
                $card->expires_at ?? '',
                $card->created_at
            );
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="gift-cards-' . date('Y-m-d') . '.csv"',
        ]);
    }
}
