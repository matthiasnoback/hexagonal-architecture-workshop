<?php

declare(strict_types=1);

namespace App\ExternalEvents;

final class SynchronousExternalEventPublisher implements ExternalEventPublisher
{
    /**
     * @param array<ExternalEventConsumer> $externalEventConsumers
     */
    public function __construct(
        private readonly array $externalEventConsumers,
    ) {
    }

    public function publish(string $eventType, array $eventData): void
    {
        foreach ($this->externalEventConsumers as $eventConsumer) {
            $eventConsumer->whenExternalEventReceived($eventType, $eventData,);
        }
    }
}
