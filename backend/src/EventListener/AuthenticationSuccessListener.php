<?php

namespace App\EventListener;

use App\Service\RefreshTokenManager;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RequestStack;

class AuthenticationSuccessListener
{
    public function __construct(
        private RefreshTokenManager $refreshTokenManager,
        private LoggerInterface $loggerInterface,
        private RequestStack $requestStack,
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
        $request = $this->requestStack->getCurrentRequest();

        // Si l'utilisateur a la 2FA activée, on ne devrait pas arriver ici
        // car l'authenticator personnalisé devrait déjà avoir géré ce cas
        if ($user->isTwoFactorEnabled()) {
            $this->loggerInterface->warning('User with 2FA reached AuthenticationSuccessListener - this should not happen');

            return;
        }

        $this->loggerInterface->info('Creating refresh token for user without 2FA');

        // Pour les utilisateurs sans 2FA, comportement normal
        $refreshToken = $this->refreshTokenManager->create($user);

        $cookie = Cookie::create('REFRESH_TOKEN')
            ->withValue($refreshToken->getToken())
            ->withHttpOnly(true)
            ->withSecure($request ? $request->isSecure() : false)
            ->withSameSite('strict')
            ->withExpires($refreshToken->getExpiresAt());

        $response->headers->setCookie($cookie);
        $event->setData($data);
    }
}
