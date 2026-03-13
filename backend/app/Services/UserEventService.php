<?php

namespace App\Services;

use App\Models\Event;
use Illuminate\Database\Eloquent\Collection;

final class UserEventService
{
    /**
     * @param array $data ['title' => string, 'occurrence' => datetime, 'description' => string|null, 'user_id' => int]
     */
    public function createByUser(array $data, int $userId): Event
    {
        return Event::create([
            'title' => $data['title'],
            'occurrence' => $data['occurrence'],
            'description' => $data['description'] ?? null,
            'user_id' => $userId,
        ]);
    }

    public function findByUser(int $eventId, int $userId): Event
    {
        return Event::where(['id' => $eventId, 'user_id' => $userId])->firstOrFail();
    }

    public function listByUser(int $userId): Collection
    {
        return Event::where('user_id', $userId)
            ->orderBy('occurrence', 'asc')
            ->get();
    }

    /**
     * @throws ModelNotFoundException
     */
    public function updateByUser(int $eventId, array $data, int $userId): Event
    {
        $event = Event::where(['id' => $eventId, 'user_id' => $userId])->firstOrFail();
        $event->update($data);
        return $event;
    }

    /**
     * @throws ModelNotFoundException
     */
    public function deleteByUser(int $eventId, int $userId): bool
    {
        $event = Event::where(['id' => $eventId, 'user_id' => $userId])->firstOrFail();
        $event->delete();
        return true;
    }
}
