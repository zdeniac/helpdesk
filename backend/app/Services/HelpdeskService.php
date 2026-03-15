<?php

namespace App\Services;

use App\DTOs\ConversationDTO;
use App\Enums\ConversationStatus;
use App\Enums\HelpdeskMessageSenderType;
use App\Models\Conversation;
use App\Models\HelpdeskArticle;
use App\Models\HelpdeskMessage;
use App\DTOs\HelpdeskMessageDTO;

final class HelpdeskService
{
    const string DEFAULT_ANSWER = 'Please contact our colleagues.';

    public function replyByAgent(string $agentAnswer, int $conversationId, int $agentId): ConversationDTO
    {
        $conversation = Conversation::with('messages')
            ->findOrFail($conversationId);
        
        $conversation->assigned_agent_id = $agentId;

        // We change the conversation status back to agent
        $conversation = $this->updateConversationStatus(
            $conversation,
            ConversationStatus::AGENT,
        );

        $reply = $this->storeMessage($conversationId, HelpdeskMessageSenderType::AGENT, $agentAnswer);

        $conversation->setRelation('messages', $conversation->messages->concat([$reply]));

        return $this->toDTO($conversation);
    }

    public function findConversationWithMessages(int $conversationId): ConversationDTO
    {
        $conversation = Conversation::with('messages')->findOrFail($conversationId);
        return $this->toDTO($conversation);
    }

    public function closeConversation(int $conversationId): Conversation
    {
        return $this->updateConversationStatus(
            Conversation::findOrFail($conversationId), 
            ConversationStatus::CLOSED
        );
    }

    /**
     * Return last non-closed conversation or create a new one (returns model)
     */
    public function startConversation(int $userId): ConversationDTO
    {
        $conversation = $this->getConversation($userId) 
        ?? Conversation::create([
            'user_id' => $userId,
            'status' => ConversationStatus::OPEN->value,
        ]);

        return $this->toDTO($conversation);
    }

    public function replyByBot(string $question, int $userId, bool $useFullTextSearch = true): ConversationDTO
    {
        $conversation = $this->getConversation($userId);

        $userMsg = $this->storeMessage($conversation->id, HelpdeskMessageSenderType::USER, $question);

        if ($conversation->status === ConversationStatus::AGENT->value) {
            $conversation->setRelation('messages', $conversation->messages->concat([$userMsg]));
            return $this->toDTO($conversation);
        }
        
        $answer = $this->getAnswer($question, $useFullTextSearch);

        if ($answer === self::DEFAULT_ANSWER) {
            $conversation->status = ConversationStatus::WAITING_AGENT->value;
            $conversation->save();
        }

        $botMsg = $this->storeMessage($conversation->id, HelpdeskMessageSenderType::BOT, $answer);
        // Attach the new messages to conversation without querying
        $conversation->setRelation('messages', $conversation->messages->concat([$userMsg, $botMsg]));

        return $this->toDTO($conversation);
    }

    private function storeMessage(int $conversationId, HelpdeskMessageSenderType $senderType, string $message): HelpdeskMessage
    {
        return HelpdeskMessage::create([
            'conversation_id' => $conversationId,
            'sender_type' => $senderType->value,
            'message' => $message,
        ]);
    }

    private function getConversation(int $userId): ?Conversation
    {
        return Conversation::where('user_id', $userId)
            ->where('status', '!=', ConversationStatus::CLOSED->value)
            ->with('messages')
            ->first();
    }

    private function getAnswer(string $question, bool $useFullTextSearch): string
    {
        $query = HelpdeskArticle::query();

        if ($useFullTextSearch) {
            $query->whereFullText(['question', 'answer'], $question);
        } else {
            $query->where('question', 'like', "%{$question}%");
        }

        return $query->first()?->answer ?? self::DEFAULT_ANSWER;
    }

    private function updateConversationStatus(
        Conversation $conversation, 
        ConversationStatus $status
    ): Conversation {
        $conversation->status = $status->value;
        $conversation->save();
        return $conversation;
    }

    private function toDTO(Conversation $conversation): ConversationDTO
    {
        $messages = $conversation->messages->map(fn(HelpdeskMessage $msg) => new HelpdeskMessageDTO(
            $msg->id,
            $conversation->id,
            $msg->sender_type,
            $msg->message,
            $msg->created_at
        ));

        return new ConversationDTO(
            $conversation->id,
            $conversation->user_id,
            $conversation->status,
            $messages
        );
    }
}