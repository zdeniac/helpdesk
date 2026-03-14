<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Enums\ConversationStatus;

class ConversationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'assigned_agent_id' => null,
            'status' => fake()->randomElement([
                ConversationStatus::OPEN->value,
                ConversationStatus::WAITING_AGENT->value,
                ConversationStatus::CLOSED->value,
            ]),
        ];
    }
}