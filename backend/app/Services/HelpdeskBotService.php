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
     * Return the last non-closed conversation or create a new one
     */
    public function startConversation(int $userId): Conversation
    {
        $conversation = Conversation::where('user_id', $userId)
            ->where('status', '!=', ConversationStatus::CLOSED->value)
            ->with('messages')
            ->first();

        return $conversation ?? Conversation::create([
            'user_id' => $userId,
            'status' => ConversationStatus::OPEN->value,
        ]);
    }

    /**
     * User asks a question, bot replies.
     */
    public function reply(string $question, int $userId, bool $useFullTextSearch = true): ConversationDTO
    {
        $conversation = $this->startConversation($userId);

        $this->storeMessage($conversation->id, HelpdeskMessageSenderType::USER->value, $question);

        $answer = $this->getAnswer($question, $useFullTextSearch);

        if ($answer === self::DEFAULT_ANSWER) {
            $conversation->status = ConversationStatus::WAITING_AGENT->value;
            $conversation->save();
        }

        $this->storeMessage($conversation->id, HelpdeskMessageSenderType::BOT->value, $answer);

        return $this->toConversationDTO($conversation);
    }

    private function storeMessage(int $conversationId, string $senderType, string $message): void
    {
        HelpdeskMessage::create([
            'conversation_id' => $conversationId,
            'sender_type' => $senderType,
            'message' => $message,
        ]);
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

    private function toConversationDTO(Conversation $conversation): ConversationDTO
    {
        // refresh messages
        $conversation->load('messages');

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