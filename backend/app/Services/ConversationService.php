<?php

namespace App\Services;

use App\Enums\ConversationStatus;
use App\Models\Conversation;
use Illuminate\Support\Collection;

final class ConversationService
{
    public function listForAgent(
        ConversationStatus $status = ConversationStatus::WAITING_AGENT,
        string $orderBy = 'created_at',
        string $order = 'DESC',
    ): Collection {
        return Conversation::orderBy($orderBy, $order)
            ->where([
                'status' => ConversationStatus::WAITING_AGENT->value, 
                'status' => ConversationStatus::AGENT->value,
            ])
            ->with('user')
            ->get();        
    }

    public function find(int $id): Conversation
    {
        return Conversation::findOrFail($id);
    }
}