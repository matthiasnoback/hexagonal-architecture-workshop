<?php
declare(strict_types=1);

namespace MeetupOrganizing\Event;

use App\ApplicationInterface;

final class RsvpOrganizer
{
    public function __construct(
        private ApplicationInterface $application
    )
    {
    }

    public function whenMeetupWasScheduledByOrganizer(
        MeetupWasScheduledByOrganizer $event
    ): void {
        $this->application->rsvpForMeetup(
            $event->meetupId,
            $event->userId
        );
    }
}
