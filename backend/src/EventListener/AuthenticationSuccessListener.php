<?php

namespace App\EventListener;

use App\Service\RefreshTokenManager;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\HttpFoundation\Cookie;

class AuthenticationSuccessListener
{
    public function __construct(
        private RefreshTokenManager $refreshTokenManager,
    ) {
    }

    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event): void
    {
        $user = $event->getUser();

        if (!$user instanceof \App\Entity\User) {
            throw new \LogicException('Expected App\Entity\User');
        }

        /** @var \App\Entity\User $user */
        $data = $event->getData();
        $response = $event->getResponse();

        $refreshToken = $this->refreshTokenManager->create($user);

        $cookie = Cookie::create('REFRESH_TOKEN')
            ->withValue($refreshToken->getToken())
            ->withHttpOnly(true)
            ->withSecure(true)
            ->withSameSite('strict')
            ->withExpires($refreshToken->getExpiresAt());

        $response->headers->setCookie($cookie);
        $event->setData($data);
    }
}
