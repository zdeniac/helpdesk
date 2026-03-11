<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use App\Service\EventService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventServiceTest extends TestCase
{
    use RefreshDatabase;

    private EventService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new EventService();
    }

    public function test_it_creates_event(): void
    {
        $user = User::factory()->create();

        $data = [
            'title' => 'Test event',
            'occurrence' => Carbon::parse('2026-01-01 12:00:00'),
            'description' => 'Test description',
            'user_id' => $user->id,
        ];

        $event = $this->service->create($data);

        $this->assertDatabaseHas('events', [
            'title' => 'Test event',
            'description' => 'Test description',
            'user_id' => $user->id,
        ]);

        $this->assertInstanceOf(Event::class, $event);
    }

    public function test_it_returns_all_events(): void
    {
        Event::factory()->count(3)->create();

        $events = $this->service->all();

        $this->assertCount(3, $events);
    }

    public function test_it_lists_events_by_user(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        Event::factory()->create([
            'user_id' => $userA->id,
            'occurrence' => now()->addDay(),
        ]);

        Event::factory()->create([
            'user_id' => $userA->id,
            'occurrence' => now()->addDays(3),
        ]);

        Event::factory()->create([
            'user_id' => $userB->id,
        ]);

        $events = $this->service->listByUser($userA->id);

        $this->assertCount(2, $events);
        $this->assertEquals($userA->id, $events->first()->user_id);
    }

    public function test_it_updates_event_description(): void
    {
        $event = Event::factory()->create([
            'description' => 'Old description'
        ]);

        $updated = $this->service->update($event->id, ['description' => 'New description']);

        $this->assertEquals('New description', $updated->description);

        $this->assertDatabaseHas('events', [
            'id' => $event->id,
            'description' => 'New description',
        ]);
    }

    public function test_it_deletes_event(): void
    {
        $event = Event::factory()->create();

        $result = $this->service->delete($event->id);

        $this->assertTrue($result);

        $this->assertDatabaseMissing('events', [
            'id' => $event->id,
        ]);
    }
}
