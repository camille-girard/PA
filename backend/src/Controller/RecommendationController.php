<?php

namespace App\Controller;

use App\Entity\Client;
use App\Repository\AccommodationRepository;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api')]
#[OA\Tag(name: 'Recommendations')]
final class RecommendationController extends AbstractController
{
    #[Route('/recommendations', name: 'app_recommendations', methods: ['GET'])]
    public function getPersonalizedRecommendations(
        AccommodationRepository $accommodationRepository,
        SerializerInterface $serializer,
    ): JsonResponse {
        /** @var Client|null $user */
        $user = $this->getUser();

        $userPreferences = [];

        if ($user instanceof Client && !empty($user->getPreferences())) {
            $userPreferences = $user->getPreferences();
        }

        $accommodations = $accommodationRepository->findByPreferences($userPreferences, 6);
        $json = $serializer->serialize($accommodations, 'json', [
            'groups' => ['accommodation:read'],
            'ignored_attributes' => ['owner', 'bookings', 'comments'],
        ]);

        return JsonResponse::fromJsonString($json);
    }
}
