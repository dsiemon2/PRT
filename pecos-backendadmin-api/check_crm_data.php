<?php
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== CRM DATA AUDIT ===\n\n";

// Check all tables
$tables = [
    'customers' => 'Phase 1 - Customer Management',
    'customer_segments' => 'Phase 1 - Segmentation',
    'segment_rules' => 'Phase 1 - Segment Rules',
    'customer_segment_members' => 'Phase 1 - Segment Members',
    'customer_tags' => 'Phase 1 - Tags',
    'customer_tag_assignments' => 'Phase 1 - Tag Assignments',
    'communication_templates' => 'Phase 2 - Templates',
    'communication_logs' => 'Phase 2 - Communication Logs',
    'support_tickets' => 'Phase 2 - Support Tickets',
    'ticket_responses' => 'Phase 2 - Ticket Responses',
    'satisfaction_surveys' => 'Phase 2 - Surveys',
    'survey_responses' => 'Phase 2 - Survey Responses',
    'lead_sources' => 'Phase 3 - Lead Sources',
    'leads' => 'Phase 3 - Leads',
    'lead_activities' => 'Phase 3 - Lead Activities',
    'deal_stages' => 'Phase 3 - Deal Stages',
    'deals' => 'Phase 3 - Deals',
    'deal_activities' => 'Phase 3 - Deal Activities',
    'wholesale_accounts' => 'Phase 3 - Wholesale Accounts',
    'wholesale_orders' => 'Phase 3 - Wholesale Orders',
    'custom_reports' => 'Phase 3 - Custom Reports',
    'api_keys' => 'Phase 3 - API Keys',
    'webhooks' => 'Phase 3 - Webhooks',
];

foreach ($tables as $table => $description) {
    try {
        $count = DB::table($table)->count();
        $status = $count > 0 ? "OK" : "EMPTY";
        echo sprintf("%-35s %-30s %d records [%s]\n", $table, $description, $count, $status);
    } catch (Exception $e) {
        echo sprintf("%-35s %-30s ERROR: %s\n", $table, $description, $e->getMessage());
    }
}

echo "\n=== CHECKING LEGACY CUSTOMER DATA ===\n";
// Check if there's a users table or other customer source
$legacyTables = ['users', 'members', 'accounts', 'contacts'];
foreach ($legacyTables as $table) {
    try {
        $count = DB::table($table)->count();
        echo "$table: $count records\n";
    } catch (Exception $e) {
        echo "$table: table not found\n";
    }
}

echo "\n=== CHECKING ORDERS FOR CUSTOMER REFERENCES ===\n";
try {
    $orderCount = DB::table('orders')->count();
    echo "orders: $orderCount records\n";

    // Check what customer fields exist in orders
    $columns = DB::select("SHOW COLUMNS FROM orders");
    $customerFields = [];
    foreach ($columns as $col) {
        if (stripos($col->Field, 'customer') !== false || stripos($col->Field, 'user') !== false || stripos($col->Field, 'email') !== false) {
            $customerFields[] = $col->Field;
        }
    }
    echo "Customer-related fields in orders: " . implode(', ', $customerFields) . "\n";

    // Get unique customer emails from orders
    if (in_array('Email', $customerFields) || in_array('email', $customerFields)) {
        $emailField = in_array('Email', $customerFields) ? 'Email' : 'email';
        $uniqueEmails = DB::table('orders')->whereNotNull($emailField)->distinct()->count($emailField);
        echo "Unique customer emails in orders: $uniqueEmails\n";
    }
} catch (Exception $e) {
    echo "Error checking orders: " . $e->getMessage() . "\n";
}

echo "\n=== RELATIONSHIP ISSUES ===\n";
// Check leads with customer_id
$leadsWithCustomer = DB::table('leads')->whereNotNull('customer_id')->where('customer_id', '>', 0)->count();
$leadsWithoutCustomer = DB::table('leads')->where(function($q) { $q->whereNull('customer_id')->orWhere('customer_id', 0); })->count();
echo "Leads with customer_id: $leadsWithCustomer\n";
echo "Leads without customer_id: $leadsWithoutCustomer\n";

// Check deals with customer_id
$dealsWithCustomer = DB::table('deals')->whereNotNull('customer_id')->where('customer_id', '>', 0)->count();
$dealsWithoutCustomer = DB::table('deals')->where(function($q) { $q->whereNull('customer_id')->orWhere('customer_id', 0); })->count();
echo "Deals with customer_id: $dealsWithCustomer\n";
echo "Deals without customer_id: $dealsWithoutCustomer\n";

// Check support tickets with customer_id
$ticketsWithCustomer = DB::table('support_tickets')->whereNotNull('customer_id')->where('customer_id', '>', 0)->count();
$ticketsWithoutCustomer = DB::table('support_tickets')->where(function($q) { $q->whereNull('customer_id')->orWhere('customer_id', 0); })->count();
echo "Support tickets with customer_id: $ticketsWithCustomer\n";
echo "Support tickets without customer_id: $ticketsWithoutCustomer\n";

// Check wholesale accounts with customer_id
$wholesaleWithCustomer = DB::table('wholesale_accounts')->whereNotNull('customer_id')->where('customer_id', '>', 0)->count();
$wholesaleWithoutCustomer = DB::table('wholesale_accounts')->where(function($q) { $q->whereNull('customer_id')->orWhere('customer_id', 0); })->count();
echo "Wholesale accounts with customer_id: $wholesaleWithCustomer\n";
echo "Wholesale accounts without customer_id: $wholesaleWithoutCustomer\n";

echo "\n=== DONE ===\n";
