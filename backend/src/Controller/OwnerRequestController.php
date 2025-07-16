<?php

namespace App\Controller;

use App\Entity\Client;
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

            if (in_array('ROLE_OWNER', $user->getRoles())) {
                return $this->json(['error' => 'Vous êtes déjà propriétaire.'], 400);
            }

            $existing = $em->getRepository(OwnerRequest::class)->findOneBy([
                'user' => $user,
                'reviewed' => false,
            ]);

            if ($existing) {
                return $this->json([
                    'error' => 'Vous avez déjà une demande en attente. Merci d\'attendre qu\'elle soit traitée.',
                ], 409);
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

    #[Route('/api/owner-requests', name: 'api_owner_requests_index', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function index(EntityManagerInterface $em): JsonResponse
    {
        try {
            $ownerRequests = $em->getRepository(OwnerRequest::class)->findBy([], ['createdAt' => 'DESC']);

            $data = [];
            foreach ($ownerRequests as $request) {
                $data[] = [
                    'id' => $request->getId(),
                    'message' => $request->getMessage(),
                    'createdAt' => $request->getCreatedAt()->format('Y-m-d H:i:s'),
                    'reviewed' => $request->isReviewed(),
                    'user' => [
                        'id' => $request->getUser()->getId(),
                        'firstName' => $request->getUser()->getFirstName(),
                        'lastName' => $request->getUser()->getLastName(),
                        'email' => $request->getUser()->getEmail(),
                    ],
                ];
            }

            return $this->json($data);
        } catch (\Exception $e) {
            error_log('Erreur owner requests index: '.$e->getMessage());

            return $this->json(['error' => 'Erreur lors du chargement des demandes.'], 500);
        }
    }

    #[Route('/api/owner-requests/{id}/accept', name: 'api_owner_requests_accept', methods: ['PATCH'])]
    #[IsGranted('ROLE_ADMIN')]
    public function accept(int $id, EntityManagerInterface $em): JsonResponse
    {
        try {
            $ownerRequest = $em->getRepository(OwnerRequest::class)->find($id);

            if (!$ownerRequest) {
                return $this->json(['error' => 'Demande non trouvée.'], 404);
            }

            if ($ownerRequest->isReviewed()) {
                return $this->json(['error' => 'Cette demande a déjà été traitée.'], 400);
            }

            $user = $ownerRequest->getUser();

            $client = $em->getRepository(Client::class)->find($user->getId());
            if (!$client) {
                return $this->json(['error' => 'L\'utilisateur doit être un client pour devenir propriétaire.'], 400);
            }

            if (count($client->getBookings()) > 0) {
                return $this->json([
                    'error' => 'Impossible de promouvoir en propriétaire : le client possède des réservations en cours.',
                ], 409);
            }

            $conn = $em->getConnection();
            $conn->beginTransaction();

            try {
                $conn->executeStatement('DELETE FROM client WHERE id = :id', ['id' => $user->getId()]);

                $conn->executeStatement(
                    'INSERT INTO owner (id, bio, notation) VALUES (:id, NULL, 0.0)',
                    ['id' => $user->getId()]
                );

                $conn->executeStatement(
                    'UPDATE user SET discr = :discr, roles = :roles WHERE id = :id',
                    [
                        'discr' => 'owner',
                        'roles' => json_encode(['ROLE_OWNER']),
                        'id' => $user->getId(),
                    ]
                );

                $ownerRequest->setReviewed(true);

                $em->flush();
                $conn->commit();

                return $this->json([
                    'success' => true,
                    'message' => 'Demande acceptée avec succès. L\'utilisateur est maintenant propriétaire.',
                ]);
            } catch (\Exception $e) {
                $conn->rollBack();
                error_log('Erreur transaction owner request accept: '.$e->getMessage());

                return $this->json(['error' => 'Erreur lors du traitement de la demande.'], 500);
            }
        } catch (\Exception $e) {
            error_log('Erreur owner request accept: '.$e->getMessage());

            return $this->json(['error' => 'Erreur lors de l\'acceptation de la demande.'], 500);
        }
    }

    #[Route('/api/owner-requests/{id}/reject', name: 'api_owner_requests_reject', methods: ['PATCH'])]
    #[IsGranted('ROLE_ADMIN')]
    public function reject(int $id, EntityManagerInterface $em): JsonResponse
    {
        try {
            $ownerRequest = $em->getRepository(OwnerRequest::class)->find($id);

            if (!$ownerRequest) {
                return $this->json(['error' => 'Demande non trouvée.'], 404);
            }

            if ($ownerRequest->isReviewed()) {
                return $this->json(['error' => 'Cette demande a déjà été traitée.'], 400);
            }

            $ownerRequest->setReviewed(true);
            $em->flush();

            return $this->json([
                'success' => true,
                'message' => 'Demande rejetée avec succès.',
            ]);
        } catch (\Exception $e) {
            error_log('Erreur owner request reject: '.$e->getMessage());

            return $this->json(['error' => 'Erreur lors du rejet de la demande.'], 500);
        }
    }

    #[Route('/api/owner-requests/{id}', name: 'api_owner_requests_show', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function show(int $id, EntityManagerInterface $em): JsonResponse
    {
        try {
            $ownerRequest = $em->getRepository(OwnerRequest::class)->find($id);

            if (!$ownerRequest) {
                return $this->json(['error' => 'Demande non trouvée.'], 404);
            }

            $data = [
                'id' => $ownerRequest->getId(),
                'message' => $ownerRequest->getMessage(),
                'createdAt' => $ownerRequest->getCreatedAt()->format('Y-m-d H:i:s'),
                'reviewed' => $ownerRequest->isReviewed(),
                'user' => [
                    'id' => $ownerRequest->getUser()->getId(),
                    'firstName' => $ownerRequest->getUser()->getFirstName(),
                    'lastName' => $ownerRequest->getUser()->getLastName(),
                    'email' => $ownerRequest->getUser()->getEmail(),
                    'phone' => $ownerRequest->getUser()->getPhone(),
                    'isVerified' => $ownerRequest->getUser()->isVerified(),
                    'createdAt' => $ownerRequest->getUser()->getCreatedAt()->format('Y-m-d H:i:s'),
                ],
            ];

            return $this->json($data);
        } catch (\Exception $e) {
            error_log('Erreur owner request show: '.$e->getMessage());

            return $this->json(['error' => 'Erreur lors du chargement du détail.'], 500);
        }
    }
}
