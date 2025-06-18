<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use OTPHP\TOTP;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/api')]
final class TOTPController extends AbstractController
{
    #[Route('/2fa/setup', name: 'app_2fa_setup', methods: ['POST'])]
    public function setup(UserInterface $user, EntityManagerInterface $entityManager): JsonResponse
    {
        /** @var User $user */

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

    #[Route('/2fa/verify', name: 'app_2fa_verify', methods: ['POST'])]
    public function verify(Request $request, UserInterface $user): JsonResponse
    {
        /** @var User $user */

        $data = json_decode($request->getContent(), true);
        $code = $data['code'] ?? null;

        if (!$code) {
            return $this->json(['error' => 'Code is required'], 400);
        }

        $totp = TOTP::create($user->getTotpSecret());
        $isValid = $totp->verify($code);

        if ($isValid) {
            return $this->json(['message' => '2FA verification successful']);
        }

        return $this->json(['error' => 'Invalid code'], 400);
    }
}
