<?php

namespace App\Controller;

use App\Repository\AccommodationRepository;
use App\Repository\BookingRepository;
use App\Repository\ClientRepository;
use App\Repository\OwnerRepository;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Doctrine\ORM\EntityManagerInterface;

#[OA\Tag(name: 'Dashboard')]
#[IsGranted('ROLE_ADMIN')]
#[Route('/api/dashboard', name: 'api_dashboard_')]
final class DashboardController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private ClientRepository $clientRepository,
        private OwnerRepository $ownerRepository,
        private BookingRepository $bookingRepository,
        private AccommodationRepository $accommodationRepository,
    ) {
    }

    #[Route('/stats', name: 'stats', methods: ['GET'])]
    public function stats(): JsonResponse
    {
        $conn = $this->em->getConnection();
        // Count entities
        $clients = $this->clientRepository->count(['isDeleted' => false]);
        $owners = $this->ownerRepository->count(['isDeleted' => false]);
        $bookings = $this->bookingRepository->count([]);
        $accommodations = $this->accommodationRepository->count([]);

        // Count bookings by status
        $statuses = ['accepted', 'pending', 'refused'];
        $bookingStatusCounts = [];
        foreach ($statuses as $status) {
            $bookingStatusCounts[$status] = $this->bookingRepository->count(['status' => $status]);
        }

        // Bookings per month for the last 12 months
        $conn = $this->em->getConnection();
        $sql = "
            SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, COUNT(*) AS count
            FROM booking
            WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
            GROUP BY month
            ORDER BY month ASC
        ";
        $monthlyBookings = $conn->executeQuery($sql)->fetchAllAssociative();

        return $this->json([
            'counts' => [
                'clients' => $clients,
                'owners' => $owners,
                'bookings' => $bookings,
                'accommodations' => $accommodations,
            ],
            'bookingStatusCounts' => $bookingStatusCounts,
            'monthlyBookings' => $monthlyBookings,
        ]);
    }
}
