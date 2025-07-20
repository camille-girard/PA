<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\RefreshTokenManager;
use Doctrine\ORM\EntityManagerInterface;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use OpenApi\Attributes as OA;
use OTPHP\TOTP;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/2fa')]
class TOTPController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private LoggerInterface $logger,
        private UserPasswordHasherInterface $passwordHasher,
        private JWTTokenManagerInterface $jwtManager,
        private RefreshTokenManager $refreshTokenManager,
        private RequestStack $requestStack,
    ) {
    }

    #[Route('/setup', name: 'totp_setup', methods: ['POST'])]
    #[OA\Post(
        path: '/api/2fa/setup',
        summary: 'Setup TOTP authentication for user',
        tags: ['2FA'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'TOTP setup successful',
                content: new OA\JsonContent(
                    properties: [
                        'qrCode' => new OA\Property(property: 'qrCode', type: 'string', description: 'QR code URI for authenticator app'),
                        'secret' => new OA\Property(property: 'secret', type: 'string', description: 'TOTP secret for manual entry'),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(response: 401, description: 'User not authenticated'),
        ]
    )]
    public function setup(): JsonResponse
    {
        /** @var User|null $user */
        $user = $this->getUser();

        if (!$user) {
            return $this->json(['message' => 'User not authenticated'], Response::HTTP_UNAUTHORIZED);
        }

        // Create TOTP instance
        $totp = TOTP::create();
        $totp->setLabel($user->getUserIdentifier());
        $totp->setIssuer('PopnBed');

        // Store the secret
        $user->setTotpSecret($totp->getSecret());
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        // Generate QR code as base64
        $provisioningUri = $totp->getProvisioningUri();

        $builder = new Builder(
            writer: new PngWriter(),
            writerOptions: [],
            validateResult: false,
            data: $provisioningUri,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: 300,
            margin: 10,
            roundBlockSizeMode: RoundBlockSizeMode::Margin
        );

        $qrCodeResult = $builder->build();

        // Generate a data URI to include image data inline (i.e. inside an <img> tag)
        $qrCodeDataUrl = $qrCodeResult->getDataUri();

        return $this->json([
            'qrCode' => $qrCodeDataUrl,
            'secret' => $totp->getSecret(),
        ]);
    }

    #[Route('/verify', name: 'totp_verify', methods: ['POST'])]
    #[OA\Post(
        path: '/api/2fa/verify',
        summary: 'Verify TOTP code',
        tags: ['2FA'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    'code' => new OA\Property(property: 'code', type: 'string', description: '6-digit TOTP code'),
                ],
                type: 'object'
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'TOTP verification result',
                content: new OA\JsonContent(
                    properties: [
                        'valid' => new OA\Property(property: 'valid', type: 'boolean', description: 'Whether the code is valid'),
                        'message' => new OA\Property(property: 'message', type: 'string', description: 'Result message'),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(response: 400, description: 'Missing code'),
            new OA\Response(response: 401, description: 'User not authenticated'),
        ]
    )]
    public function verify(Request $request): JsonResponse
    {
        /** @var User|null $user */
        $user = $this->getUser();

        if (!$user) {
            return $this->json(['message' => 'User not authenticated'], Response::HTTP_UNAUTHORIZED);
        }

        $data = json_decode($request->getContent(), true);
        $code = $data['code'] ?? null;

        if (!$code) {
            return $this->json(['message' => 'Code is required'], Response::HTTP_BAD_REQUEST);
        }

        $totpSecret = $user->getTotpSecret();
        if (!$totpSecret) {
            return $this->json(['message' => 'TOTP not configured'], Response::HTTP_BAD_REQUEST);
        }

        // Create TOTP instance with stored secret
        $totp = TOTP::create($totpSecret);
        $totp->setLabel($user->getUserIdentifier());
        $totp->setIssuer('PopnBed');

        // Verify the code with time window tolerance (±1 period = 30s before/after)
        $currentTimestamp = time();
        $isValid = $totp->verify($code, $currentTimestamp, 1);

        // Debug logs
        $this->logger->info('TOTP Debug', [
            'user' => $user->getUserIdentifier(),
            'code_provided' => $code,
            'current_timestamp' => $currentTimestamp,
            'expected_code' => $totp->at($currentTimestamp),
            'is_valid' => $isValid,
        ]);

        if ($isValid) {
            // Active la 2FA pour l'utilisateur
            $user->setIsTwoFactorEnabled(true);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }

        return $this->json([
            'valid' => $isValid,
            'message' => $isValid ? 'Code is valid' : 'Invalid code',
        ]);
    }

    #[Route('/login-verify', name: 'totp_login_verify', methods: ['POST'])]
    #[OA\Post(
        path: '/api/2fa/login-verify',
        summary: 'Verify TOTP code during login',
        tags: ['2FA'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    'email' => new OA\Property(property: 'email', type: 'string', description: 'User email'),
                    'password' => new OA\Property(property: 'password', type: 'string', description: 'User password'),
                    'code' => new OA\Property(property: 'code', type: 'string', description: '6-digit TOTP code'),
                ],
                type: 'object'
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Login successful with 2FA',
                content: new OA\JsonContent(
                    properties: [
                        'token' => new OA\Property(property: 'token', type: 'string', description: 'JWT token'),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(response: 400, description: 'Invalid credentials or code'),
            new OA\Response(response: 401, description: 'Authentication failed'),
        ]
    )]
    public function loginVerify(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;
        $code = $data['code'] ?? null;

        if (!$email || !$password || !$code) {
            return $this->json(['message' => 'Email, password and code are required'], Response::HTTP_BAD_REQUEST);
        }

        // Vérifier les identifiants
        $userRepository = $this->entityManager->getRepository(User::class);
        $user = $userRepository->findOneBy(['email' => $email]);

        if (!$user || !$this->passwordHasher->isPasswordValid($user, $password)) {
            return $this->json(['message' => 'Invalid credentials'], Response::HTTP_UNAUTHORIZED);
        }

        if (!$user->isTwoFactorEnabled()) {
            return $this->json(['message' => '2FA not enabled for this user'], Response::HTTP_BAD_REQUEST);
        }

        $totpSecret = $user->getTotpSecret();
        if (!$totpSecret) {
            return $this->json(['message' => 'TOTP not configured'], Response::HTTP_BAD_REQUEST);
        }

        // Vérifier le code TOTP
        $totp = TOTP::create($totpSecret);
        $totp->setLabel($user->getUserIdentifier());
        $totp->setIssuer('PopnBed');

        $currentTimestamp = time();
        $isValid = $totp->verify($code, $currentTimestamp, 1);

        $this->logger->info('TOTP Login Verification', [
            'user' => $user->getUserIdentifier(),
            'code_provided' => $code,
            'current_timestamp' => $currentTimestamp,
            'expected_code' => $totp->at($currentTimestamp),
            'is_valid' => $isValid,
        ]);

        if (!$isValid) {
            return $this->json(['message' => 'Invalid 2FA code'], Response::HTTP_UNAUTHORIZED);
        }

        // Créer le JWT et le refresh token
        $token = $this->jwtManager->create($user);
        $refreshToken = $this->refreshTokenManager->create($user);

        $request = $this->requestStack->getCurrentRequest();
        $isSecure = $request ? $request->isSecure() : false;

        // Détermine la configuration des cookies selon l'environnement
        $isProduction = $request && str_contains($request->getHost(), 'popnbed.com');
        $cookieDomain = $isProduction ? '.popnbed.com' : null;
        $sameSite = $isProduction ? 'none' : 'strict';

        // Cookie REFRESH_TOKEN
        $refreshCookie = Cookie::create('REFRESH_TOKEN')
            ->withValue($refreshToken->getToken())
            ->withHttpOnly(true)
            ->withSecure($isSecure)
            ->withSameSite($sameSite)
            ->withExpires($refreshToken->getExpiresAt());

        // Cookie BEARER pour le JWT
        $bearerCookie = Cookie::create('BEARER')
            ->withValue($token)
            ->withHttpOnly(true)
            ->withSecure($isSecure)
            ->withSameSite($sameSite);

        // Ajouter le domaine seulement en production
        if ($cookieDomain) {
            $refreshCookie = $refreshCookie->withDomain($cookieDomain);
            $bearerCookie = $bearerCookie->withDomain($cookieDomain);
        }

        $response = new JsonResponse(['token' => $token]);
        $response->headers->setCookie($refreshCookie);
        $response->headers->setCookie($bearerCookie);

        return $response;
    }
}
