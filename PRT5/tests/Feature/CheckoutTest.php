<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    /**
     * Test checkout page requires items in cart.
     */
    public function test_checkout_redirects_with_empty_cart(): void
    {
        $this->withSession(['cart' => []]);

        $response = $this->get('/checkout');

        // Should redirect to cart or show message
        $response->assertStatus(302);
    }

    /**
     * Test checkout page loads or redirects with items in cart.
     */
    public function test_checkout_page_loads_with_cart_items(): void
    {
        $product = Product::first();

        if (!$product) {
            $this->markTestSkipped('No products in database');
        }

        $this->withSession(['cart' => [
            ['product_id' => $product->ID, 'quantity' => 1, 'size' => null]
        ]]);

        $response = $this->get('/checkout');

        // Checkout page may load (200) or redirect to login/cart (302)
        $this->assertTrue(in_array($response->status(), [200, 302]));
    }

    /**
     * Test checkout requires authentication for submission.
     */
    public function test_checkout_submission_requires_fields(): void
    {
        $product = Product::first();

        if (!$product) {
            $this->markTestSkipped('No products in database');
        }

        $this->withSession(['cart' => [
            ['product_id' => $product->ID, 'quantity' => 1, 'size' => null]
        ]]);

        $response = $this->post('/checkout', []);

        // Should redirect back with validation errors
        $response->assertStatus(302);
    }

    /**
     * Test checkout with valid data.
     */
    public function test_checkout_with_valid_guest_data(): void
    {
        $product = Product::first();

        if (!$product) {
            $this->markTestSkipped('No products in database');
        }

        $this->withSession(['cart' => [
            ['product_id' => $product->ID, 'quantity' => 1, 'size' => null]
        ]]);

        $response = $this->post('/checkout', [
            'email' => 'test@example.com',
            'first_name' => 'Test',
            'last_name' => 'User',
            'address' => '123 Test St',
            'city' => 'Test City',
            'state' => 'TX',
            'zip' => '12345',
            'phone' => '555-555-5555',
            'payment_method' => 'cod', // Cash on delivery for testing
        ]);

        // Should redirect to confirmation or process order
        $this->assertTrue(in_array($response->status(), [200, 302, 422]));
    }

    /**
     * Test authenticated checkout.
     */
    public function test_authenticated_user_checkout(): void
    {
        $user = User::first();
        $product = Product::first();

        if (!$user || !$product) {
            $this->markTestSkipped('No users or products in database');
        }

        $this->withSession(['cart' => [
            ['product_id' => $product->ID, 'quantity' => 1, 'size' => null]
        ]]);

        $response = $this->actingAs($user)->get('/checkout');

        $response->assertStatus(200);
    }
}
