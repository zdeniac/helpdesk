<?php

namespace Tests\Unit;

use App\Enums\UserRole;
use App\Models\Event;
use App\Models\User;
use App\Services\UserEventService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class UserEventServiceTest extends TestCase
{
    use RefreshDatabase;

    private UserEventService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new UserEventService();
    }

    public function test_creates_event(): void
    {
        $user = User::factory()->create(['role' => UserRole::USER->value]);

        $data = [
            'title' => 'Test event',
            'occurrence' => Carbon::parse('2026-01-01 12:00:00'),
            'description' => 'Test description',
            'user_id' => $user->id,
        ];

        $event = $this->service->createByUser($data, $user->id);

        $this->assertDatabaseHas('events', [
            'title' => 'Test event',
            'description' => 'Test description',
            'user_id' => $user->id,
        ]);

        $this->assertInstanceOf(Event::class, $event);
    }

    public function test_returns_all_events_for_user(): void
    {
        /** @var Collection $user */
        $user = User::factory()
            ->count(1)
            ->hasEvents(3)
            ->create(['role' => UserRole::USER->value]);

        $events = $this->service->listByUser($user[0]->id);
        $this->assertCount(3, $events);
    }

    public function test_lists_events_by_user(): void
    {
        $userA = User::factory()->create(['role' => UserRole::USER->value]);
        $userB = User::factory()->create(['role' => UserRole::USER->value]);

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

    public function test_updates_event(): void
    {
        $user = User::factory()->create(['role' => UserRole::USER->value]);

        $event = Event::factory()->create([
            'description' => 'Old description',
            'user_id' => $user->id
        ]);

        $updated = $this->service->updateByUser($event->id, ['description' => 'New description'], $user->id);

        $this->assertEquals('New description', $updated->description);

        $this->assertDatabaseHas('events', [
            'id' => $event->id,
            'description' => 'New description',
        ]);
    }

    public function test_deletes_event(): void
    {
        $user = User::factory()->create(['role' => UserRole::USER->value]);
        $event = Event::factory()->create(['user_id' => $user->id]);

        $result = $this->service->deleteByUser($event->id, $user->id);

        $this->assertTrue($result);

        $this->assertDatabaseMissing('events', [
            'id' => $event->id,
        ]);
    }
}
