<?php

namespace App\Enums;

enum HelpdeskMessageSenderType: string
{
    case USER = 'user';
    case BOT = 'bot';
    case AGENT = 'agent';
}