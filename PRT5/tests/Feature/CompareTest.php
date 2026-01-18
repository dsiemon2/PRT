<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompareTest extends TestCase
{
    /**
     * Test compare page loads when empty.
     */
    public function test_compare_page_loads_when_empty(): void
    {
        $response = $this->get('/compare');

        $response->assertStatus(200);
        $response->assertViewIs('shop.products.compare');
        $response->assertSee('No Products to Compare');
    }

    /**
     * Test can add product to comparison.
     */
    public function test_can_add_product_to_comparison(): void
    {
        $product = Product::first();

        if (!$product) {
            $this->markTestSkipped('No products in database');
        }

        $response = $this->postJson('/compare/add', [
            'product_id' => $product->ID,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Product added to comparison.',
            'count' => 1,
        ]);
    }

    /**
     * Test cannot add more than 4 products to comparison.
     */
    public function test_cannot_add_more_than_4_products_to_comparison(): void
    {
        $products = Product::take(5)->get();

        if ($products->count() < 5) {
            $this->markTestSkipped('Need at least 5 products in database');
        }

        // Add first 4 products
        foreach ($products->take(4) as $product) {
            $this->postJson('/compare/add', [
                'product_id' => $product->ID,
            ]);
        }

        // Try to add 5th product
        $response = $this->postJson('/compare/add', [
            'product_id' => $products->last()->ID,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => false,
        ]);
        $response->assertJsonFragment(['message' => 'You can compare up to 4 products at a time.']);
    }

    /**
     * Test compare count endpoint.
     */
    public function test_compare_count_endpoint(): void
    {
        $response = $this->getJson('/compare/count');

        $response->assertStatus(200);
        $response->assertJsonStructure(['count', 'product_ids']);
    }

    /**
     * Test compare count returns correct count after adding.
     */
    public function test_compare_count_returns_correct_count(): void
    {
        $products = Product::take(2)->get();

        if ($products->count() < 2) {
            $this->markTestSkipped('Need at least 2 products in database');
        }

        // Add products
        foreach ($products as $product) {
            $this->postJson('/compare/add', [
                'product_id' => $product->ID,
            ]);
        }

        $response = $this->getJson('/compare/count');

        $response->assertStatus(200);
        $response->assertJson(['count' => 2]);
    }

    /**
     * Test can remove product from comparison.
     */
    public function test_can_remove_product_from_comparison(): void
    {
        $product = Product::first();

        if (!$product) {
            $this->markTestSkipped('No products in database');
        }

        // Add product first
        $this->postJson('/compare/add', [
            'product_id' => $product->ID,
        ]);

        // Remove product
        $response = $this->postJson('/compare/remove', [
            'product_id' => $product->ID,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Product removed from comparison.',
        ]);
    }

    /**
     * Test can clear all products from comparison.
     */
    public function test_can_clear_all_products_from_comparison(): void
    {
        $products = Product::take(3)->get();

        if ($products->count() < 3) {
            $this->markTestSkipped('Need at least 3 products in database');
        }

        // Add products
        foreach ($products as $product) {
            $this->postJson('/compare/add', [
                'product_id' => $product->ID,
            ]);
        }

        // Clear all
        $response = $this->postJson('/compare/remove', [
            'clear_all' => true,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'All products removed from comparison.',
            'count' => 0,
        ]);

        // Verify count is 0
        $countResponse = $this->getJson('/compare/count');
        $countResponse->assertJson(['count' => 0]);
    }

    /**
     * Test compare page shows products after adding.
     */
    public function test_compare_page_shows_added_products(): void
    {
        $product = Product::first();

        if (!$product) {
            $this->markTestSkipped('No products in database');
        }

        // Add product
        $this->postJson('/compare/add', [
            'product_id' => $product->ID,
        ]);

        // Check compare page
        $response = $this->get('/compare');

        $response->assertStatus(200);
        $response->assertDontSee('No Products to Compare');
        $response->assertSee($product->ShortDescription);
    }

    /**
     * Test duplicate product is not added twice.
     */
    public function test_duplicate_product_not_added_twice(): void
    {
        $product = Product::first();

        if (!$product) {
            $this->markTestSkipped('No products in database');
        }

        // Add product twice
        $this->postJson('/compare/add', ['product_id' => $product->ID]);
        $this->postJson('/compare/add', ['product_id' => $product->ID]);

        // Check count is still 1
        $response = $this->getJson('/compare/count');
        $response->assertJson(['count' => 1]);
    }
}
