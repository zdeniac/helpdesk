<?php

namespace App\Enums;

enum ConversationStatus: string
{
    // a beszélgetés létrejött
    case OPEN = 'open';
    // agentre vár a beszélgetés             
    case WAITING_AGENT = 'waiting_agent';
    // lezárt beszélgetés
    case CLOSED = 'closed';
    // agent beszél
    case AGENT = 'agent';
}