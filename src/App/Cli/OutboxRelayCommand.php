<?php
declare(strict_types=1);

namespace App\Cli;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class OutboxRelayCommand extends Command
{
    private bool $keepRunning = true;

    public function __construct()
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('outbox:relay');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        pcntl_signal(SIGTERM, function () {
            $this->keepRunning = false;
        });

        while ($this->keepRunning) {
            $this->publishNextMessage();

            usleep (1000);
            pcntl_signal_dispatch();
        }

        return 0;
    }

    private function publishNextMessage(): void
    {
        // TODO implement
    }
}
