<?php

namespace Database\Factories;

use App\Models\Conversation;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Enums\HelpdeskMessageSenderType;

class HelpdeskMessageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'conversation_id' => Conversation::factory(),
            'sender_type' => $this->faker->randomElement([
                HelpdeskMessageSenderType::USER->value,
                HelpdeskMessageSenderType::BOT->value,
                HelpdeskMessageSenderType::AGENT->value,
            ]),
            'message' => $this->faker->sentence(),
        ];
    }
}