<?php
declare(strict_types=1);

namespace MeetupOrganizing\ViewModel;

final class MeetupForList
{
    public function __construct(
        public readonly string $meetupId,
        public readonly string $name,
        public readonly string $scheduledFor,
        public readonly string $organizerId,
    ) {
    }
}
