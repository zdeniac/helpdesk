<?php

namespace Tests\Unit;

use App\Enums\ConversationStatus;
use App\Enums\UserRole;
use App\Models\Conversation;
use App\Models\User;
use App\Services\HelpdeskAgentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\TestCase;

class HelpdeskAgentServiceTest extends TestCase
{
    use RefreshDatabase;

    private readonly HelpdeskAgentService $service;
    private readonly User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new HelpdeskAgentService();
        $this->user = User::factory()->create(['role' => UserRole::USER->value]);
    }
/*
    public function test_can_close_a_conversation(): void
    {
        $user = $this->user;

        $conversation = Conversation::create([
            'user_id' => $user->id,
            'status' => ConversationStatus::OPEN->value,
        ]);

        $closed = $this->service->closeConversation($conversation->id);

        $this->assertEquals(ConversationStatus::CLOSED->value, $closed->status);

        $this->assertDatabaseHas('conversations', [
            'id' => $conversation->id,
            'status' => ConversationStatus::CLOSED->value,
        ]);
    }*/
}
