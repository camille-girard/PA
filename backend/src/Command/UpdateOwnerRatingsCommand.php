<?php

namespace App\Command;

use App\Repository\OwnerRepository;
use App\Service\RatingService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:update-owner-ratings',
    description: 'Recalcule les notes de tous les owners basées sur leurs commentaires'
)]
class UpdateOwnerRatingsCommand extends Command
{
    public function __construct(
        private OwnerRepository $ownerRepository,
        private RatingService $ratingService,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Mise à jour des notes des propriétaires');

        $owners = $this->ownerRepository->findAll();

        if (empty($owners)) {
            $io->warning('Aucun propriétaire trouvé dans la base de données.');

            return Command::SUCCESS;
        }

        $io->progressStart(count($owners));

        $updatedCount = 0;
        foreach ($owners as $owner) {
            $this->ratingService->updateOwnerRating($owner);
            ++$updatedCount;
            $io->progressAdvance();
        }

        $io->progressFinish();

        $io->success(sprintf(
            'Notes mises à jour avec succès pour %d propriétaire(s).',
            $updatedCount
        ));

        return Command::SUCCESS;
    }
}
