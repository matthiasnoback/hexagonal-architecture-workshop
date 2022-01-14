<?php

declare(strict_types=1);

namespace App\Handler;

use App\Entity\User;

final class MeetupDetails
{
    public function __construct(
        private readonly array $meetupRecord,
        private readonly User $organizer,
        private readonly array $attendeeRecords
    ) {
    }

    public function name(): string
    {
        return $this->meetupRecord['name'];
    }

    public function scheduledFor(): string
    {
        return $this->meetupRecord['scheduledFor'];
    }

    public function description(): string
    {
        return $this->meetupRecord['description'];
    }

    public function organizerId(): string
    {
        return $this->meetupRecord['organizerId'];
    }

    public function meetupId(): int
    {
        return $this->meetupRecord['meetupId'];
    }

    public function organizer(): User
    {
        return $this->organizer;
    }

    public function attendees(): array
    {
        return $this->attendeeRecords;
    }
}
