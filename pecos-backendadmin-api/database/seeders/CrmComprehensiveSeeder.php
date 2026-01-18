<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CrmComprehensiveSeeder extends Seeder
{
    /**
     * Comprehensive CRM Seeder - Creates all sample data with proper relationships
     * Works with legacy customers table structure
     */
    public function run(): void
    {
        $this->command->info('Starting CRM Comprehensive Seeder...');

        // Step 1: Create sample customers in legacy table
        $this->seedLegacyCustomers();

        // Step 2: Assign customers to segments
        $this->seedSegmentMembers();

        // Step 3: Assign tags to customers
        $this->seedTagAssignments();

        // Step 4: Update leads with customer relationships
        $this->updateLeadsWithCustomers();

        // Step 5: Update deals with customer relationships
        $this->updateDealsWithCustomers();

        // Step 6: Add deal activities
        $this->seedDealActivities();

        // Step 7: Add wholesale orders
        $this->seedWholesaleOrders();

        // Step 8: Update support tickets with proper customer data
        $this->updateSupportTickets();

        // Step 9: Add customer notes and activities
        $this->seedCustomerNotes();
        $this->seedCustomerActivities();

        // Step 10: Update customer metrics
        $this->updateCustomerMetrics();

        $this->command->info('CRM Comprehensive Seeder completed!');
    }

    /**
     * Create sample customers in legacy customers table
     * Legacy table uses: ID, NameFirst, NameLast, Email, Phone, Company, etc.
     */
    private function seedLegacyCustomers(): void
    {
        $this->command->info('Seeding legacy customers...');

        $existingCount = DB::table('customers')->count();

        if ($existingCount >= 10) {
            $this->command->info("Already have $existingCount customers, skipping creation");
            return;
        }

        $sampleCustomers = [
            ['John', 'Smith', 'john.smith@example.com', '555-0101', 'TechCorp', 'Austin', 'TX'],
            ['Sarah', 'Johnson', 'sarah.j@example.com', '555-0102', 'MusicStore', 'Dallas', 'TX'],
            ['Mike', 'Williams', 'mike.w@example.com', '555-0103', 'SoundPro', 'Houston', 'TX'],
            ['Emily', 'Brown', 'emily.b@example.com', '555-0104', '', 'San Antonio', 'TX'],
            ['David', 'Miller', 'david.m@example.com', '555-0105', 'AudioMax', 'El Paso', 'TX'],
            ['Lisa', 'Davis', 'lisa.d@example.com', '555-0106', '', 'Fort Worth', 'TX'],
            ['James', 'Wilson', 'james.w@example.com', '555-0107', 'MusicWorld', 'Arlington', 'TX'],
            ['Jennifer', 'Taylor', 'jennifer.t@example.com', '555-0108', '', 'Plano', 'TX'],
            ['Robert', 'Anderson', 'robert.a@example.com', '555-0109', 'ProSound', 'Lubbock', 'TX'],
            ['Michelle', 'Thomas', 'michelle.t@example.com', '555-0110', '', 'Laredo', 'TX'],
            ['William', 'Jackson', 'william.j@example.com', '555-0111', 'GuitarShop', 'Irving', 'TX'],
            ['Amanda', 'White', 'amanda.w@example.com', '555-0112', '', 'Amarillo', 'TX'],
        ];

        $created = 0;
        foreach ($sampleCustomers as $customer) {
            $existing = DB::table('customers')->where('Email', $customer[2])->first();
            if ($existing) continue;

            DB::table('customers')->insert([
                'NameFirst' => $customer[0],
                'NameLast' => $customer[1],
                'Email' => $customer[2],
                'Phone' => $customer[3],
                'Company' => $customer[4],
                'City' => $customer[5],
                'State' => $customer[6],
                'Country' => 'US',
                'BillName' => $customer[0] . ' ' . $customer[1],
                'BillAddress1' => rand(100, 9999) . ' Main Street',
                'BillCity' => $customer[5],
                'BillState' => $customer[6],
                'BillZip' => '7' . rand(5000, 9999),
                'BillPhone' => $customer[3],
                'ShipName' => $customer[0] . ' ' . $customer[1],
                'ShipAddress1' => rand(100, 9999) . ' Main Street',
                'ShipCity' => $customer[5],
                'ShipState' => $customer[6],
                'ShipZip' => '7' . rand(5000, 9999),
                'ShipPhone' => $customer[3],
                'Validated' => 1,
                'ProfileType' => 'Customer',
                'CreateTime' => now()->subDays(rand(30, 365))->format('Y-m-d H:i:s'),
                'UpdateTime' => now()->format('Y-m-d H:i:s'),
            ]);
            $created++;
        }

        $this->command->info("Created $created new customers");
    }

    /**
     * Assign customers to segments
     */
    private function seedSegmentMembers(): void
    {
        $this->command->info('Assigning customers to segments...');

        // Customers table uses 'id' as primary key
        $customers = DB::table('customers')->select('id')->get();
        $segments = DB::table('customer_segments')->get();

        if ($customers->isEmpty() || $segments->isEmpty()) {
            $this->command->warn('No customers or segments found, skipping segment assignments');
            return;
        }

        // Clear existing assignments
        DB::table('customer_segment_members')->truncate();

        $assignments = 0;
        foreach ($customers as $customer) {
            // Assign each customer to 1-3 random segments
            $segmentCount = rand(1, 3);
            $selectedSegments = $segments->random(min($segmentCount, $segments->count()));

            foreach ($selectedSegments as $segment) {
                DB::table('customer_segment_members')->insert([
                    'customer_id' => $customer->id,
                    'segment_id' => $segment->id,
                    'added_at' => now()->subDays(rand(1, 90)),
                ]);
                $assignments++;
            }
        }

        $this->command->info("Created $assignments segment assignments");
    }

    /**
     * Assign tags to customers
     */
    private function seedTagAssignments(): void
    {
        $this->command->info('Assigning tags to customers...');

        $customers = DB::table('customers')->select('id')->get();
        $tags = DB::table('customer_tags')->get();

        if ($customers->isEmpty() || $tags->isEmpty()) {
            $this->command->warn('No customers or tags found, skipping tag assignments');
            return;
        }

        // Clear existing assignments
        DB::table('customer_tag_assignments')->truncate();

        $assignments = 0;
        foreach ($customers as $customer) {
            // Assign each customer to 0-4 random tags
            $tagCount = rand(1, 4);
            $selectedTags = $tags->random(min($tagCount, $tags->count()));

            foreach ($selectedTags as $tag) {
                DB::table('customer_tag_assignments')->insert([
                    'customer_id' => $customer->id,
                    'tag_id' => $tag->id,
                    'assigned_by' => 1,
                    'assigned_at' => now()->subDays(rand(1, 60)),
                ]);
                $assignments++;
            }
        }

        $this->command->info("Created $assignments tag assignments");
    }

    /**
     * Update leads with customer relationships
     */
    private function updateLeadsWithCustomers(): void
    {
        $this->command->info('Updating leads with customer relationships...');

        $customers = DB::table('customers')->select('id', 'Email')->get();
        if ($customers->isEmpty()) {
            $this->command->warn('No customers found, skipping lead updates');
            return;
        }

        // Get leads without customer_id
        $leads = DB::table('leads')->where(function ($q) {
            $q->whereNull('customer_id')->orWhere('customer_id', 0);
        })->get();

        $updated = 0;
        foreach ($leads as $lead) {
            // Try to match by email first
            $customer = $customers->first(function ($c) use ($lead) {
                return strtolower($c->Email) === strtolower($lead->email);
            });

            if (!$customer) {
                // Assign a random customer for demo purposes
                $customer = $customers->random();
            }

            DB::table('leads')->where('id', $lead->id)->update([
                'customer_id' => $customer->id,
                'updated_at' => now(),
            ]);
            $updated++;
        }

        $this->command->info("Updated $updated leads with customer relationships");
    }

    /**
     * Update deals with customer relationships
     */
    private function updateDealsWithCustomers(): void
    {
        $this->command->info('Updating deals with customer relationships...');

        $customers = DB::table('customers')->select('id')->get();
        if ($customers->isEmpty()) {
            $this->command->warn('No customers found, skipping deal updates');
            return;
        }

        // Get deals without customer_id
        $deals = DB::table('deals')->where(function ($q) {
            $q->whereNull('customer_id')->orWhere('customer_id', 0);
        })->get();

        $updated = 0;
        foreach ($deals as $deal) {
            // Try to link via lead
            $lead = DB::table('leads')->where('id', $deal->lead_id)->first();
            $customerId = $lead ? $lead->customer_id : null;

            if (!$customerId) {
                // Assign a random customer
                $customerId = $customers->random()->id;
            }

            DB::table('deals')->where('id', $deal->id)->update([
                'customer_id' => $customerId,
                'updated_at' => now(),
            ]);
            $updated++;
        }

        $this->command->info("Updated $updated deals with customer relationships");
    }

    /**
     * Add deal activities
     */
    private function seedDealActivities(): void
    {
        $this->command->info('Seeding deal activities...');

        $deals = DB::table('deals')->get();
        if ($deals->isEmpty()) {
            $this->command->warn('No deals found, skipping deal activities');
            return;
        }

        // Clear existing
        DB::table('deal_activities')->truncate();

        $activityTypes = ['call', 'email', 'meeting', 'note', 'task', 'stage_change', 'other'];
        $outcomes = ['completed', 'positive', 'negative', 'follow_up', 'cancelled', null];

        $activities = 0;
        foreach ($deals as $deal) {
            // Add 2-5 activities per deal
            $activityCount = rand(2, 5);

            for ($i = 0; $i < $activityCount; $i++) {
                $type = $activityTypes[array_rand($activityTypes)];
                $daysAgo = rand(1, 30);

                DB::table('deal_activities')->insert([
                    'deal_id' => $deal->id,
                    'type' => $type,
                    'subject' => $this->getActivitySubject($type),
                    'description' => $this->getActivityDescription($type),
                    'user_id' => 1,
                    'metadata' => json_encode(['outcome' => $outcomes[array_rand($outcomes)]]),
                    'created_at' => now()->subDays($daysAgo),
                    'updated_at' => now()->subDays($daysAgo),
                ]);
                $activities++;
            }
        }

        $this->command->info("Created $activities deal activities");
    }

    /**
     * Add wholesale orders
     */
    private function seedWholesaleOrders(): void
    {
        $this->command->info('Seeding wholesale orders...');

        $accounts = DB::table('wholesale_accounts')->where('status', 'approved')->get();
        if ($accounts->isEmpty()) {
            // Update all accounts to approved first
            DB::table('wholesale_accounts')->update(['status' => 'approved', 'approved_at' => now()]);
            $accounts = DB::table('wholesale_accounts')->get();
        }

        if ($accounts->isEmpty()) {
            $this->command->warn('No wholesale accounts found, skipping orders');
            return;
        }

        // Clear existing
        DB::table('wholesale_orders')->truncate();

        $orderStatuses = ['pending', 'confirmed', 'processing', 'shipped', 'delivered'];
        $paymentStatuses = ['pending', 'paid', 'partial'];

        $orders = 0;
        foreach ($accounts as $account) {
            // Add 1-3 orders per account
            $orderCount = rand(1, 3);

            for ($i = 0; $i < $orderCount; $i++) {
                $totalAmount = rand(500, 5000);
                $discountPercent = $account->discount_percentage ?? 10;
                $discountAmount = $totalAmount * ($discountPercent / 100);
                $netAmount = $totalAmount - $discountAmount;

                DB::table('wholesale_orders')->insert([
                    'account_id' => $account->id,
                    'order_number' => 'WHO-' . date('Ymd') . '-' . str_pad($orders + 1, 4, '0', STR_PAD_LEFT),
                    'subtotal' => $totalAmount,
                    'discount_amount' => $discountAmount,
                    'tax_amount' => $netAmount * 0.0825,
                    'total' => $netAmount + ($netAmount * 0.0825),
                    'status' => $orderStatuses[array_rand($orderStatuses)],
                    'payment_status' => $paymentStatuses[array_rand($paymentStatuses)],
                    'due_date' => now()->addDays(30),
                    'notes' => 'Sample wholesale order - PO#' . rand(10000, 99999),
                    'created_at' => now()->subDays(rand(1, 60)),
                    'updated_at' => now(),
                ]);
                $orders++;
            }
        }

        $this->command->info("Created $orders wholesale orders");
    }

    /**
     * Update support tickets with proper customer data
     */
    private function updateSupportTickets(): void
    {
        $this->command->info('Updating support tickets...');

        $customers = DB::table('customers')->select('id')->get();
        if ($customers->isEmpty()) {
            $this->command->warn('No customers found, skipping ticket updates');
            return;
        }

        // Get tickets
        $tickets = DB::table('support_tickets')->get();

        $updated = 0;
        foreach ($tickets as $ticket) {
            // Check if customer_id exists
            $customerExists = $customers->first(function ($c) use ($ticket) {
                return $c->id == $ticket->customer_id;
            });

            if (!$customerExists) {
                // Assign a random customer
                $customer = $customers->random();
                DB::table('support_tickets')->where('id', $ticket->id)->update([
                    'customer_id' => $customer->id,
                    'updated_at' => now(),
                ]);
                $updated++;
            }
        }

        $this->command->info("Updated $updated support tickets with valid customer relationships");
    }

    /**
     * Seed customer notes
     */
    private function seedCustomerNotes(): void
    {
        $this->command->info('Seeding customer notes...');

        $customers = DB::table('customers')->select('id')->limit(10)->get();
        if ($customers->isEmpty()) {
            $this->command->warn('No customers found, skipping customer notes');
            return;
        }

        // Check table structure
        $columns = DB::select("SHOW COLUMNS FROM customer_notes");
        $columnNames = array_map(fn($c) => $c->Field, $columns);

        // Clear existing
        DB::table('customer_notes')->truncate();

        $sampleNotes = [
            'Customer expressed interest in new product line',
            'Follow up on pending order inquiry',
            'Discussed bulk pricing options',
            'Resolved shipping delay concern',
            'Customer upgraded loyalty tier',
            'Requested product catalog',
            'Scheduled demo for next week',
            'Positive feedback on recent purchase',
        ];

        $notes = 0;
        foreach ($customers as $customer) {
            // Add 1-3 notes per customer
            $noteCount = rand(1, 3);

            for ($i = 0; $i < $noteCount; $i++) {
                $noteData = [
                    'customer_id' => $customer->id,
                    'note' => $sampleNotes[array_rand($sampleNotes)],
                    'is_pinned' => rand(0, 1) == 1,
                    'created_by' => 1,
                    'created_at' => now()->subDays(rand(1, 60)),
                    'updated_at' => now(),
                ];

                if (in_array('is_private', $columnNames)) {
                    $noteData['is_private'] = false;
                }

                DB::table('customer_notes')->insert($noteData);
                $notes++;
            }
        }

        $this->command->info("Created $notes customer notes");
    }

    /**
     * Seed customer activities
     */
    private function seedCustomerActivities(): void
    {
        $this->command->info('Seeding customer activities...');

        $customers = DB::table('customers')->select('id')->limit(10)->get();
        if ($customers->isEmpty()) {
            $this->command->warn('No customers found, skipping customer activities');
            return;
        }

        // Clear existing
        DB::table('customer_activities')->truncate();

        $activityTypes = ['order', 'email', 'support', 'review', 'loyalty', 'login', 'account'];
        $activityTitles = [
            'order' => ['Placed new order', 'Order completed', 'Order shipped'],
            'email' => ['Opened marketing email', 'Clicked email link', 'Email bounced'],
            'support' => ['Opened support ticket', 'Support ticket resolved', 'Live chat initiated'],
            'review' => ['Submitted product review', 'Review approved'],
            'loyalty' => ['Earned points', 'Redeemed reward', 'Tier upgraded'],
            'login' => ['Logged in', 'Password reset'],
            'account' => ['Updated profile', 'Changed email preferences', 'Added payment method'],
        ];

        $activities = 0;
        foreach ($customers as $customer) {
            // Add 3-8 activities per customer
            $activityCount = rand(3, 8);

            for ($i = 0; $i < $activityCount; $i++) {
                $type = $activityTypes[array_rand($activityTypes)];
                $titles = $activityTitles[$type];
                $daysAgo = rand(1, 90);

                DB::table('customer_activities')->insert([
                    'customer_id' => $customer->id,
                    'activity_type' => $type,
                    'title' => $titles[array_rand($titles)],
                    'description' => null,
                    'metadata' => json_encode(['source' => 'seeder']),
                    'created_by' => null,
                    'created_at' => now()->subDays($daysAgo),
                    'updated_at' => now()->subDays($daysAgo),
                ]);
                $activities++;
            }
        }

        $this->command->info("Created $activities customer activities");
    }

    /**
     * Update customer metrics
     */
    private function updateCustomerMetrics(): void
    {
        $this->command->info('Updating customer metrics...');

        $customers = DB::table('customers')->select('id')->get();
        if ($customers->isEmpty()) {
            $this->command->warn('No customers found, skipping metrics update');
            return;
        }

        // Clear existing
        DB::table('customer_metrics')->truncate();

        $rfmSegments = ['Champions', 'Loyal Customers', 'Potential Loyalists', 'New Customers', 'At Risk', 'Hibernating'];

        $metrics = 0;
        foreach ($customers as $customer) {
            // Get order data from orders table
            $orderData = DB::table('orders')
                ->where('user_id', $customer->id)
                ->selectRaw('COUNT(*) as total_orders, COALESCE(SUM(total_amount), 0) as total_spent, MIN(order_date) as first_order, MAX(order_date) as last_order')
                ->first();

            $totalOrders = $orderData->total_orders ?? rand(1, 20);
            $totalSpent = $orderData->total_spent ?? rand(500, 10000);

            DB::table('customer_metrics')->insert([
                'customer_id' => $customer->id,
                'lifetime_value' => $totalSpent,
                'total_orders' => $totalOrders,
                'avg_order_value' => $totalOrders > 0 ? $totalSpent / $totalOrders : 0,
                'first_order_date' => $orderData->first_order ?? now()->subDays(rand(60, 365))->toDateString(),
                'last_order_date' => $orderData->last_order ?? now()->subDays(rand(1, 60))->toDateString(),
                'days_since_last_order' => rand(1, 90),
                'purchase_frequency' => rand(1, 10) / 10,
                'rfm_recency_score' => rand(1, 5),
                'rfm_frequency_score' => rand(1, 5),
                'rfm_monetary_score' => rand(1, 5),
                'rfm_segment' => $rfmSegments[array_rand($rfmSegments)],
                'churn_risk_score' => rand(0, 100) / 100,
                'health_score' => rand(40, 100),
                'email_open_rate' => rand(10, 60),
                'email_click_rate' => rand(2, 20),
                'calculated_at' => now(),
            ]);
            $metrics++;
        }

        $this->command->info("Created $metrics customer metrics records");
    }

    private function getActivitySubject(string $type): string
    {
        $subjects = [
            'call' => ['Discovery call', 'Follow-up call', 'Demo call', 'Pricing discussion'],
            'email' => ['Quote sent', 'Proposal follow-up', 'Introduction email', 'Order confirmation'],
            'meeting' => ['Initial meeting', 'Product demo', 'Contract review', 'Onboarding session'],
            'note' => ['Internal note', 'Customer feedback', 'Research notes', 'Requirements update'],
            'task' => ['Follow up task', 'Send proposal', 'Prepare demo', 'Review contract'],
            'stage_change' => ['Moved to next stage', 'Status update', 'Progress update'],
            'other' => ['General activity', 'Client interaction', 'Documentation update'],
        ];

        return $subjects[$type][array_rand($subjects[$type])] ?? 'Activity';
    }

    private function getActivityDescription(string $type): ?string
    {
        $descriptions = [
            'Discussed requirements and timeline',
            'Customer requested additional information',
            'Positive response, moving forward',
            'Need to follow up next week',
            'Waiting for customer decision',
            'Successfully completed',
            null,
        ];

        return $descriptions[array_rand($descriptions)];
    }
}
