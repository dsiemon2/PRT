<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CrmSeeder extends Seeder
{
    public function run(): void
    {
        // Create default customer tags
        $tags = [
            ['name' => 'VIP', 'color' => '#9b59b6', 'description' => 'High-value customers requiring premium service', 'is_auto' => false],
            ['name' => 'Wholesale', 'color' => '#3498db', 'description' => 'Business/wholesale account', 'is_auto' => false],
            ['name' => 'Influencer', 'color' => '#e91e63', 'description' => 'Social media influencer or brand ambassador', 'is_auto' => false],
            ['name' => 'Problem Customer', 'color' => '#e74c3c', 'description' => 'History of issues or complaints', 'is_auto' => false],
            ['name' => 'First-Time Buyer', 'color' => '#27ae60', 'description' => 'Made their first purchase', 'is_auto' => true],
            ['name' => 'High Spender', 'color' => '#f39c12', 'description' => 'Spent over $1000 lifetime', 'is_auto' => true],
            ['name' => 'Frequent Buyer', 'color' => '#1abc9c', 'description' => '5+ orders in the last year', 'is_auto' => true],
            ['name' => 'At Risk', 'color' => '#e67e22', 'description' => 'No purchase in 90+ days', 'is_auto' => true],
            ['name' => 'Boot Enthusiast', 'color' => '#8B4513', 'description' => 'Purchased from Boots category', 'is_auto' => true],
            ['name' => 'Workwear Buyer', 'color' => '#2c3e50', 'description' => 'Purchased from Workwear category', 'is_auto' => true],
            ['name' => 'Local Pickup', 'color' => '#00bcd4', 'description' => 'Prefers in-store pickup', 'is_auto' => false],
            ['name' => 'Tax Exempt', 'color' => '#607d8b', 'description' => 'Has tax exemption on file', 'is_auto' => false],
        ];

        foreach ($tags as $tag) {
            DB::table('customer_tags')->updateOrInsert(
                ['name' => $tag['name']],
                array_merge($tag, ['created_at' => now(), 'updated_at' => now()])
            );
        }

        $this->command->info('Created ' . count($tags) . ' customer tags');

        // Create preset segments
        $segments = [
            [
                'name' => 'New Customers',
                'description' => 'Customers who made their first order in the last 30 days',
                'rules' => json_encode([
                    ['field' => 'created_at', 'operator' => 'within_days', 'value' => 30]
                ]),
                'is_preset' => true
            ],
            [
                'name' => 'VIP Customers',
                'description' => 'Customers with lifetime value over $1000 or Gold/Platinum tier',
                'rules' => json_encode([
                    ['field' => 'total_spent', 'operator' => '>=', 'value' => 1000]
                ]),
                'is_preset' => true
            ],
            [
                'name' => 'At Risk',
                'description' => 'Previously active customers with no order in 90+ days',
                'rules' => json_encode([
                    ['field' => 'order_count', 'operator' => '>=', 'value' => 2],
                    ['field' => 'last_order_days', 'operator' => '>=', 'value' => 90]
                ]),
                'is_preset' => true
            ],
            [
                'name' => 'Churned',
                'description' => 'No order in 180+ days',
                'rules' => json_encode([
                    ['field' => 'last_order_days', 'operator' => '>=', 'value' => 180]
                ]),
                'is_preset' => true
            ],
            [
                'name' => 'One-Time Buyers',
                'description' => 'Exactly 1 order, placed 60+ days ago',
                'rules' => json_encode([
                    ['field' => 'order_count', 'operator' => '=', 'value' => 1],
                    ['field' => 'last_order_days', 'operator' => '>=', 'value' => 60]
                ]),
                'is_preset' => true
            ],
            [
                'name' => 'Frequent Buyers',
                'description' => '5+ orders in the last 12 months',
                'rules' => json_encode([
                    ['field' => 'order_count', 'operator' => '>=', 'value' => 5]
                ]),
                'is_preset' => true
            ],
            [
                'name' => 'High AOV',
                'description' => 'Average order value over $200',
                'rules' => json_encode([
                    ['field' => 'avg_order_value', 'operator' => '>=', 'value' => 200]
                ]),
                'is_preset' => true
            ],
            [
                'name' => 'Email Engaged',
                'description' => 'Opened email in the last 30 days',
                'rules' => json_encode([
                    ['field' => 'email_opened_days', 'operator' => '<=', 'value' => 30]
                ]),
                'is_preset' => true
            ]
        ];

        foreach ($segments as $segment) {
            DB::table('customer_segments')->updateOrInsert(
                ['name' => $segment['name']],
                array_merge($segment, [
                    'is_dynamic' => true,
                    'customer_count' => 0,
                    'created_at' => now(),
                    'updated_at' => now()
                ])
            );
        }

        $this->command->info('Created ' . count($segments) . ' preset segments');

        // Create default email templates
        $templates = [
            [
                'name' => 'Welcome Email',
                'category' => 'transactional',
                'subject' => 'Welcome to Pecos River Traders, {{customer.first_name}}!',
                'body_html' => '<h1>Welcome to the Family!</h1><p>Hi {{customer.first_name}},</p><p>Thank you for creating an account with Pecos River Traders. We\'re excited to have you join our community of Western wear enthusiasts!</p><p>As a new member, here are some things you can do:</p><ul><li>Browse our latest collections</li><li>Earn loyalty points on every purchase</li><li>Get exclusive member-only deals</li></ul><p>Happy shopping!</p><p>The Pecos River Traders Team</p>',
                'body_text' => "Welcome to the Family!\n\nHi {{customer.first_name}},\n\nThank you for creating an account with Pecos River Traders.\n\nHappy shopping!\nThe Pecos River Traders Team",
                'is_active' => true
            ],
            [
                'name' => 'Thank You - First Purchase',
                'category' => 'personal',
                'subject' => 'Thank you for your first order!',
                'body_html' => '<h1>Thank You!</h1><p>Hi {{customer.first_name}},</p><p>We wanted to personally thank you for placing your first order with Pecos River Traders.</p><p>Your order #{{order.number}} is being prepared with care.</p><p>We hope you love your new items!</p><p>Best regards,<br>The Pecos River Traders Team</p>',
                'body_text' => "Thank You!\n\nHi {{customer.first_name}},\n\nWe wanted to personally thank you for your first order.\n\nBest regards,\nThe Pecos River Traders Team",
                'is_active' => true
            ],
            [
                'name' => 'Win-Back Email',
                'category' => 'marketing',
                'subject' => 'We miss you, {{customer.first_name}}!',
                'body_html' => '<h1>We Miss You!</h1><p>Hi {{customer.first_name}},</p><p>It\'s been a while since your last visit. We\'ve got some new arrivals we think you\'ll love!</p><p>Come back and check out what\'s new. As a thank you for being a valued customer, use code <strong>COMEBACK15</strong> for 15% off your next order.</p><p>See you soon!</p>',
                'body_text' => "We Miss You!\n\nHi {{customer.first_name}},\n\nIt\'s been a while since your last visit. Use code COMEBACK15 for 15% off.\n\nSee you soon!",
                'is_active' => true
            ],
            [
                'name' => 'Issue Apology',
                'category' => 'service',
                'subject' => 'We\'re sorry about your recent experience',
                'body_html' => '<h1>Our Sincere Apologies</h1><p>Hi {{customer.first_name}},</p><p>We\'re truly sorry to hear about the issue with your recent order. Your satisfaction is our top priority, and we take this matter very seriously.</p><p>We\'ve taken the following steps to resolve this:</p><p>[Describe resolution here]</p><p>If you have any questions or concerns, please don\'t hesitate to reach out to us directly.</p><p>Thank you for your patience and understanding.</p>',
                'body_text' => "Our Sincere Apologies\n\nHi {{customer.first_name}},\n\nWe\'re truly sorry about your recent experience.\n\nThank you for your patience.",
                'is_active' => true
            ],
            [
                'name' => 'VIP Thank You',
                'category' => 'personal',
                'subject' => 'You\'re a VIP! Thank you for your loyalty',
                'body_html' => '<h1>You\'re a VIP!</h1><p>Hi {{customer.first_name}},</p><p>We wanted to take a moment to thank you for being such a valued customer. Your continued support means the world to us!</p><p>As one of our VIP customers, you enjoy:</p><ul><li>Priority customer service</li><li>Early access to new arrivals</li><li>Exclusive VIP-only promotions</li><li>Double loyalty points on select items</li></ul><p>Thank you for being part of the Pecos River Traders family!</p>',
                'body_text' => "You\'re a VIP!\n\nHi {{customer.first_name}},\n\nThank you for being such a valued customer!\n\nThe Pecos River Traders Team",
                'is_active' => true
            ]
        ];

        foreach ($templates as $template) {
            DB::table('email_templates')->updateOrInsert(
                ['name' => $template['name']],
                array_merge($template, ['created_at' => now(), 'updated_at' => now()])
            );
        }

        $this->command->info('Created ' . count($templates) . ' email templates');
    }
}
