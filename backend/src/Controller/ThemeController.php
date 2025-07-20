<?php

namespace App\Controller;

use App\Entity\Theme;
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

#[Route('/api/themes', name: 'api_themes_')]
#[OA\Tag(name: 'Themes')]
final class ThemeController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ThemeRepository $themeRepository,
        private ValidatorInterface $validator,
        private ValidationErrorFormatterService $errorFormatter,
    ) {
    }

    #[Route('', name: 'index', methods: ['GET'])]
    public function index(SerializerInterface $serializer): JsonResponse
    {
        $themes = $this->themeRepository->findAll();

        $json = $serializer->serialize(
            ['themes' => $themes],
            'json',
            [
                'groups' => ['theme:list'],
            ]
        );

        return JsonResponse::fromJsonString($json, Response::HTTP_OK);
    }

    #[Route('/accommodation', name: 'accommodation', methods: ['GET'])]
    public function getThemesWithAccommodations(SerializerInterface $serializer): JsonResponse
    {
        $themes = $this->themeRepository->findAllWithAccommodations();

        if (empty($themes)) {
            return $this->json(['message' => 'No themes found'], Response::HTTP_NOT_FOUND);
        }

        $json = $serializer->serialize(
            ['themes' => $themes],
            'json',
            [
                'groups' => ['theme:read', 'accommodation:summary', 'owner:summary'],
                'enable_max_depth' => true,
            ]
        );

        return JsonResponse::fromJsonString($json, Response::HTTP_OK);
    }

    #[Route('/accommodation/{slug}', name: 'accommodations_by_slug', methods: ['GET'])]
    public function getThemesWithAccommodationsBySlug(string $slug, SerializerInterface $serializer): JsonResponse
    {
        $theme = $this->themeRepository->findOneBy(['slug' => $slug]);
        if (!$theme) {
            return $this->json(['message' => 'Theme not found'], Response::HTTP_NOT_FOUND);
        }

        $json = $serializer->serialize(
            $theme,
            'json',
            [
                'groups' => ['theme:read', 'accommodation:summary', 'owner:summary'],
                'enable_max_depth' => true,
            ]
        );

        return JsonResponse::fromJsonString($json, Response::HTTP_OK);
    }

    #[Route('/{id<\d+>}', name: 'show', methods: ['GET'])]
    public function show(int $id, SerializerInterface $serializer): JsonResponse
    {
        $theme = $this->themeRepository->find($id);

        if (!$theme) {
            return $this->json(['message' => 'Theme not found'], Response::HTTP_NOT_FOUND);
        }

        $json = $serializer->serialize(
            $theme,
            'json',
            [
                'groups' => ['theme:read', 'accommodation:read', 'owner:summary'],
                'enable_max_depth' => true,
            ]
        );

        return JsonResponse::fromJsonString($json, Response::HTTP_OK);
    }

    #[Route('/{slug}', name: 'show_by_slug', methods: ['GET'])]
    public function showBySlug(string $slug, SerializerInterface $serializer): JsonResponse
    {
        $theme = $this->themeRepository->findOneBy(['slug' => $slug]);

        if (!$theme) {
            return $this->json(['message' => 'Theme not found'], Response::HTTP_NOT_FOUND);
        }

        $json = $serializer->serialize(
            $theme,
            'json',
            [
                'groups' => ['theme:read', 'accommodation:read', 'owner:summary'],
                'enable_max_depth' => true,
            ]
        );

        return JsonResponse::fromJsonString($json, Response::HTTP_OK);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (null === $data) {
            return $this->json(['message' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
        }

        $requiredFields = ['name', 'description'];
        $missingFields = [];

        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty(trim($data[$field]))) {
                $missingFields[] = $field;
            }
        }

        if (!empty($missingFields)) {
            return $this->json([
                'message' => 'Missing required fields',
                'missingFields' => $missingFields,
            ], Response::HTTP_BAD_REQUEST);
        }

        $name = trim($data['name']);
        $description = trim($data['description']);

        if (strlen($name) < 2) {
            return $this->json([
                'message' => 'Le nom doit contenir au moins 2 caractères',
            ], Response::HTTP_BAD_REQUEST);
        }

        if (strlen($description) < 10) {
            return $this->json([
                'message' => 'La description doit contenir au moins 10 caractères',
            ], Response::HTTP_BAD_REQUEST);
        }

        $theme = new Theme();
        $theme->setName($name);
        $theme->setDescription($description);

        if (isset($data['slug']) && !empty(trim($data['slug']))) {
            $theme->setSlug(trim($data['slug']));
        } else {
            $slug = strtolower(str_replace(' ', '-', $name));
            $slug = preg_replace('/[^a-z0-9\-]/', '', $slug); // Nettoyer le slug
            $theme->setSlug($slug);
        }

        $errors = $this->validator->validate($theme);
        if (count($errors) > 0) {
            return $this->errorFormatter->createValidationErrorResponse($errors);
        }

        try {
            $this->entityManager->persist($theme);
            $this->entityManager->flush();

            $this->entityManager->refresh($theme);
        } catch (\Exception $e) {
            return $this->json([
                'message' => 'Erreur lors de la sauvegarde',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json([
            'message' => 'Theme successfully created',
            'theme' => $theme,
        ], Response::HTTP_CREATED);
    }

    #[Route('/{id<\d+>}', name: 'update', methods: ['PUT'])]
    public function update(int $id, Request $request, SerializerInterface $serializer): JsonResponse
    {
        $theme = $this->themeRepository->find($id);

        if (!$theme) {
            return $this->json(['message' => 'Theme not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            return $this->json(['message' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
        }

        if (isset($data['name'])) {
            $name = trim($data['name']);
            if (strlen($name) < 2) {
                return $this->json(['message' => 'Le nom doit contenir au moins 2 caractères'], Response::HTTP_BAD_REQUEST);
            }
            $theme->setName($name);
        }

        if (isset($data['description'])) {
            $description = trim($data['description']);
            if (strlen($description) < 10) {
                return $this->json(['message' => 'La description doit contenir au moins 10 caractères'], Response::HTTP_BAD_REQUEST);
            }
            $theme->setDescription($description);
        }

        if (isset($data['slug'])) {
            $slug = trim($data['slug']);
            if (!empty($slug)) {
                $slug = strtolower(str_replace(' ', '-', $slug));
                $slug = preg_replace('/[^a-z0-9\-]/', '', $slug);
                $theme->setSlug($slug);
            }
        }

        $errors = $this->validator->validate($theme);
        if (count($errors) > 0) {
            return $this->errorFormatter->createValidationErrorResponse($errors);
        }

        try {
            $this->entityManager->flush();
        } catch (\Exception $e) {
            return $this->json([
                'message' => 'Erreur lors de la mise à jour',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $themeJson = $serializer->serialize($theme, 'json', [
            'groups' => ['theme:read'],
        ]);

        return JsonResponse::fromJsonString(json_encode([
            'message' => 'Thème mis à jour avec succès.',
            'theme' => json_decode($themeJson),
        ]), Response::HTTP_OK);
    }

    #[Route('/{id<\d+>}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $theme = $this->themeRepository->find($id);

        if (!$theme) {
            return $this->json(['message' => 'Theme not found'], Response::HTTP_NOT_FOUND);
        }

        if (count($theme->getAccommodations()) > 0) {
            return $this->json([
                'message' => 'Impossible de supprimer ce thème car il possède encore des logements associés.',
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $this->entityManager->remove($theme);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            return $this->json([
                'message' => 'Erreur lors de la suppression',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json([
            'message' => 'Theme successfully deleted',
        ], Response::HTTP_OK);
    }
}
