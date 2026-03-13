<?php

namespace App\Services;

use App\Models\Event;
use Illuminate\Database\Eloquent\Collection;

final class EventService
{
    /**
     * @param array $data ['title' => string, 'occurrence' => datetime, 'description' => string|null, 'user_id' => int]
     */
    public function create(array $data): Event
    {
        return Event::create([
            'title' => $data['title'],
            'occurrence' => $data['occurrence'],
            'description' => $data['description'] ?? null,
            'user_id' => $data['user_id'],
        ]);
    }

    public function all(): Collection
    {
        return Event::all();
    }

    public function find(int $id): Event
    {
        return Event::findOrFail($id);
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
    public function update(int $id, array $data): Event
    {
        $event = Event::findOrFail($id);
        $event->update($data);
        return $event;
    }

    /**
     * @throws ModelNotFoundException
     */
    public function delete(int $id): bool
    {
        $event = Event::findOrFail($id);
        return $event->delete();
    }
}
