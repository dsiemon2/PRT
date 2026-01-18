@php
    use App\Models\Category;
    use App\Models\Product;
    use App\Models\ContactMessage;
    use App\Services\BrandingService;
    use App\Services\FeaturesService;

    $cartCount = session('ItemsInBasket', 0);

    // Get branding settings from admin API
    $brandingService = new BrandingService();
    $brandingSettings = $brandingService->getSettings();
    $navbarClasses = $brandingService->getNavbarClasses();

    // Get feature flags from admin API
    $featuresService = new FeaturesService();

    // Admin notifications for managers
    $adminNotificationCount = 0;
    $adminMessageCount = 0;
    $adminNotifications = [];
    $adminMessages = [];

    if (auth()->check() && auth()->user()->isManager()) {
        try {
            // Get low stock products count
            $adminNotificationCount = Product::where('track_inventory', 1)
                ->whereRaw('(stock_quantity - reserved_quantity) <= reorder_point')
                ->count();

            // Get recent low stock products for dropdown
            $adminNotifications = Product::where('track_inventory', 1)
                ->whereRaw('(stock_quantity - reserved_quantity) <= reorder_point')
                ->selectRaw('id as product_id, ShortDescription, stock_quantity, reserved_quantity,
                            (stock_quantity - reserved_quantity) as current_stock,
                            reorder_point as threshold_value,
                            CASE WHEN (stock_quantity - reserved_quantity) <= 0
                                THEN "out_of_stock" ELSE "low_stock" END as alert_type')
                ->orderByRaw('(stock_quantity - reserved_quantity) ASC')
                ->limit(5)
                ->get();

            // Get unread messages count
            $adminMessageCount = ContactMessage::where('status', 'unread')->count();

            // Get recent messages for dropdown
            $adminMessages = ContactMessage::whereIn('status', ['unread', 'read'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        } catch (\Exception $e) {
            // Table may not exist yet
        }
    }

    // Get loyalty points if enabled and user is logged in
    $userLoyaltyPoints = 0;
    if (auth()->check() && $featuresService->isEnabled('loyalty')) {
        try {
            $loyaltyAccount = \DB::table('loyalty_members')
                ->where('user_id', auth()->id())
                ->first();
            if ($loyaltyAccount) {
                $userLoyaltyPoints = $loyaltyAccount->available_points ?? 0;
            }
        } catch (\Exception $e) {
            // Table may not exist
        }
    }
@endphp

{!! $brandingService->getAnnouncementBar() !!}

<!-- Top Navigation Bar -->
<nav class="{{ $navbarClasses }}">
    <div class="container-fluid">
        <div class="{{ $brandingService->getLogoWrapperClasses() }}">
            <a href="{{ url('/') }}">
                <img src="{{ asset('assets/images/PRT-High-Res-Logo.png') }}" alt="{{ config('app.name') }}" class="navbar-header-image" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline';">
                <span class="navbar-brand d-none" style="color: var(--prt-header-text, #fff);">{{ config('app.name') }}</span>
            </a>
        </div>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}">
                        <i class="bi bi-house-door"></i> Home
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('about*') ? 'active' : '' }}" href="{{ route('about') }}">
                        <i class="bi bi-info-circle"></i> About Us
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('products*') ? 'active' : '' }}" href="{{ route('products.index') }}">
                        <i class="bi bi-grid"></i> All Products
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('cart*') ? 'active' : '' }}" href="{{ route('cart.index') }}">
                        <i class="bi bi-cart3"></i> Cart
                        @if($cartCount > 0)
                            <span class="badge bg-danger">{{ $cartCount }}</span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('contact*') ? 'active' : '' }}" href="{{ route('contact') }}">
                        <i class="bi bi-envelope"></i> Contact
                    </a>
                </li>

                @auth
                    @if(auth()->user()->isManager() && $featuresService->isEnabled('admin_link'))
                    <!-- Admin Notifications Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle position-relative" href="#" id="notificationsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-bell"></i>
                            @if($adminNotificationCount > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                                    {{ $adminNotificationCount > 99 ? '99+' : $adminNotificationCount }}
                                </span>
                            @endif
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationsDropdown" style="min-width: 320px; max-height: 400px; overflow-y: auto;">
                            <li><h6 class="dropdown-header"><i class="bi bi-bell"></i> Stock Alerts</h6></li>
                            @if(count($adminNotifications) > 0)
                                @foreach($adminNotifications as $notif)
                                    <li>
                                        <a class="dropdown-item py-2" href="{{ route('admin.stock-alerts.index') }}">
                                            <div class="d-flex align-items-start">
                                                <i class="bi {{ $notif->alert_type == 'out_of_stock' ? 'bi-x-circle text-danger' : 'bi-exclamation-triangle text-warning' }} me-2 mt-1"></i>
                                                <div>
                                                    <div class="fw-semibold small">{{ Str::limit($notif->ShortDescription, 40) }}</div>
                                                    <small class="text-muted">{{ ucwords(str_replace('_', ' ', $notif->alert_type)) }}</small>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                @endforeach
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-center text-primary" href="{{ route('admin.stock-alerts.index') }}"><i class="bi bi-arrow-right"></i> View All Alerts</a></li>
                            @else
                                <li><span class="dropdown-item-text text-muted text-center py-3"><i class="bi bi-check-circle text-success"></i> No active alerts</span></li>
                            @endif
                        </ul>
                    </li>

                    <!-- Admin Messages Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle position-relative" href="#" id="messagesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-envelope"></i>
                            @if($adminMessageCount > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                                    {{ $adminMessageCount > 99 ? '99+' : $adminMessageCount }}
                                </span>
                            @endif
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="messagesDropdown" style="min-width: 320px; max-height: 400px; overflow-y: auto;">
                            <li><h6 class="dropdown-header"><i class="bi bi-envelope"></i> Contact Messages</h6></li>
                            @if(count($adminMessages) > 0)
                                @foreach($adminMessages as $msg)
                                    <li>
                                        <a class="dropdown-item py-2" href="{{ route('admin.messages.index', ['id' => $msg->id]) }}">
                                            <div class="d-flex align-items-start">
                                                <i class="bi {{ $msg->status == 'unread' ? 'bi-envelope-fill text-primary' : 'bi-envelope-open text-muted' }} me-2 mt-1"></i>
                                                <div>
                                                    <div class="fw-semibold small {{ $msg->status == 'unread' ? '' : 'text-muted' }}">{{ $msg->name }}</div>
                                                    <div class="small {{ $msg->status == 'unread' ? '' : 'text-muted' }}">{{ Str::limit($msg->subject, 35) }}</div>
                                                    <small class="text-muted">{{ $msg->created_at->format('M d, g:i A') }}</small>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                @endforeach
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-center text-primary" href="{{ route('admin.messages.index') }}"><i class="bi bi-arrow-right"></i> View All Messages</a></li>
                            @else
                                <li><span class="dropdown-item-text text-muted text-center py-3"><i class="bi bi-inbox"></i> No messages</span></li>
                            @endif
                        </ul>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.dashboard') }}">
                            <i class="bi bi-speedometer2"></i> Admin
                        </a>
                    </li>
                    @endif
                @endauth

                <li class="nav-item dropdown">
                    @auth
                        <!-- Logged In -->
                        <a class="nav-link dropdown-toggle" href="#" id="accountDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle"></i> Account
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="accountDropdown">
                            @if($featuresService->isEnabled('loyalty'))
                            <li>
                                <div class="dropdown-item-text" style="background: linear-gradient(135deg, #fff3cd 0%, #ffe69c 100%); border-radius: 5px; margin: 5px; padding: 10px; border: 1px solid #ffc107;">
                                    <div class="text-center">
                                        <i class="bi bi-award-fill" style="color: #ff6b35;"></i>
                                        <strong class="d-block">{{ number_format($userLoyaltyPoints) }} Points</strong>
                                        <small class="text-muted">Reward Balance</small>
                                    </div>
                                </div>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            @endif
                            <li><a class="dropdown-item" href="{{ route('account.index') }}"><i class="bi bi-person"></i> My Account</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ route('account.orders.index') }}"><i class="bi bi-box-seam"></i> Your Orders</a></li>
                            <li><a class="dropdown-item" href="{{ route('account.buy-again') }}"><i class="bi bi-arrow-repeat"></i> Buy Again</a></li>
                            @if($featuresService->isEnabled('wishlists'))
                            <li><a class="dropdown-item" href="{{ route('account.wishlist.index') }}"><i class="bi bi-heart"></i> Your Lists</a></li>
                            @endif
                            @if($featuresService->isEnabled('loyalty'))
                            <li><a class="dropdown-item" href="{{ route('account.index') }}"><i class="bi bi-trophy"></i> Loyalty Rewards</a></li>
                            @endif
                            <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-gear"></i> Account Settings</a></li>
                            <li><a class="dropdown-item" href="{{ route('contact') }}"><i class="bi bi-headset"></i> Support Requests</a></li>
                            <li><hr class="dropdown-divider"></li>
                            @if($featuresService->isEnabled('gift_cards'))
                            <li><a class="dropdown-item" href="{{ route('gift-cards') }}"><i class="bi bi-gift"></i> Gift Cards</a></li>
                            @endif
                            @if($featuresService->isEnabled('faq'))
                            <li><a class="dropdown-item" href="{{ route('faq') }}"><i class="bi bi-question-circle"></i> FAQ</a></li>
                            @endif
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="#" onclick="logout(event)">
                                    <i class="bi bi-box-arrow-right"></i> Sign Out
                                </a>
                            </li>
                        </ul>
                    @else
                        <!-- Logged Out -->
                        <a class="nav-link dropdown-toggle" href="#" id="accountDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person"></i> Account
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="accountDropdown">
                            <li><a class="dropdown-item" href="{{ route('login') }}"><i class="bi bi-box-arrow-in-right"></i> Login</a></li>
                            <li><a class="dropdown-item" href="{{ route('register') }}"><i class="bi bi-person-plus"></i> Create Account</a></li>
                            <li><hr class="dropdown-divider"></li>
                            @if($featuresService->isEnabled('gift_cards'))
                            <li><a class="dropdown-item" href="{{ route('gift-cards') }}"><i class="bi bi-gift"></i> Gift Cards</a></li>
                            @endif
                            @if($featuresService->isEnabled('faq'))
                            <li><a class="dropdown-item" href="{{ route('faq') }}"><i class="bi bi-question-circle"></i> FAQ</a></li>
                            @endif
                        </ul>
                    @endauth
                </li>
            </ul>
        </div>
    </div>
</nav>

<script>
function logout(e) {
    e.preventDefault();
    if (confirm('Are you sure you want to sign out?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("logout") }}';

        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = CSRF_TOKEN;
        form.appendChild(csrfInput);

        document.body.appendChild(form);
        form.submit();
    }
}
</script>
