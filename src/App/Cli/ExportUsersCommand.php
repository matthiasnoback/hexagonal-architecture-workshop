<?php
declare(strict_types=1);

namespace App\Cli;

use App\ExternalEventPublisher;
use App\Mapping;
use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ExportUsersCommand extends Command
{
    public function __construct(
        private Connection $connection,
        private ExternalEventPublisher $externalEventPublisher,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('users:export');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $records = $this->connection->fetchAllAssociative('SELECT * FROM users');

        foreach ($records as $record) {
            $this->externalEventPublisher->publish(
                'user.signed_up',
                [
                    'id' => Mapping::getString($record, 'userId'),
                    'name' => Mapping::getString($record, 'name'),
                    'type' => Mapping::getString($record, 'userType'),
                ]
            );
        }

        return 0;
    }
}
