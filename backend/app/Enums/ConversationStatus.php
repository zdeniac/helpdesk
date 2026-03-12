<?php

namespace App\Enums;

enum ConversationStatus: string
{
    // a beszélgetés létrejött
    case OPEN = 'open';
    // agentre vár a beszélgetés             
    case WAITING_AGENT = 'waiting_agent';
    // agent aktívan válaszol
    case AGENT = 'agent';
    // lezárt beszélgetés
    case CLOSED = 'closed';
}