<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Services\Payments\PaymentManager;

class SettingsController extends Controller
{
    /**
     * Get all settings grouped by category.
     */
    public function index(): JsonResponse
    {
        $settings = DB::table('settings')
            ->orderBy('setting_group')
            ->orderBy('setting_key')
            ->get();

        // Group settings by category
        $grouped = [];
        foreach ($settings as $setting) {
            $group = $setting->setting_group;
            if (!isset($grouped[$group])) {
                $grouped[$group] = [];
            }

            // Convert value based on type
            $value = $setting->setting_value;
            if ($setting->setting_type === 'boolean') {
                $value = $value === '1' || $value === 'true';
            } elseif ($setting->setting_type === 'number') {
                $value = is_numeric($value) ? floatval($value) : $value;
            }

            $grouped[$group][$setting->setting_key] = $value;
        }

        return response()->json([
            'success' => true,
            'data' => $grouped
        ]);
    }

    /**
     * Get settings for a specific group.
     */
    public function getGroup(string $group): JsonResponse
    {
        $settings = DB::table('settings')
            ->where('setting_group', $group)
            ->orderBy('setting_key')
            ->get();

        $result = [];
        foreach ($settings as $setting) {
            $value = $setting->setting_value;
            if ($setting->setting_type === 'boolean') {
                $value = $value === '1' || $value === 'true';
            } elseif ($setting->setting_type === 'number') {
                $value = is_numeric($value) ? floatval($value) : $value;
            }
            $result[$setting->setting_key] = $value;
        }

        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }

    /**
     * Update settings for a group.
     */
    public function updateGroup(Request $request, string $group): JsonResponse
    {
        $settings = $request->all();

        foreach ($settings as $key => $value) {
            // Determine type
            $type = 'string';
            if (is_bool($value)) {
                $type = 'boolean';
                $value = $value ? '1' : '0';
            } elseif (is_numeric($value)) {
                $type = 'number';
            }

            // Upsert setting
            $existing = DB::table('settings')
                ->where('setting_group', $group)
                ->where('setting_key', $key)
                ->first();

            if ($existing) {
                DB::table('settings')
                    ->where('id', $existing->id)
                    ->update([
                        'setting_value' => $value,
                        'setting_type' => $type,
                        'updated_at' => now()
                    ]);
            } else {
                DB::table('settings')->insert([
                    'setting_group' => $group,
                    'setting_key' => $key,
                    'setting_value' => $value,
                    'setting_type' => $type,
                    'updated_at' => now()
                ]);
            }
        }

        // Clear payment manager cache when features are updated
        if ($group === 'features') {
            try {
                app(PaymentManager::class)->clearCache();
            } catch (\Exception $e) {
                // Log but don't fail if cache clearing fails
                report($e);
            }
        }

        return response()->json([
            'success' => true,
            'message' => ucfirst($group) . ' settings updated successfully'
        ]);
    }
}
