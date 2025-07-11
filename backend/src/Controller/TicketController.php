<?php

namespace App\Controller;

use App\Entity\Ticket;
use App\Entity\TicketMessage;
use App\Entity\User;
use App\Enum\TicketStatus;
use App\Repository\TicketRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api')]
final class TicketController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private SerializerInterface $serializer,
    ) {
    }

    #[Route('/tickets', methods: ['GET'])]
    #[IsGranted('ROLE_OWNER')]
    public function listMyTickets(TicketRepository $repo): JsonResponse
    {
        $user = $this->getUser();
        $tickets = $repo->findBy(['owner' => $user]);

        $json = $this->serializer->serialize($tickets, 'json', ['groups' => ['ticket:list']]);

        return JsonResponse::fromJsonString($json);
    }

    #[Route('/tickets', methods: ['POST'])]
    #[IsGranted('ROLE_OWNER')]
    public function createTicket(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['title']) || empty($data['message'])) {
            return $this->json(['message' => 'Title and message are required.'], 400);
        }

        $ticket = new Ticket();

        /** @var User $user */
        $user = $this->getUser();

        $ticket->setTitle($data['title'])
            ->setDescription($data['description'] ?? null)
            ->setOwner($user)
            ->setCreatedAt(new \DateTimeImmutable())
            ->setUpdatedAt(new \DateTimeImmutable());

        $message = new TicketMessage();

        /** @var User $author */
        $author = $this->getUser();

        $message->setContent($data['message'])
            ->setAuthor($author)
            ->setCreatedAt(new \DateTimeImmutable())
            ->setTicket($ticket);

        $this->em->persist($ticket);
        $this->em->persist($message);
        $this->em->flush();

        $json = $this->serializer->serialize($ticket, 'json', ['groups' => ['ticket:detail']]);

        return JsonResponse::fromJsonString($json, 201);
    }

    #[Route('/tickets/{id}', methods: ['GET'])]
    public function showTicket(Ticket $ticket): JsonResponse
    {
        $user = $this->getUser();

        if ($this->isGranted('ROLE_OWNER') && $ticket->getOwner() !== $user) {
            return $this->json(['message' => 'Access Denied.'], 403);
        }

        $json = $this->serializer->serialize($ticket, 'json', ['groups' => ['ticket:detail']]);

        return JsonResponse::fromJsonString($json);
    }

    #[Route('/tickets/{id}/messages', methods: ['POST'])]
    public function addMessage(Request $request, Ticket $ticket): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        if ($this->isGranted('ROLE_OWNER') && $ticket->getOwner() !== $user) {
            return $this->json(['message' => 'Access Denied.'], 403);
        }

        $data = json_decode($request->getContent(), true);

        if (empty($data['content'])) {
            return $this->json(['message' => 'Content is required.'], 400);
        }

        $message = new TicketMessage();
        $message->setTicket($ticket)
            ->setAuthor($user)
            ->setContent($data['content'])
            ->setCreatedAt(new \DateTimeImmutable());

        $ticket->setUpdatedAt(new \DateTimeImmutable());

        $this->em->persist($message);
        $this->em->flush();

        $json = $this->serializer->serialize($message, 'json', ['groups' => ['ticket:message']]);

        return JsonResponse::fromJsonString($json, 201);
    }

    #[Route('/admin/tickets', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function listAllTickets(Request $request, TicketRepository $repo): JsonResponse
    {
        $status = $request->query->get('status');

        $criteria = [];
        if ($status) {
            $criteria['status'] = TicketStatus::from($status);
        }

        $tickets = $repo->findBy($criteria);

        return $this->json(['tickets' => $tickets], 200, [], ['groups' => ['ticket:list']]);
    }

    #[Route('/admin/tickets/{id}/status', methods: ['PATCH'])]
    #[IsGranted('ROLE_ADMIN')]
    public function updateStatus(Request $request, Ticket $ticket): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['status'])) {
            return $this->json(['message' => 'Status is required.'], 400);
        }

        try {
            $ticket->setStatus(TicketStatus::from($data['status']));
        } catch (\ValueError) {
            return $this->json(['message' => 'Invalid status.'], 400);
        }

        $ticket->setUpdatedAt(new \DateTimeImmutable());
        $this->em->flush();

        return $this->json(['message' => 'Status updated successfully.']);
    }
}
