<?php

declare(strict_types=1);

namespace App\Cli;

use App\ApplicationInterface;
use App\Entity\UserType;
use App\SignUp;
use Assert\Assert;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

final class SignUpCommand extends Command
{
    public function __construct(
        private readonly ApplicationInterface $application
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('sign-up')
            ->setDescription('Sign up as a new user')
            ->addArgument('name', InputArgument::REQUIRED, 'Name of the user')
            ->addArgument('emailAddress', InputArgument::REQUIRED, 'Email address of the user')
            ->addArgument('userType', InputArgument::REQUIRED, 'Type of user');
    }

    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        /** @var QuestionHelper $questionHelper */
        $questionHelper = $this->getHelper('question');

        if ($input->getArgument('name') === null) {
            $input->setArgument('name', $questionHelper->ask($input, $output, new Question('Name')));
        }
        if ($input->getArgument('emailAddress') === null) {
            $input->setArgument('emailAddress', $questionHelper->ask($input, $output, new Question('Email address')));
        }
        if ($input->getArgument('userType') === null) {
            $input->setArgument(
                'userType',
                $questionHelper->ask($input, $output, new ChoiceQuestion('User type', UserType::namesAndLabels()))
            );
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getArgument('name');
        Assert::that($name)->string();
        $emailAddress = $input->getArgument('emailAddress');
        Assert::that($emailAddress)->string();
        $userType = $input->getArgument('userType');
        Assert::that($userType)->string();

        $this->application->signUp(new SignUp($name, $emailAddress, $userType));

        $output->writeln('<info>User was signed up successfully</info>');

        return 0;
    }
}
