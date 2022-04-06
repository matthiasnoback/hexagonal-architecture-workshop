<?php
declare(strict_types=1);

namespace App;

use Shared\PublishedExternalEvent;

interface ExternalEventPublisher
{
    /**
     * @deprecated Use publishEvent() instead
     */
    public function publish(string $eventType, array $eventData): void;

    public function publishEvent(PublishedExternalEvent $event): void;
}
