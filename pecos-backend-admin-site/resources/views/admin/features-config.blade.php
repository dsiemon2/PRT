@extends('layouts.admin')

@section('title', 'Feature Configuration')

@section('content')
<div class="page-header">
    <h1>Feature Configuration</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Feature Configuration</li>
        </ol>
    </nav>
</div>

<div id="loadingFeatures" class="text-center py-5">
    <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
    <p class="mt-2">Loading feature configuration...</p>
</div>

<div id="featuresContent" style="display: none;">
    <div class="row">
        <div class="col-lg-8">
            <!-- Feature Toggles -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-toggles me-2"></i>Feature Toggles</h5>
                    <small class="text-muted">Enable or disable features across the entire system</small>
                </div>
                <div class="card-body">
                    <form id="featuresForm" onsubmit="saveFeatures(event)">
                        <!-- FAQ -->
                        <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                            <div>
                                <h6 class="mb-1">FAQ Section</h6>
                                <small class="text-muted">Show FAQ page and related links</small>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="feature_faq_enabled" name="faq_enabled" style="width: 3em; height: 1.5em;">
                            </div>
                        </div>

                        <!-- Loyalty Points -->
                        <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                            <div>
                                <h6 class="mb-1">Loyalty Points Program</h6>
                                <small class="text-muted">Show loyalty points in Account dropdown, menus, and admin</small>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="feature_loyalty_enabled" name="loyalty_enabled" style="width: 3em; height: 1.5em;">
                            </div>
                        </div>

                        <!-- Digital Downloads -->
                        <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                            <div>
                                <h6 class="mb-1">Digital Downloads</h6>
                                <small class="text-muted">Enable digital download products and categories</small>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="feature_digital_downloads_enabled" name="digital_downloads_enabled" style="width: 3em; height: 1.5em;">
                            </div>
                        </div>

                        <!-- Gift Cards -->
                        <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                            <div>
                                <h6 class="mb-1">Gift Cards</h6>
                                <small class="text-muted">Show gift cards in Account dropdown and admin</small>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="feature_gift_cards_enabled" name="gift_cards_enabled" style="width: 3em; height: 1.5em;">
                            </div>
                        </div>

                        <!-- Wishlists -->
                        <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                            <div>
                                <h6 class="mb-1">Wishlists (Your List)</h6>
                                <small class="text-muted">Show heart icons on products and "Your List" in Account</small>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="feature_wishlists_enabled" name="wishlists_enabled" style="width: 3em; height: 1.5em;">
                            </div>
                        </div>

                        <!-- Blog -->
                        <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                            <div>
                                <h6 class="mb-1">Blog</h6>
                                <small class="text-muted">Show blog section and related links</small>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="feature_blog_enabled" name="blog_enabled" style="width: 3em; height: 1.5em;">
                            </div>
                        </div>

                        <!-- Events -->
                        <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                            <div>
                                <h6 class="mb-1">Events</h6>
                                <small class="text-muted">Show events section and calendar</small>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="feature_events_enabled" name="events_enabled" style="width: 3em; height: 1.5em;">
                            </div>
                        </div>

                        <!-- Reviews -->
                        <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                            <div>
                                <h6 class="mb-1">Product Reviews</h6>
                                <small class="text-muted">Allow customers to leave product reviews</small>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="feature_reviews_enabled" name="reviews_enabled" style="width: 3em; height: 1.5em;">
                            </div>
                        </div>

                        <!-- Product Sticky Bar -->
                        <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                            <div>
                                <h6 class="mb-1">Product Page Sticky Bar</h6>
                                <small class="text-muted">Show sticky navigation bar on product detail pages (with tabs: Description, Reviews, Q&A)</small>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="feature_product_sticky_bar_enabled" name="product_sticky_bar_enabled" style="width: 3em; height: 1.5em;">
                            </div>
                        </div>

                        <!-- Admin Link -->
                        <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                            <div>
                                <h6 class="mb-1">Frontend Admin Link</h6>
                                <small class="text-muted">Show "Admin" link in frontend top navigation</small>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="feature_admin_link_enabled" name="admin_link_enabled" style="width: 3em; height: 1.5em;">
                            </div>
                        </div>

                        <!-- Tell-A-Friend -->
                        <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                            <div>
                                <h6 class="mb-1">Tell-A-Friend</h6>
                                <small class="text-muted">Show Tell-A-Friend page and footer link</small>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="feature_tell_a_friend_enabled" name="tell_a_friend_enabled" style="width: 3em; height: 1.5em;">
                            </div>
                        </div>

                        <!-- Newsletter Signup -->
                        <div class="d-flex justify-content-between align-items-center py-3">
                            <div>
                                <h6 class="mb-1">Newsletter Signup</h6>
                                <small class="text-muted">Show newsletter signup section in footer</small>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="feature_newsletter_enabled" name="newsletter_enabled" style="width: 3em; height: 1.5em;">
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-prt">
                                <i class="bi bi-check-lg me-1"></i> Save Feature Configuration
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Live Chat Configuration -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-chat-dots me-2"></i>Live Chat Configuration</h5>
                    <small class="text-muted">Configure live chat providers for customer support</small>
                </div>
                <div class="card-body">
                    <form id="liveChatForm" onsubmit="saveLiveChat(event)">
                        <!-- Master Live Chat Toggle -->
                        <div class="d-flex justify-content-between align-items-center py-3 border-bottom bg-light px-3 rounded mb-3">
                            <div>
                                <h6 class="mb-1"><i class="bi bi-power text-success me-2"></i>Live Chat</h6>
                                <small class="text-muted">Master switch - Enable/disable live chat on the storefront</small>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="feature_live_chat_enabled" name="live_chat_enabled" style="width: 3em; height: 1.5em;" onchange="toggleChatProviders()">
                            </div>
                        </div>

                        <div id="chatProvidersSection">
                            <!-- Tawk.to -->
                            <div class="border rounded p-3 mb-3" id="tawktoSection">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <h6 class="mb-1"><img src="https://www.tawk.to/wp-content/uploads/2020/04/tawk-sitelogo.png" alt="Tawk.to" style="height: 20px;" class="me-2">Tawk.to</h6>
                                        <small class="text-muted">Free live chat - <a href="https://www.tawk.to" target="_blank">tawk.to</a></small>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="feature_tawkto_enabled" name="tawkto_enabled" style="width: 3em; height: 1.5em;" onchange="handleProviderToggle('tawkto')">
                                    </div>
                                </div>
                                <div class="row g-2" id="tawktoFields">
                                    <div class="col-md-6">
                                        <label class="form-label small">Property ID</label>
                                        <input type="text" class="form-control form-control-sm" id="feature_tawkto_property_id" name="tawkto_property_id" placeholder="e.g., 1234567890abcdef">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small">Widget ID</label>
                                        <input type="text" class="form-control form-control-sm" id="feature_tawkto_widget_id" name="tawkto_widget_id" placeholder="e.g., 1a2b3c4d">
                                    </div>
                                </div>
                            </div>

                            <!-- Tidio -->
                            <div class="border rounded p-3 mb-3" id="tidioSection">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <h6 class="mb-1"><i class="bi bi-chat-left-text text-primary me-2"></i>Tidio</h6>
                                        <small class="text-muted">AI-powered chat - <a href="https://www.tidio.com" target="_blank">tidio.com</a></small>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="feature_tidio_enabled" name="tidio_enabled" style="width: 3em; height: 1.5em;" onchange="handleProviderToggle('tidio')">
                                    </div>
                                </div>
                                <div class="row g-2" id="tidioFields">
                                    <div class="col-12">
                                        <label class="form-label small">Public Key</label>
                                        <input type="text" class="form-control form-control-sm" id="feature_tidio_public_key" name="tidio_public_key" placeholder="e.g., abcdefghijklmnop1234567890">
                                        <small class="text-muted">Find this in Tidio Dashboard > Settings > Developer</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info small mb-3">
                            <i class="bi bi-info-circle me-1"></i>
                            <strong>Note:</strong> Only one chat provider can be active at a time. Enabling one will automatically disable the other.
                        </div>

                        <button type="submit" class="btn btn-prt">
                            <i class="bi bi-check-lg me-1"></i> Save Live Chat Configuration
                        </button>
                    </form>
                </div>
            </div>

            <!-- Notification Configuration -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-bell me-2"></i>Notification Configuration</h5>
                    <small class="text-muted">Control which notifications are available to customers</small>
                </div>
                <div class="card-body">
                    <form id="notificationsForm" onsubmit="saveNotifications(event)">
                        <!-- Master Notifications Toggle -->
                        <div class="d-flex justify-content-between align-items-center py-3 border-bottom bg-light px-3 rounded mb-3">
                            <div>
                                <h6 class="mb-1"><i class="bi bi-power text-success me-2"></i>All Notifications</h6>
                                <small class="text-muted">Master switch - Enable/disable all customer notifications</small>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="feature_notifications_enabled" name="notifications_enabled" style="width: 3em; height: 1.5em;" onchange="toggleNotificationSections()">
                            </div>
                        </div>

                        <div id="notificationSectionsWrapper">
                            <!-- Channel Toggles -->
                            <div class="mb-4">
                                <h6 class="text-muted mb-3"><i class="bi bi-broadcast me-1"></i> Notification Channels</h6>
                                <div class="row g-3">
                                    <!-- Email -->
                                    <div class="col-md-4">
                                        <div class="border rounded p-3 h-100">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <i class="bi bi-envelope text-primary me-2"></i>
                                                    <strong>Email</strong>
                                                </div>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="feature_notif_email_enabled" name="notif_email_enabled" style="width: 2.5em; height: 1.25em;">
                                                </div>
                                            </div>
                                            <small class="text-muted d-block mt-1">Send email notifications</small>
                                        </div>
                                    </div>
                                    <!-- SMS -->
                                    <div class="col-md-4">
                                        <div class="border rounded p-3 h-100">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <i class="bi bi-phone text-success me-2"></i>
                                                    <strong>SMS</strong>
                                                </div>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="feature_notif_sms_enabled" name="notif_sms_enabled" style="width: 2.5em; height: 1.25em;">
                                                </div>
                                            </div>
                                            <small class="text-muted d-block mt-1">Send SMS via Twilio</small>
                                        </div>
                                    </div>
                                    <!-- Push -->
                                    <div class="col-md-4">
                                        <div class="border rounded p-3 h-100">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <i class="bi bi-app-indicator text-warning me-2"></i>
                                                    <strong>Push</strong>
                                                </div>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="feature_notif_push_enabled" name="notif_push_enabled" style="width: 2.5em; height: 1.25em;">
                                                </div>
                                            </div>
                                            <small class="text-muted d-block mt-1">Browser push notifications</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Category Toggles -->
                            <div class="mb-4">
                                <h6 class="text-muted mb-3"><i class="bi bi-collection me-1"></i> Notification Categories</h6>

                                <!-- Delivery Notifications -->
                                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                    <div>
                                        <i class="bi bi-truck text-info me-2"></i>
                                        <strong>Delivery Updates</strong>
                                        <small class="text-muted d-block ms-4">Order shipped, delivered, tracking updates</small>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="feature_notif_delivery_enabled" name="notif_delivery_enabled" style="width: 2.5em; height: 1.25em;">
                                    </div>
                                </div>

                                <!-- Promotional Notifications -->
                                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                    <div>
                                        <i class="bi bi-tag text-danger me-2"></i>
                                        <strong>Promotional</strong>
                                        <small class="text-muted d-block ms-4">Sales, discounts, back-in-stock alerts</small>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="feature_notif_promo_enabled" name="notif_promo_enabled" style="width: 2.5em; height: 1.25em;">
                                    </div>
                                </div>

                                <!-- Payment Notifications -->
                                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                    <div>
                                        <i class="bi bi-credit-card text-success me-2"></i>
                                        <strong>Payment Alerts</strong>
                                        <small class="text-muted d-block ms-4">Payment received, failed, refund processed</small>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="feature_notif_payment_enabled" name="notif_payment_enabled" style="width: 2.5em; height: 1.25em;">
                                    </div>
                                </div>

                                <!-- Security Notifications -->
                                <div class="d-flex justify-content-between align-items-center py-2">
                                    <div>
                                        <i class="bi bi-shield-lock text-warning me-2"></i>
                                        <strong>Security Alerts</strong>
                                        <small class="text-muted d-block ms-4">Login alerts, password changes</small>
                                        <span class="badge bg-warning text-dark ms-4">Recommended: Always ON</span>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="feature_notif_security_enabled" name="notif_security_enabled" style="width: 2.5em; height: 1.25em;">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info small mb-3">
                            <i class="bi bi-info-circle me-1"></i>
                            <strong>Note:</strong> These settings control which notifications are available site-wide. Users can still customize their own preferences within these limits.
                        </div>

                        <button type="submit" class="btn btn-prt">
                            <i class="bi bi-check-lg me-1"></i> Save Notification Configuration
                        </button>
                    </form>
                </div>
            </div>

            <!-- Payment Gateway Configuration -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-credit-card me-2"></i>Payment Gateway Configuration</h5>
                    <small class="text-muted">Configure payment processors for accepting payments</small>
                </div>
                <div class="card-body">
                    <form id="paymentGatewayForm" onsubmit="savePaymentGateway(event)">
                        <!-- Master Payment Gateway Toggle -->
                        <div class="d-flex justify-content-between align-items-center py-3 border-bottom bg-light px-3 rounded mb-3">
                            <div>
                                <h6 class="mb-1"><i class="bi bi-power text-success me-2"></i>Payment Processing</h6>
                                <small class="text-muted">Master switch - Enable/disable payment gateway integration</small>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="feature_payment_gateway_enabled" name="payment_gateway_enabled" style="width: 3em; height: 1.5em;" onchange="togglePaymentProviders()">
                            </div>
                        </div>

                        <div id="paymentProvidersSection">
                            <!-- Stripe -->
                            <div class="border rounded p-3 mb-3" id="stripeSection">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <h6 class="mb-1"><i class="bi bi-stripe text-primary me-2" style="font-size: 1.2em;"></i>Stripe</h6>
                                        <small class="text-muted">Cards + Apple Pay + Google Pay - <a href="https://stripe.com" target="_blank">stripe.com</a></small>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="feature_stripe_enabled" name="stripe_enabled" style="width: 3em; height: 1.5em;" onchange="handlePaymentProviderToggle('stripe')">
                                    </div>
                                </div>
                                <div class="row g-2" id="stripeFields">
                                    <div class="col-md-6">
                                        <label class="form-label small">Publishable Key</label>
                                        <input type="text" class="form-control form-control-sm" id="feature_stripe_publishable_key" name="stripe_publishable_key" placeholder="pk_live_... or pk_test_...">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small">Secret Key</label>
                                        <input type="password" class="form-control form-control-sm" id="feature_stripe_secret_key" name="stripe_secret_key" placeholder="sk_live_... or sk_test_..." autocomplete="new-password">
                                    </div>
                                    <div class="col-6 mt-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="feature_stripe_test_mode" name="stripe_test_mode">
                                            <label class="form-check-label small" for="feature_stripe_test_mode">
                                                <i class="bi bi-bug me-1"></i>Test Mode (use test keys)
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-6 mt-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="feature_stripe_ach_enabled" name="stripe_ach_enabled">
                                            <label class="form-check-label small" for="feature_stripe_ach_enabled">
                                                <i class="bi bi-bank me-1"></i>Enable ACH Bank Transfers
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-12 mt-2" id="stripeAchInfo" style="display: none;">
                                        <div class="alert alert-info small mb-0 py-2">
                                            <i class="bi bi-info-circle me-1"></i>
                                            <strong>ACH Direct Debit:</strong> Low-cost bank transfers for US customers.
                                            Fees: 0.8% capped at $5. Processing: 1-3 business days.
                                            <a href="https://stripe.com/docs/payments/ach-debit" target="_blank">Learn more</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Braintree -->
                            <div class="border rounded p-3 mb-3" id="braintreeSection">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <h6 class="mb-1"><i class="bi bi-paypal text-info me-2"></i>Braintree (PayPal)</h6>
                                        <small class="text-muted">Cards + PayPal + Venmo + Wallets - <a href="https://braintreepayments.com" target="_blank">braintreepayments.com</a></small>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="feature_braintree_enabled" name="braintree_enabled" style="width: 3em; height: 1.5em;" onchange="handlePaymentProviderToggle('braintree')">
                                    </div>
                                </div>
                                <div class="row g-2" id="braintreeFields">
                                    <div class="col-md-6">
                                        <label class="form-label small">Merchant ID</label>
                                        <input type="text" class="form-control form-control-sm" id="feature_braintree_merchant_id" name="braintree_merchant_id" placeholder="Your Merchant ID">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small">Public Key</label>
                                        <input type="text" class="form-control form-control-sm" id="feature_braintree_public_key" name="braintree_public_key" placeholder="Your Public Key">
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label small">Private Key</label>
                                        <input type="password" class="form-control form-control-sm" id="feature_braintree_private_key" name="braintree_private_key" placeholder="Your Private Key" autocomplete="new-password">
                                    </div>
                                    <div class="col-12 mt-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="feature_braintree_sandbox" name="braintree_sandbox">
                                            <label class="form-check-label small" for="feature_braintree_sandbox">
                                                <i class="bi bi-bug me-1"></i>Sandbox Mode (testing)
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- PayPal Direct -->
                            <div class="border rounded p-3 mb-3" id="paypalSection">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <h6 class="mb-1"><i class="bi bi-paypal text-primary me-2"></i>PayPal Checkout</h6>
                                        <small class="text-muted">PayPal Express Checkout - <a href="https://developer.paypal.com" target="_blank">developer.paypal.com</a></small>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="feature_paypal_enabled" name="paypal_enabled" style="width: 3em; height: 1.5em;">
                                    </div>
                                </div>
                                <div class="row g-2" id="paypalFields">
                                    <div class="col-md-6">
                                        <label class="form-label small">Client ID</label>
                                        <input type="text" class="form-control form-control-sm" id="feature_paypal_client_id" name="paypal_client_id" placeholder="Your PayPal Client ID">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small">Client Secret</label>
                                        <input type="password" class="form-control form-control-sm" id="feature_paypal_client_secret" name="paypal_client_secret" placeholder="Your PayPal Client Secret" autocomplete="new-password">
                                    </div>
                                    <div class="col-12 mt-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="feature_paypal_sandbox" name="paypal_sandbox">
                                            <label class="form-check-label small" for="feature_paypal_sandbox">
                                                <i class="bi bi-bug me-1"></i>Sandbox Mode (testing)
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Square -->
                            <div class="border rounded p-3 mb-3" id="squareSection">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <h6 class="mb-1"><i class="bi bi-square text-dark me-2"></i>Square</h6>
                                        <small class="text-muted">Cards + Square Wallet + Afterpay - <a href="https://squareup.com/developers" target="_blank">squareup.com/developers</a></small>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="feature_square_enabled" name="square_enabled" style="width: 3em; height: 1.5em;" onchange="handlePaymentProviderToggle('square')">
                                    </div>
                                </div>
                                <div class="row g-2" id="squareFields">
                                    <div class="col-md-6">
                                        <label class="form-label small">Application ID</label>
                                        <input type="text" class="form-control form-control-sm" id="feature_square_application_id" name="square_application_id" placeholder="sq0idp-...">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small">Access Token</label>
                                        <input type="password" class="form-control form-control-sm" id="feature_square_access_token" name="square_access_token" placeholder="Your Access Token" autocomplete="new-password">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small">Location ID</label>
                                        <input type="text" class="form-control form-control-sm" id="feature_square_location_id" name="square_location_id" placeholder="Your Location ID">
                                    </div>
                                    <div class="col-6 mt-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="feature_square_sandbox" name="square_sandbox">
                                            <label class="form-check-label small" for="feature_square_sandbox">
                                                <i class="bi bi-bug me-1"></i>Sandbox Mode (testing)
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Authorize.net -->
                            <div class="border rounded p-3 mb-3" id="authorizenetSection">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <h6 class="mb-1"><i class="bi bi-shield-check text-success me-2"></i>Authorize.net</h6>
                                        <small class="text-muted">Cards + eChecks + Digital Payments - <a href="https://developer.authorize.net" target="_blank">developer.authorize.net</a></small>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="feature_authorizenet_enabled" name="authorizenet_enabled" style="width: 3em; height: 1.5em;" onchange="handlePaymentProviderToggle('authorizenet')">
                                    </div>
                                </div>
                                <div class="row g-2" id="authorizenetFields">
                                    <div class="col-md-6">
                                        <label class="form-label small">API Login ID</label>
                                        <input type="text" class="form-control form-control-sm" id="feature_authorizenet_login_id" name="authorizenet_login_id" placeholder="Your API Login ID">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small">Transaction Key</label>
                                        <input type="password" class="form-control form-control-sm" id="feature_authorizenet_transaction_key" name="authorizenet_transaction_key" placeholder="Your Transaction Key" autocomplete="new-password">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small">Signature Key (Optional)</label>
                                        <input type="password" class="form-control form-control-sm" id="feature_authorizenet_signature_key" name="authorizenet_signature_key" placeholder="For webhook verification" autocomplete="new-password">
                                    </div>
                                    <div class="col-6 mt-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="feature_authorizenet_sandbox" name="authorizenet_sandbox">
                                            <label class="form-check-label small" for="feature_authorizenet_sandbox">
                                                <i class="bi bi-bug me-1"></i>Sandbox Mode (testing)
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="alert alert-info small mb-3">
                            <i class="bi bi-info-circle me-1"></i>
                            <strong>Security Note:</strong> API keys are stored securely. Never share your secret keys. Use test/sandbox mode for development.
                        </div>

                        <div class="alert alert-warning small mb-3">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            <strong>Note:</strong> You can enable multiple payment gateways. Customers will see all enabled options at checkout.
                        </div>

                        <button type="submit" class="btn btn-prt">
                            <i class="bi bi-check-lg me-1"></i> Save Payment Gateway Configuration
                        </button>
                    </form>
                </div>
            </div>

            <!-- Tax Calculation Provider -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-calculator me-2"></i>Tax Calculation Provider</h5>
                    <small class="text-muted">Choose how taxes are calculated at checkout</small>
                </div>
                <div class="card-body">
                    <form id="taxProviderForm" onsubmit="saveTaxProvider(event)">
                        <!-- Master Tax Calculation Toggle -->
                        <div class="d-flex justify-content-between align-items-center py-3 border-bottom bg-light px-3 rounded mb-3">
                            <div>
                                <h6 class="mb-1"><i class="bi bi-power text-success me-2"></i>Tax Calculation</h6>
                                <small class="text-muted">Master switch - Enable/disable tax calculation at checkout</small>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="feature_tax_calculation_enabled" name="tax_calculation_enabled" style="width: 3em; height: 1.5em;" onchange="toggleTaxProviders()">
                            </div>
                        </div>

                        <div id="taxProvidersSection">
                            <!-- Provider Selection (Radio buttons - only one active) -->
                            <h6 class="text-muted mb-3"><i class="bi bi-diagram-3 me-1"></i> Select Tax Provider</h6>

                            <!-- Custom Tax Table -->
                            <div class="border rounded p-3 mb-3 tax-provider-option" id="customTaxSection">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="tax_provider" id="tax_provider_custom" value="custom" onchange="handleTaxProviderChange('custom')">
                                    <label class="form-check-label" for="tax_provider_custom">
                                        <h6 class="mb-1 d-inline"><i class="bi bi-table text-primary me-2"></i>Custom Tax Table</h6>
                                    </label>
                                </div>
                                <div class="ms-4 mt-2">
                                    <small class="text-muted d-block">Use rates configured in Settings &gt; Tax</small>
                                    <div class="mt-2">
                                        <span class="badge bg-success">Free</span>
                                        <span class="badge bg-secondary">Full Control</span>
                                        <span class="badge bg-info">Offline Capable</span>
                                    </div>
                                    <small class="text-muted d-block mt-2">
                                        <strong>Best for:</strong> Simple US-only sales, B2B with known customers
                                    </small>
                                </div>
                            </div>

                            <!-- Stripe Tax -->
                            <div class="border rounded p-3 mb-3 tax-provider-option" id="stripeTaxSection">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="tax_provider" id="tax_provider_stripe" value="stripe" onchange="handleTaxProviderChange('stripe')">
                                    <label class="form-check-label" for="tax_provider_stripe">
                                        <h6 class="mb-1 d-inline"><i class="bi bi-stripe text-primary me-2"></i>Stripe Tax</h6>
                                    </label>
                                </div>
                                <div class="ms-4 mt-2">
                                    <small class="text-muted d-block">Automatic tax calculation via Stripe</small>
                                    <div class="mt-2">
                                        <span class="badge bg-warning text-dark">0.5% per transaction</span>
                                        <span class="badge bg-success">Global Coverage</span>
                                        <span class="badge bg-info">Nexus Tracking</span>
                                    </div>
                                    <small class="text-muted d-block mt-2">
                                        <strong>Best for:</strong> Already using Stripe, want automated compliance
                                    </small>
                                    <div id="stripeTaxWarning" class="alert alert-warning small mt-2 mb-0 py-2" style="display: none;">
                                        <i class="bi bi-exclamation-triangle me-1"></i>
                                        <strong>Note:</strong> Requires Stripe Payment to be enabled above.
                                    </div>
                                </div>
                            </div>

                            <!-- TaxJar -->
                            <div class="border rounded p-3 mb-3 tax-provider-option" id="taxjarSection">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="tax_provider" id="tax_provider_taxjar" value="taxjar" onchange="handleTaxProviderChange('taxjar')">
                                    <label class="form-check-label" for="tax_provider_taxjar">
                                        <h6 class="mb-1 d-inline"><i class="bi bi-journal-text text-success me-2"></i>TaxJar</h6>
                                    </label>
                                </div>
                                <div class="ms-4 mt-2">
                                    <small class="text-muted d-block">Professional tax automation service - <a href="https://www.taxjar.com" target="_blank">taxjar.com</a></small>
                                    <div class="mt-2">
                                        <span class="badge bg-warning text-dark">$19-99/mo</span>
                                        <span class="badge bg-success">Multi-Channel</span>
                                        <span class="badge bg-info">Auto Filing</span>
                                    </div>
                                    <small class="text-muted d-block mt-2">
                                        <strong>Best for:</strong> High volume, multi-channel sellers needing automated tax filing
                                    </small>
                                    <div id="taxjarFields" class="mt-3" style="display: none;">
                                        <label class="form-label small">TaxJar API Token</label>
                                        <input type="password" class="form-control form-control-sm" id="feature_taxjar_api_token" name="taxjar_api_token" placeholder="Your TaxJar API Token" autocomplete="new-password">
                                        <small class="text-muted">Find this in TaxJar Dashboard &gt; Account &gt; API</small>
                                        <div class="form-check mt-2">
                                            <input class="form-check-input" type="checkbox" id="feature_taxjar_sandbox" name="taxjar_sandbox">
                                            <label class="form-check-label small" for="feature_taxjar_sandbox">
                                                <i class="bi bi-bug me-1"></i>Sandbox Mode (testing)
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Comparison Table -->
                        <div class="table-responsive mt-4 mb-3">
                            <table class="table table-sm table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Feature</th>
                                        <th class="text-center">Custom</th>
                                        <th class="text-center">Stripe Tax</th>
                                        <th class="text-center">TaxJar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Cost</td>
                                        <td class="text-center text-success">Free</td>
                                        <td class="text-center">0.5%/txn</td>
                                        <td class="text-center">$19-99/mo</td>
                                    </tr>
                                    <tr>
                                        <td>US Sales Tax</td>
                                        <td class="text-center"><i class="bi bi-check-circle text-success"></i></td>
                                        <td class="text-center"><i class="bi bi-check-circle text-success"></i></td>
                                        <td class="text-center"><i class="bi bi-check-circle text-success"></i></td>
                                    </tr>
                                    <tr>
                                        <td>International VAT/GST</td>
                                        <td class="text-center"><i class="bi bi-dash text-muted"></i></td>
                                        <td class="text-center"><i class="bi bi-check-circle text-success"></i></td>
                                        <td class="text-center"><i class="bi bi-check-circle text-success"></i></td>
                                    </tr>
                                    <tr>
                                        <td>Auto Nexus Tracking</td>
                                        <td class="text-center"><i class="bi bi-x-circle text-danger"></i></td>
                                        <td class="text-center"><i class="bi bi-check-circle text-success"></i></td>
                                        <td class="text-center"><i class="bi bi-check-circle text-success"></i></td>
                                    </tr>
                                    <tr>
                                        <td>Auto Filing</td>
                                        <td class="text-center"><i class="bi bi-x-circle text-danger"></i></td>
                                        <td class="text-center"><i class="bi bi-x-circle text-danger"></i></td>
                                        <td class="text-center"><i class="bi bi-check-circle text-success"></i></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="alert alert-info small mb-3">
                            <i class="bi bi-info-circle me-1"></i>
                            <strong>Fallback:</strong> If Stripe Tax or TaxJar API fails, the system will automatically fall back to your Custom Tax Table rates.
                        </div>

                        <button type="submit" class="btn btn-prt">
                            <i class="bi bi-check-lg me-1"></i> Save Tax Provider Configuration
                        </button>
                    </form>
                </div>
            </div>

            <!-- Social Media Configuration -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-share me-2"></i>Social Media Configuration</h5>
                    <small class="text-muted">Configure social media links displayed on Contact and About pages</small>
                </div>
                <div class="card-body">
                    <form id="socialMediaForm" onsubmit="saveSocialMedia(event)">
                        <!-- Master Social Media Toggle -->
                        <div class="d-flex justify-content-between align-items-center py-3 border-bottom bg-light px-3 rounded mb-3">
                            <div>
                                <h6 class="mb-1"><i class="bi bi-power text-success me-2"></i>Social Media Links</h6>
                                <small class="text-muted">Master switch - Show/hide social media section on pages</small>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="feature_social_media_enabled" name="social_media_enabled" style="width: 3em; height: 1.5em;" onchange="toggleSocialMediaSection()">
                            </div>
                        </div>

                        <div id="socialMediaSection">
                            <!-- Facebook -->
                            <div class="border rounded p-3 mb-2">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="feature_social_facebook_enabled" name="social_facebook_enabled" style="width: 2.5em; height: 1.25em;">
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-facebook text-primary" style="font-size: 1.5em;"></i>
                                    </div>
                                    <div class="col">
                                        <input type="url" class="form-control form-control-sm" id="feature_social_facebook_url" name="social_facebook_url" placeholder="https://facebook.com/yourpage">
                                    </div>
                                </div>
                            </div>

                            <!-- Instagram -->
                            <div class="border rounded p-3 mb-2">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="feature_social_instagram_enabled" name="social_instagram_enabled" style="width: 2.5em; height: 1.25em;">
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-instagram" style="font-size: 1.5em; color: #E4405F;"></i>
                                    </div>
                                    <div class="col">
                                        <input type="url" class="form-control form-control-sm" id="feature_social_instagram_url" name="social_instagram_url" placeholder="https://instagram.com/yourhandle">
                                    </div>
                                </div>
                            </div>

                            <!-- Twitter/X -->
                            <div class="border rounded p-3 mb-2">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="feature_social_twitter_enabled" name="social_twitter_enabled" style="width: 2.5em; height: 1.25em;">
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-twitter-x" style="font-size: 1.5em;"></i>
                                    </div>
                                    <div class="col">
                                        <input type="url" class="form-control form-control-sm" id="feature_social_twitter_url" name="social_twitter_url" placeholder="https://x.com/yourhandle">
                                    </div>
                                </div>
                            </div>

                            <!-- YouTube -->
                            <div class="border rounded p-3 mb-2">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="feature_social_youtube_enabled" name="social_youtube_enabled" style="width: 2.5em; height: 1.25em;">
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-youtube text-danger" style="font-size: 1.5em;"></i>
                                    </div>
                                    <div class="col">
                                        <input type="url" class="form-control form-control-sm" id="feature_social_youtube_url" name="social_youtube_url" placeholder="https://youtube.com/@yourchannel">
                                    </div>
                                </div>
                            </div>

                            <!-- Pinterest -->
                            <div class="border rounded p-3 mb-2">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="feature_social_pinterest_enabled" name="social_pinterest_enabled" style="width: 2.5em; height: 1.25em;">
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-pinterest" style="font-size: 1.5em; color: #BD081C;"></i>
                                    </div>
                                    <div class="col">
                                        <input type="url" class="form-control form-control-sm" id="feature_social_pinterest_url" name="social_pinterest_url" placeholder="https://pinterest.com/yourprofile">
                                    </div>
                                </div>
                            </div>

                            <!-- LinkedIn -->
                            <div class="border rounded p-3 mb-2">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="feature_social_linkedin_enabled" name="social_linkedin_enabled" style="width: 2.5em; height: 1.25em;">
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-linkedin text-primary" style="font-size: 1.5em;"></i>
                                    </div>
                                    <div class="col">
                                        <input type="url" class="form-control form-control-sm" id="feature_social_linkedin_url" name="social_linkedin_url" placeholder="https://linkedin.com/company/yourcompany">
                                    </div>
                                </div>
                            </div>

                            <!-- TikTok -->
                            <div class="border rounded p-3 mb-2">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="feature_social_tiktok_enabled" name="social_tiktok_enabled" style="width: 2.5em; height: 1.25em;">
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-tiktok" style="font-size: 1.5em;"></i>
                                    </div>
                                    <div class="col">
                                        <input type="url" class="form-control form-control-sm" id="feature_social_tiktok_url" name="social_tiktok_url" placeholder="https://tiktok.com/@yourhandle">
                                    </div>
                                </div>
                            </div>

                            <!-- WhatsApp Business -->
                            <div class="border rounded p-3 mb-2">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="feature_social_whatsapp_enabled" name="social_whatsapp_enabled" style="width: 2.5em; height: 1.25em;">
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-whatsapp text-success" style="font-size: 1.5em;"></i>
                                    </div>
                                    <div class="col">
                                        <input type="url" class="form-control form-control-sm" id="feature_social_whatsapp_url" name="social_whatsapp_url" placeholder="https://wa.me/1234567890">
                                        <small class="text-muted">Use wa.me link or WhatsApp Business URL</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Snapchat -->
                            <div class="border rounded p-3 mb-2">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="feature_social_snapchat_enabled" name="social_snapchat_enabled" style="width: 2.5em; height: 1.25em;">
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-snapchat" style="font-size: 1.5em; color: #FFFC00;"></i>
                                    </div>
                                    <div class="col">
                                        <input type="url" class="form-control form-control-sm" id="feature_social_snapchat_url" name="social_snapchat_url" placeholder="https://snapchat.com/add/yourhandle">
                                    </div>
                                </div>
                            </div>

                            <!-- Threads -->
                            <div class="border rounded p-3 mb-2">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="feature_social_threads_enabled" name="social_threads_enabled" style="width: 2.5em; height: 1.25em;">
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-threads" style="font-size: 1.5em;"></i>
                                    </div>
                                    <div class="col">
                                        <input type="url" class="form-control form-control-sm" id="feature_social_threads_url" name="social_threads_url" placeholder="https://threads.net/@yourhandle">
                                    </div>
                                </div>
                            </div>

                            <!-- Discord -->
                            <div class="border rounded p-3 mb-2">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="feature_social_discord_enabled" name="social_discord_enabled" style="width: 2.5em; height: 1.25em;">
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-discord" style="font-size: 1.5em; color: #5865F2;"></i>
                                    </div>
                                    <div class="col">
                                        <input type="url" class="form-control form-control-sm" id="feature_social_discord_url" name="social_discord_url" placeholder="https://discord.gg/yourserver">
                                    </div>
                                </div>
                            </div>

                            <!-- Yelp -->
                            <div class="border rounded p-3 mb-2">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="feature_social_yelp_enabled" name="social_yelp_enabled" style="width: 2.5em; height: 1.25em;">
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-yelp text-danger" style="font-size: 1.5em;"></i>
                                    </div>
                                    <div class="col">
                                        <input type="url" class="form-control form-control-sm" id="feature_social_yelp_url" name="social_yelp_url" placeholder="https://yelp.com/biz/yourbusiness">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info small mt-3 mb-3">
                            <i class="bi bi-info-circle me-1"></i>
                            <strong>Note:</strong> Social media icons will appear on the Contact Us and About Us pages. Only enabled platforms with valid URLs will be displayed.
                        </div>

                        <button type="submit" class="btn btn-prt">
                            <i class="bi bi-check-lg me-1"></i> Save Social Media Configuration
                        </button>
                    </form>
                </div>
            </div>

            <!-- Category Configuration -->
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-folder me-2"></i>Category Configuration</h5>
                    <small class="text-muted">Define which categories are used for special features</small>
                </div>
                <div class="card-body">
                    <form id="categoriesForm" onsubmit="saveCategories(event)">
                        <div class="mb-3">
                            <label class="form-label">Digital Download Categories</label>
                            <select class="form-select" id="feature_digital_download_categories" name="digital_download_categories" multiple size="6">
                                <option value="" disabled>Loading categories...</option>
                            </select>
                            <small class="text-muted">Hold Ctrl (Cmd on Mac) to select multiple categories for digital downloads</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Specialty Product Categories</label>
                            <select class="form-select" id="feature_specialty_product_categories" name="specialty_product_categories" multiple size="6">
                                <option value="" disabled>Loading categories...</option>
                            </select>
                            <small class="text-muted">Hold Ctrl (Cmd on Mac) to select multiple categories for specialty products</small>
                        </div>

                        <button type="submit" class="btn btn-prt">
                            <i class="bi bi-check-lg me-1"></i> Save Category Configuration
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Info Card -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>About Feature Configuration</h5>
                </div>
                <div class="card-body">
                    <p class="mb-3">Use these toggles to enable or disable features across your entire e-commerce system.</p>
                    <p class="mb-3"><strong>When a feature is disabled:</strong></p>
                    <ul class="mb-3">
                        <li>Hidden from frontend navigation and menus</li>
                        <li>Hidden from Account dropdown</li>
                        <li>Grayed out in admin sidebar</li>
                        <li>Hidden from frontend admin page</li>
                    </ul>
                    <p class="mb-0"><strong>Changes take effect immediately</strong> on the next page refresh.</p>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-lightning me-2"></i>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <button type="button" class="btn btn-outline-success w-100 mb-2" onclick="enableAllFeatures()">
                        <i class="bi bi-check-all me-1"></i> Enable All Features
                    </button>
                    <button type="button" class="btn btn-outline-secondary w-100" onclick="disableAllFeatures()">
                        <i class="bi bi-x-lg me-1"></i> Disable All Features
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
var API_BASE = '{{ rtrim(env("API_PUBLIC_URL", "http://localhost:8300/api/v1"), "/") }}';
var featureSettings = {};
var categoriesList = [];

