<?php

namespace App\Command;

use App\Repository\RefreshTokenRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:clean-expired-tokens',
    description: 'Nettoie les tokens de refresh expirés de la base de données',
)]
class CleanExpiredTokensCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private RefreshTokenRepository $refreshTokenRepository,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Nettoyage des tokens expirés');

        // Récupérer tous les tokens expirés
        $expiredTokens = $this->refreshTokenRepository->findExpiredTokens();

        if (empty($expiredTokens)) {
            $io->success('Aucun token expiré trouvé.');

            return Command::SUCCESS;
        }

        $count = count($expiredTokens);
        $io->info(sprintf('Suppression de %d token(s) expiré(s)...', $count));

        // Supprimer les tokens expirés
        foreach ($expiredTokens as $token) {
            $this->entityManager->remove($token);
        }

        $this->entityManager->flush();

        $io->success(sprintf('%d token(s) expiré(s) supprimé(s) avec succès.', $count));

        return Command::SUCCESS;
    }
}
