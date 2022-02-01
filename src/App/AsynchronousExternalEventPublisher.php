<?php
declare(strict_types=1);

namespace App;

use TailEventStream\Producer;

final class AsynchronousExternalEventPublisher implements ExternalEventPublisher
{
    public function __construct(
        private Producer $producer)
    {
    }

    public function publish(string $eventType, array $eventData): void
    {
        $this->producer->produce(
            $eventType, $eventData
        );
    }
}
