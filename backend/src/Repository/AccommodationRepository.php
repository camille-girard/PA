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

    /**
     * @return array<Accommodation>
     */
    public function findByOwnerId(int $ownerId): array
    {
        // Méthode simple sans jointures pour éviter les problèmes
        return $this->createQueryBuilder('a')
            ->andWhere('a.owner = :ownerId')
            ->setParameter('ownerId', $ownerId)
            ->orderBy('a.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param array<string, mixed> $criteria
     *
     * @return array<Accommodation>
     *
     * @throws \Exception
     */
    public function searchAccommodations(array $criteria): array
    {
        $qb = $this->createQueryBuilder('a')
            ->leftJoin('a.owner', 'o')
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
            try {
                $arrivalDate = new \DateTime($criteria['arrivalDate']);
                $departureDate = new \DateTime($criteria['departureDate']);

                $qb->andWhere('NOT EXISTS (
                    SELECT 1 FROM App\Entity\Booking booking 
                    WHERE booking.accommodation = a 
                    AND booking.status IN (:active_statuses)
                    AND (
                        (booking.startDate <= :arrival AND booking.endDate > :arrival) OR
                        (booking.startDate < :departure AND booking.endDate >= :departure) OR
                        (booking.startDate >= :arrival AND booking.endDate <= :departure)
                    )
                )')
                ->setParameter('arrival', $arrivalDate)
                ->setParameter('departure', $departureDate)
                ->setParameter('active_statuses', ['pending', 'accepted']); // Seules les réservations actives bloquent
            } catch (\Exception $e) {
                throw new \Exception('Format de date invalide: '.$e->getMessage());
            }
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
        $qb = $this->createQueryBuilder('a');

        $qb->andWhere('NOT EXISTS (
            SELECT 1 FROM App\Entity\Booking booking 
            WHERE booking.accommodation = a 
            AND booking.status IN (:active_statuses)
            AND (
                (booking.startDate <= :start AND booking.endDate > :start) OR
                (booking.startDate < :end AND booking.endDate >= :end) OR
                (booking.startDate >= :start AND booking.endDate <= :end)
            )
        )')
        ->setParameter('start', $startDate)
        ->setParameter('end', $endDate)
        ->setParameter('active_statuses', ['pending', 'accepted']);

        if ($capacity) {
            $qb->andWhere('a.capacity >= :capacity')
               ->setParameter('capacity', $capacity);
        }

        return $qb->orderBy('a.name', 'ASC')->getQuery()->getResult();
    }

    /**
     * @param array<string, mixed> $criteria
     *
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
            try {
                $arrivalDate = new \DateTime($criteria['arrivalDate']);
                $departureDate = new \DateTime($criteria['departureDate']);

                $qb->andWhere('NOT EXISTS (
                    SELECT 1 FROM App\Entity\Booking booking 
                    WHERE booking.accommodation = a 
                    AND booking.status IN (:active_statuses)
                    AND (
                        (booking.startDate <= :arrival AND booking.endDate > :arrival) OR
                        (booking.startDate < :departure AND booking.endDate >= :departure) OR
                        (booking.startDate >= :arrival AND booking.endDate <= :departure)
                    )
                )')
                ->setParameter('arrival', $arrivalDate)
                ->setParameter('departure', $departureDate)
                ->setParameter('active_statuses', ['pending', 'accepted']);
            } catch (\Exception $e) {
                throw new \Exception('Format de date invalide: '.$e->getMessage());
            }
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @param array<string> $preferences
     *
     * @return Accommodation[]
     */
    public function findByPreferences(array $preferences, int $limit = 6): array
    {
        $qb = $this->createQueryBuilder('a');

        if (empty($preferences)) {
            return $qb->orderBy('a.createdAt', 'DESC')
                      ->setMaxResults($limit)
                      ->getQuery()
                      ->getResult();
        }

        $orConditions = [];
        $paramIndex = 0;

        foreach ($preferences as $preference) {
            $preference = trim(strtolower($preference));
            if (!empty($preference)) {
                $paramName = 'pref'.$paramIndex;
                $orConditions[] = "(
                    LOWER(a.practicalInformations) LIKE :$paramName OR 
                    LOWER(a.description) LIKE :$paramName OR 
                    LOWER(a.name) LIKE :$paramName
                )";
                $qb->setParameter($paramName, '%'.$preference.'%');
                ++$paramIndex;
            }
        }

        if (!empty($orConditions)) {
            $qb->where(implode(' OR ', $orConditions));
        }

        $results = $qb->orderBy('a.createdAt', 'DESC')
                      ->setMaxResults($limit)
                      ->getQuery()
                      ->getResult();

        if (empty($results)) {
            return $this->createQueryBuilder('a')
                        ->orderBy('a.createdAt', 'DESC')
                        ->setMaxResults($limit)
                        ->getQuery()
                        ->getResult();
        }

        return $results;
    }
}
