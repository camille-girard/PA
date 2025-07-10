<?php

namespace App\Controller;

use App\Entity\OwnerRequest;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class OwnerRequestController extends AbstractController
{
    #[Route('/api/owner-request', name: 'api_owner_request', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function request(Request $request, EntityManagerInterface $em): JsonResponse
    {
        try {
            /** @var User|null $user */
            $user = $this->getUser();

            if (!$user) {
                return $this->json(['error' => 'Non authentifié'], 401);
            }

            $data = json_decode($request->getContent(), true);

            if (!$data || empty(trim($data['message'] ?? ''))) {
                return $this->json(['error' => 'Message requis.'], 400);
            }

            $message = trim($data['message']);

            if (strlen($message) < 10) {
                return $this->json(['error' => 'Le message doit contenir au moins 10 caractères.'], 400);
            }

            $existing = $em->getRepository(OwnerRequest::class)->findOneBy([
                'user' => $user,
                'reviewed' => false,
            ]);

            if ($existing) {
                return $this->json(['error' => 'Vous avez déjà une demande en attente.'], 400);
            }

            $ownerRequest = new OwnerRequest();
            $ownerRequest->setUser($user);
            $ownerRequest->setMessage($message);

            $em->persist($ownerRequest);
            $em->flush();

            return $this->json([
                'success' => true,
                'message' => 'Votre demande a été envoyée avec succès.',
            ]);
        } catch (\Exception $e) {
            error_log('Erreur owner request: '.$e->getMessage());

            return $this->json([
                'error' => 'Une erreur est survenue. Veuillez réessayer.',
            ], 500);
        }
    }
}
