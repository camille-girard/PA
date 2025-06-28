<?php

namespace App\Service;

use App\Entity\RefreshToken;
use App\Entity\User;
use App\Repository\RefreshTokenRepository;
use Doctrine\ORM\EntityManagerInterface;

class RefreshTokenManager
{
    public function __construct(
        private EntityManagerInterface $em,
        private RefreshTokenRepository $repository,
    ) {
    }

    public function create(User $user, int $ttl = 604800): RefreshToken
    {
        $token = bin2hex(random_bytes(64));

        $refreshToken = new RefreshToken();
        $refreshToken->setUser($user);
        $refreshToken->setToken($token);
        $refreshToken->setExpiresAt((new \DateTime())->modify("+{$ttl} seconds"));

        $this->em->persist($refreshToken);
        $this->em->flush();

        return $refreshToken;
    }

    public function get(string $token): ?RefreshToken
    {
        return $this->repository->findOneBy(['token' => $token]);
    }

    public function invalidate(RefreshToken $token): void
    {
        $this->em->remove($token);
        $this->em->flush();
    }

    public function rotate(RefreshToken $token, int $ttl = 604800): RefreshToken
    {
        $this->invalidate($token);

        return $this->create($token->getUser(), $ttl);
    }
}
