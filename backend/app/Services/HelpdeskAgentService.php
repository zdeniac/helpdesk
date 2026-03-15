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
        $conversation = $this->updateConversationStatus(
            $conversation,
            ConversationStatus::OPEN,
        );

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
        return $this->updateConversationStatus(
            Conversation::findOrFail($conversationId), 
            ConversationStatus::CLOSED
        );
    }

    public function updateConversationStatus(Conversation $conversation, ConversationStatus $status): Conversation
    {
        $conversation->status = $status->value;
        $conversation->save();
        return $conversation;
    }
}