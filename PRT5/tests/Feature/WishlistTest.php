<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use App\Models\WishlistItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WishlistTest extends TestCase
{
    /**
     * Test wishlist requires authentication.
     */
    public function test_wishlist_page_requires_authentication(): void
    {
        $response = $this->get('/account/wishlist');

        $response->assertRedirect('/login');
    }

    /**
     * Test wishlist toggle requires authentication.
     */
    public function test_wishlist_toggle_requires_authentication(): void
    {
        $product = Product::first();

        if (!$product) {
            $this->markTestSkipped('No products in database');
        }

        $response = $this->postJson('/account/wishlist/toggle', [
            'product_id' => $product->ID,
        ]);

        $response->assertStatus(401);
    }

    /**
     * Test authenticated user can add to wishlist.
     */
    public function test_authenticated_user_can_add_to_wishlist(): void
    {
        $user = User::first();
        $product = Product::first();

        if (!$user || !$product) {
            $this->markTestSkipped('No users or products in database');
        }

        // Clean up any existing wishlist items for this test
        WishlistItem::where('user_id', $user->id)
            ->where('product_id', $product->ID)
            ->delete();

        $response = $this->actingAs($user)->postJson('/account/wishlist/toggle', [
            'product_id' => $product->ID,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'in_wishlist' => true,
        ]);
    }

    /**
     * Test authenticated user can remove from wishlist.
     */
    public function test_authenticated_user_can_remove_from_wishlist(): void
    {
        $user = User::first();
        $product = Product::first();

        if (!$user || !$product) {
            $this->markTestSkipped('No users or products in database');
        }

        // Add to wishlist first
        WishlistItem::firstOrCreate([
            'user_id' => $user->id,
            'product_id' => $product->ID,
        ]);

        // Toggle (should remove)
        $response = $this->actingAs($user)->postJson('/account/wishlist/toggle', [
            'product_id' => $product->ID,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'in_wishlist' => false,
        ]);
    }

    /**
     * Test wishlist check returns correct items.
     */
    public function test_wishlist_check_returns_correct_items(): void
    {
        $user = User::first();
        $products = Product::take(3)->get();

        if (!$user || $products->count() < 3) {
            $this->markTestSkipped('Need user and at least 3 products');
        }

        // Clean up
        WishlistItem::where('user_id', $user->id)->delete();

        // Add some products to wishlist
        foreach ($products->take(2) as $product) {
            WishlistItem::create([
                'user_id' => $user->id,
                'product_id' => $product->ID,
            ]);
        }

        $productIds = $products->pluck('ID')->implode(',');
        $response = $this->actingAs($user)->getJson('/account/wishlist/check?product_ids=' . $productIds);

        $response->assertStatus(200);
        $response->assertJsonStructure(['wishlisted']);
        $this->assertCount(2, $response->json('wishlisted'));
    }

    /**
     * Test wishlist page shows items.
     */
    public function test_wishlist_page_shows_items(): void
    {
        $user = User::first();
        $product = Product::first();

        if (!$user || !$product) {
            $this->markTestSkipped('No users or products in database');
        }

        // Add to wishlist
        WishlistItem::firstOrCreate([
            'user_id' => $user->id,
            'product_id' => $product->ID,
        ]);

        $response = $this->actingAs($user)->get('/account/wishlist');

        $response->assertStatus(200);
        $response->assertViewIs('account.wishlist.index');
    }

    /**
     * Test wishlist validates product exists.
     */
    public function test_wishlist_validates_product_exists(): void
    {
        $user = User::first();

        if (!$user) {
            $this->markTestSkipped('No users in database');
        }

        $response = $this->actingAs($user)->postJson('/account/wishlist/toggle', [
            'product_id' => 999999,
        ]);

        $response->assertStatus(422); // Validation error
    }
}
