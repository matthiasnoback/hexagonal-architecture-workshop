<?php

declare(strict_types=1);

namespace MeetupOrganizing\ViewModel;

final class MeetupDetails
{
    /**
     * @param array<string> $attendeeNames
     */
    public function __construct(
        private readonly int $meetupId,
        private readonly string $name,
        private readonly string $description,
        private readonly string $scheduledFor,
        private readonly Organizer $organizer,
        private readonly array $attendeeNames
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
