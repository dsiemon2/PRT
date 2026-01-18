# Payment Gateway Implementation

Complete guide for the Stripe and PayPal payment integration in Pecos River Traders.

## Overview

The website supports two payment methods:
- **Stripe**: Credit card processing with Stripe Elements
- **PayPal**: PayPal checkout with buyer protection

Payment is processed **before** the order is saved to the database, ensuring no orders are created without successful payment.

## Features Implemented

### Stripe Integration
- ✅ Secure card input using Stripe Elements (PCI compliant)
- ✅ Support for all major credit cards (Visa, Mastercard, Amex, Discover)
- ✅ Real-time card validation
- ✅ Billing address collection
- ✅ Test and live mode support
- ✅ Transaction ID storage for refunds/disputes

### PayPal Integration
- ✅ PayPal Checkout button integration
- ✅ PayPal order verification on backend
- ✅ Amount validation (prevents tampering)
- ✅ Sandbox and live mode support
- ✅ Transaction ID storage for refunds/disputes

### Security Features
- ✅ CSRF protection on all forms
- ✅ Payment processed before order creation
- ✅ Server-side payment verification
- ✅ No credit card data stored (Stripe handles tokenization)
- ✅ Transaction IDs logged for audit trail
- ✅ SSL/HTTPS required for production

## File Structure

```
PRT2/
├── config/
│   └── payment.php           # Payment gateway configuration
├── cart/
│   ├── checkout.php          # Checkout form with payment selection
│   ├── process_order.php     # Order processing with payment
│   └── order-confirmation.php # Order confirmation page
├── sql/
│   └── add_payment_gateway_columns.sql  # Database migration
└── docs/
    └── PAYMENT_GATEWAY_IMPLEMENTATION.md  # This file
```

## Setup Instructions

### 1. Database Migration

Run the SQL migration to add payment gateway columns:

```bash
# Via phpMyAdmin or MySQL command line:
mysql -u root -p pecosriver < sql/add_payment_gateway_columns.sql
```

This adds:
- `payment_method` column (VARCHAR 50) - stores 'stripe' or 'paypal'
- `transaction_id` column (VARCHAR 255) - stores Stripe Payment Intent ID or PayPal Order ID
- Index on `transaction_id` for faster lookups

### 2. Stripe Setup

#### Get Stripe API Keys

1. Create a Stripe account at https://stripe.com
2. Go to Dashboard > Developers > API keys
3. Copy your **Publishable key** and **Secret key**
4. For testing, use the **Test mode** keys
5. For production, use the **Live mode** keys

#### Configure Stripe Keys

Edit `config/payment.php` and add your keys:

```php
// Test Mode Keys
define('STRIPE_TEST_PUBLISHABLE_KEY', 'pk_test_YOUR_KEY_HERE');
define('STRIPE_TEST_SECRET_KEY', 'sk_test_YOUR_KEY_HERE');

// Live Mode Keys (for production)
define('STRIPE_LIVE_PUBLISHABLE_KEY', 'pk_live_YOUR_KEY_HERE');
define('STRIPE_LIVE_SECRET_KEY', 'sk_live_YOUR_KEY_HERE');
```

#### Install Stripe PHP Library

```bash
cd C:\xampp\htdocs\PRT2
composer require stripe/stripe-php
```

If you don't have Composer:
1. Download from https://getcomposer.org/download/
2. Install and run the command above

### 3. PayPal Setup

#### Get PayPal API Credentials

1. Create a PayPal Business account at https://www.paypal.com/businessaccount
2. Go to https://developer.paypal.com
3. Create a new app in Dashboard > My Apps & Credentials
4. Copy your **Client ID** and **Secret**
5. Use **Sandbox** credentials for testing
6. Use **Live** credentials for production

#### Configure PayPal Keys

Edit `config/payment.php` and add your keys:

```php
// Sandbox/Test Mode Keys
define('PAYPAL_TEST_CLIENT_ID', 'YOUR_SANDBOX_CLIENT_ID');
define('PAYPAL_TEST_SECRET', 'YOUR_SANDBOX_SECRET');

// Live Mode Keys (for production)
define('PAYPAL_LIVE_CLIENT_ID', 'YOUR_LIVE_CLIENT_ID');
define('PAYPAL_LIVE_SECRET', 'YOUR_LIVE_SECRET');
```

### 4. Set Payment Mode

In `config/payment.php`, set the payment mode:

