<?php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Psr\Log\LoggerInterface;

class JWTCreatedListener
{
    public function __construct(
        private LoggerInterface $loggerInterface,
    ) {
    }

    public function onJWTCreated(JWTCreatedEvent $event): void
    {
        $user = $event->getUser();

        if (!$user instanceof \App\Entity\User) {
            return;
        }

        $this->loggerInterface->info('JWT Created for user: '.$user->getUserIdentifier());
    }
}
