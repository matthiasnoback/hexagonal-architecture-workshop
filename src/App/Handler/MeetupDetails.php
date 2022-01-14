<?php

declare(strict_types=1);

namespace App\Handler;

use App\Mapping;

final class MeetupDetails
{
    /**
     * @param array<string> $attendeeNames
     */
    public function __construct(
        private int $meetupId,
        private string $name,
        private string $description,
        private string $scheduledFor,
        private Organizer $organizer,
        private array $attendeeNames
    ) {
    }

    public function name(): string
    {
        return $this->name;
    }

    public function scheduledFor(): string
    {
        return $this->scheduledFor;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function organizer(): Organizer
    {
        return $this->organizer;
    }

    public function meetupId(): int
    {
        return $this->meetupId;
    }

    public function attendeeNames(): array
    {
        return $this->attendeeNames;
    }
}