document.addEventListener('DOMContentLoaded', function() {
    loadCategories();
    loadFeatures();
});

async function loadCategories() {
    try {
        var response = await fetch(API_BASE + '/categories');
        var data = await response.json();

        if (data.success && data.data) {
            categoriesList = data.data;
            populateCategoryDropdowns();
        }
    } catch (error) {
        console.error('Error loading categories:', error);
    }
}

function populateCategoryDropdowns() {
    var digitalSelect = document.getElementById('feature_digital_download_categories');
    var specialtySelect = document.getElementById('feature_specialty_product_categories');

    // Clear existing options
    digitalSelect.innerHTML = '';
    specialtySelect.innerHTML = '';

    // Sort categories by CategoryCode
    var sortedCategories = categoriesList.sort(function(a, b) {
        return a.CategoryCode - b.CategoryCode;
    });

    // Add options to both dropdowns
    sortedCategories.forEach(function(cat) {
        var levelIndent = '';
        for (var i = 0; i < cat.Level; i++) {
            levelIndent += '— ';
        }

        var optionText = cat.CategoryCode + ' - ' + levelIndent + cat.Category;

        var option1 = document.createElement('option');
        option1.value = cat.CategoryCode;
        option1.textContent = optionText;
        digitalSelect.appendChild(option1);

        var option2 = document.createElement('option');
        option2.value = cat.CategoryCode;
        option2.textContent = optionText;
        specialtySelect.appendChild(option2);
    });

    // If feature settings are already loaded, select the saved values
    if (featureSettings.digital_download_categories) {
        selectCategoriesInDropdown('feature_digital_download_categories', featureSettings.digital_download_categories);
    }
    if (featureSettings.specialty_product_categories) {
        selectCategoriesInDropdown('feature_specialty_product_categories', featureSettings.specialty_product_categories);
    }
}

