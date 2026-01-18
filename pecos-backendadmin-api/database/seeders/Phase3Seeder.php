<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Phase3Seeder extends Seeder
{
    public function run(): void
    {
        // Lead Sources
        $sources = [
            ['name' => 'Website', 'code' => 'website', 'description' => 'Leads from website forms and chat'],
            ['name' => 'Trade Show', 'code' => 'trade_show', 'description' => 'Leads from industry trade shows'],
            ['name' => 'Referral', 'code' => 'referral', 'description' => 'Referrals from existing customers'],
            ['name' => 'Cold Call', 'code' => 'cold_call', 'description' => 'Outbound sales calls'],
            ['name' => 'Social Media', 'code' => 'social', 'description' => 'Leads from social media campaigns'],
            ['name' => 'Email Campaign', 'code' => 'email', 'description' => 'Leads from email marketing'],
            ['name' => 'Partner', 'code' => 'partner', 'description' => 'Leads from business partners'],
            ['name' => 'Advertisement', 'code' => 'ads', 'description' => 'Paid advertising leads'],
        ];

        foreach ($sources as $source) {
            DB::table('lead_sources')->insert(array_merge($source, [
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // Deal Stages
        $stages = [
            ['name' => 'Prospect', 'code' => 'prospect', 'color' => '#6c757d', 'probability' => 10, 'sort_order' => 1],
            ['name' => 'Qualified', 'code' => 'qualified', 'color' => '#17a2b8', 'probability' => 25, 'sort_order' => 2],
            ['name' => 'Proposal', 'code' => 'proposal', 'color' => '#007bff', 'probability' => 50, 'sort_order' => 3],
            ['name' => 'Negotiation', 'code' => 'negotiation', 'color' => '#ffc107', 'probability' => 75, 'sort_order' => 4],
            ['name' => 'Closed Won', 'code' => 'won', 'color' => '#28a745', 'probability' => 100, 'sort_order' => 5, 'is_won' => true],
            ['name' => 'Closed Lost', 'code' => 'lost', 'color' => '#dc3545', 'probability' => 0, 'sort_order' => 6, 'is_lost' => true],
        ];

        foreach ($stages as $stage) {
            DB::table('deal_stages')->insert(array_merge([
                'is_won' => false,
                'is_lost' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ], $stage));
        }

        // Sample Leads
        $leads = [
            [
                'lead_number' => 'LEAD-001',
                'first_name' => 'John',
                'last_name' => 'Baker',
                'email' => 'john.baker@guitarshop.com',
                'phone' => '555-123-4567',
                'company' => 'Baker Guitar Shop',
                'job_title' => 'Owner',
                'status' => 'qualified',
                'priority' => 'high',
                'estimated_value' => 15000.00,
                'probability' => 60,
                'source_id' => 3, // Referral
                'lead_score' => 85,
                'notes' => 'Interested in bulk ordering vintage style guitars',
            ],
            [
                'lead_number' => 'LEAD-002',
                'first_name' => 'Sarah',
                'last_name' => 'Mitchell',
                'email' => 'sarah@musicstudio.edu',
                'phone' => '555-234-5678',
                'company' => 'Mitchell Music Academy',
                'job_title' => 'Director',
                'status' => 'proposal',
                'priority' => 'hot',
                'estimated_value' => 25000.00,
                'probability' => 75,
                'source_id' => 2, // Trade Show
                'lead_score' => 92,
                'notes' => 'Need 50 student guitars for music program',
            ],
            [
                'lead_number' => 'LEAD-003',
                'first_name' => 'Mike',
                'last_name' => 'Rodriguez',
                'email' => 'mike@stringmusic.com',
                'phone' => '555-345-6789',
                'company' => 'String Music Retail',
                'job_title' => 'Purchasing Manager',
                'status' => 'contacted',
                'priority' => 'medium',
                'estimated_value' => 8000.00,
                'probability' => 30,
                'source_id' => 1, // Website
                'lead_score' => 65,
                'notes' => 'Inquired about dealer pricing',
            ],
            [
                'lead_number' => 'LEAD-004',
                'first_name' => 'Lisa',
                'last_name' => 'Chen',
                'email' => 'lisa.chen@recordstudio.com',
                'phone' => '555-456-7890',
                'company' => 'Golden Records Studio',
                'job_title' => 'Studio Manager',
                'status' => 'new',
                'priority' => 'medium',
                'estimated_value' => 5000.00,
                'probability' => 20,
                'source_id' => 5, // Social Media
                'lead_score' => 45,
                'notes' => 'Looking for studio quality instruments',
            ],
            [
                'lead_number' => 'LEAD-005',
                'first_name' => 'Robert',
                'last_name' => 'Williams',
                'email' => 'rwilliams@countrymusic.org',
                'phone' => '555-567-8901',
                'company' => 'Country Music Association',
                'job_title' => 'Events Coordinator',
                'status' => 'negotiation',
                'priority' => 'hot',
                'estimated_value' => 50000.00,
                'probability' => 80,
                'source_id' => 7, // Partner
                'lead_score' => 95,
                'notes' => 'Annual festival sponsorship opportunity',
            ],
        ];

        foreach ($leads as $lead) {
            DB::table('leads')->insert(array_merge($lead, [
                'expected_close_date' => Carbon::now()->addDays(rand(14, 60)),
                'last_contacted_at' => Carbon::now()->subDays(rand(1, 14)),
                'created_at' => Carbon::now()->subDays(rand(7, 30)),
                'updated_at' => now(),
            ]));
        }

        // Sample Deals
        $stageIds = DB::table('deal_stages')->pluck('id', 'code');
        $deals = [
            [
                'deal_number' => 'DEAL-001',
                'title' => 'Baker Guitar Shop - Initial Order',
                'lead_id' => 1,
                'stage_id' => $stageIds['proposal'],
                'value' => 15000.00,
                'probability' => 50,
                'expected_close_date' => Carbon::now()->addDays(21),
            ],
            [
                'deal_number' => 'DEAL-002',
                'title' => 'Mitchell Academy - Student Guitar Program',
                'lead_id' => 2,
                'stage_id' => $stageIds['negotiation'],
                'value' => 25000.00,
                'probability' => 75,
                'expected_close_date' => Carbon::now()->addDays(14),
            ],
            [
                'deal_number' => 'DEAL-003',
                'title' => 'Country Music Festival Sponsorship',
                'lead_id' => 5,
                'stage_id' => $stageIds['negotiation'],
                'value' => 50000.00,
                'probability' => 80,
                'expected_close_date' => Carbon::now()->addDays(30),
            ],
            [
                'deal_number' => 'DEAL-004',
                'title' => 'Previous Won Deal - Music Store Chain',
                'stage_id' => $stageIds['won'],
                'value' => 35000.00,
                'probability' => 100,
                'actual_close_date' => Carbon::now()->subDays(15),
                'won_at' => Carbon::now()->subDays(15),
            ],
            [
                'deal_number' => 'DEAL-005',
                'title' => 'Lost Opportunity - School District',
                'stage_id' => $stageIds['lost'],
                'value' => 20000.00,
                'probability' => 0,
                'actual_close_date' => Carbon::now()->subDays(7),
                'lost_at' => Carbon::now()->subDays(7),
                'lost_reason' => 'Budget constraints - went with cheaper option',
            ],
        ];

        foreach ($deals as $deal) {
            DB::table('deals')->insert(array_merge([
                'currency' => 'USD',
                'notes' => null,
                'line_items' => null,
                'lead_id' => null,
                'customer_id' => null,
                'won_at' => null,
                'lost_at' => null,
                'lost_reason' => null,
                'actual_close_date' => null,
                'created_at' => Carbon::now()->subDays(rand(7, 30)),
                'updated_at' => now(),
            ], $deal));
        }

        // Sample Wholesale Accounts
        $wholesaleAccounts = [
            [
                'account_number' => 'WS-001',
                'customer_id' => 1,
                'business_name' => 'Guitar World Distributors',
                'business_type' => 'Distributor',
                'tax_id' => '12-3456789',
                'tier' => 'gold',
                'discount_percentage' => 25.00,
                'credit_limit' => 50000.00,
                'payment_terms_days' => 30,
                'status' => 'approved',
                'approved_at' => Carbon::now()->subMonths(6),
                'primary_contact_name' => 'James Wilson',
                'primary_contact_email' => 'james@guitarworld.com',
                'primary_contact_phone' => '555-111-2222',
                'billing_address' => '123 Music Lane, Nashville, TN 37203',
                'shipping_address' => '456 Warehouse Blvd, Nashville, TN 37204',
            ],
            [
                'account_number' => 'WS-002',
                'customer_id' => 2,
                'business_name' => 'Melody Music Shops',
                'business_type' => 'Retail Chain',
                'tax_id' => '98-7654321',
                'tier' => 'silver',
                'discount_percentage' => 20.00,
                'credit_limit' => 25000.00,
                'payment_terms_days' => 15,
                'status' => 'approved',
                'approved_at' => Carbon::now()->subMonths(3),
                'primary_contact_name' => 'Amanda Garcia',
                'primary_contact_email' => 'amanda@melodymusic.com',
                'primary_contact_phone' => '555-333-4444',
                'billing_address' => '789 Retail Row, Austin, TX 78701',
                'shipping_address' => '789 Retail Row, Austin, TX 78701',
            ],
            [
                'account_number' => 'WS-003',
                'customer_id' => 3,
                'business_name' => 'School Music Supply Co',
                'business_type' => 'Educational',
                'tax_id' => '45-6789012',
                'tier' => 'bronze',
                'discount_percentage' => 15.00,
                'credit_limit' => 10000.00,
                'payment_terms_days' => 30,
                'status' => 'pending',
                'primary_contact_name' => 'David Brown',
                'primary_contact_email' => 'david@schoolmusic.org',
                'primary_contact_phone' => '555-555-6666',
                'billing_address' => '321 Education Ave, Chicago, IL 60601',
            ],
        ];

        foreach ($wholesaleAccounts as $account) {
            DB::table('wholesale_accounts')->insert(array_merge([
                'resale_certificate' => null,
                'approved_by' => null,
                'shipping_address' => null,
                'notes' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ], $account));
        }

        // Sample Custom Reports
        $reports = [
            [
                'name' => 'Monthly Sales Summary',
                'slug' => 'monthly-sales-summary',
                'description' => 'Overview of sales performance by month',
                'type' => 'sales',
                'metrics' => json_encode(['revenue', 'orders', 'average_order_value']),
                'dimensions' => json_encode(['month', 'category']),
                'chart_type' => 'bar',
                'is_public' => true,
                'created_by' => 1,
            ],
            [
                'name' => 'Customer Acquisition Report',
                'slug' => 'customer-acquisition',
                'description' => 'Track new customer signups and sources',
                'type' => 'customers',
                'metrics' => json_encode(['new_customers', 'conversion_rate', 'acquisition_cost']),
                'dimensions' => json_encode(['source', 'week']),
                'chart_type' => 'line',
                'is_public' => true,
                'created_by' => 1,
            ],
            [
                'name' => 'Product Performance',
                'slug' => 'product-performance',
                'description' => 'Best and worst performing products',
                'type' => 'products',
                'metrics' => json_encode(['units_sold', 'revenue', 'margin']),
                'dimensions' => json_encode(['product', 'category']),
                'chart_type' => 'table',
                'is_public' => false,
                'created_by' => 1,
            ],
            [
                'name' => 'Lead Pipeline Report',
                'slug' => 'lead-pipeline',
                'description' => 'Current leads by stage and value',
                'type' => 'sales',
                'metrics' => json_encode(['lead_count', 'total_value', 'conversion_rate']),
                'dimensions' => json_encode(['stage', 'source']),
                'chart_type' => 'pie',
                'is_public' => true,
                'created_by' => 1,
            ],
        ];

        foreach ($reports as $report) {
            DB::table('custom_reports')->insert(array_merge([
                'filters' => null,
                'date_range' => json_encode(['type' => 'last_30_days']),
                'shared_with' => null,
                'is_scheduled' => false,
                'schedule_frequency' => null,
                'schedule_recipients' => null,
                'last_run_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ], $report));
        }

        // Sample API Keys
        DB::table('api_keys')->insert([
            'name' => 'Development API Key',
            'key' => 'pk_dev_' . bin2hex(random_bytes(16)),
            'secret' => bcrypt('sk_dev_' . bin2hex(random_bytes(24))),
            'description' => 'API key for development and testing',
            'scopes' => json_encode(['read:products', 'read:orders', 'read:customers']),
            'rate_limit_per_minute' => 100,
            'is_active' => true,
            'created_by' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Sample Webhook
        DB::table('webhooks')->insert([
            'name' => 'Order Notification Webhook',
            'url' => 'https://example.com/webhooks/orders',
            'secret' => bin2hex(random_bytes(16)),
            'events' => json_encode(['order.created', 'order.updated', 'order.shipped']),
            'is_active' => false,
            'timeout_seconds' => 30,
            'max_retries' => 3,
            'created_by' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Sample Lead Activities
        $activities = [
            ['lead_id' => 1, 'type' => 'call', 'subject' => 'Initial discovery call', 'outcome' => 'completed', 'duration_minutes' => 25],
            ['lead_id' => 1, 'type' => 'email', 'subject' => 'Sent product catalog', 'outcome' => 'completed'],
            ['lead_id' => 2, 'type' => 'meeting', 'subject' => 'Trade show introduction', 'outcome' => 'completed', 'duration_minutes' => 45],
            ['lead_id' => 2, 'type' => 'call', 'subject' => 'Follow-up on requirements', 'outcome' => 'completed', 'duration_minutes' => 30],
            ['lead_id' => 5, 'type' => 'meeting', 'subject' => 'Sponsorship proposal presentation', 'outcome' => 'completed', 'duration_minutes' => 60],
        ];

        foreach ($activities as $activity) {
            DB::table('lead_activities')->insert(array_merge([
                'user_id' => 1,
                'description' => null,
                'scheduled_at' => null,
                'completed_at' => Carbon::now()->subDays(rand(1, 14)),
                'metadata' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ], $activity));
        }

        echo "Phase 3 seeder completed: Leads, Deals, Wholesale Accounts, Reports, API Keys, Webhooks\n";
    }
}
