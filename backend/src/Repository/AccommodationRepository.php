<?php

namespace App\Repository;

use App\Entity\Accommodation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Accommodation>
 */
class AccommodationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Accommodation::class);
    }

    public function findByOwnerId(int $ownerId): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.owner = :ownerId')
            ->setParameter('ownerId', $ownerId)
            ->getQuery()
            ->getResult();
    }

    /**
     * @throws \Exception
     */
    public function searchAccommodations(array $criteria): array
    {
        $qb = $this->createQueryBuilder('a')
            ->leftJoin('a.owner', 'o')
            ->leftJoin('a.theme', 't')
            ->leftJoin('a.bookings', 'b');

        if (!empty($criteria['destination'])) {
            $qb->andWhere('(a.name LIKE :destination OR a.address LIKE :destination OR a.description LIKE :destination OR t.name LIKE :destination)')
               ->setParameter('destination', '%'.$criteria['destination'].'%');
        }

        if (!empty($criteria['capacity'])) {
            $qb->andWhere('a.capacity >= :capacity')
               ->setParameter('capacity', $criteria['capacity']);
        }

        if (!empty($criteria['arrivalDate']) && !empty($criteria['departureDate'])) {
            $qb->andWhere('NOT EXISTS (
                SELECT 1 FROM App\Entity\Booking booking 
                WHERE booking.accommodation = a 
                AND booking.status != :cancelled_status
                AND (
                    (booking.startDate <= :arrival AND booking.endDate > :arrival) OR
                    (booking.startDate < :departure AND booking.endDate >= :departure) OR
                    (booking.startDate >= :arrival AND booking.endDate <= :departure)
                )
            )')
            ->setParameter('arrival', new \DateTime($criteria['arrivalDate']))
            ->setParameter('departure', new \DateTime($criteria['departureDate']))
            ->setParameter('cancelled_status', 'cancelled'); // Assumons qu'il y a un statut cancelled
        }

        if (!empty($criteria['minPrice'])) {
            $qb->andWhere('a.price >= :minPrice')
               ->setParameter('minPrice', $criteria['minPrice']);
        }

        if (!empty($criteria['maxPrice'])) {
            $qb->andWhere('a.price <= :maxPrice')
               ->setParameter('maxPrice', $criteria['maxPrice']);
        }

        if (!empty($criteria['themeId'])) {
            $qb->andWhere('a.theme = :themeId')
               ->setParameter('themeId', $criteria['themeId']);
        }

        $orderBy = $criteria['orderBy'] ?? 'name';
        $orderDirection = $criteria['orderDirection'] ?? 'ASC';

        switch ($orderBy) {
            case 'price':
                $qb->orderBy('a.price', $orderDirection);
                break;
            case 'capacity':
                $qb->orderBy('a.capacity', $orderDirection);
                break;
            case 'created':
                $qb->orderBy('a.createdAt', $orderDirection);
                break;
            default:
                $qb->orderBy('a.name', $orderDirection);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @return Accommodation[]
     */
    public function findAvailableAccommodations(\DateTime $startDate, \DateTime $endDate, ?int $capacity = null): array
    {
        $qb = $this->createQueryBuilder('a')
            ->leftJoin('a.bookings', 'b');

        $qb->andWhere('NOT EXISTS (
            SELECT 1 FROM App\Entity\Booking booking 
            WHERE booking.accommodation = a 
            AND booking.status != :cancelled_status
            AND (
                (booking.startDate <= :start AND booking.endDate > :start) OR
                (booking.startDate < :end AND booking.endDate >= :end) OR
                (booking.startDate >= :start AND booking.endDate <= :end)
            )
        )')
        ->setParameter('start', $startDate)
        ->setParameter('end', $endDate)
        ->setParameter('cancelled_status', 'cancelled');

        if ($capacity) {
            $qb->andWhere('a.capacity >= :capacity')
               ->setParameter('capacity', $capacity);
        }

        return $qb->orderBy('a.name', 'ASC')->getQuery()->getResult();
    }

    /**
     * @throws \Exception
     */
    public function countByCriteria(array $criteria): int
    {
        $qb = $this->createQueryBuilder('a')
            ->select('COUNT(a.id)')
            ->leftJoin('a.theme', 't');

        if (!empty($criteria['destination'])) {
            $qb->andWhere('(a.name LIKE :destination OR a.address LIKE :destination OR a.description LIKE :destination OR t.name LIKE :destination)')
               ->setParameter('destination', '%'.$criteria['destination'].'%');
        }

        if (!empty($criteria['capacity'])) {
            $qb->andWhere('a.capacity >= :capacity')
               ->setParameter('capacity', $criteria['capacity']);
        }

        if (!empty($criteria['arrivalDate']) && !empty($criteria['departureDate'])) {
            $qb->andWhere('NOT EXISTS (
                SELECT 1 FROM App\Entity\Booking booking 
                WHERE booking.accommodation = a 
                AND booking.status != :cancelled_status
                AND (
                    (booking.startDate <= :arrival AND booking.endDate > :arrival) OR
                    (booking.startDate < :departure AND booking.endDate >= :departure) OR
                    (booking.startDate >= :arrival AND booking.endDate <= :departure)
                )
            )')
            ->setParameter('arrival', new \DateTime($criteria['arrivalDate']))
            ->setParameter('departure', new \DateTime($criteria['departureDate']))
            ->setParameter('cancelled_status', 'cancelled');
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }
}