function selectCategoriesInDropdown(selectId, categoriesString) {
    var select = document.getElementById(selectId);
    if (!select || !categoriesString) return;

    // Parse comma-separated category codes
    var selectedCodes = categoriesString.toString().split(',').map(function(code) {
        return code.trim();
    });

    // Select matching options
    Array.from(select.options).forEach(function(option) {
        if (selectedCodes.includes(option.value)) {
            option.selected = true;
        }
    });
}

async function loadFeatures() {
    try {
        var response = await fetch(API_BASE + '/admin/settings/features');
        var data = await response.json();

        if (data.success) {
            featureSettings = data.data;
            populateForm();
            document.getElementById('loadingFeatures').style.display = 'none';
            document.getElementById('featuresContent').style.display = '';
        } else {
            alert('Failed to load feature configuration');
        }
    } catch (error) {
        console.error('Error loading features:', error);
        // If no features exist yet, show empty form with defaults
        setDefaultValues();
        document.getElementById('loadingFeatures').style.display = 'none';
        document.getElementById('featuresContent').style.display = '';
    }
}

function populateForm() {
    // Feature toggles
    var toggles = [
        'faq_enabled', 'loyalty_enabled', 'digital_downloads_enabled',
        'gift_cards_enabled', 'wishlists_enabled',
        'blog_enabled', 'events_enabled', 'reviews_enabled', 'product_sticky_bar_enabled',
        'admin_link_enabled', 'tell_a_friend_enabled', 'newsletter_enabled'
    ];

    toggles.forEach(function(key) {
        var element = document.getElementById('feature_' + key);
        if (element) {
            var value = featureSettings[key];
            element.checked = value === true || value === '1' || value === 'true' || value === 1;
        }
    });

    // Category fields - select options in dropdowns
    if (featureSettings.digital_download_categories) {
        selectCategoriesInDropdown('feature_digital_download_categories', featureSettings.digital_download_categories);
    }
    if (featureSettings.specialty_product_categories) {
        selectCategoriesInDropdown('feature_specialty_product_categories', featureSettings.specialty_product_categories);
    }

    // Live Chat settings
    var liveChatToggles = ['live_chat_enabled', 'tawkto_enabled', 'tidio_enabled'];
    liveChatToggles.forEach(function(key) {
        var element = document.getElementById('feature_' + key);
        if (element) {
            var value = featureSettings[key];
            element.checked = value === true || value === '1' || value === 'true' || value === 1;
        }
    });

    // Live Chat text fields
    var liveChatFields = ['tawkto_property_id', 'tawkto_widget_id', 'tidio_public_key'];
    liveChatFields.forEach(function(key) {
        var element = document.getElementById('feature_' + key);
        if (element) {
            element.value = featureSettings[key] || '';
        }
    });

    // Notification settings
    var notifToggles = [
        'notifications_enabled',
        'notif_email_enabled', 'notif_sms_enabled', 'notif_push_enabled',
        'notif_delivery_enabled', 'notif_promo_enabled', 'notif_payment_enabled', 'notif_security_enabled'
    ];
    notifToggles.forEach(function(key) {
        var element = document.getElementById('feature_' + key);
        if (element) {
            var value = featureSettings[key];
            // Default to true for most notification settings if not set
            if (value === undefined || value === null) {
                element.checked = true;
            } else {
                element.checked = value === true || value === '1' || value === 'true' || value === 1;
            }
        }
    });

    // Payment Gateway settings
    var paymentToggles = [
        'payment_gateway_enabled', 'stripe_enabled', 'stripe_test_mode', 'stripe_ach_enabled',
        'braintree_enabled', 'braintree_sandbox',
        'paypal_enabled', 'paypal_sandbox',
        'square_enabled', 'square_sandbox',
        'authorizenet_enabled', 'authorizenet_sandbox'
    ];
    paymentToggles.forEach(function(key) {
        var element = document.getElementById('feature_' + key);
        if (element) {
            var value = featureSettings[key];
            element.checked = value === true || value === '1' || value === 'true' || value === 1;
        }
    });

    // Payment Gateway text fields
    var paymentFields = [
        'stripe_publishable_key', 'stripe_secret_key',
        'braintree_merchant_id', 'braintree_public_key', 'braintree_private_key',
        'paypal_client_id', 'paypal_client_secret',
        'square_application_id', 'square_access_token', 'square_location_id',
        'authorizenet_login_id', 'authorizenet_transaction_key', 'authorizenet_signature_key'
    ];
    paymentFields.forEach(function(key) {
        var element = document.getElementById('feature_' + key);
        if (element) {
            element.value = featureSettings[key] || '';
        }
    });

    // Tax Provider settings
    var taxCalcEnabled = document.getElementById('feature_tax_calculation_enabled');
    if (taxCalcEnabled) {
        var value = featureSettings['tax_calculation_enabled'];
        // Default to true if not set
        if (value === undefined || value === null) {
            taxCalcEnabled.checked = true;
        } else {
            taxCalcEnabled.checked = value === true || value === '1' || value === 'true' || value === 1;
        }
    }

    // Tax Provider selection
    var taxProvider = featureSettings['tax_provider'] || 'custom';
    var providerRadio = document.getElementById('tax_provider_' + taxProvider);
    if (providerRadio) {
        providerRadio.checked = true;
        handleTaxProviderChange(taxProvider);
    } else {
        // Default to custom
        var customRadio = document.getElementById('tax_provider_custom');
        if (customRadio) {
            customRadio.checked = true;
            handleTaxProviderChange('custom');
        }
    }

    // TaxJar settings
    var taxjarToken = document.getElementById('feature_taxjar_api_token');
    if (taxjarToken) {
        taxjarToken.value = featureSettings['taxjar_api_token'] || '';
    }
    var taxjarSandbox = document.getElementById('feature_taxjar_sandbox');
    if (taxjarSandbox) {
        var value = featureSettings['taxjar_sandbox'];
        taxjarSandbox.checked = value === true || value === '1' || value === 'true' || value === 1;
    }

    // Social Media settings
    var socialPlatforms = ['facebook', 'instagram', 'twitter', 'youtube', 'pinterest', 'linkedin', 'tiktok', 'whatsapp', 'snapchat', 'threads', 'discord', 'yelp'];

    // Master toggle
    var socialMasterToggle = document.getElementById('feature_social_media_enabled');
    if (socialMasterToggle) {
        var value = featureSettings['social_media_enabled'];
        // Default to true if not set
        if (value === undefined || value === null) {
            socialMasterToggle.checked = true;
        } else {
            socialMasterToggle.checked = value === true || value === '1' || value === 'true' || value === 1;
        }
    }

    // Individual platform toggles and URLs
    socialPlatforms.forEach(function(platform) {
        var toggle = document.getElementById('feature_social_' + platform + '_enabled');
        var urlField = document.getElementById('feature_social_' + platform + '_url');

        if (toggle) {
            var value = featureSettings['social_' + platform + '_enabled'];
            // Default some platforms to true
            var defaultEnabled = ['facebook', 'instagram', 'twitter'].includes(platform);
            if (value === undefined || value === null) {
                toggle.checked = defaultEnabled;
            } else {
                toggle.checked = value === true || value === '1' || value === 'true' || value === 1;
            }
        }

        if (urlField) {
            urlField.value = featureSettings['social_' + platform + '_url'] || '';
        }
    });

    // Update UI state
    toggleChatProviders();
    toggleNotificationSections();
    togglePaymentProviders();
    toggleTaxProviders();
    toggleSocialMediaSection();
    populateHeaderConfig();
}

