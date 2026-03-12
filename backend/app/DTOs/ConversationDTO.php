<?php

namespace App\DTOs;

final readonly class ConversationDTO
{
    public function __construct(
        public int $id,
        public int $userId,
        public string $status,
        /** @param iterable<HelpdeskMessageDTO> */
        public iterable $messages
    ) {}
}