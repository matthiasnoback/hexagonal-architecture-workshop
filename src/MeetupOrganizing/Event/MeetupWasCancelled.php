<?php
declare(strict_types=1);

namespace MeetupOrganizing\Event;

final class MeetupWasCancelled
{
    public function __construct(
        public readonly int $meetupId
    ) {
    }
}