function setDefaultValues() {
    // All features ON by default for Pecos River Traders
    var toggles = document.querySelectorAll('#featuresForm input[type="checkbox"]');
    toggles.forEach(function(toggle) {
        toggle.checked = true;
    });

    // Default categories - select 103 (Short Stories) if available
    selectCategoriesInDropdown('feature_digital_download_categories', '103');
    selectCategoriesInDropdown('feature_specialty_product_categories', '103');
}

async function saveFeatures(event) {
    event.preventDefault();

    var formData = {};
    var toggles = [
        'faq_enabled', 'loyalty_enabled', 'digital_downloads_enabled',
        'gift_cards_enabled', 'wishlists_enabled',
        'blog_enabled', 'events_enabled', 'reviews_enabled', 'product_sticky_bar_enabled',
        'admin_link_enabled', 'tell_a_friend_enabled', 'newsletter_enabled'
    ];

    toggles.forEach(function(key) {
        var element = document.getElementById('feature_' + key);
        formData[key] = element.checked;
    });

    try {
        var response = await fetch(API_BASE + '/admin/settings/features', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(formData)
        });

        var data = await response.json();

        if (data.success) {
            showAlert('success', 'Feature configuration saved successfully');
        } else {
            showAlert('danger', data.message || 'Failed to save features');
        }
    } catch (error) {
        console.error('Error saving features:', error);
        showAlert('danger', 'Error saving features: ' + error.message);
    }
}

