<?php

namespace App\Security;

use App\Entity\User;
use App\Service\RefreshTokenManager;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;

class TwoFactorAwareJsonLoginAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private JWTTokenManagerInterface $jwtTokenManager,
        private RefreshTokenManager $refreshTokenManager,
        private RequestStack $requestStack,
    ) {
    }

    public function supports(Request $request): ?bool
    {
        return '/api/login' === $request->getPathInfo()
            && $request->isMethod('POST')
            && str_contains($request->headers->get('Content-Type', ''), 'application/json');
    }

    public function authenticate(Request $request): Passport
    {
        $data = json_decode($request->getContent(), true);

        $email = $data['username'] ?? $data['email'] ?? '';
        $password = $data['password'] ?? '';

        if (empty($email) || empty($password)) {
            throw new AuthenticationException('Email and password are required');
        }

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($password)
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            throw new \LogicException('Expected App\Entity\User');
        }

        // Si l'utilisateur a la 2FA activée, retourner une réponse spéciale
        if ($user->isTwoFactorEnabled()) {
            return new JsonResponse([
                'requiresTwoFactor' => true,
                'message' => '2FA verification required',
                'email' => $user->getUserIdentifier(),
            ], Response::HTTP_OK);
        }

        // Si pas de 2FA, créer et retourner le JWT directement
        $jwt = $this->jwtTokenManager->create($user);
        $refreshToken = $this->refreshTokenManager->create($user);

        $currentRequest = $this->requestStack->getCurrentRequest();
        $isSecure = $currentRequest ? $currentRequest->isSecure() : false;

        // Cookie REFRESH_TOKEN
        $refreshCookie = Cookie::create('REFRESH_TOKEN')
            ->withValue($refreshToken->getToken())
            ->withHttpOnly(true)
            ->withSecure($isSecure)
            ->withSameSite('strict')
            ->withExpires($refreshToken->getExpiresAt());

        // Cookie BEARER pour le JWT
        $bearerCookie = Cookie::create('BEARER')
            ->withValue($jwt)
            ->withHttpOnly(true)
            ->withSecure($isSecure)
            ->withSameSite('strict');

        $response = new JsonResponse(['token' => $jwt]);
        $response->headers->setCookie($refreshCookie);
        $response->headers->setCookie($bearerCookie);

        return $response;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse([
            'message' => 'Authentication failed: '.$exception->getMessage(),
        ], Response::HTTP_UNAUTHORIZED);
    }
}
