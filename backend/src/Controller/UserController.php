<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\CloudflareR2Service;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api')]
#[OA\Tag(name: 'Users')]
final class UserController extends AbstractController
{
    #[Route('/me', name: 'app_user', methods: ['GET'])]
    public function index(UserRepository $userRepository, SerializerInterface $serializerInterface): JsonResponse
    {
        $current_user = $this->getUser();
        if (!$current_user instanceof User) {
            return new JsonResponse(['message' => 'Utilisateur non trouvé'], 404);
        }
        $user = $userRepository->find($current_user->getId());

        $userSerialized = $serializerInterface->serialize($user, 'json', [
            'ignored_attributes' => ['password', 'userIdentifier'],
            'groups' => ['me:read'],
        ]);

        return JsonResponse::fromJsonString($userSerialized);
    }

    #[Route('/me', name: 'app_user_update', methods: ['PUT'])]
    public function update(
        Request $request,
        EntityManagerInterface $em,
        SerializerInterface $serializer,
    ): JsonResponse {
        /** @var User $user */
        $user = $this->getUser();

        $data = json_decode($request->getContent(), true);

        if (isset($data['firstName'])) {
            $user->setFirstName($data['firstName']);
        }

        if (isset($data['lastName'])) {
            $user->setLastName($data['lastName']);
        }

        if (isset($data['email'])) {
            $user->setEmail($data['email']);
        }

        if (isset($data['phone'])) {
            $user->setPhone($data['phone']);
        }

        if (isset($data['address'])) {
            $user->setAddress($data['address']);
        }

        if (isset($data['preferences'])) {
            /** @var Client $user */
            $user->setPreferences($data['preferences']);
        }

        $em->flush();

        $json = $serializer->serialize($user, 'json', [
            'ignored_attributes' => ['password', 'userIdentifier'],
        ]);

        return JsonResponse::fromJsonString($json);
    }

    #[Route('/me', name: 'app_user_delete', methods: ['DELETE'])]
    public function delete(EntityManagerInterface $em): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        $em->remove($user);
        $em->flush();

        return new JsonResponse(['message' => 'Utilisateur supprimé'], 204);
    }

    #[Route('/me/password', name: 'app_user_update_password', methods: ['PUT'])]
    public function updatePassword(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher,
    ): JsonResponse {
        /** @var User $user */
        $user = $this->getUser();
        $data = json_decode($request->getContent(), true);

        if (empty($data['oldPassword']) || empty($data['newPassword'])) {
            return $this->json(['message' => 'Ancien et nouveau mot de passe requis.'], 400);
        }

        if (!$passwordHasher->isPasswordValid($user, $data['oldPassword'])) {
            return $this->json(['message' => 'Mot de passe actuel incorrect.'], 400);
        }

        $user->setPassword($passwordHasher->hashPassword($user, $data['newPassword']));
        $em->flush();

        return $this->json(['message' => 'Mot de passe mis à jour avec succès.']);
    }

    #[Route('/me/avatar', name: 'app_user_upload_avatar', methods: ['POST'])]
    public function uploadAvatar(
        Request $request,
        CloudflareR2Service $r2Service,
        EntityManagerInterface $em,
        SerializerInterface $serializer,
    ): JsonResponse {
        /** @var User $user */
        $user = $this->getUser();

        $file = $request->files->get('file') ?: $request->files->get('avatar');

        if (!$file) {
            return $this->json(['message' => 'Aucun fichier fourni'], 400);
        }

        try {
            // Delete old avatar if exists
            if ($user->getAvatar()) {
                $r2Service->deleteFile($user->getAvatar());
            }

            // Upload new avatar
            $avatarUrl = $r2Service->uploadAvatar($file, $user->getId());

            // Update user
            $user->setAvatar($avatarUrl);
            $em->flush();

            // Return updated user data
            $userSerialized = $serializer->serialize($user, 'json', [
                'ignored_attributes' => ['password', 'userIdentifier'],
                'groups' => ['me:read'],
            ]);

            return JsonResponse::fromJsonString($userSerialized);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['message' => $e->getMessage()], 400);
        } catch (\RuntimeException $e) {
            return $this->json(['message' => 'Erreur lors de l\'upload: '.$e->getMessage()], 500);
        }
    }

    #[Route('/me/avatar', name: 'app_user_delete_avatar', methods: ['DELETE'])]
    public function deleteAvatar(
        CloudflareR2Service $r2Service,
        EntityManagerInterface $em,
        SerializerInterface $serializer,
    ): JsonResponse {
        /** @var User $user */
        $user = $this->getUser();

        if (!$user->getAvatar()) {
            return $this->json(['message' => 'Aucun avatar à supprimer'], 400);
        }

        try {
            // Delete avatar from R2
            $r2Service->deleteFile($user->getAvatar());

            // Update user
            $user->setAvatar(null);
            $em->flush();

            // Return updated user data
            $userSerialized = $serializer->serialize($user, 'json', [
                'ignored_attributes' => ['password', 'userIdentifier'],
                'groups' => ['me:read'],
            ]);

            return JsonResponse::fromJsonString($userSerialized);
        } catch (\RuntimeException $e) {
            return $this->json(['message' => 'Erreur lors de la suppression: '.$e->getMessage()], 500);
        }
    }
}
