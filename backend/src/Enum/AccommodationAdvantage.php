<?php

namespace App\Enum;

enum AccommodationAdvantage: string
{
    case BREAKFAST = 'breakfast';
    case SNACK = 'snack';
    case GARDEN = 'garden';
    case GAME_ROOM = 'game_room';
    case TERRACE = 'terrace';
    case BALCONY = 'balcony';
    case BARBECUE = 'barbecue';
    case CASTLE_ACCESS = 'castle_access';
    case CASTLE_VISIT = 'castle_visit';
    case WIFI = 'wifi';
    case PARKING = 'parking';
    case POOL = 'pool';
    case SPA = 'spa';
    case GYM = 'gym';
    case KITCHEN = 'kitchen';
    case LAUNDRY = 'laundry';
    case AIR_CONDITIONING = 'air_conditioning';
    case HEATING = 'heating';
    case TV = 'tv';
    case COMMON_ROOM_ACCESS = 'common_room_access';

    public function label(): string
    {
        return match ($this) {
            self::BREAKFAST => 'Petit-déjeuner offert',
            self::SNACK => 'Goûter',
            self::GARDEN => 'Jardin',
            self::GAME_ROOM => 'Salle de jeux',
            self::TERRACE => 'Terrasse',
            self::BALCONY => 'Balcon',
            self::BARBECUE => 'Barbecue',
            self::CASTLE_ACCESS => 'Accès au château',
            self::CASTLE_VISIT => 'Visite du château',
            self::WIFI => 'Wi-Fi gratuit',
            self::PARKING => 'Parking',
            self::POOL => 'Piscine',
            self::SPA => 'Spa',
            self::GYM => 'Salle de sport',
            self::KITCHEN => 'Cuisine équipée',
            self::LAUNDRY => 'Lave-linge',
            self::AIR_CONDITIONING => 'Climatisation',
            self::HEATING => 'Chauffage',
            self::TV => 'Télévision',
            self::COMMON_ROOM_ACCESS => 'Accès à la salle commune',
        };
    }

    public static function fromLabel(string $label): ?self
    {
        foreach (self::cases() as $case) {
            if ($case->label() === $label) {
                return $case;
            }
        }

        return null;
    }
}
