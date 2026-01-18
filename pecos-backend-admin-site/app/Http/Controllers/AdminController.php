<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    protected $api;

    public function __construct(ApiService $api)
    {
        $this->api = $api;
    }

    /**
     * Dashboard with real stats.
     */
    public function dashboard()
    {
        $orderStats = $this->api->getAdminOrderStats();
        $customerStats = $this->api->getAdminCustomerStats();
        $inventoryStats = $this->api->getInventoryStats();

        return view('admin.dashboard', [
            'orderStats' => $orderStats['data'] ?? $orderStats ?? [],
            'customerStats' => $customerStats['data'] ?? $customerStats ?? [],
            'inventoryStats' => $inventoryStats['data'] ?? $inventoryStats ?? [],
        ]);
    }

    /**
     * Products list.
     */
    public function products(Request $request)
    {
        $params = $request->only(['search', 'category', 'status', 'min_price', 'max_price', 'page', 'per_page']);
        $response = $this->api->getProducts($params);
        $categories = $this->api->getCategories();

        // Build paginated response structure
        $products = [
            'data' => $response['data'] ?? [],
            'current_page' => $response['meta']['current_page'] ?? 1,
            'last_page' => $response['meta']['last_page'] ?? 1,
            'per_page' => $response['meta']['per_page'] ?? 15,
            'total' => $response['meta']['total'] ?? 0,
        ];

        return view('admin.products', [
            'products' => $products,
            'categories' => $categories['data'] ?? [],
            'filters' => $params,
        ]);
    }

    /**
     * Categories list.
     */
    public function categories()
    {
        return view('admin.categories');
    }

    /**
     * Orders list.
     */
    public function orders(Request $request)
    {
        $params = $request->only(['status', 'from_date', 'to_date', 'search', 'page', 'per_page']);
        $orders = $this->api->getAdminOrders($params);
        $stats = $this->api->getAdminOrderStats();

        return view('admin.orders', [
            'orders' => $orders,
            'stats' => $stats,
            'filters' => $params,
        ]);
    }

    /**
     * Single order detail.
     */
    public function orderDetail($id)
    {
        $order = $this->api->getAdminOrder($id);

        return view('admin.order-detail', [
            'order' => $order['data'] ?? null,
        ]);
    }

    /**
     * Customers list.
     */
    public function customers(Request $request)
    {
        $params = $request->only(['search', 'role', 'from_date', 'to_date', 'page', 'per_page']);
        $customers = $this->api->getAdminCustomers($params);
        $stats = $this->api->getAdminCustomerStats();

        return view('admin.customers', [
            'customers' => $customers,
            'stats' => $stats['data'] ?? [],
            'filters' => $params,
        ]);
    }

    /**
     * Single customer detail (Customer 360 View).
     */
    public function customerDetail($id)
    {
        $response = $this->api->getAdminCustomer($id);
        $orders = $this->api->getAdminCustomerOrders($id);

        // Merge customer data with stats
        $customer = $response['customer'] ?? null;
        if ($customer && isset($response['stats'])) {
            $customer = array_merge($customer, $response['stats']);
        }

        // Get CRM 360 data
        $crm360 = $this->api->getCustomer360($id);

        // Build CRM data array for the view
        $crm = [
            'metrics' => $crm360['metrics'] ?? null,
            'tags' => collect($crm360['tags'] ?? []),
            'activities' => collect($crm360['activities'] ?? []),
            'pinned_notes' => collect($crm360['pinned_notes'] ?? []),
            'segments' => collect($crm360['segments'] ?? []),
            'loyalty' => $crm360['loyalty'] ?? null,
        ];

        // Merge loyalty data from CRM if available
        if ($customer && isset($crm360['loyalty'])) {
            $customer['loyalty_tier'] = $crm360['loyalty']->tier_name ?? 'bronze';
            $customer['loyalty_points'] = $crm360['loyalty']->points_balance ?? 0;
        }

        return view('admin.customer-detail', [
            'customer' => $customer,
            'orders' => $orders['data'] ?? $orders ?? [],
            'crm' => $crm,
        ]);
    }

    /**
     * Inventory management.
     */
    public function inventory(Request $request)
    {
        $params = $request->only(['search', 'category', 'stock_status', 'page', 'per_page']);
        $products = $this->api->getInventoryProducts($params);
        $stats = $this->api->getInventoryStats();
        $categories = $this->api->getCategories();

        return view('admin.inventory', [
            'products' => $products['data'] ?? [],
            'stats' => $stats['data'] ?? [],
            'categories' => $categories['data'] ?? [],
            'filters' => $params,
        ]);
    }

    /**
     * Stock alerts.
     */
    public function stockAlerts()
    {
        $alerts = $this->api->getStockAlerts();
        $stats = $this->api->getInventoryStats();

        return view('admin.stock-alerts', [
            'alerts' => $alerts['data'] ?? [],
            'stats' => $stats['data'] ?? [],
        ]);
    }

    /**
     * Inventory reports.
     */
    public function inventoryReports(Request $request)
    {
        $reportType = $request->get('report', 'valuation');
        $stats = $this->api->getInventoryStats();
        $reportData = $this->api->getInventoryReports($reportType);

        return view('admin.inventory-reports', [
            'stats' => $stats['data'] ?? [],
            'reportData' => $reportData['data'] ?? [],
            'reportType' => $reportType,
        ]);
    }

    /**
     * Bulk stock update.
     */
    public function bulkUpdate(Request $request)
    {
        $params = $request->only(['search', 'category']);
        $response = $this->api->getBulkUpdateProducts($params);

        return view('admin.bulk-update', [
            'products' => $response['data']['products'] ?? [],
            'categories' => $response['data']['categories'] ?? [],
            'filters' => $params,
        ]);
    }

    /**
     * Inventory export.
     */
    public function inventoryExport()
    {
        $categories = $this->api->getCategories();

        return view('admin.inventory-export', [
            'categories' => $categories['data'] ?? [],
        ]);
    }

    /**
     * Inventory receiving page with barcode scanner.
     */
    public function inventoryReceive()
    {
        return view('admin.inventory-receive');
    }

    /**
     * Purchase Orders management.
     */
    public function purchaseOrders(Request $request)
    {
        $params = $request->only(['status', 'supplier', 'search', 'page']);
        $purchaseOrders = $this->api->getPurchaseOrders($params);

        return view('admin.purchase-orders', [
            'purchaseOrders' => $purchaseOrders['data'] ?? [],
            'filters' => $params,
        ]);
    }

    /**
     * Create new purchase order.
     */
    public function createPurchaseOrder()
    {
        return view('admin.purchase-order-create');
    }

    /**
     * Purchase order detail.
     */
    public function purchaseOrderDetail($id)
    {
        $purchaseOrder = $this->api->getPurchaseOrder($id);

        return view('admin.purchase-order-detail', [
            'purchaseOrder' => $purchaseOrder['data'] ?? null,
        ]);
    }

    /**
     * Blog management.
     */
    public function blog(Request $request)
    {
        $params = $request->only(['search', 'status', 'page']);
        $response = $this->api->getAdminBlog($params);
        $categoriesResponse = $this->api->getBlogCategories();

        // Build pagination structure if API doesn't return it
        $postsData = $response['data'] ?? [];
        $posts = [
            'data' => $postsData,
            'current_page' => $response['current_page'] ?? $response['meta']['current_page'] ?? 1,
            'last_page' => $response['last_page'] ?? $response['meta']['last_page'] ?? 1,
            'per_page' => $response['per_page'] ?? $response['meta']['per_page'] ?? 15,
            'total' => $response['total'] ?? $response['meta']['total'] ?? count($postsData),
        ];

        return view('admin.blog', [
            'posts' => $posts,
            'filters' => $params,
            'categories' => $categoriesResponse['data'] ?? [],
        ]);
    }

    /**
     * Blog post detail.
     */
    public function blogDetail($id)
    {
        $post = $this->api->getBlogPost($id);

        return view('admin.blog-detail', [
            'post' => $post['data'] ?? null,
        ]);
    }

    /**
     * Events management.
     */
    public function events(Request $request)
    {
        $params = $request->only(['search', 'status', 'page']);
        $response = $this->api->getAdminEvents($params);

        // Build pagination structure if API doesn't return it
        $eventsData = $response['data'] ?? [];
        $events = [
            'data' => $eventsData,
            'current_page' => $response['current_page'] ?? $response['meta']['current_page'] ?? 1,
            'last_page' => $response['last_page'] ?? $response['meta']['last_page'] ?? 1,
            'per_page' => $response['per_page'] ?? $response['meta']['per_page'] ?? 15,
            'total' => $response['total'] ?? $response['meta']['total'] ?? count($eventsData),
        ];

        return view('admin.events', [
            'events' => $events,
            'filters' => $params,
        ]);
    }

    /**
     * Reviews management.
     */
    public function reviews(Request $request)
    {
        $params = $request->only(['search', 'status', 'rating', 'page']);
        $response = $this->api->getAdminReviews($params);

        // Build paginated response structure
        $reviews = [
            'data' => $response['data'] ?? [],
            'current_page' => $response['meta']['current_page'] ?? 1,
            'last_page' => $response['meta']['last_page'] ?? 1,
            'per_page' => $response['meta']['per_page'] ?? 20,
            'total' => $response['meta']['total'] ?? 0,
        ];

        return view('admin.reviews', [
            'reviews' => $reviews,
            'filters' => $params,
        ]);
    }

    /**
     * FAQ statistics.
     */
    public function faqStats()
    {
        $stats = $this->api->getFaqStats();

        return view('admin.faq-stats', [
            'stats' => $stats['data'] ?? [],
        ]);
    }

    /**
     * Gift cards list.
     */
    public function giftCards(Request $request)
    {
        $params = $request->only(['search', 'status', 'page']);
        $response = $this->api->getGiftCards($params);
        $stats = $this->api->getGiftCardStats();

        // Build pagination structure if API doesn't return it
        $giftCardsData = $response['data'] ?? [];
        $giftCards = [
            'data' => $giftCardsData,
            'current_page' => $response['current_page'] ?? $response['meta']['current_page'] ?? 1,
            'last_page' => $response['last_page'] ?? $response['meta']['last_page'] ?? 1,
            'per_page' => $response['per_page'] ?? $response['meta']['per_page'] ?? 15,
            'total' => $response['total'] ?? $response['meta']['total'] ?? count($giftCardsData),
        ];

        return view('admin.gift-cards', [
            'giftCards' => $giftCards,
            'stats' => $stats['data'] ?? [],
            'filters' => $params,
        ]);
    }

    /**
     * Single gift card detail.
     */
    public function giftCardDetail($id)
    {
        $giftCard = $this->api->getGiftCard($id);

        return view('admin.gift-card-detail', [
            'giftCard' => $giftCard['data'] ?? null,
        ]);
    }

    /**
     * Coupons list.
     */
    public function coupons(Request $request)
    {
        $params = $request->only(['search', 'active', 'page']);
        $response = $this->api->getCoupons($params);

        // Build paginated response
        $coupons = [
            'data' => $response['data'] ?? [],
            'current_page' => $response['meta']['current_page'] ?? 1,
            'last_page' => $response['meta']['last_page'] ?? 1,
            'per_page' => $response['meta']['per_page'] ?? 20,
            'total' => $response['meta']['total'] ?? 0,
        ];

        return view('admin.coupons', [
            'coupons' => $coupons,
            'filters' => $params,
        ]);
    }

    /**
     * Users list.
     */
    public function users(Request $request)
    {
        $params = $request->only(['search', 'role', 'active', 'page']);
        $response = $this->api->getUsers($params);
        $stats = $this->api->getUserStats();

        // Build paginated response
        $users = [
            'data' => $response['data'] ?? [],
            'current_page' => $response['meta']['current_page'] ?? 1,
            'last_page' => $response['meta']['last_page'] ?? 1,
            'per_page' => $response['meta']['per_page'] ?? 20,
            'total' => $response['meta']['total'] ?? 0,
        ];

        return view('admin.users', [
            'users' => $users,
            'stats' => $stats['data'] ?? [],
            'filters' => $params,
        ]);
    }

    /**
     * Loyalty program.
     */
    public function loyalty(Request $request)
    {
        $params = $request->only(['search', 'tier', 'page']);
        $response = $this->api->getLoyaltyMembers($params);
        $stats = $this->api->getLoyaltyStats();
        $tiers = $this->api->getLoyaltyTiers();

        // Build paginated response
        $members = [
            'data' => $response['data'] ?? [],
            'current_page' => $response['meta']['current_page'] ?? 1,
            'last_page' => $response['meta']['last_page'] ?? 1,
            'per_page' => $response['meta']['per_page'] ?? 20,
            'total' => $response['meta']['total'] ?? 0,
        ];

        return view('admin.loyalty', [
            'members' => $members,
            'stats' => $stats['data'] ?? [],
            'tiers' => $tiers['data'] ?? [],
            'filters' => $params,
        ]);
    }

    /**
     * Sales dashboard.
     */
    public function salesDashboard()
    {
        $orderStats = $this->api->getAdminOrderStats();
        $customerStats = $this->api->getAdminCustomerStats();
        $inventoryStats = $this->api->getInventoryStats();

        return view('admin.sales-dashboard', [
            'orderStats' => $orderStats['data'] ?? $orderStats ?? [],
            'customerStats' => $customerStats['data'] ?? $customerStats ?? [],
            'inventoryStats' => $inventoryStats['data'] ?? $inventoryStats ?? [],
        ]);
    }

    /**
     * Reports & Analytics.
     */
    public function reports()
    {
        $orderStats = $this->api->getAdminOrderStats();
        $customerStats = $this->api->getAdminCustomerStats();

        return view('admin.reports', [
            'orderStats' => $orderStats['data'] ?? $orderStats ?? [],
            'customerStats' => $customerStats['data'] ?? $customerStats ?? [],
        ]);
    }

    /**
     * Suppliers management.
     */
    public function suppliers(Request $request)
    {
        $params = $request->only(['status', 'search', 'page']);
        $suppliers = $this->api->getSuppliers($params);
        $stats = $this->api->getSupplierStats();

        return view('admin.suppliers', [
            'suppliers' => $suppliers['data'] ?? [],
            'stats' => $stats['data'] ?? [],
            'filters' => $params,
        ]);
    }

    /**
     * Single supplier detail.
     */
    public function supplierDetail($id)
    {
        $supplier = $this->api->getSupplier($id);

        return view('admin.supplier-detail', [
            'supplier' => $supplier['data'] ?? null,
            'purchaseOrders' => $supplier['purchase_orders'] ?? [],
        ]);
    }

    /**
     * Add supplier page.
     */
    public function addSupplier()
    {
        return view('admin.supplier-add');
    }

    // =====================
    // CRM MANAGEMENT
    // =====================

    /**
     * CRM Customer Tags management.
     */
    public function crmTags()
    {
        $tags = $this->api->getCrmTags();

        return view('admin.crm-tags', [
            'tags' => $tags['data'] ?? [],
        ]);
    }

    /**
     * CRM Customer Segments management.
     */
    public function crmSegments()
    {
        $segments = $this->api->getCrmSegments();

        return view('admin.crm-segments', [
            'segments' => $segments['data'] ?? [],
        ]);
    }

    /**
     * CRM Email Templates management.
     */
    public function crmTemplates()
    {
        $templates = $this->api->get('/admin/crm/email-templates');

        return view('admin.crm-templates', [
            'templates' => $templates['data'] ?? [],
        ]);
    }

    // =====================
    // SUPPORT TICKETS
    // =====================

    /**
     * Support tickets list.
     */
    public function supportTickets(Request $request)
    {
        $params = $request->only(['status', 'priority', 'category', 'search', 'page']);
        $tickets = $this->api->get('/admin/support/tickets', $params);
        $stats = $this->api->get('/admin/support/tickets/stats');

        return view('admin.support-tickets', [
            'tickets' => $tickets,
            'stats' => $stats['data'] ?? [],
            'filters' => $params,
        ]);
    }

    /**
     * Single support ticket detail.
     */
    public function supportTicketDetail($id)
    {
        $response = $this->api->get("/admin/support/tickets/{$id}");
        $cannedResponses = $this->api->get('/admin/support/canned-responses');

        // Convert arrays to objects for blade template
        $ticket = isset($response['data']['ticket']) ? (object) $response['data']['ticket'] : null;
        $messages = array_map(function($msg) {
            return (object) $msg;
        }, $response['data']['messages'] ?? []);
        $otherTickets = array_map(function($t) {
            return (object) $t;
        }, $response['data']['other_tickets'] ?? []);
        $cannedResponsesList = array_map(function($r) {
            return (object) $r;
        }, $cannedResponses['data'] ?? []);

        return view('admin.support-ticket-detail', [
            'ticket' => $ticket,
            'messages' => $messages,
            'otherTickets' => $otherTickets,
            'cannedResponses' => $cannedResponsesList,
        ]);
    }

    /**
     * Canned responses management.
     */
    public function cannedResponses()
    {
        $responses = $this->api->get('/admin/support/canned-responses');

        return view('admin.canned-responses', [
            'responses' => $responses['data'] ?? [],
        ]);
    }

    // =====================
    // RETURNS / RMA
    // =====================

    /**
     * Returns/RMA list.
     */
    public function returns()
    {
        return view('admin.returns');
    }

    /**
     * Return detail.
     */
    public function returnDetail($id)
    {
        return view('admin.returns', ['returnId' => $id]);
    }

    /**
     * Currencies management.
     */
    public function currencies()
    {
        return view('admin.currencies');
    }

    /**
     * Languages management.
     */
    public function languages()
    {
        return view('admin.languages');
    }

    /**
     * Email Marketing campaigns management.
     */
    public function emailMarketing()
    {
        return view('admin.email-marketing');
    }

    /**
     * SMS/Push Notifications management.
     */
    public function notifications()
    {
        return view('admin.notifications');
    }

    public function search()
    {
        return view('admin.search');
    }

    public function variants()
    {
        return view('admin.variants');
    }

    /**
     * Live Chat management.
     */
    public function livechat()
    {
        return view('admin.livechat');
    }

    // =====================
    // SALES PIPELINE
    // =====================

    /**
     * Leads list.
     */
    public function leads(Request $request)
    {
        $params = $request->only(['status', 'priority', 'source_id', 'search', 'page']);
        $leads = $this->api->get('/admin/leads', $params);
        $stats = $this->api->get('/admin/leads/stats');
        $sources = $this->api->get('/admin/leads/sources');

        return view('admin.leads', [
            'leads' => $leads,
            'stats' => $stats['data'] ?? $stats ?? [],
            'sources' => $sources['data'] ?? $sources ?? [],
            'filters' => $params,
        ]);
    }

    /**
     * Single lead detail.
     */
    public function leadDetail($id)
    {
        $response = $this->api->get("/admin/leads/{$id}");
        $sources = $this->api->get('/admin/leads/sources');

        // Convert lead array to object for the view using json encoding
        $leadData = $response['data']['lead'] ?? $response['lead'] ?? null;
        $lead = $leadData ? json_decode(json_encode($leadData)) : null;

        // Convert arrays to objects for blade template
        $activities = array_map(function($a) {
            return (object) $a;
        }, $response['data']['activities'] ?? $response['activities'] ?? []);

        $deals = array_map(function($d) {
            return (object) $d;
        }, $response['data']['deals'] ?? $response['deals'] ?? []);

        $sourcesData = array_map(function($s) {
            return (object) $s;
        }, $sources['data'] ?? $sources ?? []);

        return view('admin.lead-detail', [
            'lead' => $lead,
            'activities' => $activities,
            'deals' => $deals,
            'sources' => $sourcesData,
        ]);
    }

    /**
     * Deals pipeline.
     */
    public function deals(Request $request)
    {
        $params = $request->only(['stage_id', 'search', 'page']);
        $deals = $this->api->get('/admin/deals', $params);
        $stats = $this->api->get('/admin/deals/stats');
        $stages = $this->api->get('/admin/deals/stages');
        $pipelineRaw = $this->api->get('/admin/deals/pipeline');

        // Convert pipeline data to objects for blade template
        $pipeline = array_map(function($stage) {
            $stageObj = (object) $stage;
            $stageObj->stage = isset($stage['stage']) ? (object) $stage['stage'] : null;
            $stageObj->deals = array_map(function($deal) {
                return (object) $deal;
            }, $stage['deals'] ?? []);
            return $stageObj;
        }, $pipelineRaw['data'] ?? $pipelineRaw ?? []);

        // Convert stages to objects
        $stagesData = array_map(function($s) {
            return (object) $s;
        }, $stages['data'] ?? $stages ?? []);

        // Convert deals list to objects
        $dealsList = isset($deals['data']) ? array_map(function($d) {
            return (object) $d;
        }, $deals['data']) : [];

        return view('admin.deals', [
            'deals' => ['data' => $dealsList, 'meta' => $deals['meta'] ?? []],
            'stats' => $stats['data'] ?? $stats ?? [],
            'stages' => $stagesData,
            'pipeline' => $pipeline,
            'filters' => $params,
        ]);
    }

    /**
     * Single deal detail.
     */
    public function dealDetail($id)
    {
        $response = $this->api->get("/admin/deals/{$id}");

        // Convert arrays to objects for blade template
        $dealData = $response['data']['deal'] ?? $response['deal'] ?? null;
        $deal = $dealData ? (object) $dealData : null;

        $activities = array_map(function($a) {
            return (object) $a;
        }, $response['data']['activities'] ?? $response['activities'] ?? []);

        $stages = array_map(function($s) {
            return (object) $s;
        }, $response['data']['stages'] ?? $response['stages'] ?? []);

        return view('admin.deal-detail', [
            'deal' => $deal,
            'activities' => $activities,
            'stages' => $stages,
        ]);
    }

    /**
     * Wholesale accounts.
     */
    public function wholesale(Request $request)
    {
        $params = $request->only(['status', 'tier', 'search', 'page']);
        $accounts = $this->api->get('/admin/wholesale', $params);
        $stats = $this->api->get('/admin/wholesale/stats');
        $tiers = $this->api->get('/admin/wholesale/tiers');

        return view('admin.wholesale', [
            'accounts' => $accounts,
            'stats' => $stats['data'] ?? $stats ?? [],
            'tiers' => $tiers['data'] ?? $tiers ?? [],
            'filters' => $params,
        ]);
    }

    /**
     * Single wholesale account detail.
     */
    public function wholesaleDetail($id)
    {
        $response = $this->api->get("/admin/wholesale/{$id}");
        $tiers = $this->api->get('/admin/wholesale/tiers');

        // Convert arrays to objects for blade template
        $accountData = $response['data']['account'] ?? $response['account'] ?? null;
        $account = $accountData ? (object) $accountData : null;

        $customerData = $response['data']['customer'] ?? $response['customer'] ?? null;
        $customer = $customerData ? (object) $customerData : null;

        $orders = array_map(function($o) {
            return (object) $o;
        }, $response['data']['orders'] ?? $response['orders'] ?? []);

        $tiersData = array_map(function($t) {
            return (object) $t;
        }, $tiers['data'] ?? $tiers ?? []);

        return view('admin.wholesale-detail', [
            'account' => $account,
            'orders' => $orders,
            'customer' => $customer,
            'tiers' => $tiersData,
        ]);
    }

}
