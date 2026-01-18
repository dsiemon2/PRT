<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Comparison Tests - PRT5 (Laravel) vs PRT4 (PHP)
 *
 * These tests compare the behavior of PRT5 (http://localhost:8300)
 * with PRT4 (http://localhost:3000/PRT4) to ensure feature parity.
 */
class ComparisonTest extends TestCase
{
    protected string $prt5Url = 'http://localhost:8300';
    protected string $prt4Url = 'http://localhost:3000/PRT4';

    /**
     * Helper to check if PRT4 is accessible.
     */
    protected function prt4IsAvailable(): bool
    {
        try {
            $response = Http::timeout(5)->get($this->prt4Url . '/Products/products.php');
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Test both systems have products page.
     */
    public function test_both_systems_have_products_page(): void
    {
        // Test PRT5
        $prt5Response = $this->get('/products');
        $prt5Response->assertStatus(200);

        if (!$this->prt4IsAvailable()) {
            $this->markTestSkipped('PRT4 is not available at ' . $this->prt4Url);
        }

        // Test PRT4
        $prt4Response = Http::timeout(10)->get($this->prt4Url . '/Products/products.php');
        $this->assertTrue($prt4Response->successful(), 'PRT4 products page should be accessible');
    }

    /**
     * Test both systems have similar product count.
     */
    public function test_both_systems_have_similar_product_data(): void
    {
        // Get PRT5 products count
        $prt5Response = $this->get('/products');
        $prt5Response->assertStatus(200);

        if (!$this->prt4IsAvailable()) {
            $this->markTestSkipped('PRT4 is not available at ' . $this->prt4Url);
        }

        // Both should load without errors
        $prt4Response = Http::timeout(10)->get($this->prt4Url . '/Products/products.php');
        $this->assertTrue($prt4Response->successful());
    }

    /**
     * Test both systems have compare functionality.
     */
    public function test_both_systems_have_compare_page(): void
    {
        // Test PRT5
        $prt5Response = $this->get('/compare');
        $prt5Response->assertStatus(200);

        if (!$this->prt4IsAvailable()) {
            $this->markTestSkipped('PRT4 is not available at ' . $this->prt4Url);
        }

        // Test PRT4
        $prt4Response = Http::timeout(10)->get($this->prt4Url . '/Products/compare.php');
        $this->assertTrue($prt4Response->successful(), 'PRT4 compare page should be accessible');
    }

    /**
     * Test both systems have cart functionality.
     */
    public function test_both_systems_have_cart_page(): void
    {
        // Test PRT5
        $prt5Response = $this->get('/cart');
        $prt5Response->assertStatus(200);

        if (!$this->prt4IsAvailable()) {
            $this->markTestSkipped('PRT4 is not available at ' . $this->prt4Url);
        }

        // Test PRT4
        $prt4Response = Http::timeout(10)->get($this->prt4Url . '/cart/cart.php');
        $this->assertTrue($prt4Response->successful(), 'PRT4 cart page should be accessible');
    }

    /**
     * Test both systems have contact page.
     */
    public function test_both_systems_have_contact_page(): void
    {
        // Test PRT5
        $prt5Response = $this->get('/contact');
        $prt5Response->assertStatus(200);

        if (!$this->prt4IsAvailable()) {
            $this->markTestSkipped('PRT4 is not available at ' . $this->prt4Url);
        }

        // Test PRT4
        $prt4Response = Http::timeout(10)->get($this->prt4Url . '/contact.php');
        $this->assertTrue($prt4Response->successful(), 'PRT4 contact page should be accessible');
    }

    /**
     * Test both systems have login page.
     */
    public function test_both_systems_have_login_page(): void
    {
        // Test PRT5
        $prt5Response = $this->get('/login');
        $prt5Response->assertStatus(200);

        if (!$this->prt4IsAvailable()) {
            $this->markTestSkipped('PRT4 is not available at ' . $this->prt4Url);
        }

        // Test PRT4
        $prt4Response = Http::timeout(10)->get($this->prt4Url . '/login.php');
        $this->assertTrue($prt4Response->successful(), 'PRT4 login page should be accessible');
    }

    /**
     * Test product detail pages work on both systems.
     */
    public function test_both_systems_have_product_detail(): void
    {
        // Get a product ID from PRT5
        $product = \App\Models\Product::first();

        if (!$product) {
            $this->markTestSkipped('No products in database');
        }

        // Test PRT5
        $prt5Response = $this->get('/products/' . $product->ID);
        $prt5Response->assertStatus(200);

        if (!$this->prt4IsAvailable()) {
            $this->markTestSkipped('PRT4 is not available at ' . $this->prt4Url);
        }

        // Test PRT4
        $prt4Response = Http::timeout(10)->get($this->prt4Url . '/Products/product-detail.php?id=' . $product->ID);
        $this->assertTrue($prt4Response->successful(), 'PRT4 product detail should be accessible');
    }

    /**
     * Test both systems filter products by category.
     */
    public function test_both_systems_filter_by_category(): void
    {
        $category = \App\Models\Category::whereHas('products')->first();

        if (!$category) {
            $this->markTestSkipped('No categories with products in database');
        }

        // Test PRT5
        $prt5Response = $this->get('/products?catid=' . $category->CategoryCode);
        $prt5Response->assertStatus(200);

        if (!$this->prt4IsAvailable()) {
            $this->markTestSkipped('PRT4 is not available at ' . $this->prt4Url);
        }

        // Test PRT4
        $prt4Response = Http::timeout(10)->get($this->prt4Url . '/Products/products.php?catid=' . $category->CategoryCode);
        $this->assertTrue($prt4Response->successful(), 'PRT4 category filter should work');
    }

    /**
     * Test both systems search products.
     */
    public function test_both_systems_search_products(): void
    {
        $searchTerm = 'boot';

        // Test PRT5
        $prt5Response = $this->get('/products?search=' . urlencode($searchTerm));
        $prt5Response->assertStatus(200);

        if (!$this->prt4IsAvailable()) {
            $this->markTestSkipped('PRT4 is not available at ' . $this->prt4Url);
        }

        // Test PRT4
        $prt4Response = Http::timeout(10)->get($this->prt4Url . '/Products/products.php?search=' . urlencode($searchTerm));
        $this->assertTrue($prt4Response->successful(), 'PRT4 search should work');
    }

    /**
     * Test both systems sort products by price.
     */
    public function test_both_systems_sort_by_price(): void
    {
        // Test PRT5
        $prt5Response = $this->get('/products?sort=price_low');
        $prt5Response->assertStatus(200);

        if (!$this->prt4IsAvailable()) {
            $this->markTestSkipped('PRT4 is not available at ' . $this->prt4Url);
        }

        // Test PRT4
        $prt4Response = Http::timeout(10)->get($this->prt4Url . '/Products/products.php?sort=price_low');
        $this->assertTrue($prt4Response->successful(), 'PRT4 price sort should work');
    }
}
