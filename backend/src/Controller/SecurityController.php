<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\RefreshTokenManager;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api')]
#[OA\Tag(name: 'Security')]
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

        $bearerCookie = Cookie::create('BEARER')
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

    #[Route('/register', name: 'app_register', methods: ['POST'])]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager, ValidatorInterface $validator, UserRepository $userRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (
            !isset($data['email']) || !isset($data['password'])
            || !isset($data['firstName']) || !isset($data['lastName'])
        ) {
            return new JsonResponse(['error' => 'Tous les champs requis doivent être remplis'], 400);
        }

        if ($userRepository->findOneBy(['email' => $data['email']])) {
            return new JsonResponse(['error' => 'Cet email est déjà utilisé'], 409);
        }

        $user = new User();
        $user->setEmail($data['email']);
        $user->setFirstName($data['firstName']);
        $user->setLastName($data['lastName']);
        $user->setRoles(['ROLE_CLIENT']);
        $user->setCreatedAt(new \DateTimeImmutable());
        $user->setIsVerified(false);
        $hashedPassword = $passwordHasher->hashPassword($user, $data['password']);
        $user->setPassword($hashedPassword);

        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }

            return new JsonResponse(['errors' => $errorMessages], 400);
        }

        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse([
            'message' => 'Registration successful',
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'firstName' => $user->getFirstName(),
                'lastName' => $user->getLastName(),
                'roles' => $user->getRoles(),
                'createdAt' => $user->getCreatedAt()->format('Y-m-d H:i:s'),
            ],
        ], 201);
    }
}
