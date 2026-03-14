<?php

namespace App\Services;

use App\Enums\ConversationStatus;
use App\Models\Conversation;
use Illuminate\Support\Collection;

final class ConversationService
{
    public function listByStatus(
        ConversationStatus $status = ConversationStatus::WAITING_AGENT,
        string $orderBy = 'created_at',
        string $order = 'DESC',
    ): Collection {
        return Conversation::where('status', $status->value)
            ->orderBy([$orderBy => $order])
            ->get();        
    }

    public function find(int $id): Conversation
    {
        return Conversation::findOrFail($id);
    }
}