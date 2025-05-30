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
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\SerializerInterface;

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
        $clients = $this->clientRepository->findAll();

        $json = $serializer->serialize($clients, 'json', ['groups' => 'client:read']);

        return JsonResponse::fromJsonString($json, Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $client = $this->clientRepository->find($id);

        if (!$client) {
            return $this->json(['message' => 'Client non trouvé'], Response::HTTP_NOT_FOUND);
        }

        return $this->json([
            'client' => $client,
        ], Response::HTTP_OK);
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
        $client->setPassword($data['password']); // À hasher dans un service dédié
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
    public function update(int $id, Request $request): JsonResponse
    {
        $client = $this->clientRepository->find($id);

        if (!$client) {
            return $this->json(['message' => 'Client non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            return $this->json(['message' => 'JSON invalide'], Response::HTTP_BAD_REQUEST);
        }

        if (isset($data['email'])) {
            $client->setEmail($data['email']);
        }

        if (isset($data['password'])) {
            $client->setPassword($data['password']); // À hasher dans un service dédié
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

        if (isset($data['preferences'])) {
            $client->setPreferences($data['preferences']);
        }

        if (isset($data['isVerified'])) {
            $client->setIsVerified($data['isVerified']);
        }

        $errors = $this->validator->validate($client);
        if (count($errors) > 0) {
            return $this->errorFormatter->createValidationErrorResponse($errors);
        }

        $this->entityManager->flush();

        return $this->json([
            'message' => 'Client mis à jour avec succès',
            'client' => $client,
        ], Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $client = $this->clientRepository->find($id);

        if (!$client) {
            return $this->json(['message' => 'Client non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($client);
        $this->entityManager->flush();

        return $this->json(['message' => 'Client supprimé avec succès'], Response::HTTP_OK);
    }
}
