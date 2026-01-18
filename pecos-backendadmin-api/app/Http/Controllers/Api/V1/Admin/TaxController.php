<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class TaxController extends Controller
{
    /**
     * Get all tax rates.
     */
    public function index(Request $request): JsonResponse
    {
        $query = DB::table('tax_rates')
            ->orderBy('country_code')
            ->orderBy('state_name');

        // Filter by country if specified
        if ($request->has('country')) {
            $query->where('country_code', $request->country);
        }

        $rates = $query->get();

        return response()->json([
            'success' => true,
            'data' => $rates
        ]);
    }

    /**
     * Get tax settings.
     */
    public function settings(): JsonResponse
    {
        $settings = DB::table('tax_settings')
            ->pluck('setting_value', 'setting_key');

        return response()->json([
            'success' => true,
            'data' => $settings
        ]);
    }

    /**
     * Update tax settings.
     */
    public function updateSettings(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'tax_enabled' => 'sometimes|boolean',
            'tax_calculation_address' => 'sometimes|in:shipping,billing,store',
            'tax_display_mode' => 'sometimes|in:including,excluding',
            'tax_round_at_subtotal' => 'sometimes|boolean',
        ]);

        foreach ($validated as $key => $value) {
            DB::table('tax_settings')
                ->updateOrInsert(
                    ['setting_key' => $key],
                    ['setting_value' => $value ? '1' : '0']
                );
        }

        return response()->json([
            'success' => true,
            'message' => 'Settings updated successfully'
        ]);
    }

    /**
     * Create a new tax rate.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'country' => 'required|string|max:100',
            'state' => 'nullable|string|max:100',
            'tax_name' => 'required|string|max:100',
            'rate' => 'required|numeric|min:0|max:100',
            'is_compound' => 'boolean',
            'apply_to_shipping' => 'boolean',
        ]);

        $id = DB::table('tax_rates')->insertGetId([
            'country' => $validated['country'],
            'state' => $validated['state'] ?? null,
            'tax_name' => $validated['tax_name'],
            'rate' => $validated['rate'],
            'is_compound' => $validated['is_compound'] ?? false,
            'apply_to_shipping' => $validated['apply_to_shipping'] ?? false,
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $rate = DB::table('tax_rates')->find($id);

        return response()->json([
            'success' => true,
            'message' => 'Tax rate created successfully',
            'data' => $rate
        ], 201);
    }

    /**
     * Update a tax rate.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $rate = DB::table('tax_rates')->find($id);

        if (!$rate) {
            return response()->json([
                'success' => false,
                'message' => 'Tax rate not found'
            ], 404);
        }

        $validated = $request->validate([
            'rate' => 'sometimes|numeric|min:0|max:100',
            'is_compound' => 'sometimes|boolean',
            'tax_shipping' => 'sometimes|boolean',
            'is_active' => 'sometimes|boolean',
        ]);

        $updateData = ['updated_at' => now()];

        if (isset($validated['rate'])) {
            $updateData['rate'] = $validated['rate'];
        }
        if (isset($validated['is_compound'])) {
            $updateData['is_compound'] = $validated['is_compound'] ? 1 : 0;
        }
        if (isset($validated['tax_shipping'])) {
            $updateData['tax_shipping'] = $validated['tax_shipping'] ? 1 : 0;
        }
        if (isset($validated['is_active'])) {
            $updateData['is_active'] = $validated['is_active'] ? 1 : 0;
        }

        DB::table('tax_rates')
            ->where('id', $id)
            ->update($updateData);

        $rate = DB::table('tax_rates')->find($id);

        return response()->json([
            'success' => true,
            'message' => 'Tax rate updated successfully',
            'data' => $rate
        ]);
    }

    /**
     * Delete a tax rate.
     */
    public function destroy(int $id): JsonResponse
    {
        $rate = DB::table('tax_rates')->find($id);

        if (!$rate) {
            return response()->json([
                'success' => false,
                'message' => 'Tax rate not found'
            ], 404);
        }

        DB::table('tax_rates')->where('id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tax rate deleted successfully'
        ]);
    }

    /**
     * Get tax classes.
     */
    public function classes(): JsonResponse
    {
        $classes = DB::table('tax_classes')
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $classes
        ]);
    }

    /**
     * Create a tax class.
     */
    public function storeClass(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
        ]);

        $id = DB::table('tax_classes')->insertGetId([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_default' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $class = DB::table('tax_classes')->find($id);

        return response()->json([
            'success' => true,
            'message' => 'Tax class created successfully',
            'data' => $class
        ], 201);
    }

    /**
     * Delete a tax class.
     */
    public function destroyClass(int $id): JsonResponse
    {
        $class = DB::table('tax_classes')->find($id);

        if (!$class) {
            return response()->json([
                'success' => false,
                'message' => 'Tax class not found'
            ], 404);
        }

        if ($class->is_default) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete default tax class'
            ], 400);
        }

        DB::table('tax_classes')->where('id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tax class deleted successfully'
        ]);
    }

    /**
     * Get tax report summary.
     */
    public function report(): JsonResponse
    {
        // This would calculate from actual order data
        // For now, return sample data
        $report = [
            [
                'region' => 'Pennsylvania',
                'taxable_sales' => 4567.00,
                'tax_collected' => 274.02
            ],
            [
                'region' => 'Texas',
                'taxable_sales' => 3245.00,
                'tax_collected' => 202.81
            ],
            [
                'region' => 'Canada',
                'taxable_sales' => 1234.00,
                'tax_collected' => 160.42
            ]
        ];

        $totals = [
            'taxable_sales' => array_sum(array_column($report, 'taxable_sales')),
            'tax_collected' => array_sum(array_column($report, 'tax_collected'))
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'report' => $report,
                'totals' => $totals
            ]
        ]);
    }

    /**
     * Get all tax exemptions.
     */
    public function exemptions(): JsonResponse
    {
        $exemptions = DB::table('tax_exemptions')
            ->join('users', 'tax_exemptions.user_id', '=', 'users.id')
            ->select(
                'tax_exemptions.*',
                'users.email',
                'users.first_name',
                'users.last_name'
            )
            ->orderByDesc('tax_exemptions.created_at')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $exemptions
        ]);
    }

    /**
     * Create a tax exemption.
     */
    public function storeExemption(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'exemption_type' => 'required|in:resale,nonprofit,government,other',
            'certificate_number' => 'nullable|string|max:100',
            'reason' => 'nullable|string|max:255',
            'expires_at' => 'nullable|date',
        ]);

        // Check if user already has active exemption
        $existing = DB::table('tax_exemptions')
            ->where('user_id', $validated['user_id'])
            ->where('status', 'active')
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'This customer already has an active tax exemption'
            ], 400);
        }

        $id = DB::table('tax_exemptions')->insertGetId([
            'user_id' => $validated['user_id'],
            'exemption_type' => $validated['exemption_type'],
            'certificate_number' => $validated['certificate_number'] ?? null,
            'reason' => $validated['reason'] ?? null,
            'expires_at' => $validated['expires_at'] ?? null,
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $exemption = DB::table('tax_exemptions')
            ->join('users', 'tax_exemptions.user_id', '=', 'users.id')
            ->select('tax_exemptions.*', 'users.email', 'users.first_name', 'users.last_name')
            ->where('tax_exemptions.id', $id)
            ->first();

        return response()->json([
            'success' => true,
            'message' => 'Tax exemption created successfully',
            'data' => $exemption
        ], 201);
    }

    /**
     * Revoke a tax exemption.
     */
    public function revokeExemption(int $id): JsonResponse
    {
        $exemption = DB::table('tax_exemptions')->find($id);

        if (!$exemption) {
            return response()->json([
                'success' => false,
                'message' => 'Tax exemption not found'
            ], 404);
        }

        if ($exemption->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Tax exemption is already ' . $exemption->status
            ], 400);
        }

        DB::table('tax_exemptions')
            ->where('id', $id)
            ->update([
                'status' => 'revoked',
                'updated_at' => now()
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Tax exemption revoked successfully'
        ]);
    }

    /**
     * Get customers for exemption dropdown.
     */
    public function customersForExemption(): JsonResponse
    {
        // Get customers who don't have active exemptions
        $customers = DB::table('users')
            ->leftJoin('tax_exemptions', function ($join) {
                $join->on('users.id', '=', 'tax_exemptions.user_id')
                    ->where('tax_exemptions.status', '=', 'active');
            })
            ->whereNull('tax_exemptions.id')
            ->where('users.role', 'customer')
            ->select('users.id', 'users.email', 'users.first_name', 'users.last_name')
            ->orderBy('users.last_name')
            ->limit(100)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $customers
        ]);
    }

    /**
     * Calculate tax for an order (public endpoint for frontend).
     */
    public function calculate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'country' => 'required|string|max:2',
            'state' => 'nullable|string|max:10',
            'city' => 'nullable|string|max:100',
            'subtotal' => 'required|numeric|min:0',
            'shipping' => 'nullable|numeric|min:0',
            'user_id' => 'nullable|integer',
        ]);

        $country = strtoupper($validated['country']);
        $state = $validated['state'] ?? null;
        $city = $validated['city'] ?? null;
        $subtotal = (float) $validated['subtotal'];
        $shipping = (float) ($validated['shipping'] ?? 0);
        $userId = $validated['user_id'] ?? null;

        // Check if taxes are enabled
        $taxEnabled = DB::table('tax_settings')
            ->where('setting_key', 'tax_enabled')
            ->value('setting_value');

        if ($taxEnabled !== '1') {
            return response()->json([
                'success' => true,
                'data' => [
                    'tax_amount' => 0,
                    'tax_rate' => 0,
                    'is_exempt' => false,
                    'reason' => 'Taxes disabled',
                    'breakdown' => []
                ]
            ]);
        }

        // Check for customer tax exemption
        if ($userId) {
            $exemption = DB::table('tax_exemptions')
                ->where('user_id', $userId)
                ->where('status', 'active')
                ->where(function ($query) {
                    $query->whereNull('expires_at')
                        ->orWhere('expires_at', '>', now());
                })
                ->first();

            if ($exemption) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'tax_amount' => 0,
                        'tax_rate' => 0,
                        'is_exempt' => true,
                        'reason' => 'Customer tax exempt: ' . $exemption->exemption_type,
                        'breakdown' => []
                    ]
                ]);
            }
        }

        // Only calculate tax for US, CA, MX - all others are export exempt
        $taxableCountries = ['US', 'CA', 'MX'];
        if (!in_array($country, $taxableCountries)) {
            return response()->json([
                'success' => true,
                'data' => [
                    'tax_amount' => 0,
                    'tax_rate' => 0,
                    'is_exempt' => true,
                    'reason' => 'Export sale - no tax collected',
                    'breakdown' => []
                ]
            ]);
        }

        // Get applicable tax rates
        $query = DB::table('tax_rates')
            ->where('country_code', $country)
            ->where('is_active', 1);

        // For state-level taxes
        if ($state) {
            $query->where(function ($q) use ($state) {
                $q->where('state_code', strtoupper($state))
                    ->orWhereNull('state_code');
            });
        }

        $rates = $query->orderBy('is_compound')->get();

        if ($rates->isEmpty()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'tax_amount' => 0,
                    'tax_rate' => 0,
                    'is_exempt' => false,
                    'reason' => 'No tax rate configured for this location',
                    'breakdown' => []
                ]
            ]);
        }

        // Calculate tax
        $totalTax = 0;
        $breakdown = [];
        $runningSubtotal = $subtotal;

        foreach ($rates as $rate) {
            // Skip city rates if city doesn't match
            if ($rate->city && $city && strtolower($rate->city) !== strtolower($city)) {
                continue;
            }

            // Skip state rates if state doesn't match
            if ($rate->state_code && $state && strtoupper($rate->state_code) !== strtoupper($state)) {
                continue;
            }

            $taxableAmount = $runningSubtotal;

            // Add shipping to taxable amount if configured
            if ($rate->tax_shipping && $shipping > 0) {
                $taxableAmount += $shipping;
            }

            // For compound taxes, apply to subtotal + previous taxes
            if ($rate->is_compound) {
                $taxableAmount = $runningSubtotal + $totalTax;
                if ($rate->tax_shipping && $shipping > 0) {
                    $taxableAmount += $shipping;
                }
            }

            $taxAmount = round($taxableAmount * ($rate->rate / 100), 2);
            $totalTax += $taxAmount;

            $breakdown[] = [
                'name' => $rate->state_name ?? $rate->city ?? 'Tax',
                'rate' => (float) $rate->rate,
                'amount' => $taxAmount,
                'is_compound' => (bool) $rate->is_compound,
                'includes_shipping' => (bool) $rate->tax_shipping
            ];
        }

        // Calculate effective rate
        $effectiveRate = $subtotal > 0 ? round(($totalTax / $subtotal) * 100, 3) : 0;

        return response()->json([
            'success' => true,
            'data' => [
                'tax_amount' => round($totalTax, 2),
                'tax_rate' => $effectiveRate,
                'is_exempt' => false,
                'reason' => null,
                'breakdown' => $breakdown
            ]
        ]);
    }
}
