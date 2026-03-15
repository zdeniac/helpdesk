<?php

namespace App\Services;

use App\DTOs\ConversationDTO;
use App\Enums\ConversationStatus;
use App\Enums\HelpdeskMessageSenderType;
use App\Models\Conversation;
use App\Models\HelpdeskArticle;
use App\Models\HelpdeskMessage;
use App\DTOs\HelpdeskMessageDTO;

final class HelpdeskBotService
{
    const string DEFAULT_ANSWER = 'Please contact our colleagues.';

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

    public function reply(string $question, int $userId, bool $useFullTextSearch = true): ConversationDTO
    {
        $conversation = $this->getConversation($userId);

        $userMsg = $this->storeMessage($conversation->id, HelpdeskMessageSenderType::USER->value, $question);
        $answer = $this->getAnswer($question, $useFullTextSearch);

        if ($answer === self::DEFAULT_ANSWER) {
            $conversation->status = ConversationStatus::WAITING_AGENT->value;
            $conversation->save();
        }

        $botMsg = $this->storeMessage($conversation->id, HelpdeskMessageSenderType::BOT->value, $answer);
        // Attach the new messages to conversation without querying
        $conversation->setRelation('messages', $conversation->messages->concat([$userMsg, $botMsg]));

        return $this->toDTO($conversation);
    }

    private function storeMessage(int $conversationId, string $senderType, string $message): HelpdeskMessage
    {
        return HelpdeskMessage::create([
            'conversation_id' => $conversationId,
            'sender_type' => $senderType,
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