<?php

namespace Tests\Feature;

use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    /**
     * Test about page loads.
     */
    public function test_about_page_loads(): void
    {
        $response = $this->get('/about');

        $response->assertStatus(200);
    }

    /**
     * Test contact page loads.
     */
    public function test_contact_page_loads(): void
    {
        $response = $this->get('/contact');

        $response->assertStatus(200);
    }

    /**
     * Test FAQ page loads.
     */
    public function test_faq_page_loads(): void
    {
        $response = $this->get('/faq');

        $response->assertStatus(200);
    }

    /**
     * Test privacy page loads.
     */
    public function test_privacy_page_loads(): void
    {
        $response = $this->get('/privacy');

        $response->assertStatus(200);
    }

    /**
     * Test terms page loads.
     */
    public function test_terms_page_loads(): void
    {
        $response = $this->get('/terms');

        $response->assertStatus(200);
    }

    /**
     * Test shipping page loads.
     */
    public function test_shipping_page_loads(): void
    {
        $response = $this->get('/shipping');

        $response->assertStatus(200);
    }

    /**
     * Test returns page loads.
     */
    public function test_returns_page_loads(): void
    {
        $response = $this->get('/returns');

        $response->assertStatus(200);
    }

    /**
     * Test gift cards page loads.
     */
    public function test_gift_cards_page_loads(): void
    {
        $response = $this->get('/gift-cards');

        $response->assertStatus(200);
    }

    /**
     * Test contact form submission.
     */
    public function test_contact_form_submission(): void
    {
        $response = $this->post('/contact', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'subject' => 'Test Subject',
            'message' => 'This is a test message for the contact form.',
        ]);

        // Should redirect with success or show validation
        $response->assertStatus(302);
    }

    /**
     * Test contact form validation.
     */
    public function test_contact_form_requires_fields(): void
    {
        $response = $this->post('/contact', []);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['name', 'email', 'message']);
    }
}
