<?php
declare(strict_types=1);

namespace App\ExternalEvents;

final class ExternalEventReceived
{
    public function __construct(private string $eventType, private array $eventData)
    {

    }

    public function eventType(): string
    {
        return $this->eventType;
    }

    public function eventData(): array
    {
        return $this->eventData;
    }
}
