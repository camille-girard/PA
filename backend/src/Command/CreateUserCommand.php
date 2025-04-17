<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-user',
    description: 'Creates a new user.',
    hidden: false,
    aliases: ['app:add-user']
)]
class CreateUserCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $firstName = $io->ask('First Name', null, function ($input) {
            if (empty($input)) {
                throw new \RuntimeException('First name cannot be empty');
            }

            return $input;
        });

        $lastName = $io->ask('Last Name', null, function ($input) {
            if (empty($input)) {
                throw new \RuntimeException('First name cannot be empty');
            }

            return $input;
        });

        $email = $io->ask('Email', null, function ($input) {
            if (empty($input)) {
                throw new \RuntimeException('First name cannot be empty');
            }

            return $input;
        });

        $passwordQuestion = new Question('Password');
        $passwordQuestion->setHidden(true);
        $passwordQuestion->setValidator(function ($input) {
            if (empty($input)) {
                throw new \RuntimeException('Password cannot be empty.');
            }
            if (strlen($input) < 6) {
                throw new \RuntimeException('Password must be at least 6 characters long.');
            }

            return $input;
        });

        $password = $io->askQuestion($passwordQuestion);

        $user = new User();
        $user->setEmail($email);
        $user->setFirstName($firstName);
        $user->setLastName($lastName);

        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success(sprintf('User %s %s (%s) created successfully!', $firstName, $lastName, $email));

        return Command::SUCCESS;
    }
}
