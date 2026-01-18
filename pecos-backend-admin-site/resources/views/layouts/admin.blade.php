@php
// Get API base URL from environment
$apiBaseUrl = rtrim(env('API_BASE_URL', 'http://localhost:8300/api/v1'), '/');

// Load feature settings from API
$features = session('admin_features', []);
$featuresCacheTime = session('admin_features_cached_at', 0);

// Cache features for 5 minutes
if (empty($features) || (time() - $featuresCacheTime) > 300) {
    try {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiBaseUrl . '/admin/settings/features');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200 && $response) {
            $data = json_decode($response, true);
            if (isset($data['success']) && $data['success'] && isset($data['data'])) {
                $features = $data['data'];
                session(['admin_features' => $features, 'admin_features_cached_at' => time()]);
            }
        }
    } catch (Exception $e) {
        // Use defaults on error
    }
}

// Helper function to check if feature is enabled
$isFeatureEnabled = function($name) use ($features) {
    $key = $name . '_enabled';
    return isset($features[$key]) && ($features[$key] === true || $features[$key] === '1' || $features[$key] == 1);
};

// Load stock alerts count for notifications
$stockAlerts = [];
$stockAlertsCount = 0;
$messagesCount = 0;
$messages = [];

try {
    // Get stock alerts
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiBaseUrl . '/admin/inventory/stock-alerts');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200 && $response) {
        $data = json_decode($response, true);
        if (isset($data['success']) && $data['success'] && isset($data['data'])) {
            $stockAlerts = array_slice($data['data'], 0, 5);
            $stockAlertsCount = count($data['data']);
        }
    }

    // Get contact messages
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiBaseUrl . '/admin/messages?status=unread');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200 && $response) {
        $data = json_decode($response, true);
        if (isset($data['data'])) {
            $messages = array_slice($data['data'], 0, 5);
            $messagesCount = $data['meta']['total'] ?? count($data['data']);
        }
    }
} catch (Exception $e) {
    // Use defaults on error
}

// If no messages from API, show sample messages
if (empty($messages)) {
    $messages = [
        [
            'name' => 'John Smith',
            'subject' => 'Question about boot sizing',
            'created_at' => date('Y-m-d H:i:s', strtotime('-2 hours')),
            'status' => 'unread'
        ],
        [
            'name' => 'Sarah Johnson',
            'subject' => 'Wholesale inquiry',
            'created_at' => date('Y-m-d H:i:s', strtotime('-5 hours')),
            'status' => 'unread'
        ],
        [
            'name' => 'Mike Davis',
            'subject' => 'Return request for order #4521',
            'created_at' => date('Y-m-d H:i:s', strtotime('-1 day')),
            'status' => 'unread'
        ]
    ];
    $messagesCount = 3;
}

// Fetch store name and styling from settings API
$storeName = 'Pecos River Traders'; // Default
$storeNameColor = '#8B4513'; // Default brown
$storeNameSize = '1.25rem'; // Default medium
try {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiBaseUrl . '/admin/settings');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 3);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200 && $response) {
        $data = json_decode($response, true);
        if (isset($data['data']['store']['store_name']) && !empty($data['data']['store']['store_name'])) {
            $storeName = $data['data']['store']['store_name'];
        }
        if (isset($data['data']['store']['store_name_color']) && !empty($data['data']['store']['store_name_color'])) {
            $storeNameColor = $data['data']['store']['store_name_color'];
        }
        if (isset($data['data']['store']['store_name_size']) && !empty($data['data']['store']['store_name_size'])) {
            $storeNameSize = $data['data']['store']['store_name_size'];
        }
    }
} catch (Exception $e) {
    // Use default on error
}
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Pecos River Traders Admin</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Custom Admin CSS -->
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">

    @stack('styles')
</head>
<body>

