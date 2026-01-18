<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmailMarketingSeeder extends Seeder
{
    public function run(): void
    {
        // Create default email lists
        $lists = [
            [
                'name' => 'Newsletter',
                'description' => 'Main newsletter subscribers',
                'is_active' => true,
                'double_optin' => false,
            ],
            [
                'name' => 'Promotional',
                'description' => 'Customers who opted in for promotional emails',
                'is_active' => true,
                'double_optin' => false,
            ],
            [
                'name' => 'Product Updates',
                'description' => 'Subscribers interested in new products',
                'is_active' => true,
                'double_optin' => true,
            ],
        ];

        foreach ($lists as $list) {
            DB::table('email_lists')->insert(array_merge($list, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // Create default automations
        $automations = [
            [
                'name' => 'Welcome Series',
                'description' => 'Welcome email sequence for new subscribers',
                'trigger_type' => 'signup',
                'trigger_conditions' => json_encode(['list_id' => 1]),
                'email_list_id' => 1,
                'is_active' => false,
            ],
            [
                'name' => 'Abandoned Cart Recovery',
                'description' => 'Remind customers about items left in cart',
                'trigger_type' => 'abandoned_cart',
                'trigger_conditions' => json_encode(['hours_after' => 24]),
                'email_list_id' => null,
                'is_active' => false,
            ],
            [
                'name' => 'Post-Purchase Follow-up',
                'description' => 'Thank customers and request reviews',
                'trigger_type' => 'purchase',
                'trigger_conditions' => json_encode(['days_after' => 7]),
                'email_list_id' => null,
                'is_active' => false,
            ],
        ];

        foreach ($automations as $automation) {
            DB::table('email_automations')->insert(array_merge($automation, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
