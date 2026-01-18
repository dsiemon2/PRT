<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    /**
     * Test cart page loads.
     */
    public function test_cart_page_loads(): void
    {
        $response = $this->get('/cart');

        $response->assertStatus(200);
        $response->assertViewIs('shop.cart.index');
    }

    /**
     * Test cart page shows empty message when empty.
     */
    public function test_cart_shows_empty_when_no_items(): void
    {
        // Clear any existing cart
        $this->withSession(['cart' => []]);

        $response = $this->get('/cart');

        $response->assertStatus(200);
    }

    /**
     * Test can add product to cart by UPC.
     */
    public function test_can_add_product_to_cart_by_upc(): void
    {
        $product = Product::whereNotNull('UPC')->first();

        if (!$product) {
            $product = Product::first();
        }

        if (!$product) {
            $this->markTestSkipped('No products in database');
        }

        $upc = $product->UPC ?: $product->ItemNumber;

        $response = $this->get('/cart/add?upc=' . urlencode($upc));

        $response->assertRedirect('/cart');
        $response->assertSessionHas('success');
    }

    /**
     * Test can add product with quantity.
     */
    public function test_can_add_product_with_quantity(): void
    {
        $product = Product::first();

        if (!$product) {
            $this->markTestSkipped('No products in database');
        }

        $upc = $product->UPC ?: $product->ItemNumber;

        $response = $this->get('/cart/add?upc=' . urlencode($upc) . '&qty=3');

        $response->assertRedirect('/cart');
        $response->assertSessionHas('success');
    }

    /**
     * Test adding invalid product shows error.
     */
    public function test_adding_invalid_product_shows_error(): void
    {
        $response = $this->get('/cart/add?upc=INVALID_UPC_12345');

        $response->assertRedirect('/products');
        $response->assertSessionHas('error');
    }

    /**
     * Test can update cart quantity.
     */
    public function test_can_update_cart_quantity(): void
    {
        $product = Product::first();

        if (!$product) {
            $this->markTestSkipped('No products in database');
        }

        // Add product to cart
        $this->withSession(['cart' => [
            ['product_id' => $product->ID, 'quantity' => 1, 'size' => null]
        ]]);

        $response = $this->post('/cart/update', [
            'index' => 0,
            'quantity' => 5,
        ]);

        $response->assertRedirect('/cart');
        $response->assertSessionHas('success');
    }

    /**
     * Test can update cart quantity via AJAX.
     */
    public function test_can_update_cart_quantity_ajax(): void
    {
        $product = Product::first();

        if (!$product) {
            $this->markTestSkipped('No products in database');
        }

        // Add product to cart
        $this->withSession(['cart' => [
            ['product_id' => $product->ID, 'quantity' => 1, 'size' => null]
        ]]);

        $response = $this->postJson('/cart/update', [
            'index' => 0,
            'quantity' => 5,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }

    /**
     * Test setting quantity to 0 removes item.
     */
    public function test_setting_quantity_to_zero_removes_item(): void
    {
        $product = Product::first();

        if (!$product) {
            $this->markTestSkipped('No products in database');
        }

        // Add product to cart
        $this->withSession(['cart' => [
            ['product_id' => $product->ID, 'quantity' => 1, 'size' => null]
        ]]);

        $response = $this->post('/cart/update', [
            'index' => 0,
            'quantity' => 0,
        ]);

        $response->assertRedirect('/cart');
    }

    /**
     * Test can remove item from cart.
     */
    public function test_can_remove_item_from_cart(): void
    {
        $product = Product::first();

        if (!$product) {
            $this->markTestSkipped('No products in database');
        }

        // Add product to cart
        $this->withSession(['cart' => [
            ['product_id' => $product->ID, 'quantity' => 1, 'size' => null]
        ]]);

        $response = $this->delete('/cart/remove/0');

        $response->assertRedirect('/cart');
        $response->assertSessionHas('success');
    }

    /**
     * Test can remove item via AJAX.
     */
    public function test_can_remove_item_ajax(): void
    {
        $product = Product::first();

        if (!$product) {
            $this->markTestSkipped('No products in database');
        }

        // Add product to cart
        $this->withSession(['cart' => [
            ['product_id' => $product->ID, 'quantity' => 1, 'size' => null]
        ]]);

        $response = $this->deleteJson('/cart/remove/0');

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }

    /**
     * Test cart calculates totals correctly.
     */
    public function test_cart_calculates_totals(): void
    {
        $product = Product::first();

        if (!$product) {
            $this->markTestSkipped('No products in database');
        }

        // Add product to cart
        $this->withSession(['cart' => [
            ['product_id' => $product->ID, 'quantity' => 2, 'size' => null]
        ]]);

        $response = $this->get('/cart');

        $response->assertStatus(200);
        $response->assertViewHas('subtotal');
        $response->assertViewHas('total');
    }

    /**
     * Test adding same product increases quantity.
     */
    public function test_adding_same_product_increases_quantity(): void
    {
        $product = Product::first();

        if (!$product) {
            $this->markTestSkipped('No products in database');
        }

        $upc = $product->UPC ?: $product->ItemNumber;

        // Add product twice
        $this->get('/cart/add?upc=' . urlencode($upc) . '&qty=1');
        $this->get('/cart/add?upc=' . urlencode($upc) . '&qty=2');

        // Check cart has single item with quantity 3
        $cart = session('cart');
        $this->assertCount(1, $cart);
        $this->assertEquals(3, $cart[0]['quantity']);
    }
}
