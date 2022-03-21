<?php
declare(strict_types=1);

namespace App;

final class OutboxForTesting implements Outbox
{
    public function __construct(private ExternalEventPublisher $externalEventPublisher)
    {
    }

    public function send(string $messageType, array $messageData): void
    {
        $this->externalEventPublisher->publish($messageType, $messageData);
    }
}
