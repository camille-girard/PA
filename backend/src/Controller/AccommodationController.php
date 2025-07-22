<?php

namespace App\Controller;

use App\Entity\Accommodation;
use App\Entity\AccommodationImages;
use App\Entity\Owner;
use App\Repository\AccommodationRepository;
use App\Repository\OwnerRepository;
use App\Repository\ThemeRepository;
use App\Service\CloudflareR2Service;
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
        private CloudflareR2Service $cloudflareR2Service,
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
    public function myAccommodation(SerializerInterface $serializer): JsonResponse
    {
        try {
            /** @var Owner|null $user */
            $user = $this->getUser();

            if (!$user) {
                return $this->json(['message' => 'Utilisateur non connecté'], Response::HTTP_UNAUTHORIZED);
            }

            // Test progressif pour identifier quelle propriété pose problème
            $accommodations = $this->accommodationRepository->findByOwnerId($user->getId());

            if (empty($accommodations)) {
                return $this->json(['accommodations' => []], Response::HTTP_OK);
            }

            $accommodationsData = [];

            foreach ($accommodations as $accommodation) {
                try {
                    $accommodationsData[] = [
                        'id' => $accommodation->getId(),
                        'name' => $accommodation->getName(),
                        'description' => $accommodation->getDescription(),
                        'address' => $accommodation->getAddress(),
                        'city' => $accommodation->getCity(),
                        'postalCode' => $accommodation->getPostalCode(),
                        'country' => $accommodation->getCountry(),
                        'type' => $accommodation->getType(),
                        'price' => $accommodation->getPrice(),
                        'capacity' => $accommodation->getCapacity(),
                        'bedrooms' => $accommodation->getBedrooms(),
                        'bathrooms' => $accommodation->getBathrooms(),
                        'rating' => $accommodation->getRating(),
                        'latitude' => $accommodation->getLatitude(),
                        'longitude' => $accommodation->getLongitude(),
                        'minStay' => $accommodation->getMinStay(),
                        'maxStay' => $accommodation->getMaxStay(),
                        'createdAt' => $accommodation->getCreatedAt()?->format('Y-m-d H:i:s'),
                        'theme' => $accommodation->getTheme() ? [
                            'id' => $accommodation->getTheme()->getId(),
                            'name' => $accommodation->getTheme()->getName(),
                        ] : null,
                        'images' => array_map(fn ($image) => [
                            'url' => $image->getUrl(),
                            'isMain' => $image->isMain(),
                        ], $accommodation->getImages()->toArray()),
                    ];
                } catch (\Exception $e) {
                    // Si erreur sur une accommodation, on la skip
                    error_log('Erreur sur accommodation ID '.$accommodation->getId().': '.$e->getMessage());
                }
            }

            return $this->json([
                'accommodations' => $accommodationsData,
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            // Log l'erreur complète
            error_log('Erreur dans myAccommodation: '.$e->getMessage());
            error_log('Stack trace: '.$e->getTraceAsString());

            return $this->json([
                'error' => 'Erreur serveur',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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

        // Récupérer les données depuis la requête POST (FormData)
        $data = $request->request->all();

        if (empty($data)) {
            return $this->json(['message' => 'Données manquantes'], Response::HTTP_BAD_REQUEST);
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

        // Traitement des images uploadées
        $uploadedFiles = $request->files->all();
        $isFirstImage = true;

        foreach ($uploadedFiles as $key => $file) {
            if ($file && $file->isValid()) {
                try {
                    // Persister d'abord l'accommodation pour avoir un ID
                    if (!$accommodation->getId()) {
                        $this->entityManager->persist($accommodation);
                        $this->entityManager->flush();
                    }

                    $imageUrl = $this->cloudflareR2Service->uploadAccommodationImage($file, $accommodation->getId());

                    $image = new AccommodationImages();
                    $image->setUrl($imageUrl);
                    $image->setIsMain($isFirstImage); // La première image est principale par défaut
                    $image->setAccommodation($accommodation);
                    $accommodation->addImage($image);

                    // Persister l'image
                    $this->entityManager->persist($image);

                    $isFirstImage = false;
                } catch (\Exception $e) {
                    // Log l'erreur mais continue avec les autres images
                    error_log('Erreur upload image: '.$e->getMessage());
                }
            }
        }

        $errors = $this->validator->validate($accommodation);
        if (count($errors) > 0) {
            return $this->errorFormatter->createValidationErrorResponse($errors);
        }

        // Persister l'accommodation si ce n'est pas déjà fait
        if (!$accommodation->getId()) {
            $this->entityManager->persist($accommodation);
        }
        $this->entityManager->flush();

        return $this->json([
            'message' => 'Hébergement créé avec succès',
            'accommodation' => [
                'id' => $accommodation->getId(),
            ],
        ], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT', 'POST'])]
    public function update(int $id, Request $request, SerializerInterface $serializer): JsonResponse
    {
        $accommodation = $this->accommodationRepository->find($id);

        if (!$accommodation) {
            return $this->json(['message' => 'Hébergement non trouvé'], Response::HTTP_NOT_FOUND);
        }

        // Pour les requêtes PUT avec multipart/form-data, Symfony ne parse pas automatiquement
        // On force le parsing en créant une nouvelle requête POST temporaire
        if ($request->getMethod() === 'PUT' && strpos($request->headers->get('Content-Type', ''), 'multipart/form-data') !== false) {
            $tempRequest = Request::create('/', 'POST', [], [], [], [], $request->getContent());
            $tempRequest->headers->set('Content-Type', $request->headers->get('Content-Type'));
            $data = $tempRequest->request->all();
        } else {
            $data = $request->request->all();
        }
        
        if (empty($data)) {
            return $this->json(['message' => 'Données manquantes'], Response::HTTP_BAD_REQUEST);
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
            $postalCode = trim($data['postalCode']);
            if (empty($postalCode)) {
                $accommodation->setPostalCode(null);
            } else {
                $accommodation->setPostalCode(substr($postalCode, 0, 10));
            }
        }
        if (isset($data['country'])) {
            $accommodation->setCountry($data['country']);
        }
        if (isset($data['type'])) {
            $accommodation->setType($data['type']);
        }
        if (isset($data['bedrooms'])) {
            $accommodation->setBedrooms((int) $data['bedrooms']);
        }
        if (isset($data['bathrooms'])) {
            $accommodation->setBathrooms((int) $data['bathrooms']);
        }
        if (isset($data['price'])) {
            $accommodation->setPrice((float) $data['price']);
        }
        if (isset($data['capacity'])) {
            $accommodation->setCapacity((int) $data['capacity']);
        }
        if (isset($data['latitude'])) {
            $accommodation->setLatitude($data['latitude'] ? (float) $data['latitude'] : null);
        }
        if (isset($data['longitude'])) {
            $accommodation->setLongitude($data['longitude'] ? (float) $data['longitude'] : null);
        }
        if (isset($data['minStay'])) {
            $accommodation->setMinStay((int) $data['minStay']);
        }
        if (isset($data['maxStay'])) {
            $accommodation->setMaxStay((int) $data['maxStay']);
        }

        if (isset($data['themeId']) && !empty($data['themeId'])) {
            $themeId = (int) $data['themeId'];
            if ($themeId > 0) {
                $theme = $this->themeRepository->find($themeId);
                if (!$theme) {
                    return $this->json(['message' => 'Thème non trouvé'], Response::HTTP_BAD_REQUEST);
                }
                $accommodation->setTheme($theme);
            }
        } elseif (isset($data['theme']) && !empty($data['theme'])) {
            $themeId = (int) $data['theme'];
            
            if ($themeId > 0) {
                $theme = $this->themeRepository->find($themeId);
                if (!$theme) {
                    return $this->json(['message' => 'Thème non trouvé'], Response::HTTP_BAD_REQUEST);
                }
                $accommodation->setTheme($theme);
            }
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

        $errors = $this->validator->validate($accommodation);
        if (count($errors) > 0) {
            return $this->errorFormatter->createValidationErrorResponse($errors);
        }

        $this->entityManager->flush();

        $serialized = $serializer->serialize($accommodation, 'json', [
            'groups' => ['accommodation:read'],
        ]);

        return JsonResponse::fromJsonString(json_encode([
            'message' => 'Hébergement mis à jour avec succès',
            'accommodation' => json_decode($serialized),
        ]), Response::HTTP_OK);
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

    #[Route('/{id}/images', name: 'upload_image', methods: ['POST'])]
    public function uploadImage(int $id, Request $request): JsonResponse
    {
        /** @var Owner|null $user */
        $user = $this->getUser();

        if (!$user) {
            return $this->json(['message' => 'Utilisateur non connecté'], Response::HTTP_UNAUTHORIZED);
        }

        $accommodation = $this->accommodationRepository->find($id);

        if (!$accommodation) {
            return $this->json(['message' => 'Hébergement non trouvé'], Response::HTTP_NOT_FOUND);
        }

        // Vérifier que l'utilisateur est le propriétaire de l'hébergement OU un administrateur
        $isOwner = $accommodation->getOwner()->getId() === $user->getId();
        $isAdmin = in_array('ROLE_ADMIN', $user->getRoles());

        if (!$isOwner && !$isAdmin) {
            return $this->json(['message' => 'Accès refusé'], Response::HTTP_FORBIDDEN);
        }

        $files = $request->files;
        $uploadedImages = [];

        // Gérer les uploads multiples
        foreach ($files as $file) {
            if ($file && $file->isValid()) {
                try {
                    $imageUrl = $this->cloudflareR2Service->uploadAccommodationImage($file, $accommodation->getId());

                    // Créer une nouvelle entrée AccommodationImages
                    $accommodationImage = new AccommodationImages();
                    $accommodationImage->setUrl($imageUrl);
                    $accommodationImage->setIsMain(false); // Par défaut, pas d'image principale
                    $accommodationImage->setAccommodation($accommodation);

                    $this->entityManager->persist($accommodationImage);
                    $uploadedImages[] = [
                        'url' => $imageUrl,
                        'isMain' => false,
                    ];
                } catch (\Exception $e) {
                    return $this->json([
                        'message' => 'Erreur lors de l\'upload de l\'image',
                        'error' => $e->getMessage(),
                    ], Response::HTTP_INTERNAL_SERVER_ERROR);
                }
            }
        }

        $this->entityManager->flush();

        return $this->json([
            'message' => 'Images uploadées avec succès',
            'images' => $uploadedImages,
        ], Response::HTTP_OK);
    }

    #[Route('/{id}/images/{imageId}', name: 'delete_image', methods: ['DELETE'])]
    public function deleteImage(int $id, int $imageId): JsonResponse
    {
        /** @var Owner|null $user */
        $user = $this->getUser();

        if (!$user) {
            return $this->json(['message' => 'Utilisateur non connecté'], Response::HTTP_UNAUTHORIZED);
        }

        $accommodation = $this->accommodationRepository->find($id);

        if (!$accommodation) {
            return $this->json(['message' => 'Hébergement non trouvé'], Response::HTTP_NOT_FOUND);
        }

        // Vérifier que l'utilisateur est le propriétaire de l'hébergement OU un administrateur
        $isOwner = $accommodation->getOwner()->getId() === $user->getId();
        $isAdmin = in_array('ROLE_ADMIN', $user->getRoles());

        if (!$isOwner && !$isAdmin) {
            return $this->json(['message' => 'Accès refusé'], Response::HTTP_FORBIDDEN);
        }

        // Trouver l'image à supprimer
        $image = null;
        foreach ($accommodation->getImages() as $accommodationImage) {
            if ($accommodationImage->getId() === $imageId) {
                $image = $accommodationImage;
                break;
            }
        }

        if (!$image) {
            return $this->json(['message' => 'Image non trouvée'], Response::HTTP_NOT_FOUND);
        }

        // Supprimer l'image de Cloudflare R2
        $this->cloudflareR2Service->deleteFile($image->getUrl());

        // Supprimer l'image de la base de données
        $this->entityManager->remove($image);
        $this->entityManager->flush();

        return $this->json(['message' => 'Image supprimée avec succès'], Response::HTTP_OK);
    }

    #[Route('/{id}/images/{imageId}/main', name: 'set_main_image', methods: ['PUT'])]
    public function setMainImage(int $id, int $imageId): JsonResponse
    {
        /** @var Owner|null $user */
        $user = $this->getUser();

        if (!$user) {
            return $this->json(['message' => 'Utilisateur non connecté'], Response::HTTP_UNAUTHORIZED);
        }

        $accommodation = $this->accommodationRepository->find($id);

        if (!$accommodation) {
            return $this->json(['message' => 'Hébergement non trouvé'], Response::HTTP_NOT_FOUND);
        }

        // Vérifier que l'utilisateur est le propriétaire de l'hébergement OU un administrateur
        $isOwner = $accommodation->getOwner()->getId() === $user->getId();
        $isAdmin = in_array('ROLE_ADMIN', $user->getRoles());

        if (!$isOwner && !$isAdmin) {
            return $this->json(['message' => 'Accès refusé'], Response::HTTP_FORBIDDEN);
        }

        // Réinitialiser toutes les images comme non principales
        foreach ($accommodation->getImages() as $accommodationImage) {
            $accommodationImage->setIsMain(false);
        }

        // Trouver l'image à définir comme principale
        $image = null;
        foreach ($accommodation->getImages() as $accommodationImage) {
            if ($accommodationImage->getId() === $imageId) {
                $image = $accommodationImage;
                break;
            }
        }

        if (!$image) {
            return $this->json(['message' => 'Image non trouvée'], Response::HTTP_NOT_FOUND);
        }

        $image->setIsMain(true);
        $this->entityManager->flush();

        return $this->json(['message' => 'Image principale mise à jour avec succès'], Response::HTTP_OK);
    }
}
