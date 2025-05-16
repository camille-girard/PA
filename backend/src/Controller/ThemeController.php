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
    public function index(): JsonResponse
    {
        $themes = $this->themeRepository->findAll();

        return $this->json([
            'themes' => $themes,
        ], Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $theme = $this->themeRepository->find($id);

        if (!$theme) {
            return $this->json(['message' => 'Theme not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json([
            'theme' => $theme,
        ], Response::HTTP_OK);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (null === $data) {
            return $this->json(['message' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
        }

        $requiredFields = ['name', 'description', 'image'];
        $missingFields = [];

        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                $missingFields[] = $field;
            }
        }

        if (!empty($missingFields)) {
            return $this->json([
                'message' => 'Missing required fields',
                'missingFields' => $missingFields,
            ], Response::HTTP_BAD_REQUEST);
        }

        $theme = new Theme();
        $theme->setName($data['name']);
        $theme->setDescription($data['description']);
        $theme->setImage($data['image']);

        $errors = $this->validator->validate($theme);
        if (count($errors) > 0) {
            return $this->errorFormatter->createValidationErrorResponse($errors);
        }

        $this->entityManager->persist($theme);
        $this->entityManager->flush();

        return $this->json([
            'message' => 'Theme successfully created',
            'theme' => $theme,
        ], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
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
            $theme->setName($data['name']);
        }

        if (isset($data['description'])) {
            $theme->setDescription($data['description']);
        }

        if (isset($data['image'])) {
            $theme->setImage($data['image']);
        }

        $errors = $this->validator->validate($theme);
        if (count($errors) > 0) {
            return $this->errorFormatter->createValidationErrorResponse($errors);
        }

        $this->entityManager->flush();

        return $this->json([
            'message' => 'Theme successfully updated',
            'theme' => $theme,
        ], Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $theme = $this->themeRepository->find($id);

        if (!$theme) {
            return $this->json(['message' => 'Theme not found'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($theme);
        $this->entityManager->flush();

        return $this->json([
            'message' => 'Theme successfully deleted',
        ], Response::HTTP_OK);
    }
}
