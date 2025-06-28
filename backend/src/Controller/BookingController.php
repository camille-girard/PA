<?php

namespace App\Controller;

use App\Entity\Booking;
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
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\SerializerInterface;

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

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $booking = $this->bookingRepository->find($id);

        if (!$booking) {
            return $this->json(['message' => 'Réservation non trouvée'], Response::HTTP_NOT_FOUND);
        }

        return $this->json([
            'booking' => $booking,
        ], Response::HTTP_OK);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (null === $data) {
            return $this->json(['message' => 'JSON invalide'], Response::HTTP_BAD_REQUEST);
        }

        $requiredFields = ['startDate', 'endDate', 'clientId', 'accommodationId'];
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

        $accommodation = $this->accommodationRepository->find($data['accommodationId']);
        if (!$accommodation) {
            return $this->json(['message' => 'Hébergement non trouvé'], Response::HTTP_BAD_REQUEST);
        }

        $booking = new Booking();
        $booking->setStartDate(new \DateTime($data['startDate']));
        $booking->setEndDate(new \DateTime($data['endDate']));
        $booking->setClient($client);
        $booking->setAccommodation($accommodation);

        if (isset($data['status'])) {
            $booking->setStatus($data['status']);
        }

        if (isset($data['totalPrice'])) {
            $booking->setTotalPrice($data['totalPrice']);
        }

        $errors = $this->validator->validate($booking);
        if (count($errors) > 0) {
            return $this->errorFormatter->createValidationErrorResponse($errors);
        }

        $this->entityManager->persist($booking);
        $this->entityManager->flush();

        return $this->json([
            'message' => 'Réservation créée avec succès',
            'booking' => $booking,
        ], Response::HTTP_CREATED);
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

        if (isset($data['startDate'])) {
            $booking->setStartDate(new \DateTime($data['startDate']));
        }

        if (isset($data['endDate'])) {
            $booking->setEndDate(new \DateTime($data['endDate']));
        }

        if (isset($data['status'])) {
            $booking->setStatus($data['status']);
        }

        if (isset($data['totalPrice'])) {
            $booking->setTotalPrice($data['totalPrice']);
        }

        if (isset($data['clientId'])) {
            $client = $this->clientRepository->find($data['clientId']);
            if (!$client) {
                return $this->json(['message' => 'Client non trouvé'], Response::HTTP_BAD_REQUEST);
            }
            $booking->setClient($client);
        }

        if (isset($data['accommodationId'])) {
            $accommodation = $this->accommodationRepository->find($data['accommodationId']);
            if (!$accommodation) {
                return $this->json(['message' => 'Hébergement non trouvé'], Response::HTTP_BAD_REQUEST);
            }
            $booking->setAccommodation($accommodation);
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

        return $this->json([
            'message' => 'Réservation supprimée avec succès',
        ], Response::HTTP_OK);
    }
}
