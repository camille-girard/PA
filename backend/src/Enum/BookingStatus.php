<?php

namespace App\Enum;

enum BookingStatus: string
{
    case PENDING = 'pending';
    case ACCEPTED = 'accepted';
    case REFUSED = 'refused';
    case BLOCKED = 'blocked';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'En attente',
            self::ACCEPTED => 'Acceptée',
            self::REFUSED => 'Refusée',
            self::BLOCKED => 'Bloquée',
        };
    }
}
