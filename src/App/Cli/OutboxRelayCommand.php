<?php
declare(strict_types=1);

namespace App\Cli;

use App\Json;
use App\Mapping;
use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TailEventStream\Producer;

final class OutboxRelayCommand extends Command
{
    private bool $keepRunning = true;

    public function __construct(
        private readonly Connection $connection,
        private readonly Producer $producer,
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
        if ($record === false) {
            return;
        }

        $this->connection->transactional(
            function () use ($record) {
                $this->connection->update(
                    'outbox',
                    [
                        'wasPublished' => 1,
                    ],
                    [
                        'messageId' => Mapping::getInt($record, 'messageId'),
                    ]
                );

                $this->producer->produce(
                    Mapping::getString($record, 'messageType'),
                    Json::decode(Mapping::getString($record, 'messageData')),
                );
            }
        );
    }
}
