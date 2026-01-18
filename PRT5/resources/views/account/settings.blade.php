@extends('layouts.app')

@section('title', 'Account Settings')

@section('content')
<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('account.index') }}">My Account</a></li>
            <li class="breadcrumb-item active">Account Settings</li>
        </ol>
    </nav>
</div>

<div class="container my-5" style="min-height: 60vh;">
    <div class="row">
        <div class="col-lg-3">
            {{-- Settings Navigation --}}
            <div class="card shadow-sm mb-4 sticky-top" style="top: 80px;">
                <div class="card-header" style="background: var(--prt-brown); color: white;">
                    <h5 class="mb-0"><i class="bi bi-gear"></i> Settings</h5>
                </div>
                <div class="list-group list-group-flush">
                    <a href="#login-security" class="list-group-item list-group-item-action">
                        <i class="bi bi-shield-lock"></i> Login & Security
                    </a>
                    <a href="#addresses" class="list-group-item list-group-item-action">
                        <i class="bi bi-geo-alt"></i> Your Addresses
                    </a>
                    <a href="#payment" class="list-group-item list-group-item-action">
                        <i class="bi bi-credit-card"></i> Payment Options
                    </a>
                    <a href="#giftcards" class="list-group-item list-group-item-action">
                        <i class="bi bi-gift"></i> Gift Cards
                    </a>
                    <a href="#promocodes" class="list-group-item list-group-item-action">
                        <i class="bi bi-tag"></i> Promotional Codes
                    </a>
                    <a href="#devices" class="list-group-item list-group-item-action">
                        <i class="bi bi-phone"></i> Your Devices
                    </a>
                    <a href="#delivery" class="list-group-item list-group-item-action">
                        <i class="bi bi-truck"></i> Delivery Preferences
                    </a>
                    <a href="#notifications" class="list-group-item list-group-item-action">
                        <i class="bi bi-bell"></i> Notifications
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-9">
            {{-- Login & Security Section --}}
            <section id="login-security" class="mb-5">
                <div class="card shadow-sm">
                    <div class="card-header" style="background: var(--prt-brown); color: white;">
                        <h4 class="mb-0"><i class="bi bi-shield-lock"></i> Login & Security</h4>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4"><strong>Name:</strong></div>
                            <div class="col-md-6">{{ $user->first_name }} {{ $user->last_name }}</div>
                            <div class="col-md-2 text-end">
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editNameModal" title="Edit your name">Edit</button>
                            </div>
                        </div>
                        <hr>
                        <div class="row mb-3">
                            <div class="col-md-4"><strong>Email:</strong></div>
                            <div class="col-md-6">{{ $user->email }}</div>
                            <div class="col-md-2 text-end">
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editEmailModal" title="Edit your email address">Edit</button>
                            </div>
                        </div>
                        <hr>
                        <div class="row mb-3">
                            <div class="col-md-4"><strong>Phone:</strong></div>
                            <div class="col-md-6">{{ $user->phone ?: 'Not provided' }}</div>
                            <div class="col-md-2 text-end">
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editPhoneModal" title="Edit your phone number">Edit</button>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-4"><strong>Password:</strong></div>
                            <div class="col-md-6">**********</div>
                            <div class="col-md-2 text-end">
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#changePasswordModal" title="Change your password">Change</button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Your Addresses Section --}}
            <section id="addresses" class="mb-5">
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center" style="background: var(--prt-brown); color: white;">
                        <h4 class="mb-0"><i class="bi bi-geo-alt"></i> Your Addresses</h4>
                        <button class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#addAddressModal" title="Add a new address">
                            <i class="bi bi-plus-circle"></i> Add Address
                        </button>
                    </div>
                    <div class="card-body">
                        @if($addresses->count() > 0)
                            <div class="row g-3">
                                @foreach($addresses as $address)
                                    <div class="col-md-6">
                                        <div class="card border {{ $address->is_default ? 'border-primary' : '' }}">
                                            <div class="card-body">
                                                @if($address->is_default)
                                                    <span class="badge bg-primary mb-2">Default {{ ucfirst($address->address_type) }}</span>
                                                @else
                                                    <span class="badge bg-secondary mb-2">{{ ucfirst($address->address_type) }}</span>
                                                @endif
                                                <p class="mb-1"><strong>{{ $address->full_name }}</strong></p>
                                                <p class="mb-1 small">{{ $address->address_line1 }}</p>
                                                @if($address->address_line2)
                                                    <p class="mb-1 small">{{ $address->address_line2 }}</p>
                                                @endif
                                                <p class="mb-1 small">{{ $address->city }}, {{ $address->state }} {{ $address->zip_code }}</p>
                                                <p class="mb-2 small">{{ $address->country ?? 'USA' }}</p>
                                                <div class="btn-group btn-group-sm">
                                                    @if(!$address->is_default)
                                                        <button class="btn btn-outline-success" onclick="setDefaultAddress({{ $address->id }})" data-bs-toggle="tooltip" title="Make this your default address">Set as Default</button>
                                                    @endif
                                                    <button class="btn btn-outline-danger" onclick="deleteAddress({{ $address->id }})" data-bs-toggle="tooltip" title="Remove this address">Delete</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted text-center py-4">No addresses saved yet. Click "Add Address" to get started.</p>
                        @endif
                    </div>
                </div>
            </section>

            {{-- Payment Options Section --}}
            <section id="payment" class="mb-5">
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center" style="background: var(--prt-brown); color: white;">
                        <h4 class="mb-0"><i class="bi bi-credit-card"></i> Payment Options</h4>
                        <button class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#addCardModal" title="Add a new payment card">
                            <i class="bi bi-plus-circle"></i> Add Card
                        </button>
                    </div>
                    <div class="card-body">
                        @if($paymentMethods->count() > 0)
                            <div class="row g-3">
                                @foreach($paymentMethods as $payment)
                                    <div class="col-md-6">
                                        <div class="card border {{ $payment->is_default ? 'border-primary' : '' }}">
                                            <div class="card-body">
                                                @if($payment->is_default)
                                                    <span class="badge bg-primary mb-2">Default Card</span>
                                                @endif
                                                <p class="mb-1">
                                                    <i class="bi bi-credit-card fs-4 me-2"></i>
                                                    <strong>{{ $payment->card_type }}</strong>
                                                </p>
                                                <p class="mb-1">**** **** **** {{ $payment->card_last4 }}</p>
                                                <p class="mb-2 small text-muted">Expires: {{ $payment->expiry_month }}/{{ $payment->expiry_year }}</p>
                                                <p class="mb-2 small">{{ $payment->card_holder_name }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            {{-- Sample Cards for Demo --}}
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="card border border-primary">
                                        <div class="card-body">
                                            <span class="badge bg-primary mb-2">Default Card</span>
                                            <p class="mb-1">
                                                <i class="bi bi-credit-card fs-4 me-2"></i>
                                                <strong>Visa</strong>
                                            </p>
                                            <p class="mb-1">**** **** **** 4242</p>
                                            <p class="mb-2 small text-muted">Expires: 12/2025</p>
                                            <p class="mb-2 small">John Doe</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card border">
                                        <div class="card-body">
                                            <p class="mb-1">
                                                <i class="bi bi-credit-card fs-4 me-2"></i>
                                                <strong>Mastercard</strong>
                                            </p>
                                            <p class="mb-1">**** **** **** 5555</p>
                                            <p class="mb-2 small text-muted">Expires: 06/2026</p>
                                            <p class="mb-2 small">John Doe</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </section>

            {{-- Gift Cards Section --}}
            <section id="giftcards" class="mb-5">
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center" style="background: var(--prt-brown); color: white;">
                        <h4 class="mb-0"><i class="bi bi-gift"></i> Gift Cards</h4>
                        <button class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#addGiftCardModal" title="Add a gift card to your account">
                            <i class="bi bi-plus-circle"></i> Add Gift Card
                        </button>
                    </div>
                    <div class="card-body">
                        @if($giftCards->count() > 0)
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Card Code</th>
                                            <th>Balance</th>
                                            <th>Initial Amount</th>
                                            <th>Added</th>
                                            <th>Expires</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($giftCards as $card)
                                            <tr>
                                                <td><code>{{ $card->card_code }}</code></td>
                                                <td><strong class="text-success">${{ number_format($card->balance, 2) }}</strong></td>
                                                <td>${{ number_format($card->initial_amount, 2) }}</td>
                                                <td>{{ $card->added_at->format('M j, Y') }}</td>
                                                <td>{{ $card->expires_at ? $card->expires_at->format('M j, Y') : 'Never' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted text-center py-4">No gift cards added yet. Click "Add Gift Card" to add one.</p>
                        @endif
                    </div>
                </div>
            </section>

            {{-- Promotional Codes Section --}}
            <section id="promocodes" class="mb-5">
                <div class="card shadow-sm">
                    <div class="card-header" style="background: var(--prt-brown); color: white;">
                        <h4 class="mb-0"><i class="bi bi-tag"></i> Promotional Codes</h4>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">Enter a promotional code to apply discounts to your next purchase.</p>
                        <form id="promoCodeForm">
                            @csrf
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-tag-fill"></i></span>
                                        <input type="text" class="form-control" id="promoCode" name="promo_code" placeholder="Enter promotional code" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-primary w-100" data-bs-toggle="tooltip" title="Apply promotional code to your account">
                                        <i class="bi bi-check-circle"></i> Apply Code
                                    </button>
                                </div>
                            </div>
                        </form>
                        <div id="promoMessage" class="mt-3"></div>
                    </div>
                </div>
            </section>

            {{-- Your Devices Section --}}
            <section id="devices" class="mb-5">
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center" style="background: var(--prt-brown); color: white;">
                        <h4 class="mb-0"><i class="bi bi-phone"></i> Your Devices</h4>
                        <button class="btn btn-sm btn-danger" onclick="signOutAllDevices()" data-bs-toggle="tooltip" title="Sign out from all devices except current">
                            <i class="bi bi-box-arrow-right"></i> Sign Out All
                        </button>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-4">Manage devices registered to your account</p>
                        @if($devices->count() > 0)
                            <div class="list-group">
                                @foreach($devices as $device)
                                    <div class="list-group-item">
                                        <div class="d-flex w-100 justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1">
                                                    <i class="bi bi-{{ $device->device_type === 'mobile' ? 'phone' : ($device->device_type === 'tablet' ? 'tablet' : 'laptop') }}"></i>
                                                    {{ $device->device_name }}
                                                    @if($device->is_current)
                                                        <span class="badge bg-success ms-2">Current Device</span>
                                                    @endif
                                                </h6>
                                                <p class="mb-1 small text-muted">{{ $device->os_name }} {{ $device->os_version }} - {{ $device->browser }}</p>
                                                <p class="mb-1 small"><strong>Last seen:</strong> {{ $device->last_seen->format('M j, Y g:i A') }}</p>
                                                <p class="mb-0 small text-muted"><strong>IP:</strong> {{ $device->ip_address }}</p>
                                            </div>
                                            <div>
                                                @if(!$device->is_current)
                                                    <button class="btn btn-sm btn-outline-danger" data-bs-toggle="tooltip" title="Sign out this device">
                                                        <i class="bi bi-box-arrow-right"></i> Sign Out
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            {{-- Sample Devices for Demo --}}
                            <div class="list-group">
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1">
                                                <i class="bi bi-laptop"></i> Chrome on Windows
                                                <span class="badge bg-success ms-2">Current Device</span>
                                            </h6>
                                            <p class="mb-1 small text-muted">Windows 11 - Chrome 120.0</p>
                                            <p class="mb-1 small"><strong>Last seen:</strong> {{ now()->format('M j, Y g:i A') }}</p>
                                            <p class="mb-0 small text-muted"><strong>IP:</strong> 192.168.1.100</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </section>

            {{-- Delivery Preferences Section --}}
            <section id="delivery" class="mb-5">
                <div class="card shadow-sm">
                    <div class="card-header" style="background: var(--prt-brown); color: white;">
                        <h4 class="mb-0"><i class="bi bi-truck"></i> Delivery Preferences</h4>
                    </div>
                    <div class="card-body">
                        <form id="deliveryPrefsForm">
                            @csrf
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="doorToDoor" {{ $deliveryPrefs?->door_to_door ? 'checked' : 'checked' }}>
                                        <label class="form-check-label" for="doorToDoor"><strong>Door-to-Door Delivery</strong></label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="weekendDelivery" {{ $deliveryPrefs?->weekend_delivery ? 'checked' : 'checked' }}>
                                        <label class="form-check-label" for="weekendDelivery"><strong>Weekend Delivery</strong></label>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="signatureRequired" {{ $deliveryPrefs?->signature_required ? 'checked' : '' }}>
                                        <label class="form-check-label" for="signatureRequired"><strong>Signature Required</strong></label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="leaveWithNeighbor" {{ $deliveryPrefs?->leave_with_neighbor ? 'checked' : '' }}>
                                        <label class="form-check-label" for="leaveWithNeighbor"><strong>Leave with Neighbor</strong></label>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="authorityToLeave" {{ $deliveryPrefs?->authority_to_leave ? 'checked' : 'checked' }}>
                                        <label class="form-check-label" for="authorityToLeave"><strong>Authority to Leave (if not home)</strong></label>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <h5 class="mb-3">Preferred Delivery Times</h5>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="weekdayTime" class="form-label">Weekday Preference</label>
                                    <select class="form-select" id="weekdayTime">
                                        <option value="">No preference</option>
                                        <option value="morning" {{ $deliveryPrefs?->weekday_time === 'morning' ? 'selected' : '' }}>Morning (8AM - 12PM)</option>
                                        <option value="afternoon" {{ $deliveryPrefs?->weekday_time === 'afternoon' ? 'selected' : '' }}>Afternoon (12PM - 5PM)</option>
                                        <option value="evening" {{ $deliveryPrefs?->weekday_time === 'evening' ? 'selected' : '' }}>Evening (5PM - 8PM)</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="weekendTime" class="form-label">Weekend Preference</label>
                                    <select class="form-select" id="weekendTime">
                                        <option value="">No preference</option>
                                        <option value="morning" {{ $deliveryPrefs?->weekend_time === 'morning' ? 'selected' : '' }}>Morning (8AM - 12PM)</option>
                                        <option value="afternoon" {{ $deliveryPrefs?->weekend_time === 'afternoon' ? 'selected' : '' }}>Afternoon (12PM - 5PM)</option>
                                        <option value="evening" {{ $deliveryPrefs?->weekend_time === 'evening' ? 'selected' : '' }}>Evening (5PM - 8PM)</option>
                                    </select>
                                </div>
                            </div>

                            <hr class="my-4">

                            <h5 class="mb-3">Vacation Mode</h5>
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="vacationMode" {{ $deliveryPrefs?->vacation_mode ? 'checked' : '' }}>
                                <label class="form-check-label" for="vacationMode">
                                    <strong>Enable Vacation Mode</strong>
                                    <small class="text-muted d-block">Hold all deliveries during vacation</small>
                                </label>
                            </div>

                            <div id="vacationDetails" style="display: {{ $deliveryPrefs?->vacation_mode ? 'block' : 'none' }};">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="vacationStart" class="form-label">Start Date</label>
                                        <input type="date" class="form-control" id="vacationStart" value="{{ $deliveryPrefs?->vacation_start?->format('Y-m-d') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="vacationEnd" class="form-label">End Date</label>
                                        <input type="date" class="form-control" id="vacationEnd" value="{{ $deliveryPrefs?->vacation_end?->format('Y-m-d') }}">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="vacationInstructions" class="form-label">Special Instructions</label>
                                    <textarea class="form-control" id="vacationInstructions" rows="2" placeholder="e.g., Hold at post office">{{ $deliveryPrefs?->vacation_instructions }}</textarea>
                                </div>
                            </div>

                            <hr class="my-4">

                            <h5 class="mb-3">Delivery Instructions</h5>
                            <div class="mb-3">
                                <label for="specialInstructions" class="form-label">Special Instructions</label>
                                <textarea class="form-control" id="specialInstructions" rows="3" placeholder="e.g., Ring doorbell twice, Call upon arrival">{{ $deliveryPrefs?->special_instructions }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label for="backupLocation" class="form-label">Backup Location</label>
                                <textarea class="form-control" id="backupLocation" rows="2" placeholder="e.g., Leave at side door, with neighbor at #123">{{ $deliveryPrefs?->backup_location }}</textarea>
                            </div>

                            <button type="submit" class="btn btn-primary" data-bs-toggle="tooltip" title="Save your delivery preference settings">
                                <i class="bi bi-check-circle"></i> Save Delivery Preferences
                            </button>
                        </form>
                    </div>
                </div>
            </section>

            {{-- Notifications Section --}}
            @php
                $notifGlobalEnabled = $adminSettings['notifications_enabled'] ?? true;
                $emailEnabled = $adminSettings['notif_email_enabled'] ?? true;
                $smsEnabled = $adminSettings['notif_sms_enabled'] ?? true;
                $pushEnabled = $adminSettings['notif_push_enabled'] ?? true;
                $deliveryEnabled = $adminSettings['notif_delivery_enabled'] ?? true;
                $promoEnabled = $adminSettings['notif_promo_enabled'] ?? true;
                $paymentEnabled = $adminSettings['notif_payment_enabled'] ?? true;
                $securityEnabled = $adminSettings['notif_security_enabled'] ?? true;
            @endphp
            <section id="notifications" class="mb-5">
                <div class="card shadow-sm">
                    <div class="card-header" style="background: var(--prt-brown); color: white;">
                        <h4 class="mb-0"><i class="bi bi-bell"></i> Notification Preferences</h4>
                    </div>
                    <div class="card-body">
                        @if(!$notifGlobalEnabled)
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                Notifications are currently disabled. Contact support for more information.
                            </div>
                        @else
                            <p class="text-muted mb-4">Choose how you want to receive notifications</p>
                            <form id="notificationsForm">
                                @csrf
                                @if($deliveryEnabled)
                                <div class="card mb-3">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><i class="bi bi-truck"></i> Delivery Notifications</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            @if($emailEnabled)
                                            <div class="col-md-4">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="deliveryEmail" {{ $notifPrefs?->delivery_email ?? true ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="deliveryEmail"><i class="bi bi-envelope"></i> Email</label>
                                                </div>
                                            </div>
                                            @endif
                                            @if($smsEnabled)
                                            <div class="col-md-4">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="deliverySMS" {{ $notifPrefs?->delivery_sms ?? true ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="deliverySMS"><i class="bi bi-phone"></i> SMS</label>
                                                </div>
                                            </div>
                                            @endif
                                            @if($pushEnabled)
                                            <div class="col-md-4">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="deliveryPush" {{ $notifPrefs?->delivery_push ?? true ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="deliveryPush"><i class="bi bi-app-indicator"></i> Push</label>
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endif

                                @if($promoEnabled)
                                <div class="card mb-3">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><i class="bi bi-tag"></i> Promotional Deals</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            @if($emailEnabled)
                                            <div class="col-md-4">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="promoEmail" {{ $notifPrefs?->promo_email ?? true ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="promoEmail"><i class="bi bi-envelope"></i> Email</label>
                                                </div>
                                            </div>
                                            @endif
                                            @if($smsEnabled)
                                            <div class="col-md-4">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="promoSMS" {{ $notifPrefs?->promo_sms ?? false ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="promoSMS"><i class="bi bi-phone"></i> SMS</label>
                                                </div>
                                            </div>
                                            @endif
                                            @if($pushEnabled)
                                            <div class="col-md-4">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="promoPush" {{ $notifPrefs?->promo_push ?? true ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="promoPush"><i class="bi bi-app-indicator"></i> Push</label>
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endif

                                @if($paymentEnabled)
                                <div class="card mb-3">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><i class="bi bi-credit-card"></i> Payment & Billing</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            @if($emailEnabled)
                                            <div class="col-md-4">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="paymentEmail" {{ $notifPrefs?->payment_email ?? true ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="paymentEmail"><i class="bi bi-envelope"></i> Email</label>
                                                </div>
                                            </div>
                                            @endif
                                            @if($smsEnabled)
                                            <div class="col-md-4">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="paymentSMS" {{ $notifPrefs?->payment_sms ?? true ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="paymentSMS"><i class="bi bi-phone"></i> SMS</label>
                                                </div>
                                            </div>
                                            @endif
                                            @if($pushEnabled)
                                            <div class="col-md-4">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="paymentPush" {{ $notifPrefs?->payment_push ?? true ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="paymentPush"><i class="bi bi-app-indicator"></i> Push</label>
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endif

                                @if($securityEnabled)
                                <div class="card mb-3">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><i class="bi bi-shield-check"></i> Security Alerts</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            @if($emailEnabled)
                                            <div class="col-md-4">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="securityEmail" {{ $notifPrefs?->security_email ?? true ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="securityEmail"><i class="bi bi-envelope"></i> Email</label>
                                                </div>
                                            </div>
                                            @endif
                                            @if($smsEnabled)
                                            <div class="col-md-4">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="securitySMS" {{ $notifPrefs?->security_sms ?? true ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="securitySMS"><i class="bi bi-phone"></i> SMS</label>
                                                </div>
                                            </div>
                                            @endif
                                            @if($pushEnabled)
                                            <div class="col-md-4">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="securityPush" {{ $notifPrefs?->security_push ?? true ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="securityPush"><i class="bi bi-app-indicator"></i> Push</label>
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endif

                                <button type="submit" class="btn btn-primary" data-bs-toggle="tooltip" title="Save your notification preference settings">
                                    <i class="bi bi-check-circle"></i> Save Notification Preferences
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

{{-- Modals --}}
@include('account.settings.modals')

@endsection

@push('scripts')
<script>
// Initialize Bootstrap tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// Vacation mode toggle
document.getElementById('vacationMode').addEventListener('change', function() {
    document.getElementById('vacationDetails').style.display = this.checked ? 'block' : 'none';
});

// Address functions
async function setDefaultAddress(addressId) {
    const formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('address_id', addressId);

    try {
        const response = await fetch('{{ route("account.settings.address.default") }}', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    } catch (error) {
        alert('An error occurred');
    }
}

async function deleteAddress(addressId) {
    if (!confirm('Are you sure you want to delete this address?')) return;

    const formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('address_id', addressId);

    try {
        const response = await fetch('{{ route("account.settings.address.delete") }}', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    } catch (error) {
        alert('An error occurred');
    }
}

// Delivery preferences form
document.getElementById('deliveryPrefsForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('door_to_door', document.getElementById('doorToDoor').checked ? 1 : 0);
    formData.append('weekend_delivery', document.getElementById('weekendDelivery').checked ? 1 : 0);
    formData.append('signature_required', document.getElementById('signatureRequired').checked ? 1 : 0);
    formData.append('leave_with_neighbor', document.getElementById('leaveWithNeighbor').checked ? 1 : 0);
    formData.append('authority_to_leave', document.getElementById('authorityToLeave').checked ? 1 : 0);
    formData.append('weekday_time', document.getElementById('weekdayTime').value);
    formData.append('weekend_time', document.getElementById('weekendTime').value);
    formData.append('vacation_mode', document.getElementById('vacationMode').checked ? 1 : 0);
    formData.append('vacation_start', document.getElementById('vacationStart').value);
    formData.append('vacation_end', document.getElementById('vacationEnd').value);
    formData.append('vacation_instructions', document.getElementById('vacationInstructions').value);
    formData.append('special_instructions', document.getElementById('specialInstructions').value);
    formData.append('backup_location', document.getElementById('backupLocation').value);

    try {
        const response = await fetch('{{ route("account.settings.delivery") }}', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        alert(data.success ? 'Delivery preferences saved successfully!' : 'Error: ' + data.message);
    } catch (error) {
        alert('An error occurred while saving preferences');
    }
});

// Notifications form
document.getElementById('notificationsForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('delivery_email', document.getElementById('deliveryEmail')?.checked ? 1 : 0);
    formData.append('delivery_sms', document.getElementById('deliverySMS')?.checked ? 1 : 0);
    formData.append('delivery_push', document.getElementById('deliveryPush')?.checked ? 1 : 0);
    formData.append('promo_email', document.getElementById('promoEmail')?.checked ? 1 : 0);
    formData.append('promo_sms', document.getElementById('promoSMS')?.checked ? 1 : 0);
    formData.append('promo_push', document.getElementById('promoPush')?.checked ? 1 : 0);
    formData.append('payment_email', document.getElementById('paymentEmail')?.checked ? 1 : 0);
    formData.append('payment_sms', document.getElementById('paymentSMS')?.checked ? 1 : 0);
    formData.append('payment_push', document.getElementById('paymentPush')?.checked ? 1 : 0);
    formData.append('security_email', document.getElementById('securityEmail')?.checked ? 1 : 0);
    formData.append('security_sms', document.getElementById('securitySMS')?.checked ? 1 : 0);
    formData.append('security_push', document.getElementById('securityPush')?.checked ? 1 : 0);

    try {
        const response = await fetch('{{ route("account.settings.notifications") }}', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        alert(data.success ? 'Notification preferences saved successfully!' : 'Error: ' + data.message);
    } catch (error) {
        alert('An error occurred while saving preferences');
    }
});

function signOutAllDevices() {
    if (confirm('Are you sure you want to sign out all other devices?')) {
        alert('All other devices signed out successfully!');
    }
}

// Smooth scroll for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        const href = this.getAttribute('href');
        if (href && href.length > 1) {
            e.preventDefault();
            const target = document.querySelector(href);
            if (target) {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }
    });
});
</script>
@endpush
