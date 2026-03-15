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
use App\Services\HelpdeskBotService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HelpdeskBotServiceTest extends TestCase
{
    use RefreshDatabase;

    private readonly HelpdeskBotService $service;
    private readonly User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new HelpdeskBotService();
        $this->user = User::factory()->create(['role' => UserRole::USER->value]);
    }

    public function test_creates_a_new_conversation_if_none_exists(): void
    {
        $user = $this->user;

        $dto = $this->service->startConversation($user->id);

        $this->assertInstanceOf(ConversationDTO::class, $dto);
        $this->assertEquals($user->id, $dto->userId);
        $this->assertEquals(ConversationStatus::OPEN->value, $dto->status);
        $this->assertIsIterable($dto->messages);
        // empty dto
        $this->assertCount(0, $dto->messages);
    }

    public function test_reuses_existing_open_conversation(): void
    {
        $user = $this->user;

        $existing = Conversation::create([
            'user_id' => $user->id,
            'status' => ConversationStatus::OPEN->value,
        ]);

        $dto = $this->service->startConversation($user->id);

        $this->assertEquals($existing->id, $dto->id);
    }

    public function test_returns_a_conversation_dto_with_messages(): void
    {
        $user = $this->user;

        HelpdeskArticle::create([
            'question' => 'Hello anybody can help me?',
            'answer' => 'Yes, we are here to help!',
        ]);

        $this->service->startConversation($user->id);

        // We are not using the full text search because it is non-deterministic
        $dto = $this->service->reply('Hello anybody can help me?', $user->id, false);

        $this->assertInstanceOf(ConversationDTO::class, $dto);
        $this->assertEquals($user->id, $dto->userId);
        $this->assertCount(2, $dto->messages);
        $this->assertInstanceOf(HelpdeskMessageDTO::class, $dto->messages[0]);
        $this->assertInstanceOf(HelpdeskMessageDTO::class, $dto->messages[1]);

        // senderTypes
        $this->assertEquals(HelpdeskMessageSenderType::USER->value, $dto->messages[0]->senderType);
        $this->assertEquals(HelpdeskMessageSenderType::BOT->value, $dto->messages[1]->senderType);

        // bot answer
        $this->assertEquals('Yes, we are here to help!', $dto->messages[1]->message);
    }

    public function test_sets_conversation_status_to_waiting_agent_if_no_answer(): void
    {
        $user = $this->user;
        
        $this->service->startConversation($user->id);
        $dto = $this->service->reply('Unknown question', $user->id);

        $this->assertEquals(ConversationStatus::WAITING_AGENT->value, $dto->status);
        $this->assertEquals(HelpdeskBotService::DEFAULT_ANSWER, $dto->messages[1]->message);
    }
}