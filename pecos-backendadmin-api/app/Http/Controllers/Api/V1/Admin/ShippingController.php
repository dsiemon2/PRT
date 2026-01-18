<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ShippingController extends Controller
{
    /**
     * Get all shipping zones with their methods.
     */
    public function zones(): JsonResponse
    {
        $zones = DB::table('shipping_zones')
            ->orderBy('id')
            ->get();

        foreach ($zones as $zone) {
            $zone->methods = DB::table('shipping_methods')
                ->where('zone_id', $zone->id)
                ->orderBy('rate')
                ->get();
        }

        return response()->json([
            'success' => true,
            'data' => $zones
        ]);
    }

    /**
     * Create a new shipping zone.
     */
    public function storeZone(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'regions' => 'nullable|string',
        ]);

        $id = DB::table('shipping_zones')->insertGetId([
            'name' => $request->name,
            'regions' => $request->regions,
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $zone = DB::table('shipping_zones')->find($id);

        return response()->json([
            'success' => true,
            'message' => 'Shipping zone created',
            'data' => $zone
        ], 201);
    }

    /**
     * Update a shipping zone.
     */
    public function updateZone(Request $request, $id): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'regions' => 'nullable|string',
        ]);

        DB::table('shipping_zones')
            ->where('id', $id)
            ->update([
                'name' => $request->name,
                'regions' => $request->regions,
                'updated_at' => now(),
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Shipping zone updated'
        ]);
    }

    /**
     * Delete a shipping zone.
     */
    public function destroyZone($id): JsonResponse
    {
        DB::table('shipping_zones')->where('id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Shipping zone deleted'
        ]);
    }

    /**
     * Add a shipping method to a zone.
     */
    public function storeMethod(Request $request): JsonResponse
    {
        $request->validate([
            'zone_id' => 'required|integer|exists:shipping_zones,id',
            'name' => 'required|string|max:100',
            'rate' => 'required|numeric|min:0',
            'delivery_time' => 'nullable|string|max:100',
        ]);

        $id = DB::table('shipping_methods')->insertGetId([
            'zone_id' => $request->zone_id,
            'name' => $request->name,
            'rate' => $request->rate,
            'delivery_time' => $request->delivery_time,
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $method = DB::table('shipping_methods')->find($id);

        return response()->json([
            'success' => true,
            'message' => 'Shipping method added',
            'data' => $method
        ], 201);
    }

    /**
     * Update a shipping method.
     */
    public function updateMethod(Request $request, $id): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'rate' => 'required|numeric|min:0',
            'delivery_time' => 'nullable|string|max:100',
            'is_active' => 'boolean',
        ]);

        DB::table('shipping_methods')
            ->where('id', $id)
            ->update([
                'name' => $request->name,
                'rate' => $request->rate,
                'delivery_time' => $request->delivery_time,
                'is_active' => $request->is_active ?? 1,
                'updated_at' => now(),
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Shipping method updated'
        ]);
    }

    /**
     * Delete a shipping method.
     */
    public function destroyMethod($id): JsonResponse
    {
        DB::table('shipping_methods')->where('id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Shipping method deleted'
        ]);
    }

    /**
     * Get shipping classes.
     */
    public function classes(): JsonResponse
    {
        $classes = DB::table('shipping_classes')
            ->orderBy('is_default', 'desc')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $classes
        ]);
    }

    /**
     * Create a shipping class.
     */
    public function storeClass(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'surcharge' => 'required|numeric|min:0',
        ]);

        $slug = strtolower(str_replace(' ', '-', $request->name));

        $id = DB::table('shipping_classes')->insertGetId([
            'name' => $request->name,
            'slug' => $slug,
            'surcharge' => $request->surcharge,
            'is_default' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $class = DB::table('shipping_classes')->find($id);

        return response()->json([
            'success' => true,
            'message' => 'Shipping class created',
            'data' => $class
        ], 201);
    }

    /**
     * Update a shipping class.
     */
    public function updateClass(Request $request, $id): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'surcharge' => 'required|numeric|min:0',
        ]);

        $class = DB::table('shipping_classes')->find($id);

        if (!$class) {
            return response()->json([
                'success' => false,
                'message' => 'Shipping class not found'
            ], 404);
        }

        if ($class->is_default) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot edit default shipping class'
            ], 400);
        }

        $slug = strtolower(str_replace(' ', '-', $request->name));

        DB::table('shipping_classes')
            ->where('id', $id)
            ->update([
                'name' => $request->name,
                'slug' => $slug,
                'surcharge' => $request->surcharge,
                'updated_at' => now(),
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Shipping class updated'
        ]);
    }

    /**
     * Delete a shipping class.
     */
    public function destroyClass($id): JsonResponse
    {
        $class = DB::table('shipping_classes')->find($id);

        if ($class && $class->is_default) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete default shipping class'
            ], 400);
        }

        DB::table('shipping_classes')->where('id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Shipping class deleted'
        ]);
    }

    /**
     * Get shipping settings.
     */
    public function settings(): JsonResponse
    {
        $settings = DB::table('shipping_settings')
            ->pluck('setting_value', 'setting_key')
            ->toArray();

        return response()->json([
            'success' => true,
            'data' => $settings
        ]);
    }

    /**
     * Update shipping settings.
     */
    public function updateSettings(Request $request): JsonResponse
    {
        $settings = $request->all();

        foreach ($settings as $key => $value) {
            DB::table('shipping_settings')->updateOrInsert(
                ['setting_key' => $key],
                ['setting_value' => $value, 'updated_at' => now()]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Settings updated'
        ]);
    }

    /**
     * Get all carrier integrations.
     */
    public function carriers(): JsonResponse
    {
        $carriers = DB::table('carrier_integrations')
            ->orderBy('carrier_name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $carriers
        ]);
    }

    /**
     * Get a single carrier integration.
     */
    public function getCarrier($id): JsonResponse
    {
        $carrier = DB::table('carrier_integrations')->find($id);

        if (!$carrier) {
            return response()->json([
                'success' => false,
                'message' => 'Carrier not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $carrier
        ]);
    }

    /**
     * Update carrier integration credentials.
     */
    public function updateCarrier(Request $request, $id): JsonResponse
    {
        $request->validate([
            'api_key' => 'nullable|string|max:255',
            'api_secret' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:100',
            'is_enabled' => 'boolean',
        ]);

        $carrier = DB::table('carrier_integrations')->find($id);

        if (!$carrier) {
            return response()->json([
                'success' => false,
                'message' => 'Carrier not found'
            ], 404);
        }

        $updateData = [
            'updated_at' => now(),
        ];

        if ($request->has('api_key')) {
            $updateData['api_key'] = $request->api_key;
        }
        if ($request->has('api_secret')) {
            $updateData['api_secret'] = $request->api_secret;
        }
        if ($request->has('account_number')) {
            $updateData['account_number'] = $request->account_number;
        }
        if ($request->has('is_enabled')) {
            $updateData['is_enabled'] = $request->is_enabled;
        }

        DB::table('carrier_integrations')
            ->where('id', $id)
            ->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Carrier updated'
        ]);
    }

    /**
     * Test and connect a carrier integration.
     */
    public function connectCarrier(Request $request, $id): JsonResponse
    {
        $carrier = DB::table('carrier_integrations')->find($id);

        if (!$carrier) {
            return response()->json([
                'success' => false,
                'message' => 'Carrier not found'
            ], 404);
        }

        // In a real implementation, you would test the API credentials here
        // For now, we'll simulate a connection test based on having credentials
        $hasCredentials = !empty($carrier->api_key) || !empty($carrier->account_number);

        if (!$hasCredentials) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide API credentials before connecting'
            ], 400);
        }

        // Simulate connection test (in production, call the carrier's API)
        DB::table('carrier_integrations')
            ->where('id', $id)
            ->update([
                'is_connected' => 1,
                'last_connected_at' => now(),
                'updated_at' => now(),
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Carrier connected successfully'
        ]);
    }

    /**
     * Disconnect a carrier integration.
     */
    public function disconnectCarrier($id): JsonResponse
    {
        $carrier = DB::table('carrier_integrations')->find($id);

        if (!$carrier) {
            return response()->json([
                'success' => false,
                'message' => 'Carrier not found'
            ], 404);
        }

        DB::table('carrier_integrations')
            ->where('id', $id)
            ->update([
                'is_connected' => 0,
                'updated_at' => now(),
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Carrier disconnected'
        ]);
    }
}
