<?php
declare(strict_types=1);

namespace MeetupOrganizing\ViewModel;

final class MeetupInList
{
    public function __construct(
        public readonly string $meetupId,
        public readonly string $name,
        public readonly string $numberOfAttendees,
        public readonly string $scheduledFor,
        public readonly string $organizerId
    ) {
    }
}
