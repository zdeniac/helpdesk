<?php

namespace App\Services;

use App\DTOs\HelpdeskMessageDTO;
use App\Enums\ConversationStatus;
use App\Enums\HelpdeskMessageSenderType;
use App\Models\Conversation;
use App\Models\HelpdeskMessage;

final class HelpdeskAgentService
{
    public function reply(int $conversationId, int $agentId, string $message): HelpdeskMessageDTO
    {
        $conversation = Conversation::findOrFail($conversationId);

        $conversation->assigned_agent_id = $agentId;
        // We change the conversation status back to open
        $conversation->status = ConversationStatus::OPEN->value;
        $conversation->save();

        $reply = HelpdeskMessage::create([
            'conversation_id' => $conversation->id,
            'sender_type' => HelpdeskMessageSenderType::AGENT->value,
            'message' => $message,
        ]);

        return new HelpdeskMessageDTO(
            $reply->id,
            $conversation->id,
            $reply->sender_type,
            $reply->message,
            $reply->created_at
        );
    }

    public function closeConversation(int $conversationId): Conversation
    {
        $conversation = Conversation::findOrFail($conversationId);
        $conversation->status = ConversationStatus::CLOSED->value;
        $conversation->save();

        return $conversation;
    }
}