```php
// For testing
define('PAYMENT_MODE', 'test');

// For production (after thorough testing!)
define('PAYMENT_MODE', 'live');
```

## Testing Payments

### Test Credit Cards (Stripe)

Use these test card numbers in **test mode**:

| Card Type | Number | Result |
|-----------|--------|--------|
| Visa | 4242 4242 4242 4242 | Success |
| Visa (debit) | 4000 0566 5566 5556 | Success |
| Mastercard | 5555 5555 5555 4444 | Success |
| Amex | 3782 822463 10005 | Success |
| Discover | 6011 1111 1111 1117 | Success |
| Card declined | 4000 0000 0000 0002 | Card declined |
| Insufficient funds | 4000 0000 0000 9995 | Insufficient funds |

**Expiry**: Use any future date (e.g., 12/25)
**CVV**: Use any 3 or 4 digits (e.g., 123)
**Name**: Use any name

More test cards: https://stripe.com/docs/testing

### Test PayPal (Sandbox)

1. Create sandbox test accounts at https://developer.paypal.com/dashboard/accounts
2. Create a **Personal** account (buyer) and **Business** account (seller)
3. Use the personal account email/password to test checkout
4. Sandbox accounts come with test funds

**Example Sandbox Account:**
- Email: buyer@example.com (create your own in PayPal Developer Dashboard)
- Password: Set during creation
- Funds: $9,999.99 (automatically added)

### Testing Checklist

- [ ] Stripe card payment succeeds with test card 4242...
- [ ] Stripe card decline handled gracefully
- [ ] PayPal payment succeeds with sandbox account
- [ ] Order created with correct payment_method and transaction_id
- [ ] Email confirmation sent after successful payment
- [ ] Inventory deducted after successful payment
- [ ] Payment failure doesn't create order
- [ ] Transaction IDs are unique and stored correctly
- [ ] CSRF token validated on form submission
- [ ] Form validation works for billing/shipping info
- [ ] "Same as billing" checkbox works for shipping address

## Payment Flow

### Stripe Payment Flow

1. **Customer enters billing info and card details**
   - Stripe Elements securely captures card data (never touches your server)

2. **Customer clicks "Place Order"**
   - JavaScript creates Stripe Payment Method with card + billing details
   - Payment Method ID sent to server

3. **Server processes payment** (`process_order.php`)
   - Validates all form data (CSRF, billing, shipping)
   - Calls `processPayment('stripe', $paymentData, $orderData)`
   - Stripe Payment Intent created with card charge
   - If payment succeeds, returns transaction ID

4. **Order created in database**
   - Order status: 'paid'
   - Payment method: 'stripe'
   - Transaction ID: Stripe Payment Intent ID (e.g., `pi_1A2B3C...`)

5. **Post-order actions**
   - Inventory deducted
   - Email confirmation sent
   - Cart cleared
   - Redirect to order confirmation page

### PayPal Payment Flow

1. **Customer enters billing/shipping info**

2. **Customer selects PayPal and clicks PayPal button**
   - PayPal popup opens
   - Customer logs into PayPal account
   - Customer approves payment

3. **PayPal payment approved**
   - JavaScript receives PayPal Order ID
   - Form submitted to server with Order ID

4. **Server verifies payment** (`process_order.php`)
   - Validates all form data
   - Calls `processPayment('paypal', $paymentData, $orderData)`
   - Backend verifies PayPal order via API
   - Checks order status is 'COMPLETED'
   - Validates amount matches order total

5. **Order created in database**
   - Order status: 'paid'
   - Payment method: 'paypal'
   - Transaction ID: PayPal Order ID (e.g., `8VD12345...`)

6. **Post-order actions**
   - Same as Stripe flow

## Error Handling

### Common Errors and Solutions

#### "Stripe library not installed"
**Solution:** Run `composer require stripe/stripe-php` in project directory

#### "Invalid publishable key"
**Solution:** Check that your Stripe keys are correctly set in `config/payment.php` and match the payment mode (test/live)

#### "Payment failed: Invalid request"
**Solution:** Check that all required fields (amount, currency, payment_method_id) are provided

#### "PayPal payment failed"
**Solution:**
- Verify PayPal Client ID and Secret are correct
- Check that payment mode matches keys (sandbox for test, live for production)
- Ensure PayPal order was actually completed

#### "Payment amount mismatch"
**Solution:** This security check prevents tampering. Ensure frontend and backend calculate totals identically

