@extends('layouts.app')

@section('title', 'Checkout')

@push('styles')
<style>
    #card-element {
        padding: 12px;
        border: 1px solid #ced4da;
        border-radius: 4px;
        background: #fff;
    }
    #card-errors {
        color: #dc3545;
        margin-top: 8px;
        font-size: 0.875rem;
    }
    .stripe-badge {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #6c757d;
        font-size: 0.875rem;
    }
    .stripe-badge img {
        height: 24px;
    }
</style>
@endpush

@section('content')
<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('cart.index') }}">Cart</a></li>
            <li class="breadcrumb-item active">Checkout</li>
        </ol>
    </nav>
</div>

<div class="container my-4">
    <h1 style="color: var(--prt-brown);"><i class="bi bi-credit-card"></i> Checkout</h1>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('checkout.store') }}" method="POST" id="checkoutForm">
        @csrf
        <input type="hidden" name="payment_intent_id" id="payment_intent_id" value="">
        <input type="hidden" name="payment_method" value="stripe">

        <div class="row">
            <div class="col-lg-8">
                {{-- Shipping Address --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-geo-alt"></i> Shipping Address</h5>
                    </div>
                    <div class="card-body">
                        @if($addresses->count() > 0)
                            <div class="mb-3">
                                <label class="form-label">Select Saved Address</label>
                                <select name="shipping_address_id" class="form-select" id="savedAddress" onchange="toggleNewAddress()">
                                    <option value="">Enter new address</option>
                                    @foreach($addresses as $addr)
                                        <option value="{{ $addr->id }}" {{ $defaultAddress && $defaultAddress->id == $addr->id ? 'selected' : '' }}>
                                            {{ $addr->first_name }} {{ $addr->last_name }} -
                                            {{ $addr->address_line1 }}, {{ $addr->city }}, {{ $addr->state }} {{ $addr->postal_code }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <hr>
                        @endif

                        <div id="newAddressForm" style="{{ $defaultAddress ? 'display: none;' : '' }}">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">First Name</label>
                                    <input type="text" name="shipping_first_name" class="form-control"
                                           value="{{ old('shipping_first_name', $user->first_name) }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Last Name</label>
                                    <input type="text" name="shipping_last_name" class="form-control"
                                           value="{{ old('shipping_last_name', $user->last_name) }}">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Address</label>
                                <input type="text" name="shipping_address" class="form-control"
                                       value="{{ old('shipping_address') }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Address Line 2 (Optional)</label>
                                <input type="text" name="shipping_address2" class="form-control"
                                       value="{{ old('shipping_address2') }}">
                            </div>
                            <div class="row">
                                <div class="col-md-5 mb-3">
                                    <label class="form-label">City</label>
                                    <input type="text" name="shipping_city" class="form-control"
                                           value="{{ old('shipping_city') }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">State</label>
                                    <select name="shipping_state" class="form-select">
                                        <option value="">Select State</option>
                                        @foreach(['AL','AK','AZ','AR','CA','CO','CT','DE','FL','GA','HI','ID','IL','IN','IA','KS','KY','LA','ME','MD','MA','MI','MN','MS','MO','MT','NE','NV','NH','NJ','NM','NY','NC','ND','OH','OK','OR','PA','RI','SC','SD','TN','TX','UT','VT','VA','WA','WV','WI','WY'] as $state)
                                            <option value="{{ $state }}" {{ old('shipping_state') == $state ? 'selected' : '' }}>{{ $state }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">ZIP Code</label>
                                    <input type="text" name="shipping_zip" class="form-control"
                                           value="{{ old('shipping_zip') }}">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Phone (Optional)</label>
                                <input type="tel" name="shipping_phone" class="form-control"
                                       value="{{ old('shipping_phone') }}">
                            </div>
                            <div class="form-check mb-3">
                                <input type="checkbox" name="save_address" class="form-check-input" id="saveAddress" value="1">
                                <label class="form-check-label" for="saveAddress">Save this address for future orders</label>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Payment Method - Stripe --}}
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-credit-card-2-front"></i> Payment</h5>
                        <div class="stripe-badge">
                            <span>Powered by</span>
                            <img src="https://upload.wikimedia.org/wikipedia/commons/b/ba/Stripe_Logo%2C_revised_2016.svg" alt="Stripe">
                        </div>
                    </div>
                    <div class="card-body">
                        @if($paymentIntent && $stripePublicKey)
                            <div class="mb-3">
                                <label class="form-label">Card Details</label>
                                <div id="card-element"></div>
                                <div id="card-errors" role="alert"></div>
                            </div>
                            <div class="d-flex align-items-center gap-2 text-muted small">
                                <i class="bi bi-shield-lock"></i>
                                <span>Your payment information is encrypted and secure</span>
                            </div>
                        @else
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle"></i>
                                Payment system is temporarily unavailable. Please try again later.
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Order Notes --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-chat-text"></i> Order Notes (Optional)</h5>
                    </div>
                    <div class="card-body">
                        <textarea name="notes" class="form-control" rows="3"
                                  placeholder="Special instructions for your order...">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Order Summary --}}
            <div class="col-lg-4">
                <div class="card sticky-top" style="top: 20px;">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-receipt"></i> Order Summary</h5>
                    </div>
                    <div class="card-body">
                        @foreach($cartItems as $item)
                            <div class="d-flex mb-3">
                                <img src="{{ $item['product']->primaryImage }}"
                                     style="width: 60px; height: 60px; object-fit: contain;"
                                     class="me-3"
                                     onerror="this.src='{{ asset('assets/images/no-image.svg') }}'">
                                <div class="flex-grow-1">
                                    <div class="fw-bold">{{ Str::limit($item['product']->ShortDescription, 30) }}</div>
                                    <small class="text-muted">Qty: {{ $item['quantity'] }}</small>
                                    @if($item['size'])
                                        <small class="text-muted">| Size: {{ $item['size'] }}</small>
                                    @endif
                                </div>
                                <div class="fw-bold">${{ number_format($item['total'], 2) }}</div>
                            </div>
                        @endforeach
                        <hr>
                        <table class="table table-sm mb-0">
                            <tr>
                                <td>Subtotal</td>
                                <td class="text-end">${{ number_format($totals['subtotal'], 2) }}</td>
                            </tr>
                            @if($totals['discount'] > 0)
                                <tr class="text-success">
                                    <td>Discount</td>
                                    <td class="text-end">-${{ number_format($totals['discount'], 2) }}</td>
                                </tr>
                            @endif
                            <tr>
                                <td>Shipping</td>
                                <td class="text-end">{{ $totals['shipping'] > 0 ? '$' . number_format($totals['shipping'], 2) : 'Free' }}</td>
                            </tr>
                            <tr>
                                <td>Tax</td>
                                <td class="text-end">${{ number_format($totals['tax'], 2) }}</td>
                            </tr>
                            <tr class="fw-bold fs-5">
                                <td>Total</td>
                                <td class="text-end">${{ number_format($totals['total'], 2) }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="card-footer d-grid">
                        <button type="submit" class="btn btn-primary btn-lg" id="submitBtn" {{ !$paymentIntent ? 'disabled' : '' }}>
                            <span id="btnText"><i class="bi bi-lock"></i> Pay ${{ number_format($totals['total'], 2) }}</span>
                            <span id="btnSpinner" class="d-none">
                                <span class="spinner-border spinner-border-sm me-2"></span>Processing...
                            </span>
                        </button>
                        <small class="text-muted text-center mt-2">
                            <i class="bi bi-shield-check"></i> Secure payment powered by Stripe
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
@if($paymentIntent && $stripePublicKey)
<script src="https://js.stripe.com/v3/"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Stripe
    const stripe = Stripe('{{ $stripePublicKey }}');
    const elements = stripe.elements();

    // Create card element
    const cardElement = elements.create('card', {
        style: {
            base: {
                fontSize: '16px',
                color: '#32325d',
                fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
                '::placeholder': {
                    color: '#aab7c4'
                }
            },
            invalid: {
                color: '#dc3545',
                iconColor: '#dc3545'
            }
        }
    });
    cardElement.mount('#card-element');

    // Handle card errors
    cardElement.on('change', function(event) {
        const displayError = document.getElementById('card-errors');
        if (event.error) {
            displayError.textContent = event.error.message;
        } else {
            displayError.textContent = '';
        }
    });

    // Handle form submission
    const form = document.getElementById('checkoutForm');
    const submitBtn = document.getElementById('submitBtn');
    const btnText = document.getElementById('btnText');
    const btnSpinner = document.getElementById('btnSpinner');

    form.addEventListener('submit', async function(event) {
        event.preventDefault();

        // Disable button and show spinner
        submitBtn.disabled = true;
        btnText.classList.add('d-none');
        btnSpinner.classList.remove('d-none');

        try {
            // Confirm card payment
            const { paymentIntent, error } = await stripe.confirmCardPayment(
                '{{ $paymentIntent->client_secret }}',
                {
                    payment_method: {
                        card: cardElement,
                        billing_details: {
                            name: document.querySelector('[name="shipping_first_name"]')?.value + ' ' +
                                  document.querySelector('[name="shipping_last_name"]')?.value,
                            email: '{{ $user->email }}'
                        }
                    }
                }
            );

            if (error) {
                // Show error
                document.getElementById('card-errors').textContent = error.message;
                submitBtn.disabled = false;
                btnText.classList.remove('d-none');
                btnSpinner.classList.add('d-none');
            } else if (paymentIntent.status === 'succeeded') {
                // Payment successful - submit form
                document.getElementById('payment_intent_id').value = paymentIntent.id;
                form.submit();
            }
        } catch (err) {
            document.getElementById('card-errors').textContent = 'An error occurred. Please try again.';
            submitBtn.disabled = false;
            btnText.classList.remove('d-none');
            btnSpinner.classList.add('d-none');
        }
    });

    // Initialize Bootstrap tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

function toggleNewAddress() {
    const select = document.getElementById('savedAddress');
    const form = document.getElementById('newAddressForm');
    form.style.display = select.value ? 'none' : 'block';
}
</script>
@endif
@endpush
