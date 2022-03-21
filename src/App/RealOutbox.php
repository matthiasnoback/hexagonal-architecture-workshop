<?php
declare(strict_types=1);

namespace App;

use Doctrine\DBAL\Connection;

final class RealOutbox implements Outbox
{
    public function __construct(private Connection $connection)
    {
    }

    public function send(string $messageType, array $messageData): void
    {
        $this->connection->insert('outbox', [
            'messageType' => $messageType,
            'messageData' => json_encode($messageData)
        ]);
    }
}
