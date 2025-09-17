<?php
declare(strict_types=1);

namespace MeetupOrganizing\ViewModel;

final class MeetupForList
{
    public function __construct(
        public string $meetupId,
        public string $name,
        public string $scheduledFor,
        public string $organizerId,
    )
    {
    }
}
