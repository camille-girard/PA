<?php

namespace App\Repository;

use App\Entity\Client;
use App\Entity\Conversation;
use App\Entity\Owner;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Conversation>
 */
class ConversationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Conversation::class);
    }

    /**
     * Find conversations for a user (client or owner).
     *
     * @return Conversation[]
     */
    public function findByUser(User $user): array
    {
        $qb = $this->createQueryBuilder('c');

        if (in_array('ROLE_CLIENT', $user->getRoles())) {
            $qb->andWhere('c.client = :user')
                ->setParameter('user', $user);
        } elseif (in_array('ROLE_OWNER', $user->getRoles())) {
            $qb->andWhere('c.owner = :user')
                ->setParameter('user', $user);
        }

        return $qb->orderBy('c.updatedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find a conversation between a client and an owner.
     */
    public function findOneByClientAndOwner(Client $client, Owner $owner): ?Conversation
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.client = :client')
            ->andWhere('c.owner = :owner')
            ->setParameter('client', $client)
            ->setParameter('owner', $owner)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