async function saveCategories(event) {
    event.preventDefault();

    // Get selected values from multi-select dropdowns
    var digitalSelect = document.getElementById('feature_digital_download_categories');
    var specialtySelect = document.getElementById('feature_specialty_product_categories');

    var digitalCategories = Array.from(digitalSelect.selectedOptions).map(function(opt) {
        return opt.value;
    }).join(',');

    var specialtyCategories = Array.from(specialtySelect.selectedOptions).map(function(opt) {
        return opt.value;
    }).join(',');

    var formData = {
        digital_download_categories: digitalCategories,
        specialty_product_categories: specialtyCategories
    };

    try {
        var response = await fetch(API_BASE + '/admin/settings/features', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(formData)
        });

        var data = await response.json();

        if (data.success) {
            showAlert('success', 'Category configuration saved successfully');
        } else {
            showAlert('danger', data.message || 'Failed to save categories');
        }
    } catch (error) {
        console.error('Error saving categories:', error);
        showAlert('danger', 'Error saving categories: ' + error.message);
    }
}

function enableAllFeatures() {
    var toggles = document.querySelectorAll('#featuresForm input[type="checkbox"]');
    toggles.forEach(function(toggle) {
        toggle.checked = true;
    });
    showAlert('info', 'All features enabled. Click "Save Feature Configuration" to apply.');
}

