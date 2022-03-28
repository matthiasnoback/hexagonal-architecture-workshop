<?php
declare(strict_types=1);

namespace MeetupOrganizing\Event;

final class MeetupWasScheduledByOrganizer
{
    public function __construct(
        public readonly int $meetupId,
        public readonly string $userId,
    ) {
    }
}
