<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LanguageSeeder extends Seeder
{
    /**
     * Seed the languages table.
     */
    public function run(): void
    {
        $languages = [
            [
                'code' => 'en',
                'locale' => 'en_US',
                'name' => 'English',
                'native_name' => 'English',
                'flag_icon' => '🇺🇸',
                'direction' => 'ltr',
                'is_active' => true,
                'is_default' => true,
                'sort_order' => 1,
            ],
            [
                'code' => 'es',
                'locale' => 'es_ES',
                'name' => 'Spanish',
                'native_name' => 'Español',
                'flag_icon' => '🇪🇸',
                'direction' => 'ltr',
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 2,
            ],
            [
                'code' => 'fr',
                'locale' => 'fr_FR',
                'name' => 'French',
                'native_name' => 'Français',
                'flag_icon' => '🇫🇷',
                'direction' => 'ltr',
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 3,
            ],
            [
                'code' => 'de',
                'locale' => 'de_DE',
                'name' => 'German',
                'native_name' => 'Deutsch',
                'flag_icon' => '🇩🇪',
                'direction' => 'ltr',
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 4,
            ],
            [
                'code' => 'pt',
                'locale' => 'pt_BR',
                'name' => 'Portuguese',
                'native_name' => 'Português',
                'flag_icon' => '🇧🇷',
                'direction' => 'ltr',
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 5,
            ],
            [
                'code' => 'zh',
                'locale' => 'zh_CN',
                'name' => 'Chinese (Simplified)',
                'native_name' => '简体中文',
                'flag_icon' => '🇨🇳',
                'direction' => 'ltr',
                'is_active' => false,
                'is_default' => false,
                'sort_order' => 6,
            ],
            [
                'code' => 'ja',
                'locale' => 'ja_JP',
                'name' => 'Japanese',
                'native_name' => '日本語',
                'flag_icon' => '🇯🇵',
                'direction' => 'ltr',
                'is_active' => false,
                'is_default' => false,
                'sort_order' => 7,
            ],
            [
                'code' => 'ar',
                'locale' => 'ar_SA',
                'name' => 'Arabic',
                'native_name' => 'العربية',
                'flag_icon' => '🇸🇦',
                'direction' => 'rtl',
                'is_active' => false,
                'is_default' => false,
                'sort_order' => 8,
            ],
        ];

        foreach ($languages as $language) {
            DB::table('languages')->insert(array_merge($language, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // Seed common translation keys
        $translationGroups = [
            'general' => [
                'home' => 'Home',
                'search' => 'Search',
                'login' => 'Login',
                'logout' => 'Logout',
                'register' => 'Register',
                'my_account' => 'My Account',
                'cart' => 'Cart',
                'checkout' => 'Checkout',
                'contact' => 'Contact',
                'about' => 'About',
                'help' => 'Help',
                'privacy_policy' => 'Privacy Policy',
                'terms_of_service' => 'Terms of Service',
            ],
            'products' => [
                'add_to_cart' => 'Add to Cart',
                'buy_now' => 'Buy Now',
                'in_stock' => 'In Stock',
                'out_of_stock' => 'Out of Stock',
                'price' => 'Price',
                'quantity' => 'Quantity',
                'description' => 'Description',
                'reviews' => 'Reviews',
                'related_products' => 'Related Products',
                'product_details' => 'Product Details',
            ],
            'checkout' => [
                'shipping_address' => 'Shipping Address',
                'billing_address' => 'Billing Address',
                'payment_method' => 'Payment Method',
                'order_summary' => 'Order Summary',
                'place_order' => 'Place Order',
                'subtotal' => 'Subtotal',
                'shipping' => 'Shipping',
                'tax' => 'Tax',
                'total' => 'Total',
                'apply_coupon' => 'Apply Coupon',
            ],
            'account' => [
                'my_orders' => 'My Orders',
                'order_history' => 'Order History',
                'wishlist' => 'Wishlist',
                'addresses' => 'Addresses',
                'profile' => 'Profile',
                'change_password' => 'Change Password',
                'loyalty_points' => 'Loyalty Points',
            ],
            'messages' => [
                'success' => 'Success',
                'error' => 'Error',
                'warning' => 'Warning',
                'info' => 'Information',
                'item_added' => 'Item added to cart',
                'item_removed' => 'Item removed from cart',
                'order_placed' => 'Your order has been placed successfully',
                'login_success' => 'Welcome back!',
                'logout_success' => 'You have been logged out',
            ],
        ];

        // Get the default language (English)
        $defaultLanguage = DB::table('languages')->where('is_default', true)->first();

        foreach ($translationGroups as $group => $keys) {
            foreach ($keys as $key => $defaultValue) {
                $keyId = DB::table('translation_keys')->insertGetId([
                    'group' => $group,
                    'key' => $key,
                    'description' => "Translation key for {$group}.{$key}",
                    'is_html' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Add English translation
                DB::table('translations')->insert([
                    'language_id' => $defaultLanguage->id,
                    'translation_key_id' => $keyId,
                    'value' => $defaultValue,
                    'is_reviewed' => true,
                    'translated_by' => 'seeder',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
