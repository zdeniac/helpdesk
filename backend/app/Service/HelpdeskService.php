<?php

namespace App\Service;

use App\DTOs\ConversationDTO;
use App\Enums\ConversationStatus;
use App\Enums\HelpdeskMessageSenderType;
use App\Models\Conversation;
use App\Models\HelpdeskArticle;
use App\Models\HelpdeskMessage;
use App\DTOs\HelpdeskMessageDTO;
use Illuminate\Support\Collection;

final class HelpdeskService
{
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

    public function replyToUser(int $userId, string $message): ConversationDTO
    {
        $conversation = $this->startConversation($userId);

        HelpdeskMessage::create([
            'conversation_id' => $conversation->id,
            'sender_type' => HelpdeskMessageSenderType::USER->value,
            'message' => $message,
        ]);

        $defaultAnswer = 'Please contact our colleagues for further answers.';

        $answer = HelpdeskArticle::where('question', 'like', "%{$message}%")
            ->first()?->answer ?? $defaultAnswer;
        
        if ($answer == $defaultAnswer) {
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

    public function closeConversation(int $conversationId): Conversation
    {
        $conversation = Conversation::findOrFail($conversationId);
        $conversation->status = ConversationStatus::CLOSED->value;
        $conversation->save();

        return $conversation;
    }
}