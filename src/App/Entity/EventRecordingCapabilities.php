<?php

declare(strict_types=1);

namespace App\Entity;

trait EventRecordingCapabilities
{
    /**
     * @var array<object>
     */
    private array $events = [];

    /**
     * @return array<object>
     */
    public function releaseEvents(): array
    {
        $events = $this->events;

        $this->events = [];

        return $events;
    }

    private function recordThat(object $event): void
    {
        $this->events[] = $event;
    }
}