<!-- Sidebar -->
<aside class="sidebar">
    <div class="sidebar-header">
        <h5>Admin Panel</h5>
    </div>

    <nav class="sidebar-nav" id="sidebarNav">
        <div class="nav-section">Main</div>
        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i>
            Dashboard
        </a>

        <div class="nav-section">Inventory</div>
        <a href="{{ route('admin.inventory') }}" class="nav-link {{ request()->routeIs('admin.inventory') ? 'active' : '' }}">
            <i class="bi bi-box-seam"></i>
            Inventory Dashboard
            <span class="badge bg-warning">12</span>
        </a>
        <a href="{{ route('admin.purchase.orders') }}" class="nav-link {{ request()->routeIs('admin.purchase.orders*') ? 'active' : '' }}">
            <i class="bi bi-cart-check"></i>
            Purchase Orders
        </a>
        <a href="{{ route('admin.inventory.receive') }}" class="nav-link {{ request()->routeIs('admin.inventory.receive') ? 'active' : '' }}">
            <i class="bi bi-upc-scan"></i>
            Receiving
        </a>
        <a href="{{ route('admin.inventory.alerts') }}" class="nav-link {{ request()->routeIs('admin.inventory.alerts') ? 'active' : '' }}">
            <i class="bi bi-bell"></i>
            Stock Alerts
        </a>
        <a href="{{ route('admin.inventory.reports') }}" class="nav-link {{ request()->routeIs('admin.inventory.reports') ? 'active' : '' }}">
            <i class="bi bi-file-earmark-bar-graph"></i>
            Inventory Reports
        </a>
        <a href="{{ route('admin.inventory.bulk') }}" class="nav-link {{ request()->routeIs('admin.inventory.bulk') ? 'active' : '' }}">
            <i class="bi bi-cloud-upload"></i>
            Bulk Update
        </a>
        <a href="{{ route('admin.inventory.export') }}" class="nav-link {{ request()->routeIs('admin.inventory.export') ? 'active' : '' }}">
            <i class="bi bi-download"></i>
            Export
        </a>

        <div class="nav-section">Catalog</div>
        <a href="{{ route('admin.products') }}" class="nav-link {{ request()->routeIs('admin.products*') ? 'active' : '' }}">
            <i class="bi bi-grid"></i>
            Products
        </a>
        <a href="{{ route('admin.categories') }}" class="nav-link {{ request()->routeIs('admin.categories*') ? 'active' : '' }}">
            <i class="bi bi-tags"></i>
            Categories
        </a>

        <div class="nav-section">Sales</div>
        <a href="{{ route('admin.orders') }}" class="nav-link {{ request()->routeIs('admin.orders*') ? 'active' : '' }}">
            <i class="bi bi-cart3"></i>
            Orders
            <span class="badge bg-danger">5</span>
        </a>
        <a href="{{ route('admin.customers') }}" class="nav-link {{ request()->routeIs('admin.customers*') ? 'active' : '' }}">
            <i class="bi bi-people"></i>
            Customers
        </a>
        <a href="{{ route('admin.sales.dashboard') }}" class="nav-link {{ request()->routeIs('admin.sales.dashboard') ? 'active' : '' }}">
            <i class="bi bi-bar-chart"></i>
            Sales Dashboard
        </a>

        <div class="nav-section">CRM</div>
        <a href="{{ route('admin.crm.tags') }}" class="nav-link {{ request()->routeIs('admin.crm.tags*') ? 'active' : '' }}">
            <i class="bi bi-tags-fill"></i>
            Customer Tags
        </a>
        <a href="{{ route('admin.crm.segments') }}" class="nav-link {{ request()->routeIs('admin.crm.segments*') ? 'active' : '' }}">
            <i class="bi bi-pie-chart-fill"></i>
            Segments
        </a>
        <a href="{{ route('admin.crm.templates') }}" class="nav-link {{ request()->routeIs('admin.crm.templates*') ? 'active' : '' }}">
            <i class="bi bi-envelope-paper"></i>
            Email Templates
        </a>

        <div class="nav-section">Support</div>
        <a href="{{ route('admin.support') }}" class="nav-link {{ request()->routeIs('admin.support') && !request()->routeIs('admin.support.responses') ? 'active' : '' }}">
            <i class="bi bi-headset"></i>
            Support Tickets
        </a>
        <a href="{{ route('admin.support.responses') }}" class="nav-link {{ request()->routeIs('admin.support.responses') ? 'active' : '' }}">
            <i class="bi bi-chat-square-text"></i>
            Canned Responses
        </a>

        <div class="nav-section">Sales Pipeline</div>
        <a href="{{ route('admin.leads') }}" class="nav-link {{ request()->routeIs('admin.leads*') ? 'active' : '' }}">
            <i class="bi bi-person-lines-fill"></i>
            Leads
        </a>
        <a href="{{ route('admin.deals') }}" class="nav-link {{ request()->routeIs('admin.deals*') ? 'active' : '' }}">
            <i class="bi bi-briefcase"></i>
            Deals
        </a>
        <a href="{{ route('admin.wholesale') }}" class="nav-link {{ request()->routeIs('admin.wholesale*') ? 'active' : '' }}">
            <i class="bi bi-building"></i>
            Wholesale
        </a>

        <div class="nav-section">Users</div>
        <a href="{{ route('admin.users') }}" class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
            <i class="bi bi-person-gear"></i>
            User Management
        </a>

        <div class="nav-section">Content</div>
        <a href="{{ route('admin.announcements') }}" class="nav-link {{ request()->routeIs('admin.announcements*') ? 'active' : '' }}">
            <i class="bi bi-megaphone"></i>
            Announcements
        </a>
        <a href="{{ route('admin.banners') }}" class="nav-link {{ request()->routeIs('admin.banners*') ? 'active' : '' }}">
            <i class="bi bi-image"></i>
            Homepage Banners
        </a>
        <a href="{{ route('admin.category.display') }}" class="nav-link {{ request()->routeIs('admin.category.display*') ? 'active' : '' }}">
            <i class="bi bi-grid-3x3-gap"></i>
            Category Display
        </a>
        <a href="{{ route('admin.featured.categories') }}" class="nav-link {{ request()->routeIs('admin.featured.categories*') ? 'active' : '' }}">
            <i class="bi bi-star"></i>
            Featured Categories
        </a>
        <a href="{{ route('admin.featured.products') }}" class="nav-link {{ request()->routeIs('admin.featured.products*') ? 'active' : '' }}">
            <i class="bi bi-box-seam"></i>
            Featured Products
        </a>
        <a href="{{ route('admin.specialty.products') }}" class="nav-link {{ request()->routeIs('admin.specialty.products*') ? 'active' : '' }}">
            <i class="bi bi-stars"></i>
            Specialty Products
        </a>
        <a href="{{ route('admin.sections') }}" class="nav-link {{ request()->routeIs('admin.sections*') ? 'active' : '' }}">
            <i class="bi bi-layers"></i>
            Section Management
        </a>
        <a href="{{ route('admin.product.display') }}" class="nav-link {{ request()->routeIs('admin.product.display*') ? 'active' : '' }}">
            <i class="bi bi-layout-text-window-reverse"></i>
            Product Display
        </a>
        <a href="{{ route('admin.footer.links') }}" class="nav-link {{ request()->routeIs('admin.footer*') ? 'active' : '' }}">
            <i class="bi bi-layout-text-sidebar-reverse"></i>
            Footer Navigation
        </a>
        <a href="{{ route('admin.blog') }}" class="nav-link {{ request()->routeIs('admin.blog*') ? 'active' : '' }} {{ !$isFeatureEnabled('blog') ? 'feature-disabled' : '' }}" {{ !$isFeatureEnabled('blog') ? 'title=Feature disabled' : '' }}>
            <i class="bi bi-file-earmark-richtext"></i>
            Blog Posts
        </a>
        <a href="{{ route('admin.events') }}" class="nav-link {{ request()->routeIs('admin.events*') ? 'active' : '' }} {{ !$isFeatureEnabled('events') ? 'feature-disabled' : '' }}" {{ !$isFeatureEnabled('events') ? 'title=Feature disabled' : '' }}>
            <i class="bi bi-calendar-event"></i>
            Events
        </a>
        <a href="{{ route('admin.reviews') }}" class="nav-link {{ request()->routeIs('admin.reviews*') ? 'active' : '' }} {{ !$isFeatureEnabled('reviews') ? 'feature-disabled' : '' }}" {{ !$isFeatureEnabled('reviews') ? 'title=Feature disabled' : '' }}>
            <i class="bi bi-star"></i>
            Reviews
            <span class="badge bg-info">8</span>
        </a>
        <a href="{{ route('admin.faq') }}" class="nav-link {{ request()->routeIs('admin.faq*') ? 'active' : '' }} {{ !$isFeatureEnabled('faq') ? 'feature-disabled' : '' }}" {{ !$isFeatureEnabled('faq') ? 'title=Feature disabled' : '' }}>
            <i class="bi bi-question-circle"></i>
            FAQ Statistics
        </a>

        <div class="nav-section">Marketing</div>
        <a href="{{ route('admin.coupons') }}" class="nav-link {{ request()->routeIs('admin.coupons*') ? 'active' : '' }}">
            <i class="bi bi-ticket-perforated"></i>
            Coupons
        </a>
        <a href="{{ route('admin.loyalty') }}" class="nav-link {{ request()->routeIs('admin.loyalty*') ? 'active' : '' }} {{ !$isFeatureEnabled('loyalty') ? 'feature-disabled' : '' }}" {{ !$isFeatureEnabled('loyalty') ? 'title=Feature disabled' : '' }}>
            <i class="bi bi-trophy"></i>
            Loyalty Program
        </a>
        <a href="{{ route('admin.giftcards') }}" class="nav-link {{ request()->routeIs('admin.giftcards*') ? 'active' : '' }} {{ !$isFeatureEnabled('gift_cards') ? 'feature-disabled' : '' }}" {{ !$isFeatureEnabled('gift_cards') ? 'title=Feature disabled' : '' }}>
            <i class="bi bi-gift"></i>
            Gift Cards
        </a>

        <div class="nav-section">Suppliers & Drop Shipping</div>
        <a href="{{ route('admin.suppliers') }}" class="nav-link {{ request()->routeIs('admin.suppliers*') ? 'active' : '' }}">
            <i class="bi bi-building"></i>
            Suppliers
        </a>
        <a href="{{ route('admin.dropshippers') }}" class="nav-link {{ request()->routeIs('admin.dropshippers*') ? 'active' : '' }}">
            <i class="bi bi-truck"></i>
            Drop Shippers
        </a>
        <a href="{{ route('admin.dropship.orders') }}" class="nav-link {{ request()->routeIs('admin.dropship.orders') ? 'active' : '' }}">
            <i class="bi bi-box"></i>
            Drop Ship Orders
        </a>

        <div class="nav-section">System</div>
        <a href="{{ route('admin.reports') }}" class="nav-link {{ request()->routeIs('admin.reports') ? 'active' : '' }}">
            <i class="bi bi-graph-up"></i>
            Reports
        </a>
        <a href="{{ route('admin.settings') }}" class="nav-link {{ request()->routeIs('admin.settings') && !request()->routeIs('admin.settings.*') ? 'active' : '' }}">
            <i class="bi bi-gear"></i>
            Settings
        </a>
        <a href="{{ route('admin.features') }}" class="nav-link {{ request()->routeIs('admin.features') ? 'active' : '' }}">
            <i class="bi bi-toggles"></i>
            Feature Config
        </a>
        <a href="{{ route('admin.settings.shipping') }}" class="nav-link {{ request()->routeIs('admin.settings.shipping') ? 'active' : '' }}">
            <i class="bi bi-truck"></i>
            Shipping
        </a>
        <a href="{{ route('admin.settings.tax') }}" class="nav-link {{ request()->routeIs('admin.settings.tax') ? 'active' : '' }}">
            <i class="bi bi-percent"></i>
            Tax Settings
        </a>
        <a href="{{ route('admin.api.logs') }}" class="nav-link {{ request()->routeIs('admin.api.logs') ? 'active' : '' }}">
            <i class="bi bi-activity"></i>
            API Logs
        </a>
    </nav>
