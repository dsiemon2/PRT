<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChatSeeder extends Seeder
{
    public function run(): void
    {
        // Create default chat departments
        $departments = [
            ['name' => 'Sales', 'code' => 'sales', 'description' => 'Pre-sales inquiries and product questions', 'email' => 'sales@example.com', 'sort_order' => 1],
            ['name' => 'Support', 'code' => 'support', 'description' => 'Technical support and troubleshooting', 'email' => 'support@example.com', 'sort_order' => 2],
            ['name' => 'Billing', 'code' => 'billing', 'description' => 'Orders, payments, and refunds', 'email' => 'billing@example.com', 'sort_order' => 3],
        ];

        foreach ($departments as $dept) {
            DB::table('chat_departments')->insert(array_merge($dept, [
                'is_active' => true,
                'working_hours' => json_encode([
                    'monday' => ['enabled' => true, 'start' => '09:00', 'end' => '17:00'],
                    'tuesday' => ['enabled' => true, 'start' => '09:00', 'end' => '17:00'],
                    'wednesday' => ['enabled' => true, 'start' => '09:00', 'end' => '17:00'],
                    'thursday' => ['enabled' => true, 'start' => '09:00', 'end' => '17:00'],
                    'friday' => ['enabled' => true, 'start' => '09:00', 'end' => '17:00'],
                    'saturday' => ['enabled' => false, 'start' => '10:00', 'end' => '14:00'],
                    'sunday' => ['enabled' => false, 'start' => '00:00', 'end' => '00:00'],
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // Create canned responses
        $cannedResponses = [
            ['title' => 'Greeting', 'shortcut' => '/hello', 'content' => 'Hello! Thank you for contacting us. How can I help you today?', 'category' => 'General'],
            ['title' => 'Wait Time', 'shortcut' => '/wait', 'content' => 'Thank you for your patience. I\'m looking into this for you and will have an answer shortly.', 'category' => 'General'],
            ['title' => 'Transfer', 'shortcut' => '/transfer', 'content' => 'I\'m going to transfer you to a specialist who can better assist you with this. Please hold for a moment.', 'category' => 'General'],
            ['title' => 'Close Chat', 'shortcut' => '/bye', 'content' => 'Thank you for chatting with us today! Is there anything else I can help you with before we end this conversation?', 'category' => 'General'],
            ['title' => 'Order Status', 'shortcut' => '/order', 'content' => 'I\'d be happy to check on your order status. Could you please provide your order number?', 'category' => 'Orders'],
            ['title' => 'Shipping Info', 'shortcut' => '/shipping', 'content' => 'We offer standard shipping (5-7 business days) and express shipping (2-3 business days). Shipping is free on orders over $50.', 'category' => 'Shipping'],
            ['title' => 'Return Policy', 'shortcut' => '/return', 'content' => 'We accept returns within 30 days of purchase. Items must be in original condition with tags attached. Would you like me to help you start a return?', 'category' => 'Returns'],
            ['title' => 'Product Question', 'shortcut' => '/product', 'content' => 'I\'d be happy to help with product information. Which product are you interested in learning more about?', 'category' => 'Products'],
        ];

        foreach ($cannedResponses as $response) {
            DB::table('chat_canned_responses')->insert(array_merge($response, [
                'usage_count' => 0,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // Create chat triggers
        $triggers = [
            [
                'name' => 'Welcome Message',
                'trigger_type' => 'page_time',
                'conditions' => json_encode(['seconds' => 30]),
                'message' => 'Hi there! Looking for something specific? I\'m here to help!',
                'department_code' => 'sales',
                'delay_seconds' => 30,
            ],
            [
                'name' => 'Cart Abandonment',
                'trigger_type' => 'cart_value',
                'conditions' => json_encode(['min_value' => 50, 'time_on_page' => 60]),
                'message' => 'I noticed you have items in your cart. Need help with anything? I can answer questions about shipping, sizing, or help you find a coupon code!',
                'department_code' => 'sales',
                'delay_seconds' => 60,
            ],
            [
                'name' => 'Exit Intent',
                'trigger_type' => 'exit_intent',
                'conditions' => json_encode(['enabled' => true]),
                'message' => 'Before you go! Can I help you find what you\'re looking for?',
                'department_code' => 'sales',
                'delay_seconds' => 0,
            ],
        ];

        foreach ($triggers as $trigger) {
            DB::table('chat_triggers')->insert(array_merge($trigger, [
                'is_active' => true,
                'triggered_count' => 0,
                'accepted_count' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // Create default widget settings
        $settings = [
            ['key' => 'widget_color', 'value' => '#4F46E5'],
            ['key' => 'widget_position', 'value' => 'bottom-right'],
            ['key' => 'welcome_message', 'value' => 'Welcome! How can we help you today?'],
            ['key' => 'offline_message', 'value' => 'We\'re currently offline. Please leave a message and we\'ll get back to you.'],
            ['key' => 'collect_email', 'value' => 'true'],
            ['key' => 'collect_name', 'value' => 'true'],
            ['key' => 'sound_enabled', 'value' => 'true'],
            ['key' => 'auto_reply_enabled', 'value' => 'false'],
        ];

        foreach ($settings as $setting) {
            DB::table('chat_widget_settings')->insert(array_merge($setting, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
