<?php

namespace Tests\Unit;

use App\DTOs\HelpdeskMessageDTO;
use App\Enums\ConversationStatus;
use App\Enums\HelpdeskMessageSenderType;
use App\Models\Conversation;
use App\Models\User;
use App\Services\HelpdeskAgentService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HelpdeskAgentServiceTest extends TestCase
{
    use RefreshDatabase;

    private HelpdeskAgentService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new HelpdeskAgentService();
    }

    public function test_reply_creates_a_message_and_assigns_agent(): void
    {
        $conversation = Conversation::factory()->create([
            'status' => ConversationStatus::WAITING_AGENT->value,
        ]);

        $agent = User::factory(1)->create();
        $agentId = $agent[0]->id;
        $messageText = 'Ez egy teszt válasz';

        $dto = $this->service->reply($conversation->id, $agentId, $messageText);
        $conversation->refresh();
        $this->assertEquals($agentId, $conversation->assigned_agent_id);
        $this->assertEquals(ConversationStatus::OPEN->value, $conversation->status);

        // test message exists
        $this->assertDatabaseHas('helpdesk_messages', [
            'conversation_id' => $conversation->id,
            'sender_type' => HelpdeskMessageSenderType::AGENT->value,
            'message' => $messageText,
        ]);

        // test dto
        $this->assertInstanceOf(HelpdeskMessageDTO::class, $dto);
        $this->assertEquals($conversation->id, $dto->conversationId);
        $this->assertEquals($messageText, $dto->message);
        $this->assertEquals(HelpdeskMessageSenderType::AGENT->value, $dto->senderType);
    }

    public function test_closeConversation_sets_status_to_closed(): void
    {
        $conversation = Conversation::factory()->create([
            'status' => ConversationStatus::OPEN->value,
        ]);

        $closedConversation = $this->service->closeConversation($conversation->id);

        $conversation->refresh();
        $this->assertEquals(ConversationStatus::CLOSED->value, $conversation->status);
        $this->assertEquals($conversation->id, $closedConversation->id);
    }

    public function test_reply_throws_exception_if_conversation_not_found(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $this->service->reply(9999, 1, 'test');
    }

    public function test_closeConversation_throws_exception_if_conversation_not_found(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $this->service->closeConversation(9999);
    }
}
