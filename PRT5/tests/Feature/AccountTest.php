<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class AccountTest extends TestCase
{
    /**
     * Test account dashboard requires authentication.
     */
    public function test_account_dashboard_requires_auth(): void
    {
        $response = $this->get('/account');

        $response->assertRedirect('/login');
    }

    /**
     * Test authenticated user can access account dashboard.
     */
    public function test_authenticated_user_can_access_dashboard(): void
    {
        $user = User::first();

        if (!$user) {
            $this->markTestSkipped('No users in database');
        }

        $response = $this->actingAs($user)->get('/account');

        $response->assertStatus(200);
    }

    /**
     * Test orders page requires authentication.
     */
    public function test_orders_page_requires_auth(): void
    {
        $response = $this->get('/account/orders');

        $response->assertRedirect('/login');
    }

    /**
     * Test authenticated user can access orders page.
     */
    public function test_authenticated_user_can_access_orders(): void
    {
        $user = User::first();

        if (!$user) {
            $this->markTestSkipped('No users in database');
        }

        $response = $this->actingAs($user)->get('/account/orders');

        $response->assertStatus(200);
    }

    /**
     * Test addresses page requires authentication.
     */
    public function test_addresses_page_requires_auth(): void
    {
        $response = $this->get('/account/addresses');

        $response->assertRedirect('/login');
    }

    /**
     * Test authenticated user can access addresses page.
     */
    public function test_authenticated_user_can_access_addresses(): void
    {
        $user = User::first();

        if (!$user) {
            $this->markTestSkipped('No users in database');
        }

        $response = $this->actingAs($user)->get('/account/addresses');

        $response->assertStatus(200);
    }

    /**
     * Test settings page requires authentication.
     */
    public function test_settings_page_requires_auth(): void
    {
        $response = $this->get('/account/settings');

        $response->assertRedirect('/login');
    }

    /**
     * Test authenticated user can access settings page.
     */
    public function test_authenticated_user_can_access_settings(): void
    {
        $user = User::first();

        if (!$user) {
            $this->markTestSkipped('No users in database');
        }

        $response = $this->actingAs($user)->get('/account/settings');

        $response->assertStatus(200);
    }

    /**
     * Test create address page loads.
     */
    public function test_create_address_page_loads(): void
    {
        $user = User::first();

        if (!$user) {
            $this->markTestSkipped('No users in database');
        }

        $response = $this->actingAs($user)->get('/account/addresses/create');

        $response->assertStatus(200);
    }
}
