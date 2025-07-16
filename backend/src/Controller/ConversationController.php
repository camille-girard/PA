<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Conversation;
use App\Entity\Message;
use App\Entity\User;
use App\Repository\ClientRepository;
use App\Repository\ConversationRepository;
use App\Repository\MessageRepository;
use App\Repository\OwnerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/conversations', name: 'api_conversation_')]
class ConversationController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ConversationRepository $conversationRepository,
        private ClientRepository $clientRepository,
        private OwnerRepository $ownerRepository,
        private MessageRepository $messageRepository,
        private ValidatorInterface $validator,
        private SerializerInterface $serializer,
        private HubInterface $hub,
    ) {
    }

    #[Route('', name: 'index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        /** @var User|null $user */
        $user = $this->getUser();

        if (!$user) {
            return $this->json(['error' => 'Utilisateur non connecté'], Response::HTTP_UNAUTHORIZED);
        }

        $conversations = $this->conversationRepository->findByUser($user);

        return $this->json(
            ['conversations' => $conversations],
            Response::HTTP_OK,
            [],
            ['groups' => ['conversation:list']]
        );
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        /** @var User|null $user */
        $user = $this->getUser();

        if (!$user) {
            return $this->json(['error' => 'Utilisateur non connecté'], Response::HTTP_UNAUTHORIZED);
        }

        $conversation = $this->conversationRepository->find($id);

        if (!$conversation) {
            return $this->json(['error' => 'Conversation non trouvée'], Response::HTTP_NOT_FOUND);
        }

        // Vérifier que l'utilisateur est bien participant à la conversation
        $isParticipant = false;
        if (in_array('ROLE_CLIENT', $user->getRoles())) {
            $isParticipant = $conversation->getClient()->getId() === $user->getId();
        } elseif (in_array('ROLE_OWNER', $user->getRoles())) {
            $isParticipant = $conversation->getOwner()->getId() === $user->getId();
        }

        if (!$isParticipant) {
            return $this->json(['error' => 'Vous n\'êtes pas autorisé à voir cette conversation'], Response::HTTP_FORBIDDEN);
        }

        // Marquer les messages non lus comme lus
        $unreadMessages = $this->messageRepository->findUnreadMessagesForUser($conversation, $user);
        foreach ($unreadMessages as $message) {
            $message->setIsRead(true);
        }

        // Si des messages ont été marqués comme lus, mettre à jour
        if (count($unreadMessages) > 0) {
            $conversation->setHasNewMessages(false);
            $this->entityManager->flush();
        }

        return $this->json(
            ['conversation' => $conversation],
            Response::HTTP_OK,
            [],
            ['groups' => ['conversation:read']]
        );
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        /** @var User|null $user */
        $user = $this->getUser();

        if (!$user) {
            return $this->json(['error' => 'Utilisateur non connecté'], Response::HTTP_UNAUTHORIZED);
        }

        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            return $this->json(['error' => 'JSON invalide'], Response::HTTP_BAD_REQUEST);
        }

        $requiredFields = ['clientId', 'ownerId'];
        $missingFields = [];

        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                $missingFields[] = $field;
            }
        }

        if (!empty($missingFields)) {
            return $this->json([
                'error' => 'Champs obligatoires manquants',
                'missingFields' => $missingFields,
            ], Response::HTTP_BAD_REQUEST);
        }

        $client = $this->clientRepository->find($data['clientId']);
        if (!$client) {
            return $this->json(['error' => 'Client non trouvé'], Response::HTTP_BAD_REQUEST);
        }

        $owner = $this->ownerRepository->find($data['ownerId']);
        if (!$owner) {
            return $this->json(['error' => 'Propriétaire non trouvé'], Response::HTTP_BAD_REQUEST);
        }

        // Vérifier si la conversation existe déjà
        $existingConversation = $this->conversationRepository->findOneByClientAndOwner($client, $owner);

        if ($existingConversation) {
            return $this->json(
                ['conversation' => $existingConversation],
                Response::HTTP_OK,
                [],
                ['groups' => ['conversation:read']]
            );
        }

        $conversation = new Conversation();
        $conversation->setClient($client);
        $conversation->setOwner($owner);
        $conversation->setCreatedAt(new \DateTimeImmutable());
        $conversation->setUpdatedAt(new \DateTimeImmutable());
        $conversation->setHasNewMessages(false);

        $errors = $this->validator->validate($conversation);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }

            return $this->json(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }

        $this->entityManager->persist($conversation);
        $this->entityManager->flush();

        return $this->json(
            ['conversation' => $conversation],
            Response::HTTP_CREATED,
            [],
            ['groups' => ['conversation:read']]
        );
    }

    #[Route('/{id}/messages', name: 'send_message', methods: ['POST'])]
    public function sendMessage(int $id, Request $request): JsonResponse
    {
        /** @var User|null $user */
        $user = $this->getUser();

        if (!$user) {
            return $this->json(['error' => 'Utilisateur non connecté'], Response::HTTP_UNAUTHORIZED);
        }

        $conversation = $this->conversationRepository->find($id);

        if (!$conversation) {
            return $this->json(['error' => 'Conversation non trouvée'], Response::HTTP_NOT_FOUND);
        }

        // Vérifier que l'utilisateur est bien participant à la conversation
        $isClient = in_array('ROLE_CLIENT', $user->getRoles());
        $isOwner = in_array('ROLE_OWNER', $user->getRoles());
        $isParticipant = ($isClient && $conversation->getClient()->getId() === $user->getId())
                        || ($isOwner && $conversation->getOwner()->getId() === $user->getId());

        if (!$isParticipant) {
            return $this->json(['error' => 'Vous n\'êtes pas autorisé à envoyer un message dans cette conversation'], Response::HTTP_FORBIDDEN);
        }

        $data = json_decode($request->getContent(), true);

        if (!is_array($data) || !isset($data['content']) || '' === trim($data['content'])) {
            return $this->json(['error' => 'Contenu du message requis'], Response::HTTP_BAD_REQUEST);
        }

        $message = new Message();
        $message->setContent($data['content']);
        $message->setCreatedAt(new \DateTimeImmutable());
        $message->setConversation($conversation);
        $message->setSender($user);
        $message->setIsRead(false);

        if ($isClient) {
            $message->setClient($conversation->getClient());
            $message->setOwner($conversation->getOwner());
            // Le destinataire est le propriétaire
            $recipient = $conversation->getOwner();
        } else {
            $message->setClient($conversation->getClient());
            $message->setOwner($conversation->getOwner());
            // Le destinataire est le client
            $recipient = $conversation->getClient();
        }

        $errors = $this->validator->validate($message);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }

            return $this->json(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }

        // Mettre à jour la conversation
        $conversation->setUpdatedAt(new \DateTimeImmutable());
        $conversation->setLastMessagePreview(substr($message->getContent(), 0, 50).(strlen($message->getContent()) > 50 ? '...' : ''));
        $conversation->setHasNewMessages(true);
        $conversation->addMessage($message);

        $this->entityManager->persist($message);
        $this->entityManager->flush();

        // Publier un événement Mercure pour notifier le destinataire
        $this->publishMessageEvent($message);

        return $this->json(
            ['message' => $message],
            Response::HTTP_CREATED,
            [],
            ['groups' => ['message:read']]
        );
    }

    private function publishMessageEvent(Message $message): void
    {
        try {
            $conversation = $message->getConversation();
            $sender = $message->getSender();

            // Topic pour la conversation
            $topicUrl = "conversation/{$conversation->getId()}";

            // Topic pour le destinataire
            $recipientId = $sender->getId() === $conversation->getClient()->getId()
                ? $conversation->getOwner()->getId()
                : $conversation->getClient()->getId();
            $recipientTopicUrl = "user/{$recipientId}/messages";

            // Données du message avec le plus de contexte possible
            $messageData = $this->serializer->serialize(
                $message,
                'json',
                [
                    'groups' => ['message:read', 'conversation:read', 'client:read', 'owner:read'],
                    'circular_reference_handler' => function ($object) {
                        return $object->getId();
                    },
                    'ignored_attributes' => ['password', 'bookings', 'comments', 'tickets', 'ticketMessages'],
                ]
            );

            try {
                // Publier sur le topic de la conversation
                $update = new Update(
                    $topicUrl,
                    $messageData,
                    false // Rendre public pour simplifier l'authentification en développement
                );

                $this->hub->publish($update);
                error_log('Successfully published to conversation topic : '.$topicUrl);
            } catch (\Exception $e) {
                error_log('Error publishing to conversation topic: '.$e->getMessage());
            }

            try {
                // Publier sur le topic du destinataire
                $update = new Update(
                    $recipientTopicUrl,
                    $messageData,
                    false // Rendre public pour simplifier l'authentification en développement
                );

                $this->hub->publish($update);
                error_log('Successfully published to recipient topic: '.$recipientTopicUrl);
            } catch (\Exception $e) {
                error_log('Error publishing to recipient topic: '.$e->getMessage());
            }
        } catch (\Exception $e) {
            error_log('General error in publishMessageEvent: '.$e->getMessage());
            // Ne pas relancer l'exception pour éviter de bloquer l'envoi du message
        }
    }
}
