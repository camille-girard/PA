<?php

namespace App\Service;

use App\Entity\Accommodation;
use App\Entity\Owner;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;

class RatingService
{
    public function __construct(
        private CommentRepository $commentRepository,
        private EntityManagerInterface $entityManager
    ) {
    }

    /**
     * Calcule et met à jour la note moyenne d'un accommodation
     */
    public function updateAccommodationRating(Accommodation $accommodation): void
    {
        $comments = $this->commentRepository->findBy(['accommodation' => $accommodation]);
        
        if (empty($comments)) {
            $accommodation->setRating(0);
            return;
        }

        $totalRating = 0;
        $count = count($comments);

        foreach ($comments as $comment) {
            $totalRating += $comment->getRating();
        }

        $averageRating = round($totalRating / $count, 1);
        $accommodation->setRating($averageRating);
    }

    /**
     * Calcule et met à jour la note moyenne d'un owner
     * basée sur toutes les notes reçues via ses accommodations
     */
    public function updateOwnerRating(Owner $owner): void
    {
        $accommodations = $owner->getAccommodations();
        $allComments = [];

        // Récupérer tous les commentaires pour tous les accommodations de l'owner
        foreach ($accommodations as $accommodation) {
            $comments = $this->commentRepository->findBy(['accommodation' => $accommodation]);
            $allComments = array_merge($allComments, $comments);
        }

        if (empty($allComments)) {
            $owner->setRating(0);
            return;
        }

        $totalRating = 0;
        $count = count($allComments);

        foreach ($allComments as $comment) {
            $totalRating += $comment->getRating();
        }

        $averageRating = round($totalRating / $count, 1);
        $owner->setRating($averageRating);
    }

    /**
     * Met à jour les ratings après l'ajout d'une nouvelle notation
     */
    public function updateRatingsAfterComment(Accommodation $accommodation): void
    {
        // Mettre à jour le rating de l'accommodation
        $this->updateAccommodationRating($accommodation);
        
        // Mettre à jour le rating de l'owner
        $owner = $accommodation->getOwner();
        if ($owner) {
            $this->updateOwnerRating($owner);
        }

        // Sauvegarder les changements
        $this->entityManager->flush();
    }
}