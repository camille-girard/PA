<?php

namespace App\Controller;

use App\Repository\AccommodationRepository;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/search', name: 'api_search_')]
#[OA\Tag(name: 'Search')]
final class SearchController extends AbstractController
{
    public function __construct(
        private readonly AccommodationRepository $accommodationRepository,
    ) {
    }

    #[Route('', name: 'accommodations', methods: ['POST'])]
    #[OA\Post(
        path: '/api/search',
        summary: 'Rechercher des hébergements',
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    'destination' => new OA\Property(property: 'destination', type: 'string', example: 'Paris'),
                    'arrivalDate' => new OA\Property(property: 'arrivalDate', type: 'string', format: 'date', example: '2024-07-15'),
                    'departureDate' => new OA\Property(property: 'departureDate', type: 'string', format: 'date', example: '2024-07-20'),
                    'amountTravelers' => new OA\Property(property: 'amountTravelers', type: 'integer', example: 2),
                    'minPrice' => new OA\Property(property: 'minPrice', type: 'number', example: 50.0),
                    'maxPrice' => new OA\Property(property: 'maxPrice', type: 'number', example: 200.0),
                    'themeId' => new OA\Property(property: 'themeId', type: 'integer', example: 1),
                    'orderBy' => new OA\Property(property: 'orderBy', type: 'string', enum: ['name', 'price', 'capacity', 'created'], example: 'price'),
                    'orderDirection' => new OA\Property(property: 'orderDirection', type: 'string', enum: ['ASC', 'DESC'], example: 'ASC'),
                    'useGeolocation' => new OA\Property(property: 'useGeolocation', type: 'boolean', example: false),
                    'radius' => new OA\Property(property: 'radius', type: 'number', example: 50.0),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Résultats de recherche',
                content: new OA\JsonContent(
                    properties: [
                        'accommodations' => new OA\Property(property: 'accommodations', type: 'array', items: new OA\Items(type: 'object')),
                        'total' => new OA\Property(property: 'total', type: 'integer'),
                        'filters' => new OA\Property(property: 'filters', type: 'object'),
                    ]
                )
            ),
        ]
    )]
    public function searchAccommodations(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            return $this->json(['message' => 'JSON invalide'], Response::HTTP_BAD_REQUEST);
        }

        if (isset($data['arrivalDate']) && isset($data['departureDate'])) {
            $arrivalDate = new \DateTime($data['arrivalDate']);
            $departureDate = new \DateTime($data['departureDate']);

            if ($arrivalDate >= $departureDate) {
                return $this->json([
                    'message' => 'La date d\'arrivée doit être antérieure à la date de départ',
                ], Response::HTTP_BAD_REQUEST);
            }

            if ($arrivalDate < new \DateTime('today')) {
                return $this->json([
                    'message' => 'La date d\'arrivée ne peut pas être dans le passé',
                ], Response::HTTP_BAD_REQUEST);
            }
        }

        $criteria = [];

        if (isset($data['destination']) && !empty(trim($data['destination']))) {
            $criteria['destination'] = trim($data['destination']);
        }

        if (isset($data['arrivalDate'])) {
            $criteria['arrivalDate'] = $data['arrivalDate'];
        }

        if (isset($data['departureDate'])) {
            $criteria['departureDate'] = $data['departureDate'];
        }

        if (isset($data['amountTravelers']) && $data['amountTravelers'] > 0) {
            $criteria['capacity'] = (int) $data['amountTravelers'];
        }

        if (isset($data['minPrice']) && $data['minPrice'] > 0) {
            $criteria['minPrice'] = (float) $data['minPrice'];
        }

        if (isset($data['maxPrice']) && $data['maxPrice'] > 0) {
            $criteria['maxPrice'] = (float) $data['maxPrice'];
        }

        if (isset($data['themeId']) && $data['themeId'] > 0) {
            $criteria['themeId'] = (int) $data['themeId'];
        }

        if (isset($data['orderBy'])) {
            $criteria['orderBy'] = $data['orderBy'];
        }

        if (isset($data['orderDirection'])) {
            $criteria['orderDirection'] = $data['orderDirection'];
        }

        try {
            $accommodations = $this->accommodationRepository->searchAccommodations($criteria);

            $results = [];
            foreach ($accommodations as $accommodation) {
                $images = [];
                foreach ($accommodation->getImages() as $image) {
                    $images[] = [
                        'url' => $image->getUrl(),
                        'alt' => 'Image du logement',
                        'main' => $image->isMain(),
                    ];
                }

                $results[] = [
                    'id' => $accommodation->getId(),
                    'name' => $accommodation->getName(),
                    'description' => $accommodation->getDescription(),
                    'address' => $accommodation->getAddress(),
                    'capacity' => $accommodation->getCapacity(),
                    'price' => $accommodation->getPrice(),
                    'advantage' => $accommodation->getAdvantage(),
                    'images' => $images,
                    'theme' => $accommodation->getTheme() ? [
                        'id' => $accommodation->getTheme()->getId(),
                        'name' => $accommodation->getTheme()->getName(),
                        'slug' => $accommodation->getTheme()->getSlug(),
                    ] : null,
                    'host' => [
                        'id' => $accommodation->getOwner()->getId(),
                        'lastName' => $accommodation->getOwner()->getLastName(),
                        'firstName' => $accommodation->getOwner()->getFirstName(),
                    ],
                    'coordinates' => [
                        'latitude' => $accommodation->getLatitude(),
                        'longitude' => $accommodation->getLongitude(),
                    ],
                ];
            }

            $total = $this->accommodationRepository->countByCriteria($criteria);

            return $this->json([
                'accommodations' => $results,
                'total' => $total,
                'count' => count($results),
                'filters' => $criteria,
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'message' => 'Erreur lors de la recherche',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
