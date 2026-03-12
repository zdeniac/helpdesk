<?php

namespace App\DTOs;

final readonly class HelpdeskMessageDTO
{
    public function __construct(
        public int $id,
        public int $conversationId,
        public string $senderType,
        public string $message,
        public string $createdAt,
    ) {}
}
