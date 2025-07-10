<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api', name: 'api_mercure_')]
class MercureController extends AbstractController
{
    #[Route('/mercure-info', name: 'info', methods: ['GET'])]
    public function info(): Response
    {
        /** @var User|null $user */
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['error' => 'User not authenticated'], Response::HTTP_UNAUTHORIZED);
        }

        $userId = $user->getId();

        return $this->json([
            'mercureHubUrl' => $_ENV['MERCURE_PUBLIC_URL'],
            'userTopic' => "user/{$userId}/messages",
        ]);
    }

    #[Route('/mercure-auth', name: 'auth', methods: ['GET'])]
    public function auth(Request $request): Response
    {
        /** @var User|null $user */
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['error' => 'User not authenticated'], Response::HTTP_UNAUTHORIZED);
        }

        $userId = $user->getId();

        $topics = [
            "user/{$userId}/messages",
        ];

        if ($user->getRoles() && (in_array('ROLE_CLIENT', $user->getRoles()) || in_array('ROLE_OWNER', $user->getRoles()))) {
            $topics[] = 'conversation/*';
        }

        $jwtPayload = [
            'mercure' => [
                'subscribe' => $topics,
            ],
        ];

        $jwtKey = $_ENV['MERCURE_JWT_SECRET'] ?? '!ChangeThisMercureHubJWTSecretKey!';

        $token = $this->generateJWT($jwtPayload, $jwtKey);

        $response = $this->json(['success' => true]);

        $cookie = Cookie::create(
            'mercureAuthorization',
            $token,
            strtotime('+1 hour'),
            '/.well-known/mercure',
            null,
            false,
            true,
            false,
            Cookie::SAMESITE_STRICT
        );

        $response->headers->setCookie($cookie);

        return $response;
    }

    #[Route('/mercure-token', name: 'token', methods: ['GET'])]
    public function getToken(): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['error' => 'User not authenticated'], Response::HTTP_UNAUTHORIZED);
        }

        /** @var User $user */
        $userId = $user->getId();

        $topics = [
            "user/{$userId}/messages",
            'conversation/*',
        ];

        $jwtPayload = [
            'mercure' => [
                'subscribe' => $topics,
                'publish' => [],
            ],
        ];

        $jwtKey = $_ENV['MERCURE_JWT_SECRET'] ?? '!ChangeThisMercureHubJWTSecretKey!';

        $token = $this->generateJWT($jwtPayload, $jwtKey);

        return $this->json([
            'token' => $token,
            'mercureHubUrl' => $_ENV['MERCURE_PUBLIC_URL'],
        ]);
    }

    /**
     * Génère un JWT pour Mercure.
     *
     * @param array<mixed> $payload   Le payload du JWT
     * @param string       $key       La clé secrète pour signer le JWT
     * @param int          $expiresIn Durée de validité en secondes
     *
     * @return string Le token JWT
     */
    private function generateJWT(array $payload, string $key, int $expiresIn = 3600): string
    {
        $now = new \DateTimeImmutable();
        $exp = $now->getTimestamp() + $expiresIn;

        $header = [
            'alg' => 'HS256',
            'typ' => 'JWT',
        ];

        $payload = array_merge($payload, [
            'iat' => $now->getTimestamp(),
            'exp' => $exp,
        ]);

        $base64Header = $this->base64UrlEncode(json_encode($header));
        $base64Payload = $this->base64UrlEncode(json_encode($payload));

        $signature = hash_hmac('sha256', $base64Header.'.'.$base64Payload, $key, true);
        $base64Signature = $this->base64UrlEncode($signature);

        return $base64Header.'.'.$base64Payload.'.'.$base64Signature;
    }

    /**
     * Encode en base64 URL-safe.
     */
    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
