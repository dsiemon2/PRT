<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\UserAddress;
use App\Models\Coupon;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    protected $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    public function index()
    {
        $cart = session('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')
                ->with('error', 'Your cart is empty.');
        }

        $cartItems = $this->getCartItems($cart);
        $totals = $this->calculateTotals($cartItems);

        $user = auth()->user();
        $addresses = UserAddress::where('user_id', $user->id)->get();
        $defaultAddress = $addresses->where('is_default', true)->first() ?? $addresses->first();

        // Create Stripe Payment Intent
        $paymentIntent = null;
        $stripePublicKey = null;

        try {
            $paymentIntent = $this->stripeService->createPaymentIntent(
                $totals['total'],
                'usd',
                [
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                ]
            );
            $stripePublicKey = StripeService::getPublishableKey();
        } catch (\Exception $e) {
            Log::error('Stripe Payment Intent creation failed: ' . $e->getMessage());
        }

        return view('shop.checkout.index', compact(
            'cartItems', 'totals', 'user', 'addresses', 'defaultAddress',
            'paymentIntent', 'stripePublicKey'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'shipping_address_id' => 'nullable|exists:user_addresses,id',
            'shipping_first_name' => 'required_without:shipping_address_id|string|max:100',
            'shipping_last_name' => 'required_without:shipping_address_id|string|max:100',
            'shipping_address' => 'required_without:shipping_address_id|string|max:255',
            'shipping_address2' => 'nullable|string|max:255',
            'shipping_city' => 'required_without:shipping_address_id|string|max:100',
            'shipping_state' => 'required_without:shipping_address_id|string|max:50',
            'shipping_zip' => 'required_without:shipping_address_id|string|max:20',
            'shipping_phone' => 'nullable|string|max:20',
            'billing_same' => 'boolean',
            'payment_method' => 'required|in:card,stripe',
            'payment_intent_id' => 'required_if:payment_method,card,stripe|string',
            'notes' => 'nullable|string|max:500',
            'save_address' => 'boolean',
        ]);

        $cart = session('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')
                ->with('error', 'Your cart is empty.');
        }

        $cartItems = $this->getCartItems($cart);
        $totals = $this->calculateTotals($cartItems);

        // Verify Stripe payment if card payment
        if (in_array($validated['payment_method'], ['card', 'stripe'])) {
            try {
                $paymentIntent = $this->stripeService->retrievePaymentIntent($validated['payment_intent_id']);

                if (!$this->stripeService->isPaymentSuccessful($paymentIntent)) {
                    return back()->with('error', 'Payment was not successful. Please try again.');
                }
            } catch (\Exception $e) {
                Log::error('Stripe payment verification failed: ' . $e->getMessage());
                return back()->with('error', 'Payment verification failed. Please try again.');
            }
        }

        DB::beginTransaction();
        try {
            // Get or create shipping address
            if ($validated['shipping_address_id'] ?? null) {
                $shippingAddress = UserAddress::find($validated['shipping_address_id']);
            } else {
                $addressData = [
                    'first_name' => $validated['shipping_first_name'],
                    'last_name' => $validated['shipping_last_name'],
                    'address_line1' => $validated['shipping_address'],
                    'address_line2' => $validated['shipping_address2'] ?? null,
                    'city' => $validated['shipping_city'],
                    'state' => $validated['shipping_state'],
                    'postal_code' => $validated['shipping_zip'],
                    'phone' => $validated['shipping_phone'] ?? null,
                ];

                if ($validated['save_address'] ?? false) {
                    $shippingAddress = UserAddress::create([
                        'user_id' => auth()->id(),
                        ...$addressData
                    ]);
                } else {
                    $shippingAddress = (object) $addressData;
                }
            }

            // Create order
            $order = Order::create([
                'user_id' => auth()->id(),
                'order_number' => 'ORD-' . strtoupper(uniqid()),
                'order_date' => now(),
                'status' => 'processing', // Payment confirmed
                'payment_status' => 'paid',
                'subtotal' => $totals['subtotal'],
                'discount' => $totals['discount'],
                'tax_amount' => $totals['tax'],
                'shipping_cost' => $totals['shipping'],
                'total_amount' => $totals['total'],
                'customer_first_name' => $shippingAddress->first_name ?? $validated['shipping_first_name'],
                'customer_last_name' => $shippingAddress->last_name ?? $validated['shipping_last_name'],
                'customer_email' => auth()->user()->email,
                'shipping_address1' => $shippingAddress->address_line1 ?? $validated['shipping_address'],
                'shipping_address2' => $shippingAddress->address_line2 ?? null,
                'shipping_city' => $shippingAddress->city ?? $validated['shipping_city'],
                'shipping_state' => $shippingAddress->state ?? $validated['shipping_state'],
                'shipping_zip' => $shippingAddress->postal_code ?? $validated['shipping_zip'],
                'payment_method' => 'stripe',
                'stripe_payment_intent_id' => $validated['payment_intent_id'] ?? null,
                'order_notes' => $validated['notes'] ?? null,
                'coupon_code' => session('coupon.code'),
            ]);

            // Create order items
            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product']->ID,
                    'product_name' => $item['product']->ShortDescription,
                    'product_sku' => $item['product']->ItemNumber,
                    'quantity' => $item['quantity'],
                    'price' => $item['product']->UnitPrice,
                    'size' => $item['size'] ?? null,
                    'total' => $item['total'],
                ]);

                // Update inventory
                if ($item['product']->track_inventory) {
                    $item['product']->decrement('stock_quantity', $item['quantity']);
                    $item['product']->increment('reserved_quantity', $item['quantity']);
                }
            }

            // Update coupon usage
            if ($coupon = session('coupon')) {
                Coupon::where('id', $coupon['id'])->increment('times_used');
            }

            // Clear cart and coupon
            session()->forget(['cart', 'coupon']);

            DB::commit();

            return redirect()->route('order.confirmation', $order)
                ->with('success', 'Order placed successfully! Payment confirmed.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order creation failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to process order. Please try again.');
        }
    }

    public function confirmation(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $order->load('items.product');

        return view('shop.checkout.confirmation', compact('order'));
    }

    private function getCartItems(array $cart): array
    {
        $items = [];
        foreach ($cart as $index => $item) {
            $product = Product::find($item['product_id']);
            if ($product) {
                $items[] = [
                    'index' => $index,
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'size' => $item['size'] ?? null,
                    'total' => $product->UnitPrice * $item['quantity'],
                ];
            }
        }
        return $items;
    }

    private function calculateTotals(array $cartItems): array
    {
        $subtotal = array_sum(array_column($cartItems, 'total'));

        $discount = 0;
        if ($coupon = session('coupon')) {
            if ($coupon['type'] === 'percentage') {
                $discount = $subtotal * ($coupon['value'] / 100);
            } else {
                $discount = min($coupon['value'], $subtotal);
            }
        }

        $shipping = 0; // Free shipping for now
        $taxRate = 0.0825;
        $tax = ($subtotal - $discount) * $taxRate;
        $total = $subtotal - $discount + $tax + $shipping;

        return compact('subtotal', 'discount', 'shipping', 'tax', 'total');
    }
}
