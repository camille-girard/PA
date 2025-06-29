<?php

namespace App\Controller;

use App\Repository\AccommodationRepository;
use App\Repository\BookingRepository;
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

#[Route('/api/bookings', name: 'api_bookings_')]
#[OA\Tag(name: 'Bookings')]
final class BookingController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private BookingRepository $bookingRepository,
        private ClientRepository $clientRepository,
        private AccommodationRepository $accommodationRepository,
        private ValidatorInterface $validator,
        private ValidationErrorFormatterService $errorFormatter,
    ) {
    }

    #[Route('', name: 'index', methods: ['GET'])]
    public function index(SerializerInterface $serializer): JsonResponse
    {
        $bookings = $this->bookingRepository->findAll();
        $json = $serializer->serialize($bookings, 'json', ['groups' => 'booking:read']);

        return JsonResponse::fromJsonString($json, Response::HTTP_OK);
    }

    #[Route('/me', name: 'me', methods: ['GET'])]
    public function me(SerializerInterface $serializer): JsonResponse
    {
        $user = $this->getUser();
        if (!$user || !in_array('ROLE_CLIENT', $user->getRoles(), true)) {
            return $this->json(['message' => 'Accès non autorisé'], Response::HTTP_FORBIDDEN);
        }

        $bookings = $this->bookingRepository->findBy(['client' => $user]);
        $json = $serializer->serialize($bookings, 'json', ['groups' => 'booking:read']);

        return JsonResponse::fromJsonString($json, Response::HTTP_OK);
    }

    #[Route('/owner', name: 'owner_bookings', methods: ['GET'])]
    public function ownerBookings(SerializerInterface $serializer): JsonResponse
    {
        $user = $this->getUser();
        if (!$user || !in_array('ROLE_OWNER', $user->getRoles(), true)) {
            return $this->json(['message' => 'Accès non autorisé'], Response::HTTP_FORBIDDEN);
        }

        $bookings = $this->bookingRepository->findBookingsByOwner($user);
        $json = $serializer->serialize($bookings, 'json', ['groups' => 'booking:read']);

        return JsonResponse::fromJsonString($json, Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $booking = $this->bookingRepository->find($id);
        if (!$booking) {
            return $this->json(['message' => 'Réservation non trouvée'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        if (!is_array($data)) {
            return $this->json(['message' => 'JSON invalide'], Response::HTTP_BAD_REQUEST);
        }

        if (isset($data['status'])) {
            $booking->setStatus($data['status']);
        }

        $errors = $this->validator->validate($booking);
        if (count($errors) > 0) {
            return $this->errorFormatter->createValidationErrorResponse($errors);
        }

        $this->entityManager->flush();

        return $this->json([
            'message' => 'Réservation mise à jour avec succès',
            'booking' => $booking,
        ], Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $booking = $this->bookingRepository->find($id);
        if (!$booking) {
            return $this->json(['message' => 'Réservation non trouvée'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($booking);
        $this->entityManager->flush();

        return $this->json(['message' => 'Réservation supprimée avec succès'], Response::HTTP_OK);
    }
}
