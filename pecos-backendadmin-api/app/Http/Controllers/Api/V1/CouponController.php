<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\CouponUsage;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CouponController extends Controller
{
    /**
     * Validate a coupon code.
     *
     * @OA\Post(
     *     path="/coupons/validate",
     *     summary="Validate a coupon code",
     *     tags={"Coupons"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"code", "subtotal"},
     *             @OA\Property(property="code", type="string"),
     *             @OA\Property(property="subtotal", type="number")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Coupon valid"),
     *     @OA\Response(response=400, description="Coupon invalid or expired"),
     *     @OA\Response(response=404, description="Coupon not found")
     * )
     */
    public function validate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => 'required|string',
            'subtotal' => 'required|numeric|min:0',
        ]);

        $coupon = Coupon::where('code', strtoupper($validated['code']))->first();

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid coupon code'
            ], 404);
        }

        if (!$coupon->isValid()) {
            return response()->json([
                'success' => false,
                'message' => 'Coupon is expired or inactive'
            ], 400);
        }

        // Check minimum purchase
        if ($coupon->minimum_purchase && $validated['subtotal'] < $coupon->minimum_purchase) {
            return response()->json([
                'success' => false,
                'message' => "Minimum purchase of \${$coupon->minimum_purchase} required"
            ], 400);
        }

        // Check per-user limit
        if ($request->user() && $coupon->per_user_limit) {
            $userUsage = CouponUsage::where('coupon_id', $coupon->id)
                ->where('user_id', $request->user()->id)
                ->count();

            if ($userUsage >= $coupon->per_user_limit) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already used this coupon the maximum number of times'
                ], 400);
            }
        }

        $discount = $coupon->calculateDiscount($validated['subtotal']);

        return response()->json([
            'success' => true,
            'data' => [
                'code' => $coupon->code,
                'description' => $coupon->description,
                'discount_type' => $coupon->discount_type,
                'discount_value' => $coupon->discount_value,
                'calculated_discount' => $discount,
                'new_subtotal' => round($validated['subtotal'] - $discount, 2),
            ]
        ]);
    }

    /**
     * Apply coupon to order (called during checkout).
     *
     * @OA\Post(
     *     path="/coupons/apply",
     *     summary="Apply coupon to order",
     *     tags={"Coupons"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"code", "order_id", "discount_amount"},
     *             @OA\Property(property="code", type="string"),
     *             @OA\Property(property="order_id", type="integer"),
     *             @OA\Property(property="discount_amount", type="number")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Coupon applied"),
     *     @OA\Response(response=400, description="Invalid coupon")
     * )
     */
    public function apply(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => 'required|string',
            'order_id' => 'required|integer|exists:orders,id',
            'discount_amount' => 'required|numeric|min:0',
        ]);

        $coupon = Coupon::where('code', strtoupper($validated['code']))->first();

        if (!$coupon || !$coupon->isValid()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired coupon'
            ], 400);
        }

        // Record usage
        CouponUsage::create([
            'coupon_id' => $coupon->id,
            'user_id' => $request->user()->id,
            'order_id' => $validated['order_id'],
            'discount_amount' => $validated['discount_amount'],
        ]);

        // Increment usage count
        $coupon->increment('usage_count');

        return response()->json([
            'success' => true,
            'message' => 'Coupon applied successfully'
        ]);
    }

    // Admin methods

    /**
     * Admin: List all coupons.
     *
     * @OA\Get(
     *     path="/admin/coupons",
     *     summary="List all coupons (admin)",
     *     tags={"Admin"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="active", in="query", @OA\Schema(type="boolean")),
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function adminIndex(Request $request): JsonResponse
    {
        $query = Coupon::withCount('usages');

        if ($request->has('active')) {
            $query->where('is_active', $request->boolean('active'));
        }

        $coupons = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $coupons->items(),
            'meta' => [
                'current_page' => $coupons->currentPage(),
                'last_page' => $coupons->lastPage(),
                'total' => $coupons->total(),
            ]
        ]);
    }

    /**
     * Admin: Create coupon.
     *
     * @OA\Post(
     *     path="/admin/coupons",
     *     summary="Create coupon (admin)",
     *     tags={"Admin"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"code", "discount_type", "discount_value"},
     *             @OA\Property(property="code", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="discount_type", type="string", enum={"percentage", "fixed"}),
     *             @OA\Property(property="discount_value", type="number"),
     *             @OA\Property(property="minimum_purchase", type="number"),
     *             @OA\Property(property="expires_at", type="string", format="date-time"),
     *             @OA\Property(property="is_active", type="boolean")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Coupon created")
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code',
            'description' => 'nullable|string',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'minimum_purchase' => 'nullable|numeric|min:0',
            'maximum_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'per_user_limit' => 'nullable|integer|min:1',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:starts_at',
            'is_active' => 'boolean',
        ]);

        $validated['code'] = strtoupper($validated['code']);
        $coupon = Coupon::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Coupon created successfully',
            'data' => $coupon
        ], 201);
    }

    /**
     * Admin: Update coupon.
     *
     * @OA\Put(
     *     path="/admin/coupons/{id}",
     *     summary="Update coupon (admin)",
     *     tags={"Admin"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Coupon updated"),
     *     @OA\Response(response=404, description="Coupon not found")
     * )
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $coupon = Coupon::find($id);

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Coupon not found'
            ], 404);
        }

        $validated = $request->validate([
            'description' => 'nullable|string',
            'discount_type' => 'nullable|in:percentage,fixed',
            'discount_value' => 'nullable|numeric|min:0',
            'minimum_purchase' => 'nullable|numeric|min:0',
            'maximum_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'per_user_limit' => 'nullable|integer|min:1',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date',
            'is_active' => 'boolean',
        ]);

        $coupon->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Coupon updated successfully',
            'data' => $coupon->fresh()
        ]);
    }

    /**
     * Admin: Delete coupon.
     *
     * @OA\Delete(
     *     path="/admin/coupons/{id}",
     *     summary="Delete coupon (admin)",
     *     tags={"Admin"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Coupon deleted"),
     *     @OA\Response(response=404, description="Coupon not found")
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $coupon = Coupon::find($id);

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Coupon not found'
            ], 404);
        }

        $coupon->delete();

        return response()->json([
            'success' => true,
            'message' => 'Coupon deleted successfully'
        ]);
    }
}