</aside>

<!-- Main Content -->
<main class="main-content">
    <!-- Top Header -->
    <header class="top-header">
        <div class="header-left">
            <button class="sidebar-toggle" onclick="toggleSidebar()">
                <i class="bi bi-list"></i>
            </button>
            <div class="header-search">
                <i class="bi bi-search"></i>
                <input type="text" placeholder="Search..." class="form-control">
            </div>
        </div>

        <div class="header-center">
            <h4 class="store-name mb-0" style="font-size: {{ $storeNameSize }}; color: {{ $storeNameColor }};">{{ $storeName }}</h4>
        </div>

        <div class="header-right">
            <a href="{{ config('services.storefront.url') }}" class="header-icon" title="View Store" target="_blank">
                <i class="bi bi-shop"></i>
            </a>
            <!-- Notifications Dropdown -->
            <div class="dropdown">
                <div class="header-icon" title="Stock Alerts" data-bs-toggle="dropdown" style="cursor: pointer;">
                    <i class="bi bi-bell"></i>
                    @if($stockAlertsCount > 0)
                        <span class="badge bg-danger">{{ $stockAlertsCount > 99 ? '99+' : $stockAlertsCount }}</span>
                    @endif
                </div>
                <ul class="dropdown-menu dropdown-menu-end" style="min-width: 320px; max-height: 400px; overflow-y: auto;">
                    <li><h6 class="dropdown-header"><i class="bi bi-bell"></i> Stock Alerts</h6></li>
                    @if(count($stockAlerts) > 0)
                        @foreach($stockAlerts as $alert)
                            <li>
                                <a class="dropdown-item py-2" href="{{ route('admin.inventory.alerts') }}">
                                    <div class="d-flex align-items-start">
                                        <i class="bi {{ $alert['alert_type'] == 'out_of_stock' ? 'bi-x-circle text-danger' : 'bi-exclamation-triangle text-warning' }} me-2 mt-1"></i>
                                        <div>
                                            <div class="fw-semibold small">{{ Str::limit($alert['ShortDescription'], 40) }}</div>
                                            <small class="text-muted">{{ ucwords(str_replace('_', ' ', $alert['alert_type'])) }} - Stock: {{ $alert['available'] ?? $alert['current_stock'] ?? 0 }}</small>
                                        </div>
                                    </div>
                                </a>
                            </li>
                        @endforeach
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-center text-primary" href="{{ route('admin.inventory.alerts') }}"><i class="bi bi-arrow-right"></i> View All Alerts</a></li>
                    @else
                        <li><span class="dropdown-item-text text-muted text-center py-3"><i class="bi bi-check-circle text-success"></i> No active alerts</span></li>
                    @endif
                </ul>
            </div>
            <!-- Messages Dropdown -->
            <div class="dropdown">
                <div class="header-icon" title="Messages" data-bs-toggle="dropdown" style="cursor: pointer;">
                    <i class="bi bi-envelope"></i>
                    @if($messagesCount > 0)
                        <span class="badge bg-primary">{{ $messagesCount > 99 ? '99+' : $messagesCount }}</span>
                    @endif
                </div>
                <ul class="dropdown-menu dropdown-menu-end" style="min-width: 320px; max-height: 400px; overflow-y: auto;">
                    <li><h6 class="dropdown-header"><i class="bi bi-envelope"></i> Messages</h6></li>
                    @if(count($messages) > 0)
                        @foreach($messages as $msg)
                            <li>
                                <a class="dropdown-item py-2" href="#">
                                    <div class="d-flex align-items-start">
                                        <i class="bi bi-envelope-fill text-primary me-2 mt-1"></i>
                                        <div>
                                            <div class="fw-semibold small">{{ $msg['name'] ?? 'Unknown' }}</div>
                                            <div class="small">{{ Str::limit($msg['subject'] ?? '', 35) }}</div>
                                            <small class="text-muted">{{ isset($msg['created_at']) ? \Carbon\Carbon::parse($msg['created_at'])->format('M d, g:i A') : '' }}</small>
                                        </div>
                                    </div>
                                </a>
                            </li>
                        @endforeach
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-center text-primary" href="#"><i class="bi bi-arrow-right"></i> View All Messages</a></li>
                    @else
                        <li><span class="dropdown-item-text text-muted text-center py-3"><i class="bi bi-inbox"></i> No messages</span></li>
                    @endif
                </ul>
            </div>

            <div class="dropdown">
                <div class="user-dropdown" data-bs-toggle="dropdown">
                    @php
                        $adminUser = session('admin_user');
                        $initials = $adminUser ? strtoupper(substr($adminUser['first_name'] ?? 'A', 0, 1) . substr($adminUser['last_name'] ?? 'D', 0, 1)) : 'AD';
                    @endphp
                    <div class="user-avatar">{{ $initials }}</div>
                    <div class="user-info">
                        <div class="name">{{ $adminUser['first_name'] ?? 'Admin' }} {{ $adminUser['last_name'] ?? 'User' }}</div>
                        <div class="role">{{ ucfirst($adminUser['role'] ?? 'Administrator') }}</div>
                    </div>
                    <i class="bi bi-chevron-down"></i>
                </div>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="{{ route('admin.profile') }}"><i class="bi bi-person me-2"></i>Profile</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.settings') }}"><i class="bi bi-gear me-2"></i>Settings</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST" class="d-inline w-100">
                            @csrf
                            <button type="submit" class="dropdown-item"><i class="bi bi-box-arrow-right me-2"></i>Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </header>

    <!-- Page Content -->
    <div class="page-content">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>
