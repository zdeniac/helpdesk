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
use App\Services\HelpdeskService;
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
        $dto = $this->service->replyByBot('Hello anybody can help me?', $user->id, false);

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
        $dto = $this->service->replyByBot('Unknown question', $user->id);

        $this->assertEquals(ConversationStatus::WAITING_AGENT->value, $dto->status);
        $this->assertEquals(HelpdeskService::DEFAULT_ANSWER, $dto->messages[1]->message);
    }

    public function test_closes_conversation(): void
    {
        $user = $this->user;
        $conversationDTO = $this->service->startConversation($user->id);

        $conversation = $this->service->closeConversation($conversationDTO->id);

        $this->assertEquals(ConversationStatus::CLOSED->value, $conversation->status);
    }

    public function test_reply_by_agent_stores_agent_message_and_opens_conversation(): void
    {
        $user = $this->user;
        $conversationDTO = $this->service->startConversation($user->id);

        $agentAnswer = 'Agent here to help';
        $agent = User::factory()->create(['role' => UserRole::AGENT->value]);
        
        $dto = $this->service->replyByAgent($agentAnswer, $conversationDTO->id, $agent->id);

        $this->assertInstanceOf(ConversationDTO::class, $dto);
        $this->assertCount(1, $dto->messages);
        $this->assertEquals(HelpdeskMessageSenderType::AGENT->value, $dto->messages[0]->senderType);
        $this->assertEquals($agentAnswer, $dto->messages[0]->message);

        $conversation = Conversation::find($conversationDTO->id);
        $this->assertEquals(ConversationStatus::OPEN->value, $conversation->status);
        $this->assertEquals($agent->id, $conversation->assigned_agent_id);
    }
}