<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\UuidV4;

#[AsCommand(
    name: 'app:create-user',
    description: 'Create new user.',
)]
class CreateUserCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface      $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher,
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Creates users and stores them in the database')
            ->setDefinition(
                new InputDefinition(
                    [
                        new InputOption(
                            'admin',
                            'a',
                            null,
                            'Creates user with admin role.'),
                    ]
                )
            )
            ->addArgument('email', InputArgument::REQUIRED, 'Email.')
            ->addArgument('username', InputArgument::REQUIRED, 'Username.')
            ->addArgument('password', InputArgument::REQUIRED, 'Plain password.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        /** @var string $email */
        $email = $input->getArgument('email');

        /** @var string $username */
        $username = $input->getArgument('username');

        /** @var string $plaintextPassword */
        $plaintextPassword = $input->getArgument('password');

        /** @var bool $admin */
        $admin = $input->getOption('admin');

        $user = new User(
            UuidV4::v4(),
            $email,
            $username,
            $admin ? [User::R_ADMIN] : []
        );

        $user->updatePassword(
            $this->passwordHasher->hashPassword(
                $user,
                $plaintextPassword
            )
        );

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success(sprintf('User was successfully created: %s', $user->getUserIdentifier()));

        return Command::SUCCESS;
    }
}