function disableAllFeatures() {
    var toggles = document.querySelectorAll('#featuresForm input[type="checkbox"]');
    toggles.forEach(function(toggle) {
        toggle.checked = false;
    });
    showAlert('info', 'All features disabled. Click "Save Feature Configuration" to apply.');
}

function showAlert(type, message) {
    var existing = document.querySelector('.features-alert');
    if (existing) {
        existing.remove();
    }

    var alert = document.createElement('div');
    alert.className = 'alert alert-' + type + ' alert-dismissible fade show features-alert';
    alert.style.position = 'fixed';
    alert.style.top = '20px';
    alert.style.right = '20px';
    alert.style.zIndex = '9999';
    alert.style.minWidth = '300px';
    alert.innerHTML = message + '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';

    document.body.appendChild(alert);

    setTimeout(function() {
        if (alert.parentNode) {
            alert.remove();
        }
    }, 3000);
}

// Live Chat Functions
function toggleChatProviders() {
    var masterToggle = document.getElementById('feature_live_chat_enabled');
    var providersSection = document.getElementById('chatProvidersSection');

    if (masterToggle && providersSection) {
        if (masterToggle.checked) {
            providersSection.style.opacity = '1';
            providersSection.style.pointerEvents = 'auto';
        } else {
            providersSection.style.opacity = '0.5';
            providersSection.style.pointerEvents = 'none';
        }
    }
}

function handleProviderToggle(provider) {
    var tawktoToggle = document.getElementById('feature_tawkto_enabled');
    var tidioToggle = document.getElementById('feature_tidio_enabled');

    // Auto-disable logic: only one provider can be active at a time
    if (provider === 'tawkto' && tawktoToggle.checked) {
        tidioToggle.checked = false;
    } else if (provider === 'tidio' && tidioToggle.checked) {
        tawktoToggle.checked = false;
    }
}

async function saveLiveChat(event) {
    event.preventDefault();

    var formData = {
        live_chat_enabled: document.getElementById('feature_live_chat_enabled').checked,
        tawkto_enabled: document.getElementById('feature_tawkto_enabled').checked,
        tidio_enabled: document.getElementById('feature_tidio_enabled').checked,
        tawkto_property_id: document.getElementById('feature_tawkto_property_id').value,
        tawkto_widget_id: document.getElementById('feature_tawkto_widget_id').value,
        tidio_public_key: document.getElementById('feature_tidio_public_key').value
    };

    try {
        var response = await fetch(API_BASE + '/admin/settings/features', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(formData)
        });

        var data = await response.json();

        if (data.success) {
            showAlert('success', 'Live chat configuration saved successfully');
        } else {
            showAlert('danger', data.message || 'Failed to save live chat settings');
        }
    } catch (error) {
        console.error('Error saving live chat:', error);
        showAlert('danger', 'Error saving live chat settings: ' + error.message);
    }
}

// Notification Functions
function toggleNotificationSections() {
    var masterToggle = document.getElementById('feature_notifications_enabled');
    var sectionsWrapper = document.getElementById('notificationSectionsWrapper');

    if (masterToggle && sectionsWrapper) {
        if (masterToggle.checked) {
            sectionsWrapper.style.opacity = '1';
            sectionsWrapper.style.pointerEvents = 'auto';
        } else {
            sectionsWrapper.style.opacity = '0.5';
            sectionsWrapper.style.pointerEvents = 'none';
        }
    }
}

async function saveNotifications(event) {
    event.preventDefault();

    var formData = {
        notifications_enabled: document.getElementById('feature_notifications_enabled').checked,
        notif_email_enabled: document.getElementById('feature_notif_email_enabled').checked,
        notif_sms_enabled: document.getElementById('feature_notif_sms_enabled').checked,
        notif_push_enabled: document.getElementById('feature_notif_push_enabled').checked,
        notif_delivery_enabled: document.getElementById('feature_notif_delivery_enabled').checked,
        notif_promo_enabled: document.getElementById('feature_notif_promo_enabled').checked,
        notif_payment_enabled: document.getElementById('feature_notif_payment_enabled').checked,
        notif_security_enabled: document.getElementById('feature_notif_security_enabled').checked
    };

    try {
        var response = await fetch(API_BASE + '/admin/settings/features', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(formData)
        });

        var data = await response.json();

        if (data.success) {
            showAlert('success', 'Notification configuration saved successfully');
        } else {
            showAlert('danger', data.message || 'Failed to save notification settings');
        }
    } catch (error) {
        console.error('Error saving notifications:', error);
        showAlert('danger', 'Error saving notification settings: ' + error.message);
    }
}

// Payment Gateway Functions
function togglePaymentProviders() {
    var masterToggle = document.getElementById('feature_payment_gateway_enabled');
    var providersSection = document.getElementById('paymentProvidersSection');

    if (masterToggle && providersSection) {
        if (masterToggle.checked) {
            providersSection.style.opacity = '1';
            providersSection.style.pointerEvents = 'auto';
        } else {
            providersSection.style.opacity = '0.5';
            providersSection.style.pointerEvents = 'none';
        }
    }
}

