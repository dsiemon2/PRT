<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key')->toArray();

        // Define setting groups
        $groups = [
            'store' => [
                'store_name' => 'Store Name',
                'store_email' => 'Store Email',
                'store_phone' => 'Store Phone',
                'store_address' => 'Store Address',
                'store_city' => 'City',
                'store_state' => 'State',
                'store_zip' => 'ZIP Code',
            ],
            'features' => [
                'enable_reviews' => 'Enable Product Reviews',
                'enable_wishlist' => 'Enable Wishlist',
                'enable_compare' => 'Enable Product Compare',
                'enable_loyalty' => 'Enable Loyalty Program',
                'enable_gift_cards' => 'Enable Gift Cards',
                'enable_coupons' => 'Enable Coupons',
                'enable_newsletter' => 'Enable Newsletter',
            ],
            'email' => [
                'mail_from_name' => 'From Name',
                'mail_from_address' => 'From Email',
                'order_notification_email' => 'Order Notification Email',
                'low_stock_notification_email' => 'Low Stock Alert Email',
            ],
            'inventory' => [
                'default_low_stock_threshold' => 'Default Low Stock Threshold',
                'default_reorder_point' => 'Default Reorder Point',
                'track_inventory_by_default' => 'Track Inventory by Default',
            ],
            'checkout' => [
                'tax_rate' => 'Tax Rate (%)',
                'free_shipping_threshold' => 'Free Shipping Threshold ($)',
                'min_order_amount' => 'Minimum Order Amount ($)',
            ],
        ];

        return view('admin.settings.index', compact('settings', 'groups'));
    }

    public function update(Request $request)
    {
        $settings = $request->input('settings', []);

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        // Clear settings cache
        Cache::forget('settings');

        return back()->with('success', 'Settings updated successfully.');
    }
}
