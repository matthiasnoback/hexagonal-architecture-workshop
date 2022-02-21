<?php
declare(strict_types=1);

namespace App;

use MeetupOrganizing\Entity\ScheduledDate;

final class ScheduleMeetupCommand
{
    public function __construct(
        private string $organizerId,
        private string $name,
        private string $description,
        private string $scheduledForDate,
        private string $scheduledForTime,
    )
    {
    }

    public function organizerId(): string
    {
        return $this->organizerId;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function scheduledFor(): ScheduledDate
    {
        return ScheduledDate::fromString(
            $this->scheduledForDate . ' ' . $this->scheduledForTime
        );
    }
}
