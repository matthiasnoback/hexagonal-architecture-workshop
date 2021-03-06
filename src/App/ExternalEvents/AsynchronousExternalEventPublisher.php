<?php

declare(strict_types=1);

namespace App\ExternalEvents;

use TailEventStream\Producer;

final class AsynchronousExternalEventPublisher implements ExternalEventPublisher
{
    public function __construct(
        private readonly Producer $producer
    ) {
    }

    public function publish(string $eventType, array $eventData): void
    {
        $this->producer->produce($eventType, $eventData);
    }
}
