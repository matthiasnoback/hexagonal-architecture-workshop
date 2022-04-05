<?php
declare(strict_types=1);

namespace App\Cli;

use App\ExternalEventPublisher;
use App\Mapping;
use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class OutboxRelayCommand extends Command
{
    private bool $keepRunning = true;

    public function __construct(
        private Connection $connection,
        private ExternalEventPublisher $externalEventPublisher,
    ) {
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

            usleep(1000);
            pcntl_signal_dispatch();
        }

        return 0;
    }

    private function publishNextMessage(): void
    {
        $record = $this->connection->fetchAssociative(
            'SELECT * FROM outbox WHERE wasPublished = 0 ORDER BY messageId LIMIT 1'
        );
        var_dump($record);
        if ($record === false) {
            return;
        }

        $eventData = json_decode(Mapping::getString($record, 'messageData'), true);
        $eventType = Mapping::getString($record, 'messageType');

        $this->externalEventPublisher->publish(
            $eventType,
            $eventData,
        );

        $this->connection->update('outbox',
            [
                'wasPublished' => 1
            ],
            [
                'messageId' => Mapping::getInt($record, 'messageId')
            ]
        );
    }
}
