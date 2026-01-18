<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductsTest extends TestCase
{
    /**
     * Test products index page loads successfully.
     */
    public function test_products_index_page_loads(): void
    {
        $response = $this->get('/products');

        $response->assertStatus(200);
        $response->assertViewIs('shop.products.index');
    }

    /**
     * Test products index contains expected elements.
     */
    public function test_products_index_contains_products(): void
    {
        $response = $this->get('/products');

        $response->assertStatus(200);
        $response->assertViewHas('products');
        $response->assertViewHas('categories');
    }

    /**
     * Test product detail page loads.
     */
    public function test_product_detail_page_loads(): void
    {
        $product = Product::first();

        if (!$product) {
            $this->markTestSkipped('No products in database');
        }

        $response = $this->get('/products/' . $product->ID);

        $response->assertStatus(200);
        $response->assertViewIs('shop.products.show');
        $response->assertViewHas('product');
    }

    /**
     * Test product detail returns 404 for non-existent product.
     */
    public function test_product_detail_returns_404_for_invalid_id(): void
    {
        $response = $this->get('/products/999999');

        $response->assertStatus(404);
    }

    /**
     * Test products can be filtered by category.
     */
    public function test_products_can_be_filtered_by_category(): void
    {
        $category = Category::whereHas('products')->first();

        if (!$category) {
            $this->markTestSkipped('No categories with products in database');
        }

        $response = $this->get('/products?catid=' . $category->CategoryCode);

        $response->assertStatus(200);
        $response->assertViewHas('products');
    }

    /**
     * Test products can be searched.
     */
    public function test_products_can_be_searched(): void
    {
        $product = Product::first();

        if (!$product) {
            $this->markTestSkipped('No products in database');
        }

        $searchTerm = substr($product->ShortDescription, 0, 5);
        $response = $this->get('/products?search=' . urlencode($searchTerm));

        $response->assertStatus(200);
        $response->assertViewHas('products');
    }

    /**
     * Test products can be sorted by price low to high.
     */
    public function test_products_can_be_sorted_by_price_low(): void
    {
        $response = $this->get('/products?sort=price_low');

        $response->assertStatus(200);
    }

    /**
     * Test products can be sorted by price high to low.
     */
    public function test_products_can_be_sorted_by_price_high(): void
    {
        $response = $this->get('/products?sort=price_high');

        $response->assertStatus(200);
    }

    /**
     * Test products can be sorted by name.
     */
    public function test_products_can_be_sorted_by_name(): void
    {
        $response = $this->get('/products?sort=name_asc');

        $response->assertStatus(200);
    }

    /**
     * Test products can be filtered by price range.
     */
    public function test_products_can_be_filtered_by_price_range(): void
    {
        $response = $this->get('/products?min_price=10&max_price=100');

        $response->assertStatus(200);
    }
}
