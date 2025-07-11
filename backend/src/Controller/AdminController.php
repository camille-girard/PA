<?php

namespace App\Controller;

use App\Entity\Admin;
use App\Repository\AdminRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/admins', name: 'api_admins_')]
final class AdminController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private AdminRepository $adminRepository,
        private SerializerInterface $serializer
    ) {
    }

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $admins = $this->adminRepository->findBy(['isDeleted' => false]);

        $json = $this->serializer->serialize($admins, 'json', ['groups' => 'admin:read']);

        return JsonResponse::fromJsonString($json, Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $admin = $this->adminRepository->find($id);

        if (!$admin || $admin->isDeleted()) {
            return $this->json(['message' => 'Admin non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $json = $this->serializer->serialize($admin, 'json', ['groups' => 'admin:read']);

        return JsonResponse::fromJsonString($json, Response::HTTP_OK);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (null === $data) {
            return $this->json(['message' => 'JSON invalide'], Response::HTTP_BAD_REQUEST);
        }

        // Champs requis
        $requiredFields = ['email', 'password', 'firstName', 'lastName'];
        $missingFields = [];

        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                $missingFields[] = $field;
            }
        }

        if (!empty($missingFields)) {
            return $this->json([
                'message' => 'Champs obligatoires manquants',
                'missingFields' => $missingFields,
            ], Response::HTTP_BAD_REQUEST);
        }

        // Créer un nouvel admin
        $admin = new Admin();
        $admin->setEmail($data['email']);
        $admin->setPassword(password_hash($data['password'], PASSWORD_BCRYPT)); // ou ton encodeur Symfony si tu veux
        $admin->setFirstName($data['firstName']);
        $admin->setLastName($data['lastName']);
        $admin->setIsVerified($data['isVerified'] ?? false);
        $admin->setRoles(['ROLE_ADMIN']);

        if (isset($data['phone'])) {
            $admin->setPhone($data['phone']);
        }

        if (isset($data['address'])) {
            $admin->setAddress($data['address']);
        }

        if (isset($data['avatar'])) {
            $admin->setAvatar($data['avatar']);
        }

        $admin->setCreatedAt(new \DateTimeImmutable());

        // Valider l'entité
        $errors = $validator->validate($admin);
        if (count($errors) > 0) {
            return $this->json([
                'message' => 'Erreur de validation',
                'errors' => (string) $errors
            ], Response::HTTP_BAD_REQUEST);
        }

        $this->entityManager->persist($admin);
        $this->entityManager->flush();

        // Sérialiser la réponse
        $json = $this->serializer->serialize($admin, 'json', ['groups' => 'admin:read']);

        return JsonResponse::fromJsonString($json, Response::HTTP_CREATED);
    }


    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(Request $request, int $id): JsonResponse
    {
        $admin = $this->adminRepository->find($id);

        if (!$admin || $admin->isDeleted()) {
            return $this->json(['message' => 'Admin non trouvé ou déjà supprimé'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            return $this->json(['message' => 'JSON invalide'], Response::HTTP_BAD_REQUEST);
        }

        if (isset($data['firstName'])) {
            $admin->setFirstName($data['firstName']);
        }
        if (isset($data['lastName'])) {
            $admin->setLastName($data['lastName']);
        }
        if (isset($data['email'])) {
            $admin->setEmail($data['email']);
        }
        if (isset($data['phone'])) {
            $admin->setPhone($data['phone']);
        }
        if (isset($data['address'])) {
            $admin->setAddress($data['address']);
        }
        if (isset($data['avatar'])) {
            $admin->setAvatar($data['avatar']);
        }
        if (isset($data['isVerified'])) {
            $admin->setIsVerified($data['isVerified']);
        }

        $this->entityManager->flush();

        return $this->json(['message' => 'Admin mis à jour avec succès'], Response::HTTP_OK);
    }


    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id, #[CurrentUser] ?Admin $currentUser): JsonResponse
    {
        if (!$currentUser) {
            return $this->json(['message' => 'Non authentifié'], Response::HTTP_UNAUTHORIZED);
        }

        if ($currentUser->getId() === $id) {
            return $this->json(['message' => 'Impossible de se supprimer soi-même'], Response::HTTP_FORBIDDEN);
        }

        $admin = $this->adminRepository->find($id);

        if (!$admin || $admin->isDeleted()) {
            return $this->json(['message' => 'Admin non trouvé ou déjà supprimé'], Response::HTTP_NOT_FOUND);
        }

        $admin->setIsDeleted(true);
        $this->entityManager->flush();

        return $this->json(['message' => 'Admin archivé avec succès'], Response::HTTP_OK);
    }
}