</main>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom Admin JS -->
<script>
    // Toggle Sidebar for Mobile
    function toggleSidebar() {
        document.querySelector('.sidebar').classList.toggle('show');
    }

    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(e) {
        const sidebar = document.querySelector('.sidebar');
        const toggle = document.querySelector('.sidebar-toggle');

        if (window.innerWidth <= 991) {
            if (!sidebar.contains(e.target) && !toggle.contains(e.target)) {
                sidebar.classList.remove('show');
            }
        }
    });

    // Global fix for aria-hidden focus issue on all modals
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.modal').forEach(function(modal) {
            modal.addEventListener('hide.bs.modal', function() {
                if (document.activeElement && this.contains(document.activeElement)) {
                    document.activeElement.blur();
                }
            });
        });
    });

    // Sidebar scroll position persistence
    (function() {
        var sidebar = document.querySelector('.sidebar');
        if (!sidebar) return;

        // Restore scroll position on page load
        var savedScrollPos = localStorage.getItem('sidebarScrollPos');
        if (savedScrollPos !== null) {
            sidebar.scrollTop = parseInt(savedScrollPos, 10);
        }

        // Save scroll position before navigating away
        var navLinks = sidebar.querySelectorAll('a.nav-link');
        navLinks.forEach(function(link) {
            link.addEventListener('click', function() {
                localStorage.setItem('sidebarScrollPos', sidebar.scrollTop);
            });
        });

        // Also save on scroll (debounced) for cases where page might refresh
        var scrollTimeout;
        sidebar.addEventListener('scroll', function() {
            clearTimeout(scrollTimeout);
            scrollTimeout = setTimeout(function() {
                localStorage.setItem('sidebarScrollPos', sidebar.scrollTop);
            }, 100);
        });
    })();
</script>

@stack('scripts')
</body>
</html>
