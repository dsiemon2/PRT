<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class UserSettingsController extends Controller
{
    /**
     * Get all user settings (addresses, payment methods, gift cards, devices, preferences).
     */
    public function getAllSettings(int $userId): JsonResponse
    {
        // Get user
        $user = DB::table('users')->where('id', $userId)->first();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }

        // Get addresses
        $addresses = DB::table('user_addresses')
            ->where('user_id', $userId)
            ->orderByDesc('is_default')
            ->orderByDesc('id')
            ->get();

        // Get payment methods
        $paymentMethods = DB::table('user_payment_methods')
            ->where('user_id', $userId)
            ->orderByDesc('is_default')
            ->orderByDesc('id')
            ->get();

        // Get gift cards
        $giftCards = DB::table('user_gift_cards')
            ->where('user_id', $userId)
            ->where('is_active', 1)
            ->orderByDesc('added_at')
            ->get();

        // Get devices
        $devices = DB::table('user_devices')
            ->where('user_id', $userId)
            ->orderByDesc('is_current')
            ->orderByDesc('last_seen')
            ->get();

        // Get delivery preferences
        $deliveryPrefs = DB::table('user_delivery_preferences')
            ->where('user_id', $userId)
            ->first();

        // Get notification preferences
        $notifPrefs = DB::table('user_notification_preferences')
            ->where('user_id', $userId)
            ->first();

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user,
                'addresses' => $addresses,
                'payment_methods' => $paymentMethods,
                'gift_cards' => $giftCards,
                'devices' => $devices,
                'delivery_preferences' => $deliveryPrefs,
                'notification_preferences' => $notifPrefs,
            ]
        ]);
    }
}
