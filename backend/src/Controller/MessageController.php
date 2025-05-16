<?php

namespace App\Controller;

use App\Entity\Message;
use App\Repository\ClientRepository;
use App\Repository\MessageRepository;
use App\Repository\OwnerRepository;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/messages', name: 'api_message_')]
#[OA\Tag(name: 'Messages')]
class MessageController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private MessageRepository $messageRepository,
        private ClientRepository $clientRepository,
        private OwnerRepository $ownerRepository,
        private ValidatorInterface $validator,
    ) {
    }

    #[Route('', name: 'index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $messages = $this->messageRepository->findAll();

        return $this->json([
            'messages' => $messages,
        ], Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $message = $this->messageRepository->find($id);

        if (!$message) {
            return $this->json(['message' => 'Message non trouvé'], Response::HTTP_NOT_FOUND);
        }

        return $this->json([
            'message' => $message,
        ], Response::HTTP_OK);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            return $this->json(['message' => 'JSON invalide'], Response::HTTP_BAD_REQUEST);
        }

        $requiredFields = ['content', 'clientId', 'ownerId'];
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

        $client = $this->clientRepository->find($data['clientId']);
        if (!$client) {
            return $this->json(['message' => 'Client non trouvé'], Response::HTTP_BAD_REQUEST);
        }

        $owner = $this->ownerRepository->find($data['ownerId']);
        if (!$owner) {
            return $this->json(['message' => 'Propriétaire non trouvé'], Response::HTTP_BAD_REQUEST);
        }

        $message = new Message();
        $message->setContent($data['content']);
        $message->setCreatedAt(new \DateTimeImmutable());
        $message->setClient($client);
        $message->setOwner($owner);

        $errors = $this->validator->validate($message);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }

            return $this->json([
                'message' => 'Validation échouée',
                'errors' => $errorMessages,
            ], Response::HTTP_BAD_REQUEST);
        }

        $this->entityManager->persist($message);
        $this->entityManager->flush();

        return $this->json([
            'message' => 'Message créé avec succès',
            'data' => $message,
        ], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $message = $this->messageRepository->find($id);

        if (!$message) {
            return $this->json(['message' => 'Message non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            return $this->json(['message' => 'JSON invalide'], Response::HTTP_BAD_REQUEST);
        }

        if (isset($data['content'])) {
            $message->setContent($data['content']);
        }

        if (isset($data['clientId'])) {
            $client = $this->clientRepository->find($data['clientId']);
            if (!$client) {
                return $this->json(['message' => 'Client non trouvé'], Response::HTTP_BAD_REQUEST);
            }
            $message->setClient($client);
        }

        if (isset($data['ownerId'])) {
            $owner = $this->ownerRepository->find($data['ownerId']);
            if (!$owner) {
                return $this->json(['message' => 'Propriétaire non trouvé'], Response::HTTP_BAD_REQUEST);
            }
            $message->setOwner($owner);
        }

        $errors = $this->validator->validate($message);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }

            return $this->json([
                'message' => 'Validation échouée',
                'errors' => $errorMessages,
            ], Response::HTTP_BAD_REQUEST);
        }

        $this->entityManager->flush();

        return $this->json([
            'message' => 'Message mis à jour avec succès',
            'data' => $message,
        ], Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $message = $this->messageRepository->find($id);

        if (!$message) {
            return $this->json(['message' => 'Message non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($message);
        $this->entityManager->flush();

        return $this->json(['message' => 'Message supprimé avec succès'], Response::HTTP_OK);
    }
}
