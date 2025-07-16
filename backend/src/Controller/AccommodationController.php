<?php

namespace App\Controller;

use App\Entity\Accommodation;
use App\Entity\AccommodationImages;
use App\Entity\Owner;
use App\Repository\AccommodationRepository;
use App\Repository\OwnerRepository;
use App\Repository\ThemeRepository;
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

#[Route('/api/accommodations', name: 'api_accommodation_')]
#[OA\Tag(name: 'Accommodation')]
class AccommodationController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private AccommodationRepository $accommodationRepository,
        private ThemeRepository $themeRepository,
        private OwnerRepository $ownerRepository,
        private ValidatorInterface $validator,
        private ValidationErrorFormatterService $errorFormatter,
    ) {
    }

    #[Route('', name: 'index', methods: ['GET'])]
    public function index(SerializerInterface $serializer): JsonResponse
    {
        $accommodations = $this->accommodationRepository->findAll();

        $json = $serializer->serialize($accommodations, 'json', ['groups' => 'accommodation:read']);

        return JsonResponse::fromJsonString($json, Response::HTTP_OK);
    }

    #[Route('/me', name: 'my-accommodation', methods: ['GET'])]
    public function myAccommodation(): JsonResponse
    {
        /** @var Owner|null $user */
        $user = $this->getUser();

        if (!$user) {
            return $this->json(['message' => 'Utilisateur non connecté'], Response::HTTP_UNAUTHORIZED);
        }

        $accommodations = $this->accommodationRepository->findByOwnerId($user->getId());

        return $this->json([
            'accommodations' => $accommodations,
        ], Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $accommodation = $this->accommodationRepository->find($id);

        if (!$accommodation) {
            return $this->json(['message' => 'Hébergement non trouvé'], Response::HTTP_NOT_FOUND);
        }

        return $this->json([
            'id' => $accommodation->getId(),
            'name' => $accommodation->getName(),
            'description' => $accommodation->getDescription(),
            'address' => $accommodation->getAddress(),
            'city' => $accommodation->getCity(),
            'postalCode' => $accommodation->getPostalCode(),
            'country' => $accommodation->getCountry(),
            'type' => $accommodation->getType(),
            'theme' => $accommodation->getTheme()?->getName(),
            'themeId' => $accommodation->getTheme()?->getId(),
            'bedrooms' => $accommodation->getBedrooms(),
            'bathrooms' => $accommodation->getBathrooms(),
            'capacity' => $accommodation->getCapacity(),
            'price' => $accommodation->getPrice(),
            'rating' => $accommodation->getRating(),
            'advantage' => $accommodation->getAdvantage(),
            'practicalInformations' => $accommodation->getPracticalInformations(),
            'latitude' => $accommodation->getLatitude(),
            'longitude' => $accommodation->getLongitude(),
            'createdAt' => $accommodation->getCreatedAt()?->format('Y-m-d H:i:s'),
            'images' => array_map(fn ($image) => [
                'url' => $image->getUrl(),
                'alt' => 'Image du logement',
                'main' => $image->isMain(),
            ], $accommodation->getImages()->toArray()),
            'host' => [
                'id' => $accommodation->getOwner()?->getId(),
                'lastName' => $accommodation->getOwner()?->getLastName(),
                'firstName' => $accommodation->getOwner()?->getFirstName(),
                'bio' => $accommodation->getOwner()?->getBio(),
                'email' => $accommodation->getOwner()?->getEmail(),
                'avatar' => $accommodation->getOwner()?->getAvatar(),
                'rating' => $accommodation->getOwner()?->getRating() ?? 0,
            ],
            'bookings' => array_map(fn ($booking) => [
                'id' => $booking->getId(),
                'startDate' => $booking->getStartDate()?->format('Y-m-d'),
                'endDate' => $booking->getEndDate()?->format('Y-m-d'),
                'status' => $booking->getStatus(),
                'client' => [
                    'id' => $booking->getClient()?->getId(),
                    'firstName' => $booking->getClient()?->getFirstName(),
                    'lastName' => $booking->getClient()?->getLastName(),
                ],
            ], $accommodation->getBookings()->toArray()),
        ]);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        /** @var Owner|null $user */
        $user = $this->getUser();

        if (!$user) {
            return $this->json(['message' => 'Utilisateur non connecté'], Response::HTTP_UNAUTHORIZED);
        }

        $owner = $this->ownerRepository->findOneBy(['id' => $user->getId()]);

        if (!$owner) {
            return $this->json(['message' => 'Propriétaire non trouvé'], Response::HTTP_FORBIDDEN);
        }

        $data = json_decode($request->getContent(), true);

        if (null === $data) {
            return $this->json(['message' => 'JSON invalide'], Response::HTTP_BAD_REQUEST);
        }

        $requiredFields = ['name', 'description', 'address', 'price', 'capacity'];
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

        $accommodation = new Accommodation();
        $accommodation->setName($data['name']);
        $accommodation->setDescription($data['description']);
        $accommodation->setPracticalInformations($data['practicalInformation'] ?? null);
        $accommodation->setAddress($data['address']);
        $accommodation->setCity($data['city'] ?? null);
        $accommodation->setPostalCode($data['postalCode'] ?? null);
        $accommodation->setCountry($data['country'] ?? 'France');
        $accommodation->setType($data['type'] ?? null);
        $accommodation->setPrice($data['price']);
        $accommodation->setCapacity($data['capacity']);
        $accommodation->setBedrooms($data['bedrooms'] ?? 0);
        $accommodation->setBathrooms($data['bathrooms'] ?? 0);
        $accommodation->setLatitude($data['latitude'] ?? null);
        $accommodation->setLongitude($data['longitude'] ?? null);
        $accommodation->setMinStay($data['minStay'] ?? 1);
        $accommodation->setMaxStay($data['maxStay'] ?? 7);
        $accommodation->setOwner($owner);
        $accommodation->setCreatedAt(new \DateTimeImmutable());

        if (isset($data['theme'])) {
            $theme = $this->themeRepository->find($data['theme']);
            if (!$theme) {
                return $this->json(['message' => 'Thème non trouvé'], Response::HTTP_BAD_REQUEST);
            }
            $accommodation->setTheme($theme);
        }

        if (isset($data['images']) && is_array($data['images'])) {
            foreach ($data['images'] as $imageData) {
                if (isset($imageData['url']) && filter_var($imageData['url'], FILTER_VALIDATE_URL)) {
                    $image = new AccommodationImages();
                    $image->setUrl($imageData['url']);
                    $image->setIsMain($imageData['isMain'] ?? false);
                    $image->setAccommodation($accommodation);
                    $accommodation->addImage($image);
                }
            }
        }

        $errors = $this->validator->validate($accommodation);
        if (count($errors) > 0) {
            return $this->errorFormatter->createValidationErrorResponse($errors);
        }

        $this->entityManager->persist($accommodation);
        $this->entityManager->flush();

        return $this->json([
            'message' => 'Hébergement créé avec succès',
            'accommodation' => [
                'id' => $accommodation->getId(),
            ],
        ], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $accommodation = $this->accommodationRepository->find($id);

        if (!$accommodation) {
            return $this->json(['message' => 'Hébergement non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        if (!is_array($data)) {
            return $this->json(['message' => 'JSON invalide'], Response::HTTP_BAD_REQUEST);
        }

        if (isset($data['name'])) {
            $accommodation->setName($data['name']);
        }
        if (isset($data['description'])) {
            $accommodation->setDescription($data['description']);
        }
        if (isset($data['address'])) {
            $accommodation->setAddress($data['address']);
        }
        if (isset($data['city'])) {
            $accommodation->setCity($data['city']);
        }
        if (isset($data['postalCode'])) {
            $accommodation->setPostalCode($data['postalCode']);
        }
        if (isset($data['country'])) {
            $accommodation->setCountry($data['country']);
        }
        if (isset($data['type'])) {
            $accommodation->setType($data['type']);
        }
        if (isset($data['bedrooms'])) {
            $accommodation->setBedrooms($data['bedrooms']);
        }
        if (isset($data['bathrooms'])) {
            $accommodation->setBathrooms($data['bathrooms']);
        }
        if (isset($data['price'])) {
            $accommodation->setPrice($data['price']);
        }
        if (isset($data['capacity'])) {
            $accommodation->setCapacity($data['capacity']);
        }
        if (isset($data['latitude'])) {
            $accommodation->setLatitude($data['latitude']);
        }
        if (isset($data['longitude'])) {
            $accommodation->setLongitude($data['longitude']);
        }

        if (array_key_exists('practicalInformations', $data)) {
            $accommodation->setPracticalInformations($data['practicalInformations'] ?? '');
        }

        if (array_key_exists('advantage', $data)) {
            $accommodation->setAdvantage($data['advantage'] ?? []);
        }

        if (isset($data['ownerId'])) {
            $owner = $this->ownerRepository->find($data['ownerId']);
            if (!$owner) {
                return $this->json(['message' => 'Propriétaire non trouvé'], Response::HTTP_BAD_REQUEST);
            }
            $accommodation->setOwner($owner);
        }

        if (isset($data['theme'])) {
            $theme = $this->themeRepository->find($data['theme']);
            if (!$theme) {
                return $this->json(['message' => 'Thème non trouvé'], Response::HTTP_BAD_REQUEST);
            }
            $accommodation->setTheme($theme);
        }

        $errors = $this->validator->validate($accommodation);
        if (count($errors) > 0) {
            return $this->errorFormatter->createValidationErrorResponse($errors);
        }

        $this->entityManager->flush();

        return $this->json([
            'message' => 'Hébergement mis à jour avec succès',
            'accommodation' => $accommodation,
        ], Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $accommodation = $this->accommodationRepository->find($id);

        if (!$accommodation) {
            return $this->json(['message' => 'Hébergement non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($accommodation);
        $this->entityManager->flush();

        return $this->json(['message' => 'Hébergement supprimé avec succès'], Response::HTTP_OK);
    }
}
