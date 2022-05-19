<?php

declare(strict_types=1);

namespace App\ExternalEvents;

interface ExternalEventPublisher
{
    public function publish(string $eventType, array $eventData): void;
}
