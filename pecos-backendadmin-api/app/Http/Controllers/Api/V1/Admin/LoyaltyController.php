<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class LoyaltyController extends Controller
{
    /**
     * Get loyalty program statistics.
     */
    public function stats(): JsonResponse
    {
        $totalMembers = DB::table('loyalty_members')->count();
        $activeMembers = DB::table('loyalty_members')
            ->where('available_points', '>', 0)
            ->count();
        $totalPointsIssued = DB::table('loyalty_transactions')
            ->whereIn('transaction_type', ['earned', 'bonus'])
            ->sum('points');
        $totalPointsRedeemed = DB::table('loyalty_transactions')
            ->where('transaction_type', 'redeemed')
            ->sum('points');

        // Tier breakdown
        $tierBreakdown = DB::table('loyalty_members')
            ->join('loyalty_tiers', 'loyalty_members.tier_id', '=', 'loyalty_tiers.id')
            ->select('loyalty_tiers.tier_name as tier_level', DB::raw('COUNT(*) as count'))
            ->groupBy('loyalty_tiers.tier_name')
            ->get()
            ->keyBy('tier_level');

        return response()->json([
            'success' => true,
            'data' => [
                'total_members' => $totalMembers,
                'active_members' => $activeMembers,
                'total_points_issued' => (int)$totalPointsIssued,
                'total_points_redeemed' => (int)$totalPointsRedeemed,
                'points_outstanding' => (int)($totalPointsIssued - $totalPointsRedeemed),
                'tier_breakdown' => $tierBreakdown,
            ]
        ]);
    }

    /**
     * List all loyalty members.
     */
    public function members(Request $request): JsonResponse
    {
        $query = DB::table('loyalty_members')
            ->join('users', 'loyalty_members.user_id', '=', 'users.id')
            ->leftJoin('loyalty_tiers', 'loyalty_members.tier_id', '=', 'loyalty_tiers.id')
            ->select(
                'loyalty_members.id',
                'loyalty_members.user_id',
                'loyalty_members.available_points as points_balance',
                'loyalty_members.total_points',
                'loyalty_members.lifetime_points',
                'loyalty_members.tier_id',
                'loyalty_tiers.tier_name as tier_level',
                'loyalty_members.joined_at as created_at',
                'users.email',
                'users.first_name',
                'users.last_name'
            );

        // Filter by tier
        if ($request->has('tier') && $request->tier) {
            $query->where('loyalty_tiers.tier_name', $request->tier);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('users.email', 'like', "%{$search}%")
                  ->orWhere('users.first_name', 'like', "%{$search}%")
                  ->orWhere('users.last_name', 'like', "%{$search}%");
            });
        }

        // Sort
        $sortField = $request->get('sort', 'available_points');
        $sortDir = $request->get('dir', 'desc');

        // Map common sort fields
        $sortMap = [
            'points_balance' => 'loyalty_members.available_points',
            'available_points' => 'loyalty_members.available_points',
            'lifetime_points' => 'loyalty_members.lifetime_points',
        ];
        $sortColumn = $sortMap[$sortField] ?? "loyalty_members.{$sortField}";
        $query->orderBy($sortColumn, $sortDir);

        $perPage = min($request->get('per_page', 20), 100);
        $members = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $members->items(),
            'meta' => [
                'current_page' => $members->currentPage(),
                'last_page' => $members->lastPage(),
                'per_page' => $members->perPage(),
                'total' => $members->total(),
            ]
        ]);
    }

    /**
     * Adjust points for a member.
     */
    public function adjustPoints(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'points' => 'required|integer',
            'reason' => 'required|string|max:255',
        ]);

        // Create transaction
        DB::table('loyalty_transactions')->insert([
            'user_id' => $validated['user_id'],
            'points' => abs($validated['points']),
            'transaction_type' => $validated['points'] > 0 ? 'bonus' : 'redeemed',
            'description' => $validated['reason'],
            'created_at' => now(),
        ]);

        // Update balance
        DB::table('loyalty_members')
            ->where('user_id', $validated['user_id'])
            ->increment('available_points', $validated['points']);

        if ($validated['points'] > 0) {
            DB::table('loyalty_members')
                ->where('user_id', $validated['user_id'])
                ->increment('total_points', $validated['points']);
            DB::table('loyalty_members')
                ->where('user_id', $validated['user_id'])
                ->increment('lifetime_points', $validated['points']);
        }

        $newBalance = DB::table('loyalty_members')
            ->where('user_id', $validated['user_id'])
            ->value('available_points');

        return response()->json([
            'success' => true,
            'message' => 'Points adjusted successfully',
            'data' => [
                'new_balance' => $newBalance
            ]
        ]);
    }

    /**
     * Get all tiers.
     */
    public function tiers(): JsonResponse
    {
        $tiers = DB::table('loyalty_tiers')
            ->orderBy('display_order')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $tiers
        ]);
    }

    /**
     * Get all rewards.
     */
    public function rewards(): JsonResponse
    {
        $rewards = DB::table('loyalty_rewards')
            ->orderBy('display_order')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $rewards
        ]);
    }

    /**
     * Get member transactions.
     */
    public function memberTransactions(int $userId): JsonResponse
    {
        $transactions = DB::table('loyalty_transactions')
            ->where('user_id', $userId)
            ->orderByDesc('created_at')
            ->limit(100)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $transactions
        ]);
    }

    /**
     * Update a tier.
     */
    public function updateTier(Request $request, int $tierId): JsonResponse
    {
        $validated = $request->validate([
            'min_lifetime_points' => 'required|integer|min:0',
            'points_multiplier' => 'required|numeric|min:1|max:10',
            'benefits' => 'nullable|string|max:500',
        ]);

        $tier = DB::table('loyalty_tiers')->where('id', $tierId)->first();

        if (!$tier) {
            return response()->json([
                'success' => false,
                'message' => 'Tier not found'
            ], 404);
        }

        DB::table('loyalty_tiers')
            ->where('id', $tierId)
            ->update([
                'min_lifetime_points' => $validated['min_lifetime_points'],
                'points_multiplier' => $validated['points_multiplier'],
                'benefits' => $validated['benefits'] ?? $tier->benefits,
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Tier updated successfully'
        ]);
    }
}
