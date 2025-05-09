<?php

namespace App\EventListener;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserPasswordHashListener
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * @param LifecycleEventArgs<EntityManagerInterface> $args
     */
    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof User) {
            return;
        }

        if (strlen($entity->getPassword()) < 60) { // Check if the password is not already hashed
            $hashedPassword = $this->passwordHasher->hashPassword($entity, $entity->getPassword());
            $entity->setPassword($hashedPassword);
        }
    }
}
