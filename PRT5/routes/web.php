<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AccountSettingsController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\OrderHistoryController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Admin\InventoryController as AdminInventoryController;
use App\Http\Controllers\Admin\StockAlertController as AdminStockAlertController;
use App\Http\Controllers\Admin\BlogController as AdminBlogController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\Admin\MessageController as AdminMessageController;
use App\Http\Controllers\Admin\SettingsController as AdminSettingsController;
use App\Http\Controllers\Admin\FaqController as AdminFaqController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Products
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');
Route::get('/compare', [ProductController::class, 'compare'])->name('products.compare');
Route::get('/compare/count', [ProductController::class, 'compareCount'])->name('products.compare.count');
Route::post('/compare/add', [ProductController::class, 'addToCompare'])->name('products.compare.add');
Route::post('/compare/remove', [ProductController::class, 'removeFromCompare'])->name('products.compare.remove');

// Reviews (auth required)
Route::post('/products/{product}/reviews', [ReviewController::class, 'store'])->middleware('auth')->name('reviews.store');

// Events
Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');
Route::get('/events/{event}/ics', [EventController::class, 'ics'])->name('events.ics');

// Pages
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::post('/contact', [PageController::class, 'contactSubmit'])->name('contact.submit');
Route::get('/faq', [PageController::class, 'faq'])->name('faq');
Route::get('/gift-cards', [PageController::class, 'giftCards'])->name('gift-cards');
Route::get('/privacy', [PageController::class, 'privacy'])->name('privacy');
Route::get('/terms', [PageController::class, 'terms'])->name('terms');
Route::get('/returns', [PageController::class, 'returns'])->name('returns');
Route::get('/shipping', [PageController::class, 'shipping'])->name('shipping');

// Blog
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

// Cart
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::get('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/remove/{index}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/coupon', [CartController::class, 'applyCoupon'])->name('cart.coupon');
Route::post('/cart/coupon/remove', [CartController::class, 'removeCoupon'])->name('cart.coupon.remove');

// Checkout (auth required)
Route::middleware('auth')->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/order-confirmation/{order}', [CheckoutController::class, 'confirmation'])->name('order.confirmation');
});

