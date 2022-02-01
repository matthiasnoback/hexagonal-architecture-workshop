<?php
declare(strict_types=1);

namespace App;

final class SynchronousExternalEventPublisher implements ExternalEventPublisher
{
    public function __construct(
        private EventDispatcher $eventDispatcher)
    {
    }

    public function publish(string $eventType, array $eventData): void
    {
        $this->eventDispatcher->dispatch(
            new ExternalEventReceived($eventType, $eventData)
        );
    }
}
