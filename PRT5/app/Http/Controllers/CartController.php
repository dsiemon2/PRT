<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = session('cart', []);
        $coupon = session('coupon');

        $cartItems = [];
        $subtotal = 0;

        foreach ($cart as $index => $item) {
            $product = Product::find($item['product_id']);
            if ($product) {
                $itemTotal = $product->UnitPrice * $item['quantity'];
                $cartItems[] = [
                    'index' => $index,
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'size' => $item['size'] ?? null,
                    'total' => $itemTotal,
                ];
                $subtotal += $itemTotal;
            }
        }

        $discount = 0;
        if ($coupon) {
            if ($coupon['type'] === 'percentage') {
                $discount = $subtotal * ($coupon['value'] / 100);
            } else {
                $discount = min($coupon['value'], $subtotal);
            }
        }

        $taxRate = 0.0825; // 8.25%
        $tax = ($subtotal - $discount) * $taxRate;
        $total = $subtotal - $discount + $tax;

        return view('shop.cart.index', compact(
            'cartItems', 'subtotal', 'discount', 'tax', 'total', 'coupon'
        ));
    }

    public function add(Request $request)
    {
        $upc = $request->get('upc');
        $qty = (int) $request->get('qty', 1);
        $size = $request->get('size');

        $product = Product::where('UPC', $upc)
            ->orWhere('ItemNumber', $upc)
            ->first();

        if (!$product) {
            return redirect()->route('products.index')
                ->with('error', 'Product not found.');
        }

        $cart = session('cart', []);

        // Check if product already in cart
        $found = false;
        foreach ($cart as &$item) {
            if ($item['product_id'] == $product->ID && ($item['size'] ?? null) == $size) {
                $item['quantity'] += $qty;
                $found = true;
                break;
            }
        }

        if (!$found) {
            $cart[] = [
                'product_id' => $product->ID,
                'quantity' => $qty,
                'size' => $size,
            ];
        }

        session(['cart' => $cart]);

        return redirect()->route('cart.index')
            ->with('success', $product->ShortDescription . ' added to cart!');
    }

    public function update(Request $request)
    {
        $index = $request->input('index');
        $quantity = (int) $request->input('quantity', 1);

        $cart = session('cart', []);

        if (isset($cart[$index])) {
            if ($quantity > 0) {
                $cart[$index]['quantity'] = min($quantity, 99);
            } else {
                unset($cart[$index]);
                $cart = array_values($cart);
            }
        }

        session(['cart' => $cart]);

        $isAjax = $request->ajax() || $request->wantsJson() || $request->isJson();
        if ($isAjax) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('cart.index')
            ->with('success', 'Cart updated.');
    }

    public function remove(Request $request, $index)
    {
        $cart = session('cart', []);

        if (isset($cart[$index])) {
            unset($cart[$index]);
            $cart = array_values($cart);
        }

        session(['cart' => $cart]);

        $isAjax = $request->ajax() || $request->wantsJson() || $request->isJson();
        if ($isAjax) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('cart.index')
            ->with('success', 'Item removed from cart.');
    }

    public function applyCoupon(Request $request)
    {
        $code = strtoupper(trim($request->input('coupon_code')));

        if (!$code) {
            return back()->with('error', 'Please enter a coupon code.');
        }

        $coupon = Coupon::where('code', $code)
            ->where('is_active', true)
            ->where(function($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>=', now());
            })
            ->first();

        if (!$coupon) {
            return back()->with('error', 'Invalid or expired coupon code.');
        }

        // Check minimum order
        $cart = session('cart', []);
        $subtotal = 0;
        foreach ($cart as $item) {
            $product = Product::find($item['product_id']);
            if ($product) {
                $subtotal += $product->UnitPrice * $item['quantity'];
            }
        }

        if ($coupon->min_order_amount && $subtotal < $coupon->min_order_amount) {
            return back()->with('error', "This coupon requires a minimum order of $" . number_format($coupon->min_order_amount, 2));
        }

        // Check usage limit
        if ($coupon->usage_limit && $coupon->times_used >= $coupon->usage_limit) {
            return back()->with('error', 'This coupon has reached its usage limit.');
        }

        session(['coupon' => [
            'id' => $coupon->id,
            'code' => $coupon->code,
            'type' => $coupon->discount_type,
            'value' => $coupon->discount_value,
        ]]);

        return back()->with('success', 'Coupon applied successfully!');
    }

    public function removeCoupon()
    {
        session()->forget('coupon');
        return back()->with('success', 'Coupon removed.');
    }
}
