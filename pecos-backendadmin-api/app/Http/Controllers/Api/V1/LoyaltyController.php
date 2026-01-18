<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\LoyaltyTransaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class LoyaltyController extends Controller
{
    /**
     * Get user's loyalty points balance and history.
     *
     * @OA\Get(
     *     path="/loyalty",
     *     summary="Get loyalty points balance and history",
     *     tags={"Loyalty"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer", default=20)),
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $balance = LoyaltyTransaction::where('user_id', $user->id)
            ->sum('points');

        $perPage = min($request->get('per_page', 20), 100);
        $transactions = LoyaltyTransaction::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        // Calculate tier
        $totalEarned = LoyaltyTransaction::where('user_id', $user->id)
            ->earned()
            ->sum('points');

        $tier = $this->calculateTier($totalEarned);

        return response()->json([
            'success' => true,
            'data' => [
                'balance' => $balance,
                'tier' => $tier,
                'total_earned' => $totalEarned,
                'transactions' => $transactions->items(),
            ],
            'meta' => [
                'current_page' => $transactions->currentPage(),
                'last_page' => $transactions->lastPage(),
                'total' => $transactions->total(),
            ]
        ]);
    }

    /**
     * Redeem points.
     *
     * @OA\Post(
     *     path="/loyalty/redeem",
     *     summary="Redeem loyalty points",
     *     tags={"Loyalty"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"points"},
     *             @OA\Property(property="points", type="integer", minimum=100)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Points redeemed"),
     *     @OA\Response(response=400, description="Insufficient points")
     * )
     */
    public function redeem(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'points' => 'required|integer|min:100',
        ]);

        $user = $request->user();

        // Check balance
        $balance = LoyaltyTransaction::where('user_id', $user->id)
            ->sum('points');

        if ($balance < $validated['points']) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient points',
                'balance' => $balance
            ], 400);
        }

        // Create redemption transaction (negative points)
        $transaction = LoyaltyTransaction::create([
            'user_id' => $user->id,
            'points' => -$validated['points'],
            'type' => 'redeemed',
            'description' => 'Points redeemed for discount',
        ]);

        // Calculate discount value (e.g., 100 points = $1)
        $discountValue = $validated['points'] / 100;

        return response()->json([
            'success' => true,
            'message' => 'Points redeemed successfully',
            'data' => [
                'points_redeemed' => $validated['points'],
                'discount_value' => $discountValue,
                'new_balance' => $balance - $validated['points'],
                'transaction' => $transaction
            ]
        ]);
    }

    /**
     * Get available rewards.
     *
     * @OA\Get(
     *     path="/loyalty/rewards",
     *     summary="Get available rewards",
     *     tags={"Loyalty"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function rewards(Request $request): JsonResponse
    {
        $user = $request->user();

        $balance = LoyaltyTransaction::where('user_id', $user->id)
            ->sum('points');

        // Define available rewards
        $rewards = [
            [
                'id' => 1,
                'name' => '$5 Off',
                'points_required' => 500,
                'discount_value' => 5.00,
                'available' => $balance >= 500,
            ],
            [
                'id' => 2,
                'name' => '$10 Off',
                'points_required' => 1000,
                'discount_value' => 10.00,
                'available' => $balance >= 1000,
            ],
            [
                'id' => 3,
                'name' => '$25 Off',
                'points_required' => 2500,
                'discount_value' => 25.00,
                'available' => $balance >= 2500,
            ],
            [
                'id' => 4,
                'name' => 'Free Shipping',
                'points_required' => 300,
                'discount_value' => 0,
                'available' => $balance >= 300,
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => $rewards,
            'balance' => $balance
        ]);
    }

    /**
     * Calculate tier based on total points earned.
     */
    private function calculateTier(int $totalEarned): array
    {
        if ($totalEarned >= 10000) {
            return ['name' => 'Platinum', 'multiplier' => 2.0, 'next_tier' => null, 'points_to_next' => 0];
        } elseif ($totalEarned >= 5000) {
            return ['name' => 'Gold', 'multiplier' => 1.5, 'next_tier' => 'Platinum', 'points_to_next' => 10000 - $totalEarned];
        } elseif ($totalEarned >= 1000) {
            return ['name' => 'Silver', 'multiplier' => 1.25, 'next_tier' => 'Gold', 'points_to_next' => 5000 - $totalEarned];
        } else {
            return ['name' => 'Bronze', 'multiplier' => 1.0, 'next_tier' => 'Silver', 'points_to_next' => 1000 - $totalEarned];
        }
    }

    /**
     * Get loyalty data by user ID (for PHP session-based auth).
     */
    public function userLoyalty(int $userId): JsonResponse
    {
        // Get or create loyalty account
        $account = DB::table('loyalty_members')->where('user_id', $userId)->first();

        if (!$account) {
            // Create account with default tier
            DB::table('loyalty_members')->insert([
                'user_id' => $userId,
                'available_points' => 0,
                'total_points' => 0,
                'lifetime_points' => 0,
                'tier_id' => 1,
                'joined_at' => now()
            ]);
            $account = DB::table('loyalty_members')->where('user_id', $userId)->first();
        }

        // Get tier info
        $tier = DB::table('loyalty_tiers')
            ->where('id', $account->tier_id)
            ->first();

        // Get all tiers for progress display
        $allTiers = DB::table('loyalty_tiers')
            ->orderBy('display_order')
            ->get();

        // Map to expected field names for frontend compatibility
        $accountData = (object)[
            'user_id' => $account->user_id,
            'points_balance' => $account->available_points,
            'lifetime_points' => $account->lifetime_points,
            'tier_level' => $tier ? $tier->tier_name : 'bronze',
            'tier_id' => $account->tier_id,
            'joined_at' => $account->joined_at
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'account' => $accountData,
                'tier' => $tier,
                'all_tiers' => $allTiers,
            ]
        ]);
    }

    /**
     * Get loyalty transactions by user ID.
     */
    public function userTransactions(int $userId, Request $request): JsonResponse
    {
        $limit = min($request->get('limit', 50), 100);

        $transactions = DB::table('loyalty_transactions')
            ->where('user_id', $userId)
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $transactions
        ]);
    }

    /**
     * Get all loyalty tiers.
     */
    public function allTiers(): JsonResponse
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
     * Get available rewards for user.
     */
    public function userRewards(int $userId): JsonResponse
    {
        // Get user's tier
        $account = DB::table('loyalty_members')
            ->leftJoin('loyalty_tiers', 'loyalty_members.tier_id', '=', 'loyalty_tiers.id')
            ->where('loyalty_members.user_id', $userId)
            ->select('loyalty_members.*', 'loyalty_tiers.tier_name')
            ->first();

        $userTier = $account ? $account->tier_name : 'bronze';

        $tierOrder = ['bronze' => 1, 'silver' => 2, 'gold' => 3, 'platinum' => 4];
        $userTierLevel = $tierOrder[$userTier] ?? 1;

        // Get all active rewards
        $rewards = DB::table('loyalty_rewards')
            ->where('status', 'active')
            ->orderBy('display_order')
            ->get();

        // Mark availability based on tier
        $availableRewards = $rewards->map(function ($reward) use ($tierOrder, $userTierLevel) {
            $rewardTierLevel = $tierOrder[$reward->min_tier] ?? 1;
            $reward->is_available = ($userTierLevel >= $rewardTierLevel);
            return $reward;
        });

        return response()->json([
            'success' => true,
            'data' => $availableRewards,
            'balance' => $account ? $account->available_points : 0
        ]);
    }
}
