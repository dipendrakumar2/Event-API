<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Models\Booking;
use App\Models\Event;

class EventTest extends TestCase
{
    /**
     * List  All events.
     */
 
    public function test_index()
    {
        $events = Event::all();
        $events->assertStatus(201);
    }
}
