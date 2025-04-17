<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api')]
final class UserController extends AbstractController
{
    #[Route('/me', name: 'app_user')]
    public function index(UserRepository $userRepository, SerializerInterface $serializerInterface): JsonResponse
    {
        $current_user = $this->getUser();
        $user = $userRepository->findBy(['email' => $current_user->getUserIdentifier()])[0];
        $userSerialized = $serializerInterface->serialize($user, 'json', ['ignored_attributes' => ['password', 'userIdentifier']]);

        return JsonResponse::fromJsonString($userSerialized);
    }
}
