<?php

namespace App\Services;

use App\DTOs\ConversationDTO;
use App\Enums\ConversationStatus;
use App\Enums\HelpdeskMessageSenderType;
use App\Models\Conversation;
use App\Models\HelpdeskArticle;
use App\Models\HelpdeskMessage;
use App\DTOs\HelpdeskMessageDTO;
use Illuminate\Support\Collection;

final class HelpdeskBotService
{
    const string DEFAULT_ANSWER = 'Please contact our colleagues.';

    /**
     * Return the last non-closed conversation or create a new one
     */
    public function startConversation(int $userId): Conversation
    {
        $conversation = Conversation::where([
            'user_id' => $userId,
            ['status', '!=', ConversationStatus::CLOSED->value]
        ])->with('messages')->first();

        if ($conversation === null) {
            $conversation = Conversation::create([
                'user_id' => $userId,
                'status' => ConversationStatus::OPEN->value,
            ]);
        }

        return $conversation;
    }

    public function reply(string $question, int $userId): ConversationDTO
    {
        $conversation = $this->startConversation($userId);

        HelpdeskMessage::create([
            'conversation_id' => $conversation->id,
            'sender_type' => HelpdeskMessageSenderType::USER->value,
            'message' => $question,
        ]);

        $answer = HelpdeskArticle::where('question', 'like', "%{$question}%")
            ->first()?->answer ?? self::DEFAULT_ANSWER;
        
        if ($answer == self::DEFAULT_ANSWER) {
            $conversation->status = ConversationStatus::WAITING_AGENT->value;
            $conversation->save();
        }

        HelpdeskMessage::create([
            'conversation_id' => $conversation->id,
            'sender_type' => HelpdeskMessageSenderType::BOT->value,
            'message' => $answer,
        ]);

        /** @var Collection $replies */
        $replies = $conversation->messages->map(fn (HelpdeskMessage $msg) => new HelpdeskMessageDTO(
            $msg->id,
            $conversation->id,
            $msg->sender_type,
            $msg->message,
            $msg->created_at
        ));

        return new ConversationDTO($conversation->id, $userId, $conversation->status, $replies);
    }
}