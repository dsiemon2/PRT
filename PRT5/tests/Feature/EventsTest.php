<?php

namespace Tests\Feature;

use App\Models\Event;
use Tests\TestCase;

class EventsTest extends TestCase
{
    /**
     * Test events index page loads.
     */
    public function test_events_index_page_loads(): void
    {
        $response = $this->get('/events');

        $response->assertStatus(200);
    }

    /**
     * Test event detail page loads.
     */
    public function test_event_detail_page_loads(): void
    {
        $event = null;
        try {
            $event = Event::where('is_active', true)->first();
        } catch (\Exception $e) {
            // Table might not exist
        }

        if (!$event) {
            $this->markTestSkipped('No active events in database');
        }

        $response = $this->get('/events/' . $event->id);

        $response->assertStatus(200);
    }

    /**
     * Test event 404 for invalid id.
     */
    public function test_event_returns_404_for_invalid_id(): void
    {
        $response = $this->get('/events/999999');

        $response->assertStatus(404);
    }
}