function handlePaymentProviderToggle(provider) {
    // Payment providers are NOT mutually exclusive - multiple can be enabled
    // This function can be used for any provider-specific logic if needed
    console.log(provider + ' toggled');
}

// ACH toggle handler
document.addEventListener('DOMContentLoaded', function() {
    var achToggle = document.getElementById('feature_stripe_ach_enabled');
    var achInfo = document.getElementById('stripeAchInfo');

    if (achToggle && achInfo) {
        // Set initial state
        achInfo.style.display = achToggle.checked ? 'block' : 'none';

        // Handle toggle change
        achToggle.addEventListener('change', function() {
            achInfo.style.display = this.checked ? 'block' : 'none';
        });
    }
});

async function savePaymentGateway(event) {
    event.preventDefault();

    var formData = {
        payment_gateway_enabled: document.getElementById('feature_payment_gateway_enabled').checked,
        stripe_enabled: document.getElementById('feature_stripe_enabled').checked,
        stripe_publishable_key: document.getElementById('feature_stripe_publishable_key').value,
        stripe_secret_key: document.getElementById('feature_stripe_secret_key').value,
        stripe_test_mode: document.getElementById('feature_stripe_test_mode').checked,
        stripe_ach_enabled: document.getElementById('feature_stripe_ach_enabled').checked,
        braintree_enabled: document.getElementById('feature_braintree_enabled').checked,
        braintree_merchant_id: document.getElementById('feature_braintree_merchant_id').value,
        braintree_public_key: document.getElementById('feature_braintree_public_key').value,
        braintree_private_key: document.getElementById('feature_braintree_private_key').value,
        braintree_sandbox: document.getElementById('feature_braintree_sandbox').checked,
        paypal_enabled: document.getElementById('feature_paypal_enabled').checked,
        paypal_client_id: document.getElementById('feature_paypal_client_id').value,
        paypal_client_secret: document.getElementById('feature_paypal_client_secret').value,
        paypal_sandbox: document.getElementById('feature_paypal_sandbox').checked,
        square_enabled: document.getElementById('feature_square_enabled').checked,
        square_application_id: document.getElementById('feature_square_application_id').value,
        square_access_token: document.getElementById('feature_square_access_token').value,
        square_location_id: document.getElementById('feature_square_location_id').value,
        square_sandbox: document.getElementById('feature_square_sandbox').checked,
        authorizenet_enabled: document.getElementById('feature_authorizenet_enabled').checked,
        authorizenet_login_id: document.getElementById('feature_authorizenet_login_id').value,
        authorizenet_transaction_key: document.getElementById('feature_authorizenet_transaction_key').value,
        authorizenet_signature_key: document.getElementById('feature_authorizenet_signature_key').value,
        authorizenet_sandbox: document.getElementById('feature_authorizenet_sandbox').checked
    };

    try {
        var response = await fetch(API_BASE + '/admin/settings/features', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(formData)
        });

        var data = await response.json();

        if (data.success) {
            showAlert('success', 'Payment gateway configuration saved successfully');
        } else {
            showAlert('danger', data.message || 'Failed to save payment gateway settings');
        }
    } catch (error) {
        console.error('Error saving payment gateway:', error);
        showAlert('danger', 'Error saving payment gateway settings: ' + error.message);
    }
}

// Tax Provider Functions
function toggleTaxProviders() {
    var masterToggle = document.getElementById('feature_tax_calculation_enabled');
    var providersSection = document.getElementById('taxProvidersSection');

    if (masterToggle && providersSection) {
        if (masterToggle.checked) {
            providersSection.style.opacity = '1';
            providersSection.style.pointerEvents = 'auto';
        } else {
            providersSection.style.opacity = '0.5';
            providersSection.style.pointerEvents = 'none';
        }
    }
}

function handleTaxProviderChange(provider) {
    var taxjarFields = document.getElementById('taxjarFields');
    var stripeTaxWarning = document.getElementById('stripeTaxWarning');
    var stripeEnabled = document.getElementById('feature_stripe_enabled');

    // Hide all provider-specific fields first
    if (taxjarFields) taxjarFields.style.display = 'none';
    if (stripeTaxWarning) stripeTaxWarning.style.display = 'none';

    // Update visual selection
    document.querySelectorAll('.tax-provider-option').forEach(function(el) {
        el.classList.remove('border-primary', 'bg-light');
    });

    var selectedSection = document.getElementById(provider === 'custom' ? 'customTaxSection' :
                                                   provider === 'stripe' ? 'stripeTaxSection' : 'taxjarSection');
    if (selectedSection) {
        selectedSection.classList.add('border-primary', 'bg-light');
    }

    // Show provider-specific fields
    if (provider === 'taxjar') {
        if (taxjarFields) taxjarFields.style.display = 'block';
    } else if (provider === 'stripe') {
        // Show warning if Stripe payment is not enabled
        if (stripeEnabled && !stripeEnabled.checked && stripeTaxWarning) {
            stripeTaxWarning.style.display = 'block';
        }
    }
}

async function saveTaxProvider(event) {
    event.preventDefault();

    var selectedProvider = document.querySelector('input[name="tax_provider"]:checked');

    var formData = {
        tax_calculation_enabled: document.getElementById('feature_tax_calculation_enabled').checked,
        tax_provider: selectedProvider ? selectedProvider.value : 'custom',
        taxjar_api_token: document.getElementById('feature_taxjar_api_token').value,
        taxjar_sandbox: document.getElementById('feature_taxjar_sandbox').checked
    };

    // Validate TaxJar API key if TaxJar is selected
    if (formData.tax_provider === 'taxjar' && formData.tax_calculation_enabled && !formData.taxjar_api_token) {
        showAlert('warning', 'Please enter your TaxJar API token');
        return;
    }

    // Warn if Stripe Tax is selected but Stripe payment is not enabled
    var stripeEnabled = document.getElementById('feature_stripe_enabled');
    if (formData.tax_provider === 'stripe' && formData.tax_calculation_enabled && stripeEnabled && !stripeEnabled.checked) {
        if (!confirm('Stripe Tax requires Stripe Payment to be enabled. Continue anyway?')) {
            return;
        }
    }

    try {
        var response = await fetch(API_BASE + '/admin/settings/features', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(formData)
        });

        var data = await response.json();

        if (data.success) {
            showAlert('success', 'Tax provider configuration saved successfully');
        } else {
            showAlert('danger', data.message || 'Failed to save tax provider settings');
        }
    } catch (error) {
        console.error('Error saving tax provider:', error);
        showAlert('danger', 'Error saving tax provider settings: ' + error.message);
    }
}

// Social Media Functions
function toggleSocialMediaSection() {
    var masterToggle = document.getElementById('feature_social_media_enabled');
    var section = document.getElementById('socialMediaSection');

    if (masterToggle && section) {
        if (masterToggle.checked) {
            section.style.opacity = '1';
            section.style.pointerEvents = 'auto';
        } else {
            section.style.opacity = '0.5';
            section.style.pointerEvents = 'none';
        }
    }
}