#### "Invalid security token"
**Solution:** CSRF token expired or invalid. User should refresh checkout page and try again

### Logging

Payment errors are logged to PHP error log:
```php
error_log("Payment processing error: " . $e->getMessage());
```

Check `C:\xampp\php\logs\php_error_log` or `C:\xampp\apache\logs\error.log`

## Production Deployment Checklist

Before going live with real payments:

### 1. API Keys
- [ ] Update `config/payment.php` with **live** Stripe keys
- [ ] Update `config/payment.php` with **live** PayPal keys
- [ ] Set `PAYMENT_MODE` to `'live'`
- [ ] Test one small transaction in live mode

### 2. Security
- [ ] SSL certificate installed (HTTPS)
- [ ] CSP headers configured (`config/csp.php`)
- [ ] Rate limiting enabled and tested
- [ ] CSRF protection active on all forms
- [ ] Error messages don't expose sensitive info

### 3. Email Configuration
- [ ] Update `config/email.php` with production email settings
- [ ] Test order confirmation emails
- [ ] Set up email monitoring for failures

### 4. Testing
- [ ] Complete test purchase with live keys (small amount)
- [ ] Verify funds received in Stripe dashboard
- [ ] Verify funds received in PayPal business account
- [ ] Test refund process through Stripe/PayPal dashboards
- [ ] Verify transaction IDs match in database

### 5. Business Setup
- [ ] Stripe account verified and approved
- [ ] PayPal business account verified
- [ ] Bank account linked to receive payouts
- [ ] Business details (name, address) updated in Stripe/PayPal
- [ ] Tax settings configured if applicable

### 6. Monitoring
- [ ] Set up Stripe webhook for payment events (optional)
- [ ] Set up PayPal IPN or webhooks (optional)
- [ ] Monitor error logs daily
- [ ] Set up alerts for payment failures

## Refunds and Disputes

### Processing Refunds

Refunds must be processed through the payment gateway dashboards:

**Stripe Refunds:**
1. Log into https://dashboard.stripe.com
2. Go to Payments
3. Find the payment by transaction_id
4. Click "Refund payment"
5. Enter amount and reason
6. Update order status in your database to 'refunded'

**PayPal Refunds:**
1. Log into https://www.paypal.com/businessmanage
2. Go to Activity
3. Find the transaction by transaction_id
4. Click "Issue refund"
5. Enter amount and note
6. Update order status in your database to 'refunded'

### Handling Disputes

Both Stripe and PayPal will notify you of disputes/chargebacks:
- Respond promptly through the dashboard
- Provide proof of delivery/service
- Transaction ID helps locate the order in your database

## Advanced Features (Future)

Features not yet implemented but planned:

- [ ] Stripe webhooks for payment status updates
- [ ] PayPal webhooks/IPN for order status sync
- [ ] Partial refunds
- [ ] Subscription/recurring payments
- [ ] Multiple currency support
- [ ] Digital wallet support (Apple Pay, Google Pay)
- [ ] Payment method storage for logged-in users
- [ ] Automatic retry for failed payments

## Support and Documentation

### Official Documentation
- **Stripe Docs:** https://stripe.com/docs
- **Stripe PHP Library:** https://stripe.com/docs/api/php
- **PayPal Checkout:** https://developer.paypal.com/docs/checkout/
- **PayPal REST API:** https://developer.paypal.com/docs/api/overview/

### Testing Resources
- **Stripe Test Cards:** https://stripe.com/docs/testing
- **PayPal Sandbox:** https://developer.paypal.com/developer/accounts/

### Contact
For questions or issues with this implementation:
- Check error logs first
- Review this documentation
- Search official Stripe/PayPal docs
- Contact the development team

## File Reference

### config/payment.php
Main configuration file containing:
- API keys for Stripe and PayPal
- Helper functions: `getStripeKeys()`, `getPayPalKeys()`
- Payment processing functions: `processPayment()`, `processStripePayment()`, `processPayPalPayment()`

### cart/checkout.php
Checkout form with:
- Payment method selection (Stripe/PayPal)
- Stripe Elements integration
- PayPal button integration
- Form submission handling

### cart/process_order.php
Order processing script:
- Form validation
- Payment processing before order creation
- Order database insertion
- Inventory deduction
- Email confirmation

---

**Last Updated:** November 17, 2025
**Version:** 1.0
**Status:** Production Ready (pending API key configuration)
