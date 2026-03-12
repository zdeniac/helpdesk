<?php

namespace Tests\Unit;

use App\DTOs\ConversationDTO;
use App\DTOs\HelpdeskMessageDTO;
use App\Enums\ConversationStatus;
use App\Enums\HelpdeskMessageSenderType;
use App\Enums\UserRole;
use App\Models\Conversation;
use App\Models\HelpdeskArticle;
use App\Models\User;
use App\Service\HelpdeskService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HelpdeskServiceTest extends TestCase
{
    use RefreshDatabase;

    private readonly HelpdeskService $service;
    private readonly User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new HelpdeskService();
        $this->user = User::factory()->create(['role' => UserRole::USER->value]);
    }

    public function test_creates_a_new_conversation_if_none_exists(): void
    {
        $user = $this->user;

        $conversation = $this->service->startConversation($user->id);

        $this->assertInstanceOf(Conversation::class, $conversation);
        $this->assertEquals($user->id, $conversation->user_id);
        $this->assertEquals(ConversationStatus::OPEN->value, $conversation->status);
    }

    public function test_reuses_existing_open_conversation(): void
    {
        $user = $this->user;

        $existing = Conversation::create([
            'user_id' => $user->id,
            'status' => ConversationStatus::OPEN->value,
        ]);

        $conversation = $this->service->startConversation($user->id);

        $this->assertEquals($existing->id, $conversation->id);
    }

    public function test_returns_a_conversation_dto_with_messages(): void
    {
        $user = $this->user;

        HelpdeskArticle::create([
            'question' => 'Hello',
            'answer' => 'Hi there!',
        ]);

        $dto = $this->service->botReply($user->id, 'Hello');

        $this->assertInstanceOf(ConversationDTO::class, $dto);
        $this->assertEquals($user->id, $dto->userId);
        $this->assertCount(2, $dto->messages);
        $this->assertInstanceOf(HelpdeskMessageDTO::class, $dto->messages[0]);
        $this->assertInstanceOf(HelpdeskMessageDTO::class, $dto->messages[1]);

        // sender_type-ok
        $this->assertEquals(HelpdeskMessageSenderType::USER->value, $dto->messages[0]->senderType);
        $this->assertEquals(HelpdeskMessageSenderType::BOT->value, $dto->messages[1]->senderType);

        // bot válasza
        $this->assertEquals('Hi there!', $dto->messages[1]->message);
    }

    public function test_sets_conversation_status_to_waiting_agent_if_no_answer(): void
    {
        $user = $this->user;

        $dto = $this->service->botReply($user->id, 'Unknown question');

        $this->assertEquals(ConversationStatus::WAITING_AGENT->value, $dto->status);
        $this->assertEquals('Please contact our colleagues for further answers.', $dto->messages[1]->message);
    }

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
    }
}