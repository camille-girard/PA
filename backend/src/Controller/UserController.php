<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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
        $user = $userRepository->findOneBy(['email' => $current_user->getUserIdentifier()]);

        $userSerialized = $serializerInterface->serialize($user, 'json', [
            'ignored_attributes' => ['password', 'userIdentifier'],
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
}
