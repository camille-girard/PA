<?php

namespace App\Controller;

use App\Entity\Accommodation;
use App\Repository\AccommodationRepository;
use App\Repository\OwnerRepository;
use App\Repository\ThemeRepository;
use App\Service\ValidationErrorFormatterService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/accommodations', name: 'api_accommodations_')]
final class AccommodationController extends AbstractController
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
    public function index(): JsonResponse
    {
        $accommodations = $this->accommodationRepository->findAll();

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
            'accommodation' => $accommodation,
        ], Response::HTTP_OK);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (null === $data) {
            return $this->json(['message' => 'JSON invalide'], Response::HTTP_BAD_REQUEST);
        }

        $requiredFields = ['name', 'description', 'address', 'price', 'capacity', 'ownerId'];
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

        $owner = $this->ownerRepository->find($data['ownerId']);
        if (!$owner) {
            return $this->json(['message' => 'Propriétaire non trouvé'], Response::HTTP_BAD_REQUEST);
        }

        $accommodation = new Accommodation();
        $accommodation->setName($data['name']);
        $accommodation->setDescription($data['description']);
        $accommodation->setAddress($data['address']);
        $accommodation->setPrice($data['price']);
        $accommodation->setCapacity($data['capacity']);
        $accommodation->setOwner($owner);

        if (isset($data['themeId'])) {
            $theme = $this->themeRepository->find($data['themeId']);
            if (!$theme) {
                return $this->json(['message' => 'Thème non trouvé'], Response::HTTP_BAD_REQUEST);
            }
            $accommodation->setTheme($theme);
        }

        $errors = $this->validator->validate($accommodation);
        if (count($errors) > 0) {
            return $this->errorFormatter->createValidationErrorResponse($errors);
        }

        $this->entityManager->persist($accommodation);
        $this->entityManager->flush();

        return $this->json([
            'message' => 'Hébergement créé avec succès',
            'accommodation' => $accommodation,
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

        if (isset($data['price'])) {
            $accommodation->setPrice($data['price']);
        }

        if (isset($data['capacity'])) {
            $accommodation->setCapacity($data['capacity']);
        }

        if (isset($data['ownerId'])) {
            $owner = $this->ownerRepository->find($data['ownerId']);
            if (!$owner) {
                return $this->json(['message' => 'Propriétaire non trouvé'], Response::HTTP_BAD_REQUEST);
            }
            $accommodation->setOwner($owner);
        }

        if (isset($data['themeId'])) {
            $theme = $this->themeRepository->find($data['themeId']);
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

        return $this->json([
            'message' => 'Hébergement supprimé avec succès',
        ], Response::HTTP_OK);
    }
}