// Header Configuration Functions
function previewLogo(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('newLogoPreview').src = e.target.result;
            document.getElementById('newLogoPreviewContainer').style.display = 'block';
            document.getElementById('previewLogo').src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function syncColorInput(colorInputId, value) {
    var colorInput = document.getElementById(colorInputId);
    var textInput = document.getElementById(colorInputId + '_text');

    if (value.match(/^#[0-9A-Fa-f]{6}$/)) {
        colorInput.value = value;
        textInput.value = value.toUpperCase();
        updateHeaderPreview();
        updateThemeColorPreview();
    }
}

// Set up color input sync
document.addEventListener('DOMContentLoaded', function() {
    var colorInputs = document.querySelectorAll('input[type="color"]');
    colorInputs.forEach(function(input) {
        input.addEventListener('input', function() {
            var textInput = document.getElementById(this.id + '_text');
            if (textInput) {
                textInput.value = this.value.toUpperCase();
            }
            updateHeaderPreview();
            updateThemeColorPreview();
        });
    });
});

function toggleAnnouncementFields() {
    var el = document.getElementById('header_announcement_enabled');
    if (!el) return; // Elements don't exist yet
    var enabled = el.checked;
    var fields = document.getElementById('announcementFields');
    var preview = document.getElementById('announcementPreview');
    if (fields) fields.style.display = enabled ? 'block' : 'none';
    if (preview) preview.style.display = enabled ? 'block' : 'none';
}

function updateHeaderPreview() {
    var preview = document.getElementById('headerPreview');
    if (!preview) return; // Elements don't exist yet

    var bgColorEl = document.getElementById('header_bg_color');
    var textColorEl = document.getElementById('header_text_color');
    var hoverColorEl = document.getElementById('header_hover_color');
    var announcementBgEl = document.getElementById('header_announcement_bg');
    var announcementTextColorEl = document.getElementById('header_announcement_text_color');
    var announcementTextEl = document.getElementById('header_announcement_text');

    if (!bgColorEl || !textColorEl) return;

    var bgColor = bgColorEl.value;
    var textColor = textColorEl.value;
    var hoverColor = hoverColorEl ? hoverColorEl.value : '#FFD700';
    var announcementBg = announcementBgEl ? announcementBgEl.value : '#C41E3A';
    var announcementTextColor = announcementTextColorEl ? announcementTextColorEl.value : '#FFFFFF';
    var announcementText = announcementTextEl ? (announcementTextEl.value || 'Free shipping on orders over $50!') : 'Free shipping on orders over $50!';

    var announcementPreview = document.getElementById('announcementPreview');

    preview.style.background = bgColor;
    var nav = preview.querySelector('nav');
    if (nav) {
        nav.style.color = textColor;
        var flex = nav.querySelector('.d-flex');
        if (flex) flex.style.color = textColor;
    }

    // Update hover color preview
    var cartSpan = preview.querySelector('nav span:last-child');
    if (cartSpan) cartSpan.style.color = hoverColor;

    // Update announcement bar
    if (announcementPreview) {
        announcementPreview.style.background = announcementBg;
        announcementPreview.style.color = announcementTextColor;
        announcementPreview.textContent = announcementText;
    }
}

function updateThemeColorPreview() {
    var primaryEl = document.getElementById('theme_primary_color');
    if (!primaryEl) return; // Elements don't exist yet

    var primary = primaryEl.value;
    var secondaryEl = document.getElementById('theme_secondary_color');
    var accentEl = document.getElementById('theme_accent_color');
    var textDarkEl = document.getElementById('theme_text_dark');
    var bgColorEl = document.getElementById('theme_bg_color');

    var secondary = secondaryEl ? secondaryEl.value : '#C41E3A';
    var accent = accentEl ? accentEl.value : '#FFD700';
    var textDark = textDarkEl ? textDarkEl.value : '#333333';
    var bgColor = bgColorEl ? bgColorEl.value : '#F5F5F5';

    var previewDivs = document.querySelectorAll('#themeColorPreview > div');
    if (previewDivs.length >= 5) {
        previewDivs[0].style.background = primary;
        previewDivs[1].style.background = secondary;
        previewDivs[2].style.background = accent;
        previewDivs[2].style.color = isLightColor(accent) ? '#333' : '#fff';
        previewDivs[3].style.background = textDark;
        previewDivs[4].style.background = bgColor;
    }
}

function isLightColor(color) {
    var hex = color.replace('#', '');
    var r = parseInt(hex.substr(0, 2), 16);
    var g = parseInt(hex.substr(2, 2), 16);
    var b = parseInt(hex.substr(4, 2), 16);
    var brightness = (r * 299 + g * 587 + b * 114) / 1000;
    return brightness > 155;
}

async function saveHeaderConfig(event) {
    event.preventDefault();

    var formData = {
        // Logo settings
        header_logo_height: document.getElementById('header_logo_height').value,
        header_site_title: document.getElementById('header_site_title').value,
        // Header styling
        header_bg_color: document.getElementById('header_bg_color').value,
        header_text_color: document.getElementById('header_text_color').value,
        header_hover_color: document.getElementById('header_hover_color').value,
        header_style: document.getElementById('header_style').value,
        // Behavior
        header_sticky: document.getElementById('header_sticky').checked,
        header_shadow: document.getElementById('header_shadow').checked,
        // Announcement bar
        header_announcement_enabled: document.getElementById('header_announcement_enabled').checked,
        header_announcement_text: document.getElementById('header_announcement_text').value,
        header_announcement_bg: document.getElementById('header_announcement_bg').value,
        header_announcement_text_color: document.getElementById('header_announcement_text_color').value,
        // Theme colors
        theme_primary_color: document.getElementById('theme_primary_color').value,
        theme_secondary_color: document.getElementById('theme_secondary_color').value,
        theme_accent_color: document.getElementById('theme_accent_color').value,
        theme_text_dark: document.getElementById('theme_text_dark').value,
        theme_text_light: document.getElementById('theme_text_light').value,
        theme_bg_color: document.getElementById('theme_bg_color').value
    };

    // Handle logo upload separately if file selected
    var logoFile = document.getElementById('header_logo_file').files[0];
    if (logoFile) {
        // For now, we'll just save the setting to use a custom logo
        // Actual file upload would need a separate endpoint
        formData.header_logo_custom = true;
        showAlert('info', 'Logo upload requires server-side implementation. Settings saved.');
    }

    try {
        var response = await fetch(API_BASE + '/admin/settings/features', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(formData)
        });

        var data = await response.json();

        if (data.success) {
            showAlert('success', 'Header configuration saved successfully');
        } else {
            showAlert('danger', data.message || 'Failed to save header configuration');
        }
    } catch (error) {
        console.error('Error saving header config:', error);
        showAlert('danger', 'Error saving header configuration: ' + error.message);
    }
}

function populateHeaderConfig() {
    // Helper to safely set element value
    function setVal(id, value) {
        var el = document.getElementById(id);
        if (el) el.value = value;
    }
    function setChecked(id, value) {
        var el = document.getElementById(id);
        if (el) el.checked = value;
    }

    // Logo settings
    var logoHeight = featureSettings.header_logo_height || '60';
    setVal('header_logo_height', logoHeight);
    setVal('header_site_title', featureSettings.header_site_title || 'Pecos River Traders');

    // Header styling
    setVal('header_bg_color', featureSettings.header_bg_color || '#8B4513');
    setVal('header_bg_color_text', (featureSettings.header_bg_color || '#8B4513').toUpperCase());
    setVal('header_text_color', featureSettings.header_text_color || '#FFFFFF');
    setVal('header_text_color_text', (featureSettings.header_text_color || '#FFFFFF').toUpperCase());
    setVal('header_hover_color', featureSettings.header_hover_color || '#FFD700');
    setVal('header_hover_color_text', (featureSettings.header_hover_color || '#FFD700').toUpperCase());
    setVal('header_style', featureSettings.header_style || 'solid');

    // Behavior
    var stickyVal = featureSettings.header_sticky;
    setChecked('header_sticky', stickyVal === undefined || stickyVal === null ? true : (stickyVal === true || stickyVal === '1' || stickyVal === 'true'));
    var shadowVal = featureSettings.header_shadow;
    setChecked('header_shadow', shadowVal === undefined || shadowVal === null ? true : (shadowVal === true || shadowVal === '1' || shadowVal === 'true'));

    // Announcement bar
    var announcementEnabled = featureSettings.header_announcement_enabled;
    setChecked('header_announcement_enabled', announcementEnabled === true || announcementEnabled === '1' || announcementEnabled === 'true');
    setVal('header_announcement_text', featureSettings.header_announcement_text || '');
    setVal('header_announcement_bg', featureSettings.header_announcement_bg || '#C41E3A');
    setVal('header_announcement_bg_text', (featureSettings.header_announcement_bg || '#C41E3A').toUpperCase());
    setVal('header_announcement_text_color', featureSettings.header_announcement_text_color || '#FFFFFF');
    setVal('header_announcement_text_color_text', (featureSettings.header_announcement_text_color || '#FFFFFF').toUpperCase());

    // Theme colors
    setVal('theme_primary_color', featureSettings.theme_primary_color || '#8B4513');
    setVal('theme_primary_color_text', (featureSettings.theme_primary_color || '#8B4513').toUpperCase());
    setVal('theme_secondary_color', featureSettings.theme_secondary_color || '#C41E3A');
    setVal('theme_secondary_color_text', (featureSettings.theme_secondary_color || '#C41E3A').toUpperCase());
    setVal('theme_accent_color', featureSettings.theme_accent_color || '#FFD700');
    setVal('theme_accent_color_text', (featureSettings.theme_accent_color || '#FFD700').toUpperCase());
    setVal('theme_text_dark', featureSettings.theme_text_dark || '#333333');
    setVal('theme_text_dark_text', (featureSettings.theme_text_dark || '#333333').toUpperCase());
    setVal('theme_text_light', featureSettings.theme_text_light || '#FFFFFF');
    setVal('theme_text_light_text', (featureSettings.theme_text_light || '#FFFFFF').toUpperCase());
    setVal('theme_bg_color', featureSettings.theme_bg_color || '#F5F5F5');
    setVal('theme_bg_color_text', (featureSettings.theme_bg_color || '#F5F5F5').toUpperCase());

    // Update previews (only if functions exist and elements present)
    if (typeof toggleAnnouncementFields === 'function') toggleAnnouncementFields();
    if (typeof updateHeaderPreview === 'function') updateHeaderPreview();
    if (typeof updateThemeColorPreview === 'function') updateThemeColorPreview();
}

async function saveSocialMedia(event) {
    event.preventDefault();

    var platforms = ['facebook', 'instagram', 'twitter', 'youtube', 'pinterest', 'linkedin', 'tiktok', 'whatsapp', 'snapchat', 'threads', 'discord', 'yelp'];

    var formData = {
        social_media_enabled: document.getElementById('feature_social_media_enabled').checked
    };

    platforms.forEach(function(platform) {
        formData['social_' + platform + '_enabled'] = document.getElementById('feature_social_' + platform + '_enabled').checked;
        formData['social_' + platform + '_url'] = document.getElementById('feature_social_' + platform + '_url').value;
    });

    try {
        var response = await fetch(API_BASE + '/admin/settings/features', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(formData)
        });

        var data = await response.json();

        if (data.success) {
            showAlert('success', 'Social media configuration saved successfully');
        } else {
            showAlert('danger', data.message || 'Failed to save social media settings');
        }
    } catch (error) {
        console.error('Error saving social media:', error);
        showAlert('danger', 'Error saving social media settings: ' + error.message);
    }
}
</script>
@endpush
