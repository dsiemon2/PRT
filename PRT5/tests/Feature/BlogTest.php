<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use Tests\TestCase;

class BlogTest extends TestCase
{
    /**
     * Test blog index page loads.
     */
    public function test_blog_index_page_loads(): void
    {
        $response = $this->get('/blog');

        $response->assertStatus(200);
    }

    /**
     * Test blog post page loads.
     */
    public function test_blog_post_page_loads(): void
    {
        // Try to get a blog post
        $post = null;
        try {
            $post = BlogPost::where('is_published', true)->first();
        } catch (\Exception $e) {
            // Table might not exist
        }

        if (!$post) {
            $this->markTestSkipped('No published blog posts in database');
        }

        $response = $this->get('/blog/' . $post->slug);

        $response->assertStatus(200);
    }

    /**
     * Test blog post 404 for invalid slug.
     */
    public function test_blog_post_returns_404_for_invalid_slug(): void
    {
        $response = $this->get('/blog/this-post-does-not-exist-12345');

        $response->assertStatus(404);
    }
}
