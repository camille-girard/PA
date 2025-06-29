<?php

namespace App\Repository;

use App\Entity\Booking;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Booking>
 */
class BookingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Booking::class);
    }

    /**
     * Récupère toutes les réservations liées aux hébergements appartenant à un owner donné.
     */
    public function findBookingsByOwner(User $owner): array
    {
        return $this->createQueryBuilder('b')
            ->join('b.accommodation', 'a')
            ->andWhere('a.owner = :owner')
            ->setParameter('owner', $owner)
            ->orderBy('b.startDate', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
