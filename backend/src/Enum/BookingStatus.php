<?php

namespace App\Enum;

enum BookingStatus: string
{
    case PENDING = 'en attente';
    case ACCEPTED = 'accepté';
    case REFUSED = 'refusé';
    case CANCELLED = 'Annulé';

    public function toString(): string {
        return match($this) {
            self::PENDING => 'En attente',
            self::ACCEPTED => 'Accepté',
            self::REFUSED => 'Refusé',
            self::CANCELLED => 'Annulé',
        };
    }
}
