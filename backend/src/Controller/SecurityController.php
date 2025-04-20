<?php

namespace App\Controller;

use App\Service\RefreshTokenManager;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
final class SecurityController extends AbstractController
{
    #[Route('/logout', name: 'app_logout')]
    public function logout(Request $request, RefreshTokenManager $refreshTokenManager): JsonResponse
    {
        $token = $request->cookies->get('REFRESH_TOKEN');

        if ($token) {
            $storedToken = $refreshTokenManager->get($token);
            if ($storedToken) {
                $refreshTokenManager->invalidate($storedToken);
            }
        }

        $cookie = Cookie::create('REFRESH_TOKEN')
            ->withValue('')
            ->withExpires(new \DateTime('-1 hour'))
            ->withHttpOnly(true)
            ->withSecure(true)
            ->withSameSite('strict');

        return new JsonResponse(['message' => 'Logged out'], 200, [
            'Set-Cookie' => (string) $cookie,
        ]);
    }

    #[Route('/token/refresh', name: 'app_refresh_token')]
    public function refresh(
        Request $request,
        RefreshTokenManager $refreshTokenManager,
        JWTTokenManagerInterface $jwtTokenManager,
    ): JsonResponse {
        $refreshToken = $request->cookies->get('REFRESH_TOKEN');

        if (!$refreshToken) {
            return new JsonResponse(['error' => 'Refresh token missing'], 401);
        }

        $storedToken = $refreshTokenManager->get($refreshToken);

        if (!$storedToken || $storedToken->getExpiresAt() < new \DateTime()) {
            return new JsonResponse(['error' => 'Refresh token expired'], 401);
        }

        $user = $storedToken->getUser();
        $newJwt = $jwtTokenManager->create($user);
        $newRefreshToken = $refreshTokenManager->rotate($storedToken);

        $cookie = Cookie::create('REFRESH_TOKEN')
            ->withValue($newRefreshToken->getToken())
            ->withHttpOnly(true)
            ->withSecure(true)
            ->withSameSite('strict')
            ->withExpires($newRefreshToken->getExpiresAt());

        return new JsonResponse(['token' => $newJwt], 200, ['Set-Cookie' => (string) $cookie]);
    }
}
