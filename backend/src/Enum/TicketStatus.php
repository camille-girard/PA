<?php

namespace App\Enum;

enum TicketStatus: string
{
    case OPEN = 'OPEN';
    case IN_PROGRESS = 'IN_PROGRESS';
    case CLOSED = 'CLOSED';
}
