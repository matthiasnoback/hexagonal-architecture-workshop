<?php

declare(strict_types=1);

namespace MeetupOrganizing\ViewModel;

final class MeetupDetails
{
    /**
     * @param array<Rsvp> $rsvps
     */
    public function __construct(
        private readonly string $meetupId,
        private readonly string $name,
        private readonly string $description,
        private readonly string $scheduledFor,
        private readonly Organizer $organizer,
        private readonly array $rsvps
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

    public function meetupId(): string
    {
        return $this->meetupId;
    }

    /**
     * @return array<Rsvp>
     */
    public function rsvps(): array
    {
        return $this->rsvps;
    }
}
