<?php
declare(strict_types=1);

namespace App;

final class ScheduleMeetup
{
    public function __construct(
        private readonly string $organizerId,
        private readonly string $meetupName,
        private readonly string $meetupDescription,
        private readonly string $scheduledForDate,
        private readonly string $scheduledForTime,
    )
    {
    }

    public function organizerId(): string
    {
        return $this->organizerId;
    }

    public function meetupName(): string
    {
        return $this->meetupName;
    }

    public function meetupDescription(): string
    {
        return $this->meetupDescription;
    }

    public function scheduledForDate(): string
    {
        return $this->scheduledForDate;
    }

    public function scheduledForTime(): string
    {
        return $this->scheduledForTime;
    }
}
