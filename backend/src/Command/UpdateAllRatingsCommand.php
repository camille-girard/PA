<?php

namespace App\Command;

use App\Repository\AccommodationRepository;
use App\Service\RatingService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:update-all-ratings',
    description: 'Recalcule toutes les notes (accommodations et owners) basées sur les commentaires'
)]
class UpdateAllRatingsCommand extends Command
{
    public function __construct(
        private AccommodationRepository $accommodationRepository,
        private RatingService $ratingService,
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Mise à jour de toutes les notes');

        $accommodations = $this->accommodationRepository->findAll();
        
        if (empty($accommodations)) {
            $io->warning('Aucun hébergement trouvé dans la base de données.');
            return Command::SUCCESS;
        }

        $io->progressStart(count($accommodations));

        $updatedAccommodations = 0;
        $updatedOwners = [];

        foreach ($accommodations as $accommodation) {
            $this->ratingService->updateAccommodationRating($accommodation);
            $updatedAccommodations++;

            $owner = $accommodation->getOwner();
            if ($owner && !in_array($owner->getId(), $updatedOwners)) {
                $this->ratingService->updateOwnerRating($owner);
                $updatedOwners[] = $owner->getId();
            }

            $io->progressAdvance();
        }

        $this->entityManager->flush();

        $io->progressFinish();

        $io->success(sprintf(
            'Notes mises à jour avec succès pour %d hébergement(s) et %d propriétaire(s).',
            $updatedAccommodations,
            count($updatedOwners)
        ));

        return Command::SUCCESS;
    }
}