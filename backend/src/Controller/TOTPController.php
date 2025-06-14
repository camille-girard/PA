<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use OTPHP\TOTP;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/api')]
final class TOTPController extends AbstractController
{
    #[Route('/2fa/setup', name: 'app_2fa_setup', methods: ['POST'])]
    public function index(User $user, EntityManagerInterface $entityManager): JsonResponse
    {
        $totp = TOTP::create();
        $totp->setLabel($user->getUserIdentifier());
        $totp->setIssuer('PopnBed');

        $user->setTotpSecret($totp->getSecret());
        $entityManager->flush();

        $qrContent = $totp->getProvisioningUri();

        return $this->json([
            'qrContent' => $qrContent,
            'secret' => $totp->getSecret(),
        ]);
    }
}
