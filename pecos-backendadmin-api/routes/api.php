<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\ReviewController;
use App\Http\Controllers\Api\V1\WishlistController;
use App\Http\Controllers\Api\V1\BlogController;
use App\Http\Controllers\Api\V1\EventController;
use App\Http\Controllers\Api\V1\FaqController;
use App\Http\Controllers\Api\V1\CartController;
use App\Http\Controllers\Api\V1\LoyaltyController;
use App\Http\Controllers\Api\V1\CouponController;
use App\Http\Controllers\Api\V1\Admin\InventoryController;
use App\Http\Controllers\Api\V1\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Api\V1\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Api\V1\Admin\GiftCardController;
use App\Http\Controllers\Api\V1\Admin\ExportController;
use App\Http\Controllers\Api\V1\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\V1\Admin\LoyaltyController as AdminLoyaltyController;
use App\Http\Controllers\Api\V1\Admin\TaxController;
use App\Http\Controllers\Api\V1\Admin\ShippingController;
use App\Http\Controllers\Api\V1\Admin\DropshipperController;
use App\Http\Controllers\Api\V1\Admin\SupplierController;
use App\Http\Controllers\Api\V1\Admin\ApiLogController;
use App\Http\Controllers\Api\V1\Admin\SettingsController;
use App\Http\Controllers\Api\V1\Admin\PurchaseOrderController;
use App\Http\Controllers\Api\V1\Admin\CrmController;
use App\Http\Controllers\Api\V1\Admin\SupportController;
use App\Http\Controllers\Api\V1\Admin\LeadsController;
use App\Http\Controllers\Api\V1\Admin\DealsController;
use App\Http\Controllers\Api\V1\Admin\WholesaleController;
use App\Http\Controllers\Api\V1\StateController;
use App\Http\Controllers\Api\V1\UserSettingsController;
use App\Http\Controllers\Api\V1\QAController;
use App\Http\Controllers\Api\V1\AnnouncementController;
use App\Http\Controllers\Api\V1\BannerController;
use App\Http\Controllers\Api\V1\Admin\FooterController;
use App\Http\Controllers\Api\V1\Admin\FeaturedCategoriesController;
use App\Http\Controllers\Api\V1\Admin\FeaturedProductsController;
use App\Http\Controllers\Api\V1\Admin\SpecialtyController;
use App\Http\Controllers\Api\V1\Admin\SectionController;
use App\Http\Controllers\Api\V1\Admin\ReturnsController;
use App\Http\Controllers\Api\V1\Admin\CurrencyController;
use App\Http\Controllers\Api\V1\Admin\LanguageController;
use App\Http\Controllers\Api\V1\Admin\EmailMarketingController;
use App\Http\Controllers\Api\V1\Admin\NotificationsController;
use App\Http\Controllers\Api\V1\Admin\SearchController;
use App\Http\Controllers\Api\V1\Admin\VariantsController;
use App\Http\Controllers\Api\V1\Admin\ChatController;
use App\Http\Controllers\Api\V1\PaymentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// API Version 1
Route::prefix('v1')->group(function () {

    // =====================
    // PUBLIC ROUTES
    // =====================

    // Authentication
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);

    // Products
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/search', [ProductController::class, 'search']);
    Route::get('/products/featured', [ProductController::class, 'featured']);
    Route::get('/products/by-id/{id}', [ProductController::class, 'showById']);
    Route::get('/products/{upc}', [ProductController::class, 'show']);
    Route::get('/products/category/{categoryCode}', [ProductController::class, 'byCategory']);

    // Product Images Management (public for now - add auth later)
    Route::get('/products/{upc}/images', [ProductController::class, 'getImages']);
    Route::post('/products/{upc}/images', [ProductController::class, 'uploadImages']);
    Route::put('/products/{upc}/images/reorder', [ProductController::class, 'reorderImages']);
    Route::put('/products/{upc}/images/{imageId}/primary', [ProductController::class, 'setPrimaryImage']);
    Route::delete('/products/{upc}/images/{imageId}', [ProductController::class, 'deleteImage']);

    // Product History (public for now - add auth later)
    Route::get('/products/{upc}/history', [ProductController::class, 'getHistory']);

    // Product Reviews (public read)
    Route::get('/products/{productId}/reviews', [ReviewController::class, 'index']);
    Route::post('/reviews/{reviewId}/helpful', [ReviewController::class, 'helpful']);
    Route::post('/reviews/{reviewId}/not-helpful', [ReviewController::class, 'notHelpful']);

    // Product Q&A (public)
    Route::get('/products/{productId}/questions', [QAController::class, 'productQuestions']);
    Route::post('/products/{productId}/questions', [QAController::class, 'askQuestion']);
    Route::post('/qa/vote', [QAController::class, 'vote']);

    // Admin Q&A
    Route::get('/admin/qa/questions', [QAController::class, 'adminIndex']);
    Route::get('/admin/qa/stats', [QAController::class, 'adminStats']);
    Route::get('/admin/qa/questions/{id}', [QAController::class, 'adminShow']);
    Route::put('/admin/qa/questions/{id}/status', [QAController::class, 'updateStatus']);
    Route::post('/admin/qa/questions/{id}/answer', [QAController::class, 'answerQuestion']);
    Route::delete('/admin/qa/questions/{id}', [QAController::class, 'destroy']);

    // Announcements (public)
    Route::get('/announcements', [AnnouncementController::class, 'index']);
    Route::get('/announcements/icons', [AnnouncementController::class, 'icons']);

    // Announcements (admin)
    Route::get('/admin/announcements', [AnnouncementController::class, 'adminIndex']);
    Route::post('/admin/announcements', [AnnouncementController::class, 'store']);
    Route::put('/admin/announcements/{id}', [AnnouncementController::class, 'update']);
    Route::delete('/admin/announcements/{id}', [AnnouncementController::class, 'destroy']);
    Route::put('/admin/announcements/settings', [AnnouncementController::class, 'updateSettings']);
    Route::put('/admin/announcements/reorder', [AnnouncementController::class, 'reorder']);

    // Homepage Banners (public)
    Route::get('/banners', [BannerController::class, 'index']);

    // Homepage Banners (admin)
    Route::get('/admin/banners', [BannerController::class, 'adminIndex']);
    Route::get('/admin/banners/{id}', [BannerController::class, 'show']);
    Route::post('/admin/banners', [BannerController::class, 'store']);
    Route::post('/admin/banners/{id}', [BannerController::class, 'update']); // POST for file uploads
    Route::delete('/admin/banners/{id}', [BannerController::class, 'destroy']);
    Route::put('/admin/banners/settings', [BannerController::class, 'updateSettings']);
    Route::put('/admin/banners/reorder', [BannerController::class, 'reorder']);

    // Categories
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/tree', [CategoryController::class, 'tree']);
    Route::get('/categories/bottom', [CategoryController::class, 'bottomLevel']);
    Route::get('/categories/{categoryCode}', [CategoryController::class, 'show']);

    // Blog (public)
    Route::get('/blog', [BlogController::class, 'index']);
    Route::get('/blog/categories', [BlogController::class, 'categories']);
    Route::get('/blog/recent', [BlogController::class, 'recent']);
    Route::get('/blog/{slug}', [BlogController::class, 'show']);

    // Events (public)
    Route::get('/events', [EventController::class, 'index']);
    Route::get('/events/upcoming', [EventController::class, 'upcoming']);
    Route::get('/events/{id}', [EventController::class, 'show']);

    // FAQs (public)
    Route::get('/faqs', [FaqController::class, 'index']);
    Route::get('/faqs/categories', [FaqController::class, 'categories']);
    Route::get('/faqs/{id}', [FaqController::class, 'show']);
    Route::post('/faqs/{id}/helpful', [FaqController::class, 'helpful']);
    Route::post('/faqs/{id}/not-helpful', [FaqController::class, 'notHelpful']);

    // States (public)
    Route::get('/states', [StateController::class, 'index']);

    // Guest Orders (public - for checkout without login)
    Route::post('/orders/guest', [OrderController::class, 'storeGuest']);
    Route::get('/orders/lookup/{orderNumber}', [OrderController::class, 'lookup']);
    Route::get('/orders/user/{userId}', [OrderController::class, 'userOrders']);
    Route::get('/orders/detail/{id}', [OrderController::class, 'showById']);

    // Wishlist by user ID (for PHP session-based auth)
    Route::get('/wishlist/user/{userId}', [WishlistController::class, 'userWishlist']);
    Route::delete('/wishlist/user/{userId}/{productId}', [WishlistController::class, 'removeByUserId']);
    Route::post('/wishlist/user/{userId}/add/{productId}', [WishlistController::class, 'addByUserId']);
    Route::post('/wishlist/user/{userId}/toggle/{productId}', [WishlistController::class, 'toggleByUserId']);
    Route::get('/wishlist/user/{userId}/check/{productId}', [WishlistController::class, 'checkByUserId']);
    Route::delete('/wishlist/user/{userId}/clear', [WishlistController::class, 'clearByUserId']);

    // User profile by ID (for PHP session-based auth)
    Route::get('/users/{userId}/profile', [AuthController::class, 'userProfile']);
    Route::post('/users/login', [AuthController::class, 'loginByCredentials']);
    Route::post('/users/register', [AuthController::class, 'registerUser']);

    // Buy again items by user ID
    Route::get('/users/{userId}/buy-again', [OrderController::class, 'buyAgainItems']);

    // User settings (all settings in one call)
    Route::get('/users/{userId}/settings', [UserSettingsController::class, 'getAllSettings']);

    // Customer Support Tickets (by user ID for PHP session-based auth)
    Route::get('/customer/support/tickets', [SupportController::class, 'customerTickets']);
    Route::get('/customer/support/tickets/{id}', [SupportController::class, 'customerTicketDetail']);
    Route::post('/customer/support/tickets', [SupportController::class, 'customerCreateTicket']);
    Route::post('/customer/support/tickets/{id}/reply', [SupportController::class, 'customerReply']);
    Route::post('/customer/support/tickets/{id}/rate', [SupportController::class, 'addRating']);

    // Loyalty endpoints (public by user ID)
    Route::get('/loyalty/user/{userId}', [LoyaltyController::class, 'userLoyalty']);
    Route::get('/loyalty/user/{userId}/transactions', [LoyaltyController::class, 'userTransactions']);
    Route::get('/loyalty/tiers', [LoyaltyController::class, 'allTiers']);
    Route::get('/loyalty/rewards/{userId}', [LoyaltyController::class, 'userRewards']);

    // Admin inventory endpoints (public for now - add auth later)
    Route::get('/admin/inventory/stats', [InventoryController::class, 'stats']);
    Route::get('/admin/inventory/products', [InventoryController::class, 'products']);
    Route::get('/admin/inventory/alerts', [InventoryController::class, 'alerts']);
    Route::get('/admin/inventory/stock-alerts', [InventoryController::class, 'stockAlerts']);
    Route::get('/admin/inventory/reports', [InventoryController::class, 'reports']);
    Route::get('/admin/inventory/reports-export', [InventoryController::class, 'reportsExport']);
    Route::get('/admin/inventory/product/{id}', [InventoryController::class, 'getProduct']);
    Route::get('/admin/inventory/bulk-products', [InventoryController::class, 'bulkUpdateProducts']);
    Route::get('/admin/inventory/export-data', [InventoryController::class, 'exportData']);
    Route::get('/admin/stock-alerts/full', [InventoryController::class, 'stockAlertsFull']);
    Route::post('/admin/stock-alerts/resolve', [InventoryController::class, 'resolveAlert']);
    Route::post('/admin/stock-alerts/resolve-all', [InventoryController::class, 'resolveAllAlerts']);
    Route::post('/admin/inventory/adjust-stock', [InventoryController::class, 'adjustStock']);
    Route::post('/admin/inventory/update-settings', [InventoryController::class, 'updateSettings']);
    Route::post('/admin/inventory/bulk-adjust-csv', [InventoryController::class, 'bulkAdjustCsv']);
    Route::post('/admin/inventory/bulk-adjust-manual', [InventoryController::class, 'bulkAdjustManual']);
    Route::get('/admin/reviews', [ReviewController::class, 'adminIndex']);
    Route::patch('/admin/reviews/{reviewId}/status', [ReviewController::class, 'updateStatus']);
    Route::get('/admin/blog', [BlogController::class, 'adminIndex']);
    Route::get('/admin/blog/{id}', [BlogController::class, 'showById']);
    Route::post('/admin/blog', [BlogController::class, 'store']);
    Route::put('/admin/blog/{id}', [BlogController::class, 'update']);
    Route::delete('/admin/blog/{id}', [BlogController::class, 'destroy']);
    Route::get('/admin/faq-stats', [FaqController::class, 'adminStats']);
    Route::get('/admin/events', [EventController::class, 'adminIndex']);
    Route::get('/admin/events/{id}', [EventController::class, 'show']);
    Route::post('/admin/events', [EventController::class, 'store']);
    Route::put('/admin/events/{id}', [EventController::class, 'update']);
    Route::delete('/admin/events/{id}', [EventController::class, 'destroy']);

    // Admin Coupons (public for now - add auth later)
    Route::get('/admin/coupons', [CouponController::class, 'adminIndex']);
    Route::post('/admin/coupons', [CouponController::class, 'store']);
    Route::put('/admin/coupons/{id}', [CouponController::class, 'update']);
    Route::delete('/admin/coupons/{id}', [CouponController::class, 'destroy']);

    // Admin Orders (public for now - add auth later)
    Route::get('/admin/orders', [AdminOrderController::class, 'index']);
    Route::get('/admin/orders/stats', [AdminOrderController::class, 'stats']);
    Route::get('/admin/orders/{id}', [AdminOrderController::class, 'show']);
    Route::put('/admin/orders/{id}/status', [AdminOrderController::class, 'updateStatus']);
    Route::post('/admin/orders/{id}/notes', [AdminOrderController::class, 'addNote']);
    Route::post('/admin/orders/{id}/refund', [AdminOrderController::class, 'refund']);

    // Public order cancel (for frontend without auth)
    Route::post('/orders/{id}/cancel', [AdminOrderController::class, 'cancel']);
    Route::options('/orders/{id}/cancel', function () {
        return response('', 200)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'POST, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type, Accept');
    });

    // Admin Customers (public for now - add auth later)
    Route::get('/admin/customers', [AdminCustomerController::class, 'index']);
    Route::get('/admin/customers/stats', [AdminCustomerController::class, 'stats']);
    Route::get('/admin/customers/{id}', [AdminCustomerController::class, 'show']);
    Route::get('/admin/customers/{id}/orders', [AdminCustomerController::class, 'orders']);
    Route::put('/admin/customers/{id}', [AdminCustomerController::class, 'update']);

    // Admin Users (public for now - add auth later)
    Route::get('/admin/users', [AdminUserController::class, 'index']);
    Route::get('/admin/users/stats', [AdminUserController::class, 'stats']);
    Route::get('/admin/users/{id}', [AdminUserController::class, 'show']);

    // Admin Loyalty (public for now - add auth later)
    Route::get('/admin/loyalty/stats', [AdminLoyaltyController::class, 'stats']);
    Route::get('/admin/loyalty/members', [AdminLoyaltyController::class, 'members']);
    Route::get('/admin/loyalty/members/{userId}/transactions', [AdminLoyaltyController::class, 'memberTransactions']);
    Route::post('/admin/loyalty/adjust-points', [AdminLoyaltyController::class, 'adjustPoints']);
    Route::get('/admin/loyalty/tiers', [AdminLoyaltyController::class, 'tiers']);
    Route::put('/admin/loyalty/tiers/{id}', [AdminLoyaltyController::class, 'updateTier']);
    Route::get('/admin/loyalty/rewards', [AdminLoyaltyController::class, 'rewards']);
    Route::post('/admin/users', [AdminUserController::class, 'store']);
    Route::put('/admin/users/{id}', [AdminUserController::class, 'update']);
    Route::delete('/admin/users/{id}', [AdminUserController::class, 'destroy']);

    // Admin Categories (public for now - add auth later)
    Route::get('/admin/categories', [CategoryController::class, 'index']);
    Route::post('/admin/categories', [CategoryController::class, 'store']);
    Route::put('/admin/categories/{categoryCode}', [CategoryController::class, 'update']);
    Route::delete('/admin/categories/{categoryCode}', [CategoryController::class, 'destroy']);
    Route::put('/admin/categories/reorder', [CategoryController::class, 'reorder']);

    // Admin Gift Cards (public for now - add auth later)
    Route::get('/admin/gift-cards', [GiftCardController::class, 'index']);
    Route::get('/admin/gift-cards/stats', [GiftCardController::class, 'stats']);
    Route::get('/admin/gift-cards/export', [GiftCardController::class, 'export']);
    Route::get('/admin/gift-cards/balance/{code}', [GiftCardController::class, 'checkBalance']);
    Route::get('/admin/gift-cards/{id}', [GiftCardController::class, 'show']);
    Route::post('/admin/gift-cards', [GiftCardController::class, 'store']);
    Route::put('/admin/gift-cards/{id}/void', [GiftCardController::class, 'void']);
    Route::post('/admin/gift-cards/{id}/adjust', [GiftCardController::class, 'adjustBalance']);

    // Admin Export (public for now - add auth later)
    Route::get('/admin/export/orders', [ExportController::class, 'orders']);
    Route::get('/admin/export/customers', [ExportController::class, 'customers']);
    Route::get('/admin/export/products', [ExportController::class, 'products']);

    // Admin Tax Settings (public for now - add auth later)
    Route::get('/admin/tax/rates', [TaxController::class, 'index']);
    Route::get('/admin/tax/settings', [TaxController::class, 'settings']);
    Route::post('/admin/tax/settings', [TaxController::class, 'updateSettings']);
    Route::post('/admin/tax/rates', [TaxController::class, 'store']);
    Route::put('/admin/tax/rates/{id}', [TaxController::class, 'update']);
    Route::delete('/admin/tax/rates/{id}', [TaxController::class, 'destroy']);
    Route::get('/admin/tax/classes', [TaxController::class, 'classes']);
    Route::post('/admin/tax/classes', [TaxController::class, 'storeClass']);
    Route::delete('/admin/tax/classes/{id}', [TaxController::class, 'destroyClass']);
    Route::get('/admin/tax/report', [TaxController::class, 'report']);
    Route::get('/admin/tax/exemptions', [TaxController::class, 'exemptions']);
    Route::get('/admin/tax/customers-for-exemption', [TaxController::class, 'customersForExemption']);
    Route::post('/admin/tax/exemptions', [TaxController::class, 'storeExemption']);
    Route::put('/admin/tax/exemptions/{id}/revoke', [TaxController::class, 'revokeExemption']);

    // Public Tax Calculation (for frontend checkout)
    Route::post('/tax/calculate', [TaxController::class, 'calculate']);

    // =====================
    // PAYMENT GATEWAY ROUTES
    // =====================

    // Payment Gateways (public - for checkout)
    Route::get('/payments/gateways', [PaymentController::class, 'getGateways']);
    Route::post('/payments/create', [PaymentController::class, 'createPayment']);
    Route::get('/payments/{paymentId}', [PaymentController::class, 'getPayment']);
    Route::post('/payments/{paymentId}/confirm', [PaymentController::class, 'confirmPayment']);
    Route::post('/payments/{paymentId}/cancel', [PaymentController::class, 'cancelPayment']);
    Route::post('/payments/{paymentId}/refund', [PaymentController::class, 'refundPayment']);

    // Payment Webhooks (no auth - verified by signature)
    Route::post('/webhooks/payment/{gateway}', [PaymentController::class, 'webhook']);

    // Admin Shipping Settings
    Route::get('/admin/shipping/zones', [ShippingController::class, 'zones']);
    Route::post('/admin/shipping/zones', [ShippingController::class, 'storeZone']);
    Route::put('/admin/shipping/zones/{id}', [ShippingController::class, 'updateZone']);
    Route::delete('/admin/shipping/zones/{id}', [ShippingController::class, 'destroyZone']);
    Route::post('/admin/shipping/methods', [ShippingController::class, 'storeMethod']);
    Route::put('/admin/shipping/methods/{id}', [ShippingController::class, 'updateMethod']);
    Route::delete('/admin/shipping/methods/{id}', [ShippingController::class, 'destroyMethod']);
    Route::get('/admin/shipping/classes', [ShippingController::class, 'classes']);
    Route::post('/admin/shipping/classes', [ShippingController::class, 'storeClass']);
    Route::put('/admin/shipping/classes/{id}', [ShippingController::class, 'updateClass']);
    Route::delete('/admin/shipping/classes/{id}', [ShippingController::class, 'destroyClass']);
    Route::get('/admin/shipping/settings', [ShippingController::class, 'settings']);
    Route::post('/admin/shipping/settings', [ShippingController::class, 'updateSettings']);

    // Shipping Carriers
    Route::get('/admin/shipping/carriers', [ShippingController::class, 'carriers']);
    Route::get('/admin/shipping/carriers/{id}', [ShippingController::class, 'getCarrier']);
    Route::put('/admin/shipping/carriers/{id}', [ShippingController::class, 'updateCarrier']);
    Route::post('/admin/shipping/carriers/{id}/connect', [ShippingController::class, 'connectCarrier']);
    Route::post('/admin/shipping/carriers/{id}/disconnect', [ShippingController::class, 'disconnectCarrier']);

    // Admin Dropshippers
    Route::get('/admin/dropshippers', [DropshipperController::class, 'index']);
    Route::get('/admin/dropshippers/{id}', [DropshipperController::class, 'show']);
    Route::post('/admin/dropshippers', [DropshipperController::class, 'store']);
    Route::put('/admin/dropshippers/{id}', [DropshipperController::class, 'update']);
    Route::delete('/admin/dropshippers/{id}', [DropshipperController::class, 'destroy']);
    Route::post('/admin/dropshippers/{id}/approve', [DropshipperController::class, 'approve']);
    Route::post('/admin/dropshippers/{id}/toggle-suspend', [DropshipperController::class, 'toggleSuspend']);
    Route::post('/admin/dropshippers/{id}/regenerate-key', [DropshipperController::class, 'regenerateKey']);

    // Dropship Orders
    Route::get('/admin/dropship/orders', [DropshipperController::class, 'orders']);
    Route::get('/admin/dropship/orders/{id}', [DropshipperController::class, 'showOrder']);
    Route::put('/admin/dropship/orders/{id}/status', [DropshipperController::class, 'updateOrderStatus']);

    // Admin Suppliers
    Route::get('/admin/suppliers/stats', [SupplierController::class, 'stats']);
    Route::get('/admin/suppliers', [SupplierController::class, 'index']);
    Route::get('/admin/suppliers/{id}', [SupplierController::class, 'show']);
    Route::post('/admin/suppliers', [SupplierController::class, 'store']);
    Route::put('/admin/suppliers/{id}', [SupplierController::class, 'update']);
    Route::put('/admin/suppliers/{id}/status', [SupplierController::class, 'updateStatus']);
    Route::delete('/admin/suppliers/{id}', [SupplierController::class, 'destroy']);

    // API Logs (specific routes must come before {id} route)
    Route::get('/admin/api-logs/stats', [ApiLogController::class, 'stats']);
    Route::get('/admin/api-logs/dropshippers', [ApiLogController::class, 'dropshippers']);
    Route::get('/admin/api-logs/endpoints', [ApiLogController::class, 'endpoints']);
    Route::delete('/admin/api-logs/clear', [ApiLogController::class, 'clearAll']);
    Route::get('/admin/api-logs/{id}', [ApiLogController::class, 'show']);
    Route::get('/admin/api-logs', [ApiLogController::class, 'index']);

    // Admin Settings
    Route::get('/admin/settings', [SettingsController::class, 'index']);
    Route::get('/admin/settings/{group}', [SettingsController::class, 'getGroup']);
    Route::put('/admin/settings/{group}', [SettingsController::class, 'updateGroup']);

    // Footer Configuration (Admin)
    Route::get('/admin/footer', [FooterController::class, 'index']);
    Route::put('/admin/footer/columns/{id}', [FooterController::class, 'updateColumn']);
    Route::put('/admin/footer/links/{id}', [FooterController::class, 'updateLink']);
    Route::put('/admin/footer/links-order', [FooterController::class, 'updateLinkOrder']);
    Route::post('/admin/footer/links', [FooterController::class, 'addLink']);
    Route::delete('/admin/footer/links/{id}', [FooterController::class, 'deleteLink']);

    // Footer Configuration (Public - for frontend)
    Route::get('/footer', [FooterController::class, 'getPublic']);

    // Featured Categories (Admin)
    Route::get('/admin/featured-categories', [FeaturedCategoriesController::class, 'index']);
    Route::get('/admin/featured-categories/categories', [FeaturedCategoriesController::class, 'getCategories']);
    Route::post('/admin/featured-categories', [FeaturedCategoriesController::class, 'store']);
    Route::put('/admin/featured-categories/reorder', [FeaturedCategoriesController::class, 'reorder']);
    Route::put('/admin/featured-categories/visibility', [FeaturedCategoriesController::class, 'toggleVisibility']);
    Route::put('/admin/featured-categories/{id}', [FeaturedCategoriesController::class, 'update']);
    Route::delete('/admin/featured-categories/{id}', [FeaturedCategoriesController::class, 'destroy']);
    Route::post('/admin/featured-categories/upload-image/{categoryId}', [FeaturedCategoriesController::class, 'uploadImage']);

    // Featured Categories (Public - for frontend)
    Route::get('/featured-categories', [FeaturedCategoriesController::class, 'getPublic']);

    // Featured Products (Admin)
    Route::get('/admin/featured-products', [FeaturedProductsController::class, 'index']);
    Route::get('/admin/featured-products/products', [FeaturedProductsController::class, 'getProducts']);
    Route::post('/admin/featured-products', [FeaturedProductsController::class, 'store']);
    Route::put('/admin/featured-products/reorder', [FeaturedProductsController::class, 'reorder']);
    Route::put('/admin/featured-products/visibility', [FeaturedProductsController::class, 'toggleVisibility']);
    Route::put('/admin/featured-products/title', [FeaturedProductsController::class, 'updateTitle']);
    Route::put('/admin/featured-products/{id}', [FeaturedProductsController::class, 'update']);
    Route::delete('/admin/featured-products/{id}', [FeaturedProductsController::class, 'destroy']);
    Route::post('/admin/featured-products/upload-image/{upc}', [FeaturedProductsController::class, 'uploadImage']);

    // Featured Products (Public - for frontend)
    Route::get('/featured-products', [FeaturedProductsController::class, 'getPublic']);

    // Specialty Products (Admin)
    Route::get('/admin/specialty-categories', [SpecialtyController::class, 'index']);
    Route::get('/admin/specialty-categories/{id}', [SpecialtyController::class, 'show']);
    Route::post('/admin/specialty-categories', [SpecialtyController::class, 'store']);
    Route::put('/admin/specialty-categories/reorder', [SpecialtyController::class, 'reorderCategories']);
    Route::put('/admin/specialty-categories/{id}', [SpecialtyController::class, 'update']);
    Route::delete('/admin/specialty-categories/{id}', [SpecialtyController::class, 'destroy']);
    Route::post('/admin/specialty-categories/{id}/image', [SpecialtyController::class, 'uploadCategoryImage']);
    Route::get('/admin/specialty-categories/{id}/products', [SpecialtyController::class, 'getProducts']);
    Route::post('/admin/specialty-categories/{id}/products', [SpecialtyController::class, 'addProduct']);
    Route::put('/admin/specialty-products/reorder', [SpecialtyController::class, 'reorderProducts']);
    Route::put('/admin/specialty-products/{id}', [SpecialtyController::class, 'updateProduct']);
    Route::delete('/admin/specialty-products/{id}', [SpecialtyController::class, 'destroyProduct']);
    Route::get('/admin/specialty-products/search', [SpecialtyController::class, 'searchProducts']);

    // Specialty Products (Public - for frontend)
    Route::get('/specialty-categories', [SpecialtyController::class, 'getPublicCategories']);
    Route::get('/specialty-categories/{id}', [SpecialtyController::class, 'getPublicCategory']);

    // Homepage Sections (Admin)
    Route::get('/admin/sections', [SectionController::class, 'index']);
    Route::get('/admin/sections/{id}', [SectionController::class, 'show']);
    Route::post('/admin/sections', [SectionController::class, 'store']);
    Route::put('/admin/sections/reorder', [SectionController::class, 'reorder']);
    Route::put('/admin/sections/{id}', [SectionController::class, 'update']);
    Route::put('/admin/sections/{id}/toggle', [SectionController::class, 'toggleVisibility']);
    Route::delete('/admin/sections/{id}', [SectionController::class, 'destroy']);

    // Homepage Sections (Public - for frontend)
    Route::get('/sections', [SectionController::class, 'getPublicSections']);

    // Purchase Orders
    Route::get('/admin/purchase-orders', [PurchaseOrderController::class, 'index']);
    Route::get('/admin/purchase-orders/stats', [PurchaseOrderController::class, 'stats']);
    Route::get('/admin/purchase-orders/pending-receiving', [PurchaseOrderController::class, 'pendingForReceiving']);
    Route::get('/admin/purchase-orders/suppliers', [PurchaseOrderController::class, 'suppliers']);
    Route::get('/admin/purchase-orders/{id}', [PurchaseOrderController::class, 'show']);
    Route::post('/admin/purchase-orders', [PurchaseOrderController::class, 'store']);
    Route::put('/admin/purchase-orders/{id}', [PurchaseOrderController::class, 'update']);
    Route::put('/admin/purchase-orders/{id}/status', [PurchaseOrderController::class, 'updateStatus']);
    Route::post('/admin/purchase-orders/{id}/receive', [PurchaseOrderController::class, 'receive']);
    Route::delete('/admin/purchase-orders/{id}', [PurchaseOrderController::class, 'destroy']);

    // =====================
    // CRM ROUTES
    // =====================

    // Customer Tags
    Route::get('/admin/crm/tags', [CrmController::class, 'tags']);
    Route::post('/admin/crm/tags', [CrmController::class, 'createTag']);
    Route::put('/admin/crm/tags/{id}', [CrmController::class, 'updateTag']);
    Route::delete('/admin/crm/tags/{id}', [CrmController::class, 'deleteTag']);
    Route::get('/admin/crm/customers/{customerId}/tags', [CrmController::class, 'customerTags']);
    Route::post('/admin/crm/tags/assign', [CrmController::class, 'assignTag']);
    Route::post('/admin/crm/tags/remove', [CrmController::class, 'removeTag']);

    // Customer Activities/Timeline
    Route::get('/admin/crm/customers/{customerId}/activities', [CrmController::class, 'activities']);
    Route::post('/admin/crm/activities', [CrmController::class, 'createActivity']);

    // Customer Notes
    Route::get('/admin/crm/customers/{customerId}/notes', [CrmController::class, 'notes']);
    Route::post('/admin/crm/notes', [CrmController::class, 'createNote']);
    Route::put('/admin/crm/notes/{id}', [CrmController::class, 'updateNote']);
    Route::delete('/admin/crm/notes/{id}', [CrmController::class, 'deleteNote']);

    // Customer Communications
    Route::get('/admin/crm/customers/{customerId}/communications', [CrmController::class, 'communications']);
    Route::post('/admin/crm/communications', [CrmController::class, 'logCommunication']);

    // Customer Segments
    Route::get('/admin/crm/segments', [CrmController::class, 'segments']);
    Route::get('/admin/crm/segments/{id}', [CrmController::class, 'segmentDetails']);
    Route::post('/admin/crm/segments', [CrmController::class, 'createSegment']);
    Route::put('/admin/crm/segments/{id}', [CrmController::class, 'updateSegment']);
    Route::delete('/admin/crm/segments/{id}', [CrmController::class, 'deleteSegment']);
    Route::get('/admin/crm/segments/{id}/members', [CrmController::class, 'segmentMembers']);
    Route::get('/admin/crm/segments/{id}/export', [CrmController::class, 'exportSegment']);
    Route::post('/admin/crm/segments/{id}/recalculate', [CrmController::class, 'recalculateSegment']);
    Route::post('/admin/crm/segments/init-presets', [CrmController::class, 'initPresetSegments']);

    // Customer Metrics
    Route::get('/admin/crm/customers/{customerId}/metrics', [CrmController::class, 'customerMetrics']);
    Route::post('/admin/crm/metrics/recalculate-all', [CrmController::class, 'recalculateAllMetrics']);

    // Customer 360 View
    Route::get('/admin/crm/customers/{customerId}/360', [CrmController::class, 'customer360']);

    // Email Templates
    Route::get('/admin/crm/email-templates', [CrmController::class, 'emailTemplates']);
    Route::get('/admin/crm/email-templates/{id}', [CrmController::class, 'emailTemplate']);
    Route::post('/admin/crm/email-templates', [CrmController::class, 'createEmailTemplate']);
    Route::put('/admin/crm/email-templates/{id}', [CrmController::class, 'updateEmailTemplate']);
    Route::delete('/admin/crm/email-templates/{id}', [CrmController::class, 'deleteEmailTemplate']);

    // =====================
    // SUPPORT TICKETS
    // =====================
    Route::get('/admin/support/tickets', [SupportController::class, 'tickets']);
    Route::get('/admin/support/tickets/stats', [SupportController::class, 'ticketStats']);
    Route::get('/admin/support/tickets/{id}', [SupportController::class, 'show']);
    Route::post('/admin/support/tickets', [SupportController::class, 'store']);
    Route::put('/admin/support/tickets/{id}', [SupportController::class, 'update']);
    Route::post('/admin/support/tickets/{id}/messages', [SupportController::class, 'addMessage']);
    Route::post('/admin/support/tickets/{id}/rating', [SupportController::class, 'addRating']);

    // Canned Responses
    Route::get('/admin/support/canned-responses', [SupportController::class, 'cannedResponses']);
    Route::post('/admin/support/canned-responses', [SupportController::class, 'storeCannedResponse']);
    Route::put('/admin/support/canned-responses/{id}', [SupportController::class, 'updateCannedResponse']);
    Route::delete('/admin/support/canned-responses/{id}', [SupportController::class, 'deleteCannedResponse']);

    // =====================
    // RETURNS / RMA
    // =====================
    Route::get('/admin/returns', [ReturnsController::class, 'index']);
    Route::get('/admin/returns/stats', [ReturnsController::class, 'stats']);
    Route::get('/admin/returns/reasons', [ReturnsController::class, 'reasons']);
    Route::get('/admin/returns/{id}', [ReturnsController::class, 'show']);
    Route::post('/admin/returns', [ReturnsController::class, 'store']);
    Route::put('/admin/returns/{id}/status', [ReturnsController::class, 'updateStatus']);
    Route::post('/admin/returns/{id}/notes', [ReturnsController::class, 'addNote']);
    Route::put('/admin/returns/{id}/refund', [ReturnsController::class, 'updateRefund']);
    Route::put('/admin/returns/{id}/tracking', [ReturnsController::class, 'addTracking']);
    Route::delete('/admin/returns/{id}', [ReturnsController::class, 'destroy']);

    // Return Reasons (Admin)
    Route::post('/admin/returns/reasons', [ReturnsController::class, 'storeReason']);
    Route::put('/admin/returns/reasons/{id}', [ReturnsController::class, 'updateReason']);
    Route::delete('/admin/returns/reasons/{id}', [ReturnsController::class, 'destroyReason']);

    // =====================
    // MULTI-CURRENCY
    // =====================
    Route::get('/currencies', [CurrencyController::class, 'active']);
    Route::get('/currencies/convert', [CurrencyController::class, 'convert']);

    // Admin Currency Management
    Route::get('/admin/currencies', [CurrencyController::class, 'index']);
    Route::get('/admin/currencies/{code}', [CurrencyController::class, 'show']);
    Route::post('/admin/currencies', [CurrencyController::class, 'store']);
    Route::put('/admin/currencies/{id}', [CurrencyController::class, 'update']);
    Route::delete('/admin/currencies/{id}', [CurrencyController::class, 'destroy']);
    Route::post('/admin/currencies/{id}/default', [CurrencyController::class, 'setDefault']);
    Route::put('/admin/currencies/{id}/rate', [CurrencyController::class, 'updateRate']);
    Route::get('/admin/currencies/{id}/rate-history', [CurrencyController::class, 'rateHistory']);
    Route::post('/admin/currencies/fetch-rates', [CurrencyController::class, 'fetchRates']);

    // =====================
    // MULTI-LANGUAGE
    // =====================
    // Public Language API
    Route::get('/languages', [LanguageController::class, 'active']);
    Route::get('/languages/{locale}/translations', [LanguageController::class, 'getLocaleTranslations']);

    // Admin Language Management
    Route::get('/admin/languages', [LanguageController::class, 'index']);
    Route::get('/admin/languages/stats', [LanguageController::class, 'stats']);
    Route::get('/admin/languages/{code}', [LanguageController::class, 'show']);
    Route::post('/admin/languages', [LanguageController::class, 'store']);
    Route::put('/admin/languages/{id}', [LanguageController::class, 'update']);
    Route::delete('/admin/languages/{id}', [LanguageController::class, 'destroy']);
    Route::post('/admin/languages/{id}/default', [LanguageController::class, 'setDefault']);

    // Translation Management
    Route::get('/admin/translations/groups', [LanguageController::class, 'groups']);
    Route::get('/admin/translations/groups/{group}/keys', [LanguageController::class, 'keys']);
    Route::get('/admin/translations/{languageId}', [LanguageController::class, 'translations']);
    Route::post('/admin/translations/keys', [LanguageController::class, 'storeKey']);
    Route::delete('/admin/translations/keys/{id}', [LanguageController::class, 'destroyKey']);
    Route::post('/admin/translations', [LanguageController::class, 'saveTranslation']);
    Route::post('/admin/translations/bulk', [LanguageController::class, 'bulkSaveTranslations']);
    Route::put('/admin/translations/{id}/reviewed', [LanguageController::class, 'markReviewed']);

    // Product/Category Translations
    Route::get('/admin/products/{productId}/translations', [LanguageController::class, 'productTranslations']);
    Route::post('/admin/products/{productId}/translations', [LanguageController::class, 'saveProductTranslation']);
    Route::get('/admin/categories/{categoryId}/translations', [LanguageController::class, 'categoryTranslations']);
    Route::post('/admin/categories/{categoryId}/translations', [LanguageController::class, 'saveCategoryTranslation']);

    // =====================
    // EMAIL MARKETING
    // =====================
    Route::get('/admin/email-marketing/stats', [EmailMarketingController::class, 'stats']);

    // Email Lists
    Route::get('/admin/email-lists', [EmailMarketingController::class, 'lists']);
    Route::get('/admin/email-lists/{id}', [EmailMarketingController::class, 'showList']);
    Route::post('/admin/email-lists', [EmailMarketingController::class, 'storeList']);
    Route::put('/admin/email-lists/{id}', [EmailMarketingController::class, 'updateList']);
    Route::delete('/admin/email-lists/{id}', [EmailMarketingController::class, 'destroyList']);

    // Subscribers
    Route::get('/admin/email-lists/{listId}/subscribers', [EmailMarketingController::class, 'subscribers']);
    Route::post('/admin/email-lists/{listId}/subscribers', [EmailMarketingController::class, 'storeSubscriber']);
    Route::post('/admin/email-lists/{listId}/subscribers/import', [EmailMarketingController::class, 'importSubscribers']);
    Route::put('/admin/subscribers/{id}/unsubscribe', [EmailMarketingController::class, 'unsubscribe']);
    Route::delete('/admin/subscribers/{id}', [EmailMarketingController::class, 'destroySubscriber']);

    // Campaigns
    Route::get('/admin/campaigns', [EmailMarketingController::class, 'campaigns']);
    Route::get('/admin/campaigns/{id}', [EmailMarketingController::class, 'showCampaign']);
    Route::post('/admin/campaigns', [EmailMarketingController::class, 'storeCampaign']);
    Route::put('/admin/campaigns/{id}', [EmailMarketingController::class, 'updateCampaign']);
    Route::delete('/admin/campaigns/{id}', [EmailMarketingController::class, 'destroyCampaign']);
    Route::post('/admin/campaigns/{id}/schedule', [EmailMarketingController::class, 'scheduleCampaign']);
    Route::post('/admin/campaigns/{id}/send', [EmailMarketingController::class, 'sendCampaign']);
    Route::post('/admin/campaigns/{id}/pause', [EmailMarketingController::class, 'pauseCampaign']);
    Route::post('/admin/campaigns/{id}/cancel', [EmailMarketingController::class, 'cancelCampaign']);
    Route::post('/admin/campaigns/{id}/duplicate', [EmailMarketingController::class, 'duplicateCampaign']);

    // Automations
    Route::get('/admin/automations', [EmailMarketingController::class, 'automations']);
    Route::get('/admin/automations/{id}', [EmailMarketingController::class, 'showAutomation']);
    Route::post('/admin/automations', [EmailMarketingController::class, 'storeAutomation']);
    Route::put('/admin/automations/{id}', [EmailMarketingController::class, 'updateAutomation']);
    Route::delete('/admin/automations/{id}', [EmailMarketingController::class, 'destroyAutomation']);
    Route::post('/admin/automations/{id}/toggle', [EmailMarketingController::class, 'toggleAutomation']);

    // =====================
    // SMS/PUSH NOTIFICATIONS
    // =====================

    // Stats
    Route::get('/admin/notifications/stats', [NotificationsController::class, 'stats']);
    Route::get('/admin/notifications/trigger-events', [NotificationsController::class, 'getTriggerEvents']);
    Route::get('/admin/notifications/providers', [NotificationsController::class, 'getProviders']);

    // SMS Templates
    Route::get('/admin/sms-templates', [NotificationsController::class, 'smsTemplates']);
    Route::get('/admin/sms-templates/{id}', [NotificationsController::class, 'showSmsTemplate']);
    Route::post('/admin/sms-templates', [NotificationsController::class, 'storeSmsTemplate']);
    Route::put('/admin/sms-templates/{id}', [NotificationsController::class, 'updateSmsTemplate']);
    Route::delete('/admin/sms-templates/{id}', [NotificationsController::class, 'deleteSmsTemplate']);

    // Push Templates
    Route::get('/admin/push-templates', [NotificationsController::class, 'pushTemplates']);
    Route::get('/admin/push-templates/{id}', [NotificationsController::class, 'showPushTemplate']);
    Route::post('/admin/push-templates', [NotificationsController::class, 'storePushTemplate']);
    Route::put('/admin/push-templates/{id}', [NotificationsController::class, 'updatePushTemplate']);
    Route::delete('/admin/push-templates/{id}', [NotificationsController::class, 'deletePushTemplate']);

    // Notification Channels
    Route::get('/admin/notification-channels', [NotificationsController::class, 'channels']);
    Route::get('/admin/notification-channels/{id}', [NotificationsController::class, 'showChannel']);
    Route::post('/admin/notification-channels', [NotificationsController::class, 'storeChannel']);
    Route::put('/admin/notification-channels/{id}', [NotificationsController::class, 'updateChannel']);
    Route::delete('/admin/notification-channels/{id}', [NotificationsController::class, 'deleteChannel']);
    Route::post('/admin/notification-channels/{id}/test', [NotificationsController::class, 'testChannel']);

    // Notification Campaigns
    Route::get('/admin/notification-campaigns', [NotificationsController::class, 'campaigns']);
    Route::get('/admin/notification-campaigns/{id}', [NotificationsController::class, 'showCampaign']);
    Route::post('/admin/notification-campaigns', [NotificationsController::class, 'storeCampaign']);
    Route::put('/admin/notification-campaigns/{id}', [NotificationsController::class, 'updateCampaign']);
    Route::delete('/admin/notification-campaigns/{id}', [NotificationsController::class, 'deleteCampaign']);
    Route::post('/admin/notification-campaigns/{id}/schedule', [NotificationsController::class, 'scheduleCampaign']);
    Route::post('/admin/notification-campaigns/{id}/send', [NotificationsController::class, 'sendCampaign']);
    Route::post('/admin/notification-campaigns/{id}/pause', [NotificationsController::class, 'pauseCampaign']);
    Route::post('/admin/notification-campaigns/{id}/cancel', [NotificationsController::class, 'cancelCampaign']);

    // Notification Automations
    Route::get('/admin/notification-automations', [NotificationsController::class, 'automations']);
    Route::get('/admin/notification-automations/{id}', [NotificationsController::class, 'showAutomation']);
    Route::post('/admin/notification-automations', [NotificationsController::class, 'storeAutomation']);
    Route::put('/admin/notification-automations/{id}', [NotificationsController::class, 'updateAutomation']);
    Route::delete('/admin/notification-automations/{id}', [NotificationsController::class, 'deleteAutomation']);
    Route::post('/admin/notification-automations/{id}/toggle', [NotificationsController::class, 'toggleAutomation']);

    // SMS Messages Log
    Route::get('/admin/sms-messages', [NotificationsController::class, 'smsMessages']);
    Route::get('/admin/sms-messages/{id}', [NotificationsController::class, 'showSmsMessage']);

    // Push Notifications Log
    Route::get('/admin/push-notifications', [NotificationsController::class, 'pushNotifications']);
    Route::get('/admin/push-notifications/{id}', [NotificationsController::class, 'showPushNotification']);

    // Device Tokens
    Route::get('/admin/device-tokens', [NotificationsController::class, 'deviceTokens']);
    Route::post('/admin/device-tokens/{id}/deactivate', [NotificationsController::class, 'deactivateToken']);

    // =====================
    // ADVANCED SEARCH
    // =====================

    // Stats & Types
    Route::get('/admin/search/stats', [SearchController::class, 'stats']);
    Route::get('/admin/search/facet-types', [SearchController::class, 'getFacetTypes']);

    // Facets
    Route::get('/admin/search/facets', [SearchController::class, 'facets']);
    Route::get('/admin/search/facets/{id}', [SearchController::class, 'showFacet']);
    Route::post('/admin/search/facets', [SearchController::class, 'storeFacet']);
    Route::put('/admin/search/facets/{id}', [SearchController::class, 'updateFacet']);
    Route::delete('/admin/search/facets/{id}', [SearchController::class, 'deleteFacet']);
    Route::post('/admin/search/facets/reorder', [SearchController::class, 'reorderFacets']);

    // Synonyms
    Route::get('/admin/search/synonyms', [SearchController::class, 'synonyms']);
    Route::post('/admin/search/synonyms', [SearchController::class, 'storeSynonym']);
    Route::put('/admin/search/synonyms/{id}', [SearchController::class, 'updateSynonym']);
    Route::delete('/admin/search/synonyms/{id}', [SearchController::class, 'deleteSynonym']);

    // Redirects
    Route::get('/admin/search/redirects', [SearchController::class, 'redirects']);
    Route::post('/admin/search/redirects', [SearchController::class, 'storeRedirect']);
    Route::put('/admin/search/redirects/{id}', [SearchController::class, 'updateRedirect']);
    Route::delete('/admin/search/redirects/{id}', [SearchController::class, 'deleteRedirect']);

    // Boosts
    Route::get('/admin/search/boosts', [SearchController::class, 'boosts']);
    Route::post('/admin/search/boosts', [SearchController::class, 'storeBoost']);
    Route::put('/admin/search/boosts/{id}', [SearchController::class, 'updateBoost']);
    Route::delete('/admin/search/boosts/{id}', [SearchController::class, 'deleteBoost']);

    // Search Analytics
    Route::get('/admin/search/queries', [SearchController::class, 'searchQueries']);
    Route::get('/admin/search/queries/zero-results', [SearchController::class, 'zeroResultQueries']);
    Route::get('/admin/search/popular', [SearchController::class, 'popularSearches']);
    Route::post('/admin/search/popular/{id}/toggle-featured', [SearchController::class, 'toggleFeatured']);

    // =====================
    // PRODUCT VARIANTS
    // =====================

    // Variant Stats
    Route::get('/admin/variants/stats', [VariantsController::class, 'stats']);
    Route::get('/admin/variants/display-types', [VariantsController::class, 'displayTypes']);
    Route::get('/admin/variants/price-rule-types', [VariantsController::class, 'priceRuleTypes']);

    // Attribute Types
    Route::get('/admin/attribute-types', [VariantsController::class, 'attributeTypes']);
    Route::get('/admin/attribute-types/{id}', [VariantsController::class, 'attributeType']);
    Route::post('/admin/attribute-types', [VariantsController::class, 'storeAttributeType']);
    Route::put('/admin/attribute-types/{id}', [VariantsController::class, 'updateAttributeType']);
    Route::delete('/admin/attribute-types/{id}', [VariantsController::class, 'deleteAttributeType']);

    // Attribute Values
    Route::post('/admin/attribute-types/{typeId}/values', [VariantsController::class, 'storeAttributeValue']);
    Route::put('/admin/attribute-values/{id}', [VariantsController::class, 'updateAttributeValue']);
    Route::delete('/admin/attribute-values/{id}', [VariantsController::class, 'deleteAttributeValue']);

    // Product Variants
    Route::get('/admin/products/{productId}/variants', [VariantsController::class, 'productVariants']);
    Route::post('/admin/products/{productId}/variants', [VariantsController::class, 'storeVariant']);
    Route::post('/admin/products/{productId}/variants/matrix', [VariantsController::class, 'generateVariantMatrix']);
    Route::post('/admin/products/{productId}/variants/bulk', [VariantsController::class, 'bulkCreateVariants']);
    Route::get('/admin/variants/{id}', [VariantsController::class, 'variant']);
    Route::put('/admin/variants/{id}', [VariantsController::class, 'updateVariant']);
    Route::delete('/admin/variants/{id}', [VariantsController::class, 'deleteVariant']);

    // Variant Images
    Route::get('/admin/variants/{variantId}/images', [VariantsController::class, 'variantImages']);
    Route::post('/admin/variants/{variantId}/images', [VariantsController::class, 'storeVariantImage']);
    Route::delete('/admin/variant-images/{id}', [VariantsController::class, 'deleteVariantImage']);

    // Variant Price Rules
    Route::get('/admin/variants/{variantId}/price-rules', [VariantsController::class, 'priceRules']);
    Route::post('/admin/variants/{variantId}/price-rules', [VariantsController::class, 'storePriceRule']);
    Route::put('/admin/price-rules/{id}', [VariantsController::class, 'updatePriceRule']);
    Route::delete('/admin/price-rules/{id}', [VariantsController::class, 'deletePriceRule']);

    // Variant Inventory
    Route::post('/admin/variants/{variantId}/inventory/adjust', [VariantsController::class, 'adjustInventory']);
    Route::get('/admin/variants/{variantId}/inventory/logs', [VariantsController::class, 'inventoryLogs']);
    Route::post('/admin/variants/inventory/bulk-update', [VariantsController::class, 'bulkUpdateInventory']);

    // =====================
    // LIVE CHAT
    // =====================

    // Chat Stats
    Route::get('/admin/chat/stats', [ChatController::class, 'stats']);
    Route::get('/admin/chat/agent-statuses', [ChatController::class, 'agentStatuses']);
    Route::get('/admin/chat/session-statuses', [ChatController::class, 'sessionStatuses']);
    Route::get('/admin/chat/trigger-types', [ChatController::class, 'triggerTypes']);

    // Chat Agents
    Route::get('/admin/chat/agents', [ChatController::class, 'agents']);
    Route::get('/admin/chat/agents/{id}', [ChatController::class, 'agent']);
    Route::post('/admin/chat/agents', [ChatController::class, 'storeAgent']);
    Route::put('/admin/chat/agents/{id}', [ChatController::class, 'updateAgent']);
    Route::delete('/admin/chat/agents/{id}', [ChatController::class, 'deleteAgent']);

    // Chat Sessions
    Route::get('/admin/chat/sessions', [ChatController::class, 'sessions']);
    Route::get('/admin/chat/sessions/{id}', [ChatController::class, 'session']);
    Route::post('/admin/chat/sessions/{id}/assign', [ChatController::class, 'assignSession']);
    Route::post('/admin/chat/sessions/{id}/close', [ChatController::class, 'closeSession']);

    // Chat Messages
    Route::get('/admin/chat/sessions/{sessionId}/messages', [ChatController::class, 'sessionMessages']);
    Route::post('/admin/chat/sessions/{sessionId}/messages', [ChatController::class, 'sendMessage']);

    // Chat Departments
    Route::get('/admin/chat/departments', [ChatController::class, 'departments']);
    Route::get('/admin/chat/departments/{id}', [ChatController::class, 'department']);
    Route::post('/admin/chat/departments', [ChatController::class, 'storeDepartment']);
    Route::put('/admin/chat/departments/{id}', [ChatController::class, 'updateDepartment']);
    Route::delete('/admin/chat/departments/{id}', [ChatController::class, 'deleteDepartment']);

    // Canned Responses
    Route::get('/admin/chat/canned-responses', [ChatController::class, 'cannedResponses']);
    Route::post('/admin/chat/canned-responses', [ChatController::class, 'storeCannedResponse']);
    Route::put('/admin/chat/canned-responses/{id}', [ChatController::class, 'updateCannedResponse']);
    Route::delete('/admin/chat/canned-responses/{id}', [ChatController::class, 'deleteCannedResponse']);

    // Chat Triggers
    Route::get('/admin/chat/triggers', [ChatController::class, 'triggers']);
    Route::post('/admin/chat/triggers', [ChatController::class, 'storeTrigger']);
    Route::put('/admin/chat/triggers/{id}', [ChatController::class, 'updateTrigger']);
    Route::delete('/admin/chat/triggers/{id}', [ChatController::class, 'deleteTrigger']);

    // Offline Messages
    Route::get('/admin/chat/offline-messages', [ChatController::class, 'offlineMessages']);
    Route::post('/admin/chat/offline-messages/{id}/reply', [ChatController::class, 'replyOfflineMessage']);
    Route::put('/admin/chat/offline-messages/{id}/status', [ChatController::class, 'updateOfflineMessageStatus']);

    // =====================
    // SALES PIPELINE
    // =====================

    // Leads
    Route::get('/admin/leads', [LeadsController::class, 'index']);
    Route::get('/admin/leads/stats', [LeadsController::class, 'stats']);
    Route::get('/admin/leads/sources', [LeadsController::class, 'sources']);
    Route::get('/admin/leads/{id}', [LeadsController::class, 'show']);
    Route::post('/admin/leads', [LeadsController::class, 'store']);
    Route::put('/admin/leads/{id}', [LeadsController::class, 'update']);
    Route::delete('/admin/leads/{id}', [LeadsController::class, 'destroy']);
    Route::post('/admin/leads/{id}/activities', [LeadsController::class, 'addActivity']);
    Route::post('/admin/leads/{id}/convert', [LeadsController::class, 'convertToDeal']);

    // Deals
    Route::get('/admin/deals', [DealsController::class, 'index']);
    Route::get('/admin/deals/pipeline', [DealsController::class, 'pipeline']);
    Route::get('/admin/deals/stats', [DealsController::class, 'stats']);
    Route::get('/admin/deals/stages', [DealsController::class, 'stages']);
    Route::get('/admin/deals/{id}', [DealsController::class, 'show']);
    Route::post('/admin/deals', [DealsController::class, 'store']);
    Route::put('/admin/deals/{id}', [DealsController::class, 'update']);
    Route::delete('/admin/deals/{id}', [DealsController::class, 'destroy']);
    Route::put('/admin/deals/{id}/stage', [DealsController::class, 'moveStage']);
    Route::post('/admin/deals/{id}/activities', [DealsController::class, 'addActivity']);

    // Wholesale Accounts
    Route::get('/admin/wholesale', [WholesaleController::class, 'index']);
    Route::get('/admin/wholesale/stats', [WholesaleController::class, 'stats']);
    Route::get('/admin/wholesale/tiers', [WholesaleController::class, 'tiers']);
    Route::get('/admin/wholesale/orders', [WholesaleController::class, 'orders']);
    Route::get('/admin/wholesale/{id}', [WholesaleController::class, 'show']);
    Route::post('/admin/wholesale', [WholesaleController::class, 'store']);
    Route::put('/admin/wholesale/{id}', [WholesaleController::class, 'update']);
    Route::delete('/admin/wholesale/{id}', [WholesaleController::class, 'destroy']);
    Route::post('/admin/wholesale/{id}/approve', [WholesaleController::class, 'approve']);
    Route::post('/admin/wholesale/{id}/suspend', [WholesaleController::class, 'suspend']);
    Route::post('/admin/wholesale/orders', [WholesaleController::class, 'createOrder']);
    Route::put('/admin/wholesale/orders/{id}', [WholesaleController::class, 'updateOrder']);

    // =====================
    // PROTECTED ROUTES (require authentication)
    // =====================
    Route::middleware('auth:sanctum')->group(function () {

        // Auth
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/user', [AuthController::class, 'user']);
        Route::put('/auth/profile', [AuthController::class, 'updateProfile']);
        Route::put('/auth/password', [AuthController::class, 'changePassword']);

        // Orders
        Route::get('/orders', [OrderController::class, 'index']);
        Route::get('/orders/{id}', [OrderController::class, 'show']);
        Route::post('/orders', [OrderController::class, 'store']);

        // Reviews (authenticated)
        Route::post('/products/{productId}/reviews', [ReviewController::class, 'store']);
        Route::get('/user/reviews', [ReviewController::class, 'userReviews']);

        // Wishlist
        Route::get('/wishlist', [WishlistController::class, 'index']);
        Route::post('/wishlist', [WishlistController::class, 'store']);
        Route::delete('/wishlist/{productId}', [WishlistController::class, 'destroy']);
        Route::post('/wishlist/toggle/{productId}', [WishlistController::class, 'toggle']);
        Route::get('/wishlist/check/{productId}', [WishlistController::class, 'check']);
        Route::delete('/wishlist', [WishlistController::class, 'clear']);

        // Cart
        Route::get('/cart', [CartController::class, 'index']);
        Route::post('/cart', [CartController::class, 'store']);
        Route::put('/cart/{productUpc}', [CartController::class, 'update']);
        Route::delete('/cart/{productUpc}', [CartController::class, 'destroy']);
        Route::delete('/cart', [CartController::class, 'clear']);

        // Loyalty Points
        Route::get('/loyalty', [LoyaltyController::class, 'index']);
        Route::post('/loyalty/redeem', [LoyaltyController::class, 'redeem']);
        Route::get('/loyalty/rewards', [LoyaltyController::class, 'rewards']);

        // Coupons
        Route::post('/coupons/validate', [CouponController::class, 'validate']);
        Route::post('/coupons/apply', [CouponController::class, 'apply']);

        // =====================
        // ADMIN ROUTES (require manager or admin role)
        // =====================
        Route::middleware('can:manager')->group(function () {

            // Product Management
            Route::post('/products', [ProductController::class, 'store']);
            Route::put('/products/{upc}', [ProductController::class, 'update']);
            Route::delete('/products/{upc}', [ProductController::class, 'destroy']);
            Route::patch('/products/{upc}/stock', [ProductController::class, 'updateStock']);

            // Order Management
            Route::put('/orders/{id}', [OrderController::class, 'update']);

            // Inventory Management
            Route::get('/admin/inventory/dashboard', [InventoryController::class, 'dashboard']);
            Route::get('/admin/inventory/alerts', [InventoryController::class, 'alerts']);
            Route::post('/admin/inventory/bulk-update', [InventoryController::class, 'bulkUpdate']);
            Route::get('/admin/inventory/valuation', [InventoryController::class, 'valuationReport']);
            Route::get('/admin/inventory/export', [InventoryController::class, 'export']);

            // Blog Management - moved to public routes (lines 133-136)

            // Event Management - moved to public routes (lines 139-143)

            // Coupon Management - moved to public routes (lines 146-150)

            // User Management - moved to public routes for dev (lines 161-167)

            // Loyalty Program Management - moved to public routes (lines 181-187)
        });
    });
});

// Health check endpoint
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toIso8601String(),
        'version' => '1.0.0'
    ]);
});
