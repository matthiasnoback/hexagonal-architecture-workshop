<?php
declare(strict_types=1);

namespace App\ExternalEvents;

use App\Json;
use Doctrine\DBAL\Connection;

final class OutboxPublisher implements ExternalEventPublisher
{
    public function __construct(private Connection $connection)
    {
    }

    public function publish(string $eventType, array $eventData): void
    {
        $this->connection->insert(
            'outbox',
            [
                'messageType' => $eventType,
                'messageData' => Json::encode($eventData),
            ]
        );
    }
}
