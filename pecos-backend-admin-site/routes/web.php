<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Redirect root to login or dashboard
Route::get('/', function () {
    if (session()->has('admin_logged_in')) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('login');
});

// Admin Routes (protected) - using /adminpanel prefix for Docker
Route::prefix('adminpanel')->name('admin.')->middleware('admin.auth')->group(function () {
    // Dashboard
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

    // Inventory Management
    Route::get('/inventory', [AdminController::class, 'inventory'])->name('inventory');
    Route::get('/inventory/reports', [AdminController::class, 'inventoryReports'])->name('inventory.reports');
    Route::get('/inventory/alerts', [AdminController::class, 'stockAlerts'])->name('inventory.alerts');
    Route::get('/inventory/bulk-update', [AdminController::class, 'bulkUpdate'])->name('inventory.bulk');
    Route::get('/inventory/export', [AdminController::class, 'inventoryExport'])->name('inventory.export');
    Route::get('/inventory/receive', [AdminController::class, 'inventoryReceive'])->name('inventory.receive');

    // Purchase Orders Management
    Route::get('/purchase-orders', [AdminController::class, 'purchaseOrders'])->name('purchase.orders');
    Route::get('/purchase-orders/create', [AdminController::class, 'createPurchaseOrder'])->name('purchase.orders.create');
    Route::get('/purchase-orders/{id}', [AdminController::class, 'purchaseOrderDetail'])->name('purchase.orders.detail');

    // Products Management
    Route::get('/products', [AdminController::class, 'products'])->name('products');

    // Categories Management
    Route::get('/categories', [AdminController::class, 'categories'])->name('categories');

    // Feature Configuration
    Route::get('/features', function () {
        return view('admin.features-config');
    })->name('features');

    // Customers Management
    Route::get('/customers', [AdminController::class, 'customers'])->name('customers');
    Route::get('/customers/{id}', [AdminController::class, 'customerDetail'])->name('customers.detail');

    // CRM Management
    Route::get('/crm/tags', [AdminController::class, 'crmTags'])->name('crm.tags');
    Route::get('/crm/segments', [AdminController::class, 'crmSegments'])->name('crm.segments');
    Route::get('/crm/email-templates', [AdminController::class, 'crmTemplates'])->name('crm.templates');

    // Support Tickets
    Route::get('/support', [AdminController::class, 'supportTickets'])->name('support');
    Route::get('/support/{id}', [AdminController::class, 'supportTicketDetail'])->name('support.detail');
    Route::get('/support-responses', [AdminController::class, 'cannedResponses'])->name('support.responses');

    // Returns / RMA
    Route::get('/returns', [AdminController::class, 'returns'])->name('returns');
    Route::get('/returns/{id}', [AdminController::class, 'returnDetail'])->name('returns.detail');

    // Currencies / Multi-Currency
    Route::get('/currencies', [AdminController::class, 'currencies'])->name('currencies');

    // Languages / Multi-Language
    Route::get('/languages', [AdminController::class, 'languages'])->name('languages');

    // Email Marketing
    Route::get('/email-marketing', [AdminController::class, 'emailMarketing'])->name('email-marketing');

    // SMS/Push Notifications
    Route::get('/notifications', [AdminController::class, 'notifications'])->name('notifications');

    // Advanced Search
    Route::get('/search', [AdminController::class, 'search'])->name('search');

    // Product Variants
    Route::get('/variants', [AdminController::class, 'variants'])->name('variants');

    // Live Chat
    Route::get('/livechat', [AdminController::class, 'livechat'])->name('livechat');

    // Sales Pipeline
    Route::get('/leads', [AdminController::class, 'leads'])->name('leads');
    Route::get('/leads/{id}', [AdminController::class, 'leadDetail'])->name('leads.detail');
    Route::get('/deals', [AdminController::class, 'deals'])->name('deals');
    Route::get('/deals/{id}', [AdminController::class, 'dealDetail'])->name('deals.detail');
    Route::get('/wholesale', [AdminController::class, 'wholesale'])->name('wholesale');
    Route::get('/wholesale/{id}', [AdminController::class, 'wholesaleDetail'])->name('wholesale.detail');

    // Orders Management
    Route::get('/orders', [AdminController::class, 'orders'])->name('orders');
    Route::get('/orders/{id}', [AdminController::class, 'orderDetail'])->name('orders.detail');

    // User Management
    Route::get('/users', [AdminController::class, 'users'])->name('users');

    // Blog Management
    Route::get('/blog', [AdminController::class, 'blog'])->name('blog');
    Route::get('/blog/{id}', [AdminController::class, 'blogDetail'])->name('blog.detail');

    // Events Management
    Route::get('/events', [AdminController::class, 'events'])->name('events');

    // Reviews Management
    Route::get('/reviews', [AdminController::class, 'reviews'])->name('reviews');

    // Coupons Management
    Route::get('/coupons', [AdminController::class, 'coupons'])->name('coupons');

    // Loyalty Program
    Route::get('/loyalty', [AdminController::class, 'loyalty'])->name('loyalty');

    // Gift Cards
    Route::get('/gift-cards', [AdminController::class, 'giftCards'])->name('giftcards');
    Route::get('/gift-cards/{id}', [AdminController::class, 'giftCardDetail'])->name('giftcards.detail');

    // FAQ Statistics
    Route::get('/faq-stats', [AdminController::class, 'faqStats'])->name('faq');

    // Reports
    Route::get('/reports', [AdminController::class, 'reports'])->name('reports');

    // Sales Dashboard
    Route::get('/sales-dashboard', [AdminController::class, 'salesDashboard'])->name('sales.dashboard');

    // Settings
    Route::get('/settings', function () {
        return view('admin.settings');
    })->name('settings');

    // Shipping Settings
    Route::get('/settings/shipping', function () {
        return view('admin.shipping');
    })->name('settings.shipping');

    // Tax Settings
    Route::get('/settings/tax', function () {
        return view('admin.tax-settings');
    })->name('settings.tax');

    // Drop Shippers Management
    Route::get('/dropshippers', function () {
        return view('admin.dropshippers');
    })->name('dropshippers');

    Route::get('/dropshippers/add', function () {
        return view('admin.dropshipper-add');
    })->name('dropshippers.add');

    Route::get('/dropshippers/{id}', function ($id) {
        return view('admin.dropshipper-detail');
    })->name('dropshippers.detail');

    // Suppliers Management
    Route::get('/suppliers', [AdminController::class, 'suppliers'])->name('suppliers');
    Route::get('/suppliers/add', [AdminController::class, 'addSupplier'])->name('suppliers.add');
    Route::get('/suppliers/{id}', [AdminController::class, 'supplierDetail'])->name('suppliers.detail');

    // Drop Ship Orders
    Route::get('/dropship/orders', function () {
        return view('admin.dropship-orders');
    })->name('dropship.orders');

    // API Logs
    Route::get('/api-logs', function () {
        return view('admin.api-logs');
    })->name('api.logs');

    // Profile
    Route::get('/profile', function () {
        return view('admin.profile');
    })->name('profile');

    // Announcements Management
    Route::get('/announcements', function () {
        return view('admin.announcements');
    })->name('announcements');

    // Homepage Banners Management
    Route::get('/banners', function () {
        return view('admin.banners');
    })->name('banners');

    // Category Display Settings
    Route::get('/category-display', function () {
        return view('admin.category-display');
    })->name('category.display');

    // Featured Categories Management
    Route::get('/featured-categories', function () {
        return view('admin.featured-categories');
    })->name('featured.categories');

    // Featured Products Management
    Route::get('/featured-products', function () {
        return view('admin.featured-products');
    })->name('featured.products');

    // Specialty Products Management
    Route::get('/specialty-products', function () {
        return view('admin.specialty-products');
    })->name('specialty.products');

    // Section Management
    Route::get('/sections', function () {
        return view('admin.sections');
    })->name('sections');

    // Product Display Settings
    Route::get('/product-display', function () {
        return view('admin.product-display');
    })->name('product.display');

    // Footer Navigation Management
    Route::get('/footer', function () {
        return view('admin.footer-links');
    })->name('footer.links');

    Route::get('/footer/pages', function () {
        return view('admin.footer-pages');
    })->name('footer.pages');

    Route::get('/footer/pages/{id}', function ($id) {
        return view('admin.footer-pages');
    })->name('footer.pages.edit');
});
