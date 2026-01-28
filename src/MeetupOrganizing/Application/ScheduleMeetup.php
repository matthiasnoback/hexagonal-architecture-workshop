<?php

namespace MeetupOrganizing\Application;

use App\Entity\UserId;
use DateTimeImmutable;

class ScheduleMeetup
{
    public function __construct(
        private readonly string $organizerId,
        public readonly string $name,
        public readonly string $description,
        private readonly string $scheduledFor
    )
    {
    }

    public function scheduledFor(): DateTimeImmutable
    {
        $dateTime = DateTimeImmutable::createFromFormat(
            'Y-m-d H:i',
            $this->scheduledFor
        );
        if ($dateTime === false) {
            throw new \InvalidArgumentException('Invalid date/time');
        }

        return $dateTime;
    }

    public function organizerId(): UserId
    {
        return UserId::fromString($this->organizerId);
    }
}
