<?php

namespace App\Enums;

enum UserRole: string
{
    case USER = 'user';
    case AGENT = 'agent';
}