<?php

namespace App\Controller;

use App\Entity\Client;
use App\Repository\ClientRepository;
use App\Service\ValidationErrorFormatterService;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/clients', name: 'api_clients_')]
#[OA\Tag(name: 'Clients')]
final class ClientController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ClientRepository $clientRepository,
        private ValidatorInterface $validator,
        private ValidationErrorFormatterService $errorFormatter,
    ) {
    }

    #[Route('', name: 'index', methods: ['GET'])]
    public function index(SerializerInterface $serializer): JsonResponse
    {
        $clients = $this->clientRepository->findBy(['isDeleted' => false]);

        $json = $serializer->serialize($clients, 'json', ['groups' => 'client:read']);

        return JsonResponse::fromJsonString($json, Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id, SerializerInterface $serializer): JsonResponse
    {
        $client = $this->clientRepository->find($id);

        if (!$client) {
            return $this->json(['message' => 'Client non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $json = $serializer->serialize($client, 'json', [
            'groups' => ['client:read', 'booking:read'],
        ]);

        return JsonResponse::fromJsonString($json, Response::HTTP_OK);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (null === $data) {
            return $this->json(['message' => 'JSON invalide'], Response::HTTP_BAD_REQUEST);
        }

        $requiredFields = ['email', 'password', 'firstName', 'lastName'];
        $missingFields = [];

        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                $missingFields[] = $field;
            }
        }

        if (!empty($missingFields)) {
            return $this->json([
                'message' => 'Champs obligatoires manquants',
                'missingFields' => $missingFields,
            ], Response::HTTP_BAD_REQUEST);
        }

        $client = new Client();
        $client->setEmail($data['email']);
        $client->setPassword($data['password']);
        $client->setFirstName($data['firstName']);
        $client->setLastName($data['lastName']);
        $client->setIsVerified(false);
        $client->setCreatedAt(new \DateTimeImmutable());
        $client->setRoles(['ROLE_CLIENT']);

        if (isset($data['phone'])) {
            $client->setPhone($data['phone']);
        }

        if (isset($data['avatar'])) {
            $client->setAvatar($data['avatar']);
        }

        if (isset($data['preferences'])) {
            $client->setPreferences($data['preferences']);
        }

        $errors = $this->validator->validate($client);
        if (count($errors) > 0) {
            return $this->errorFormatter->createValidationErrorResponse($errors);
        }

        $this->entityManager->persist($client);
        $this->entityManager->flush();

        return $this->json([
            'message' => 'Client créé avec succès',
            'client' => $client,
        ], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(int $id, Request $request, SerializerInterface $serializer): JsonResponse
    {
        $client = $this->clientRepository->find($id);

        if (!$client) {
            return $this->json(['message' => 'Client non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            return $this->json(['message' => 'JSON invalide'], Response::HTTP_BAD_REQUEST);
        }

        $newRole = $data['role'] ?? $client->getRoles()[0];

        if ($newRole !== $client->getRoles()[0]) {
            $discrMap = [
                'ROLE_CLIENT' => 'client',
                'ROLE_OWNER' => 'owner',
                'ROLE_ADMIN' => 'admin',
            ];

            $newDiscr = $discrMap[$newRole] ?? null;

            if (!$newDiscr) {
                return $this->json(['message' => 'Rôle invalide'], Response::HTTP_BAD_REQUEST);
            }

            if ('ROLE_CLIENT' !== $newRole && count($client->getBookings()) > 0) {
                return $this->json([
                    'message' => 'Impossible de changer le rôle vers '.$newRole.' car ce client possède des réservations.',
                ], Response::HTTP_CONFLICT);
            }

            $conn = $this->entityManager->getConnection();

            $conn->executeStatement('DELETE FROM client WHERE id = :id', ['id' => $client->getId()]);

            $conn->executeStatement(
                'UPDATE user SET discr = :discr WHERE id = :id',
                ['discr' => $newDiscr, 'id' => $client->getId()]
            );

            if ('client' === $newDiscr) {
                $prefs = isset($data['preferences']) ? serialize($data['preferences']) : serialize([]);
                $conn->executeStatement(
                    'INSERT INTO client (id, preferences) VALUES (:id, :prefs)',
                    ['id' => $client->getId(), 'prefs' => $prefs]
                );
            } elseif ('owner' === $newDiscr) {
                $conn->executeStatement(
                    'INSERT INTO owner (id) VALUES (:id)',
                    ['id' => $client->getId()]
                );
            } elseif ('admin' === $newDiscr) {
                $conn->executeStatement(
                    'INSERT INTO admin (id) VALUES (:id)',
                    ['id' => $client->getId()]
                );
            }

            $client->setRoles([$newRole]);

            if (isset($data['email'])) {
                $client->setEmail($data['email']);
            }
            if (isset($data['password'])) {
                $client->setPassword($data['password']);
            }
            if (isset($data['firstName'])) {
                $client->setFirstName($data['firstName']);
            }
            if (isset($data['lastName'])) {
                $client->setLastName($data['lastName']);
            }
            if (isset($data['phone'])) {
                $client->setPhone($data['phone']);
            }
            if (isset($data['avatar'])) {
                $client->setAvatar($data['avatar']);
            }
            if (isset($data['address'])) {
                $client->setAddress($data['address']);
            }
            if (isset($data['isVerified'])) {
                $client->setIsVerified($data['isVerified']);
            }

            $this->entityManager->flush();

            return $this->json([
                'message' => 'Utilisateur transformé en '.$newRole,
                'user' => $client,
            ], Response::HTTP_OK);
        }

        if (isset($data['email'])) {
            $client->setEmail($data['email']);
        }
        if (isset($data['password'])) {
            $client->setPassword($data['password']);
        }
        if (isset($data['firstName'])) {
            $client->setFirstName($data['firstName']);
        }
        if (isset($data['lastName'])) {
            $client->setLastName($data['lastName']);
        }
        if (isset($data['phone'])) {
            $client->setPhone($data['phone']);
        }
        if (isset($data['avatar'])) {
            $client->setAvatar($data['avatar']);
        }
        if (isset($data['address'])) {
            $client->setAddress($data['address']);
        }
        if (isset($data['isVerified'])) {
            $client->setIsVerified($data['isVerified']);
        }
        if (isset($data['preferences'])) {
            $client->setPreferences($data['preferences']);
        }

        if (isset($data['role'])) {
            $client->setRoles([$data['role']]);
        }

        $this->entityManager->flush();

        $serializedClient = $serializer->serialize($client, 'json', [
            'groups' => ['client:read'],
        ]);

        return JsonResponse::fromJsonString(json_encode([
            'message' => 'Client mis à jour avec succès',
            'client' => json_decode($serializedClient),
        ]), Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $client = $this->clientRepository->find($id);

        if (!$client) {
            return $this->json(['message' => 'Client non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $client->setIsDeleted(true);
        $this->entityManager->flush();

        return $this->json(['message' => 'Client archivé avec succès'], Response::HTTP_OK);
    }
}
