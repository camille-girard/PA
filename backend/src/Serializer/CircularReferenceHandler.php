<?php

namespace App\Serializer;

class CircularReferenceHandler
{
    public static function handle(mixed $object): mixed
    {
        return $object->getId();
    }
}
