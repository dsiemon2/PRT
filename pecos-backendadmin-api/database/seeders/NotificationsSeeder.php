<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationsSeeder extends Seeder
{
    public function run(): void
    {
        // Create SMS templates
        $smsTemplates = [
            [
                'name' => 'Order Confirmation',
                'code' => 'order_confirmation',
                'content' => 'Thank you for your order #{order_number}! Your order total is ${order_total}. We\'ll notify you when it ships.',
                'event_trigger' => 'order_placed',
                'variables' => json_encode(['order_number', 'order_total', 'customer_name']),
                'is_active' => true,
            ],
            [
                'name' => 'Order Shipped',
                'code' => 'order_shipped',
                'content' => 'Great news! Your order #{order_number} has shipped. Track it here: {tracking_url}',
                'event_trigger' => 'order_shipped',
                'variables' => json_encode(['order_number', 'tracking_number', 'tracking_url', 'carrier']),
                'is_active' => true,
            ],
            [
                'name' => 'Order Delivered',
                'code' => 'order_delivered',
                'content' => 'Your order #{order_number} has been delivered! We hope you love it. Questions? Reply to this message.',
                'event_trigger' => 'order_delivered',
                'variables' => json_encode(['order_number', 'customer_name']),
                'is_active' => true,
            ],
            [
                'name' => 'Password Reset',
                'code' => 'password_reset',
                'content' => 'Your password reset code is: {reset_code}. This code expires in 15 minutes.',
                'event_trigger' => 'password_reset',
                'variables' => json_encode(['reset_code', 'customer_name']),
                'is_active' => true,
            ],
            [
                'name' => 'Abandoned Cart Reminder',
                'code' => 'abandoned_cart',
                'content' => 'Hi {customer_name}! You left items in your cart. Complete your purchase: {cart_url}',
                'event_trigger' => 'abandoned_cart',
                'variables' => json_encode(['customer_name', 'cart_url', 'item_count']),
                'is_active' => true,
            ],
            [
                'name' => 'Back In Stock',
                'code' => 'back_in_stock',
                'content' => 'Good news! {product_name} is back in stock. Get it before it sells out: {product_url}',
                'event_trigger' => 'back_in_stock',
                'variables' => json_encode(['product_name', 'product_url', 'customer_name']),
                'is_active' => true,
            ],
        ];

        foreach ($smsTemplates as $template) {
            DB::table('sms_templates')->insert(array_merge($template, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // Create Push templates
        $pushTemplates = [
            [
                'name' => 'Order Confirmation',
                'code' => 'order_confirmation',
                'title' => 'Order Confirmed!',
                'body' => 'Your order #{order_number} has been confirmed. We\'ll start processing it right away!',
                'icon' => '/icons/order.png',
                'url' => '/orders/{order_id}',
                'event_trigger' => 'order_placed',
                'variables' => json_encode(['order_number', 'order_id', 'order_total']),
                'is_active' => true,
            ],
            [
                'name' => 'Order Shipped',
                'code' => 'order_shipped',
                'title' => 'Your Order is On Its Way!',
                'body' => 'Order #{order_number} has been shipped via {carrier}. Track your package now.',
                'icon' => '/icons/shipping.png',
                'url' => '/orders/{order_id}/tracking',
                'event_trigger' => 'order_shipped',
                'variables' => json_encode(['order_number', 'order_id', 'carrier', 'tracking_number']),
                'is_active' => true,
            ],
            [
                'name' => 'Order Delivered',
                'code' => 'order_delivered',
                'title' => 'Package Delivered!',
                'body' => 'Your order #{order_number} has been delivered. Enjoy!',
                'icon' => '/icons/delivered.png',
                'url' => '/orders/{order_id}',
                'event_trigger' => 'order_delivered',
                'variables' => json_encode(['order_number', 'order_id']),
                'is_active' => true,
            ],
            [
                'name' => 'Abandoned Cart',
                'code' => 'abandoned_cart',
                'title' => 'Your Cart Misses You!',
                'body' => 'You have {item_count} item(s) waiting in your cart. Complete your purchase today!',
                'icon' => '/icons/cart.png',
                'url' => '/cart',
                'event_trigger' => 'abandoned_cart',
                'variables' => json_encode(['item_count', 'cart_total']),
                'is_active' => true,
            ],
            [
                'name' => 'Price Drop Alert',
                'code' => 'price_drop',
                'title' => 'Price Drop Alert!',
                'body' => '{product_name} is now ${new_price} (was ${old_price}). Don\'t miss this deal!',
                'icon' => '/icons/sale.png',
                'url' => '/products/{product_slug}',
                'event_trigger' => 'price_drop',
                'variables' => json_encode(['product_name', 'product_slug', 'new_price', 'old_price']),
                'is_active' => true,
            ],
            [
                'name' => 'Back In Stock',
                'code' => 'back_in_stock',
                'title' => 'It\'s Back!',
                'body' => '{product_name} is back in stock. Get it before it\'s gone!',
                'icon' => '/icons/product.png',
                'url' => '/products/{product_slug}',
                'event_trigger' => 'back_in_stock',
                'variables' => json_encode(['product_name', 'product_slug']),
                'is_active' => true,
            ],
            [
                'name' => 'Flash Sale',
                'code' => 'flash_sale',
                'title' => 'Flash Sale!',
                'body' => '{discount}% off everything for the next {hours} hours. Shop now!',
                'icon' => '/icons/flash.png',
                'url' => '/sale',
                'event_trigger' => null,
                'variables' => json_encode(['discount', 'hours']),
                'is_active' => true,
            ],
            [
                'name' => 'Loyalty Points Earned',
                'code' => 'loyalty_points',
                'title' => 'Points Earned!',
                'body' => 'You just earned {points} loyalty points! Your balance: {total_points} points.',
                'icon' => '/icons/loyalty.png',
                'url' => '/account/loyalty',
                'event_trigger' => 'loyalty_points_earned',
                'variables' => json_encode(['points', 'total_points']),
                'is_active' => true,
            ],
        ];

        foreach ($pushTemplates as $template) {
            DB::table('push_templates')->insert(array_merge($template, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // Create default notification automations
        $automations = [
            [
                'name' => 'Order Confirmation',
                'description' => 'Send confirmation when order is placed',
                'trigger_event' => 'order_placed',
                'trigger_conditions' => json_encode([]),
                'delay_minutes' => 0,
                'notification_type' => 'both',
                'sms_template_id' => 1,
                'push_template_id' => 1,
                'is_active' => false,
            ],
            [
                'name' => 'Shipping Notification',
                'description' => 'Notify customer when order ships',
                'trigger_event' => 'order_shipped',
                'trigger_conditions' => json_encode([]),
                'delay_minutes' => 0,
                'notification_type' => 'both',
                'sms_template_id' => 2,
                'push_template_id' => 2,
                'is_active' => false,
            ],
            [
                'name' => 'Delivery Confirmation',
                'description' => 'Notify customer when order is delivered',
                'trigger_event' => 'order_delivered',
                'trigger_conditions' => json_encode([]),
                'delay_minutes' => 0,
                'notification_type' => 'both',
                'sms_template_id' => 3,
                'push_template_id' => 3,
                'is_active' => false,
            ],
            [
                'name' => 'Abandoned Cart Recovery',
                'description' => 'Remind customers about abandoned carts after 1 hour',
                'trigger_event' => 'abandoned_cart',
                'trigger_conditions' => json_encode(['min_cart_value' => 25]),
                'delay_minutes' => 60,
                'notification_type' => 'push',
                'sms_template_id' => null,
                'push_template_id' => 4,
                'is_active' => false,
            ],
            [
                'name' => 'Back In Stock Alert',
                'description' => 'Notify customers when wishlisted items are back in stock',
                'trigger_event' => 'back_in_stock',
                'trigger_conditions' => json_encode([]),
                'delay_minutes' => 0,
                'notification_type' => 'both',
                'sms_template_id' => 6,
                'push_template_id' => 6,
                'is_active' => false,
            ],
        ];

        foreach ($automations as $automation) {
            DB::table('notification_automations')->insert(array_merge($automation, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
