<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\User;
use App\Repository\BookingRepository;
use App\Repository\CommentRepository;
use App\Service\RatingService;
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
        private CommentRepository $commentRepository,
        private ValidatorInterface $validator,
        private ValidationErrorFormatterService $errorFormatter,
        private RatingService $ratingService,
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
        /** @var User|null $user */
        $user = $this->getUser();
        if (!$user || !in_array('ROLE_CLIENT', $user->getRoles(), true)) {
            return $this->json(['message' => 'Accès non autorisé'], Response::HTTP_FORBIDDEN);
        }

        $bookings = $this->bookingRepository->findBy(['client' => $user]);

        $bookingsData = [];
        foreach ($bookings as $booking) {
            $bookingData = json_decode($serializer->serialize($booking, 'json', ['groups' => 'booking:read']), true);

            $hasRated = false;
            if ($booking->getAccommodation()) {
                $hasRated = $this->commentRepository->hasUserRatedAccommodation(
                    $user->getId(),
                    $booking->getAccommodation()->getId()
                );
            }

            $bookingData['hasRated'] = $hasRated;
            $bookingsData[] = $bookingData;
        }

        return $this->json($bookingsData, Response::HTTP_OK);
    }

    #[Route('/owner', name: 'owner_bookings', methods: ['GET'])]
    public function ownerBookings(SerializerInterface $serializer): JsonResponse
    {
        /** @var User|null $user */
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

    #[Route('/{id}/rate', name: 'rate', methods: ['POST'])]
    #[OA\Post(
        summary: 'Noter une réservation terminée',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: 'object',
                properties: [
                    'accommodationRating' => new OA\Property(type: 'integer', minimum: 1, maximum: 5),
                    'accommodationComment' => new OA\Property(type: 'string', minLength: 1, maxLength: 500),
                    'ownerRating' => new OA\Property(type: 'integer', minimum: 1, maximum: 5),
                ],
                required: ['accommodationRating', 'accommodationComment', 'ownerRating']
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Notation enregistrée avec succès'),
            new OA\Response(response: 403, description: 'Accès non autorisé'),
            new OA\Response(response: 404, description: 'Réservation non trouvée'),
            new OA\Response(response: 400, description: 'Données invalides'),
        ]
    )]
    public function rate(int $id, Request $request): JsonResponse
    {
        /** @var User|null $user */
        $user = $this->getUser();

        error_log('📝 Rate endpoint called - User: '.($user ? $user->getEmail() : 'null').' - Booking ID: '.$id);

        if (!$user || !in_array('ROLE_CLIENT', $user->getRoles(), true)) {
            error_log('Access denied - User roles: '.($user ? implode(', ', $user->getRoles()) : 'null'));

            return $this->json(['message' => 'Accès non autorisé'], Response::HTTP_FORBIDDEN);
        }

        $booking = $this->bookingRepository->find($id);
        if (!$booking) {
            error_log('Booking not found with ID: '.$id);

            return $this->json(['message' => 'Réservation non trouvée'], Response::HTTP_NOT_FOUND);
        }

        error_log('Booking found - Client ID: '.($booking->getClient() ? $booking->getClient()->getId() : 'null').' - Current user ID: '.$user->getId());

        if ($booking->getClient() !== $user) {
            error_log('Booking does not belong to current user');

            return $this->json(['message' => 'Accès non autorisé - Cette réservation ne vous appartient pas'], Response::HTTP_FORBIDDEN);
        }

        error_log('Booking belongs to user');

        $now = new \DateTimeImmutable();
        error_log('Checking dates - End date: '.$booking->getEndDate()->format('Y-m-d H:i:s').' - Now: '.$now->format('Y-m-d H:i:s'));

        if ($booking->getEndDate() > $now) {
            error_log('Booking not yet finished');

            return $this->json(['message' => 'La réservation n\'est pas encore terminée'], Response::HTTP_BAD_REQUEST);
        }

        error_log('Booking is finished, proceeding with rating');

        $hasAlreadyRated = $this->commentRepository->hasUserRatedAccommodation(
            $user->getId(),
            $booking->getAccommodation()->getId()
        );

        if ($hasAlreadyRated) {
            error_log('User has already rated this accommodation');

            return $this->json(['message' => 'Vous avez déjà noté cette accommodation'], Response::HTTP_BAD_REQUEST);
        }

        error_log('User has not rated this accommodation yet');

        $data = json_decode($request->getContent(), true);
        if (!is_array($data)) {
            return $this->json(['message' => 'JSON invalide'], Response::HTTP_BAD_REQUEST);
        }

        $accommodationRating = $data['accommodationRating'] ?? null;
        $accommodationComment = $data['accommodationComment'] ?? '';
        $ownerRating = $data['ownerRating'] ?? null;

        if (!is_int($accommodationRating) || $accommodationRating < 1 || $accommodationRating > 5) {
            return $this->json(['message' => 'La note du logement doit être entre 1 et 5'], Response::HTTP_BAD_REQUEST);
        }

        if (!is_int($ownerRating) || $ownerRating < 1 || $ownerRating > 5) {
            return $this->json(['message' => 'La note de l\'hôte doit être entre 1 et 5'], Response::HTTP_BAD_REQUEST);
        }

        if (empty(trim($accommodationComment)) || strlen($accommodationComment) > 500) {
            return $this->json(['message' => 'Le commentaire est obligatoire et doit faire moins de 500 caractères'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $comment = new Comment();
            $comment->setClient($user);
            $comment->setAccommodation($booking->getAccommodation());
            $comment->setContent($accommodationComment);
            $comment->setRating($accommodationRating);
            $comment->setCreatedAt(new \DateTimeImmutable());

            $this->entityManager->persist($comment);
            $this->entityManager->flush();

            $accommodation = $booking->getAccommodation();
            $this->ratingService->updateRatingsAfterComment($accommodation);

            error_log('Ratings updated - Accommodation: '.$accommodation->getRating().' - Owner: '.($accommodation->getOwner() ? $accommodation->getOwner()->getRating() : 'null'));

            return $this->json([
                'message' => 'Notation enregistrée avec succès',
                'comment' => [
                    'id' => $comment->getId(),
                    'rating' => $comment->getRating(),
                    'content' => $comment->getContent(),
                ],
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'message' => 'Erreur lors de l\'enregistrement de la notation',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