Route::get('/dashboard', function () {
    return redirect()->route('account.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Redirect /profile to account settings
    Route::get('/profile', [AccountSettingsController::class, 'index'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Account routes
Route::middleware('auth')->prefix('account')->name('account.')->group(function () {
    Route::get('/', [AccountController::class, 'index'])->name('index');

    // Orders
    Route::get('/orders', [OrderHistoryController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderHistoryController::class, 'show'])->name('orders.show');

    // Buy Again (redirects to orders for now)
    Route::get('/buy-again', function() {
        return redirect()->route('account.orders.index');
    })->name('buy-again');

    // Wishlist
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist', [WishlistController::class, 'store'])->name('wishlist.store');
    Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
    Route::get('/wishlist/check', [WishlistController::class, 'check'])->name('wishlist.check');
    Route::delete('/wishlist/{wishlist}', [WishlistController::class, 'destroy'])->name('wishlist.destroy');

    // Addresses
    Route::get('/addresses', [AddressController::class, 'index'])->name('addresses.index');
    Route::get('/addresses/create', [AddressController::class, 'create'])->name('addresses.create');
    Route::post('/addresses', [AddressController::class, 'store'])->name('addresses.store');
    Route::get('/addresses/{address}/edit', [AddressController::class, 'edit'])->name('addresses.edit');
    Route::put('/addresses/{address}', [AddressController::class, 'update'])->name('addresses.update');
    Route::delete('/addresses/{address}', [AddressController::class, 'destroy'])->name('addresses.destroy');
    Route::post('/addresses/{address}/default', [AddressController::class, 'setDefault'])->name('addresses.default');

    // Account Settings
    Route::get('/settings', [AccountSettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings/name', [AccountSettingsController::class, 'updateName'])->name('settings.name');
    Route::post('/settings/email', [AccountSettingsController::class, 'updateEmail'])->name('settings.email');
    Route::post('/settings/phone', [AccountSettingsController::class, 'updatePhone'])->name('settings.phone');
    Route::post('/settings/password', [AccountSettingsController::class, 'updatePassword'])->name('settings.password');
    Route::post('/settings/address', [AccountSettingsController::class, 'storeAddress'])->name('settings.address.store');
    Route::post('/settings/address/default', [AccountSettingsController::class, 'setDefaultAddress'])->name('settings.address.default');
    Route::post('/settings/address/delete', [AccountSettingsController::class, 'deleteAddress'])->name('settings.address.delete');
    Route::post('/settings/delivery', [AccountSettingsController::class, 'saveDeliveryPrefs'])->name('settings.delivery');
    Route::post('/settings/notifications', [AccountSettingsController::class, 'saveNotificationPrefs'])->name('settings.notifications');
});

// Admin routes
Route::middleware(['auth', 'manager'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Reviews Management
    Route::get('/reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
    Route::put('/reviews/{review}', [AdminReviewController::class, 'update'])->name('reviews.update');
    Route::delete('/reviews/{review}', [AdminReviewController::class, 'destroy'])->name('reviews.destroy');
    Route::post('/reviews/bulk-action', [AdminReviewController::class, 'bulkAction'])->name('reviews.bulk-action');

    // Inventory Management
    Route::get('/inventory', [AdminInventoryController::class, 'index'])->name('inventory.index');
    Route::get('/inventory/reports', [AdminInventoryController::class, 'reports'])->name('inventory.reports');
    Route::get('/inventory/bulk-update', [AdminInventoryController::class, 'bulkUpdate'])->name('inventory.bulk-update');
    Route::post('/inventory/bulk-update', [AdminInventoryController::class, 'processBulkUpdate'])->name('inventory.bulk-update.store');
    Route::post('/inventory/bulk-update/csv', [AdminInventoryController::class, 'bulkUpdateCsv'])->name('inventory.bulk-update.csv');
    Route::post('/inventory/bulk-update/manual', [AdminInventoryController::class, 'bulkUpdateManual'])->name('inventory.bulk-update.manual');
    Route::get('/inventory/export', [AdminInventoryController::class, 'export'])->name('inventory.export');
    Route::get('/inventory/{product}/edit', [AdminInventoryController::class, 'edit'])->name('inventory.edit');
    Route::put('/inventory/{product}', [AdminInventoryController::class, 'update'])->name('inventory.update');

    // Stock Alerts
    Route::get('/stock-alerts', [AdminStockAlertController::class, 'index'])->name('stock-alerts.index');
    Route::put('/stock-alerts/{alert}', [AdminStockAlertController::class, 'update'])->name('stock-alerts.update');
    Route::post('/stock-alerts/bulk-resolve', [AdminStockAlertController::class, 'bulkResolve'])->name('stock-alerts.bulk-resolve');

    // Blog Management
    Route::resource('/blog', AdminBlogController::class);

    // Events Management
    Route::resource('/events', AdminEventController::class);

    // Messages
    Route::get('/messages', [AdminMessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{message}', [AdminMessageController::class, 'show'])->name('messages.show');
    Route::put('/messages/{message}', [AdminMessageController::class, 'update'])->name('messages.update');
    Route::delete('/messages/{message}', [AdminMessageController::class, 'destroy'])->name('messages.destroy');
    Route::post('/messages/bulk-action', [AdminMessageController::class, 'bulkAction'])->name('messages.bulk-action');

    // FAQ Statistics
    Route::get('/faq-statistics', [AdminFaqController::class, 'statistics'])->name('faq.statistics');

    // Settings
    Route::get('/settings', [AdminSettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [AdminSettingsController::class, 'update'])->name('settings.update');
});

require __DIR__.'/auth.php';
