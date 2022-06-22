<?php

namespace App\Command;

use App\Repository\UserRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Service\CreateUserFromCommand;

class CreateUserCommand extends Command
{
    protected static $defaultName = 'app:CreateUser';
    protected static $defaultDescription = 'Add new User from email and password';

    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;

    /**
     * @var CreateUserFromCommand
     */
    private CreateUserFromCommand $createUserFromCommand;

    public function __construct(UserRepository $userRepository, CreateUserFromCommand $createUserFromCommand)
    {
        $this->userRepository = $userRepository;
        $this->createUserFromCommand = $createUserFromCommand;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::REQUIRED, 'Имя пользователя')
            ->addArgument('email', InputArgument::REQUIRED, 'Почта пользователя')
            ->addArgument('password', InputArgument::REQUIRED, 'Пароль не менее 6 символов')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $name = $input->getArgument('name');
        $email = $input->getArgument('email');
        $pass = $input->getArgument('password');

        $hasUser = $this->userRepository->findOneBy(['email' => $email]);

        if($hasUser != null){
            $io->error(sprintf('Пользователь с такой почтой %s уже существует', $email));
            return 0;
        }

        $io->note(sprintf('Пользователь с именем %s, почтой %s и паролем %s', $name, $email, $pass));

        $this->createUserFromCommand->createUser($name, $email, $pass);

        $io->success('Пользователь создан');

        return Command::SUCCESS;
    }
}
