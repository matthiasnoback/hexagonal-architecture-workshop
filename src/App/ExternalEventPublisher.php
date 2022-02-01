<?php
declare(strict_types=1);

namespace App;

interface ExternalEventPublisher
{
    public function publish(string $eventType, array $eventData): void;
}
