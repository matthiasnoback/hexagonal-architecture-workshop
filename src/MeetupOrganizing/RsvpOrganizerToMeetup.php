<?php
declare(strict_types=1);

namespace MeetupOrganizing;

use App\ApplicationInterface;
use MeetupOrganizing\Application\RsvpToMeetup;

final class RsvpOrganizerToMeetup
{
    public function __construct(
        private ApplicationInterface $application
    ) {
    }

    public function whenMeetupWasScheduled(
        MeetupWasScheduled $event
    ): void {
        $this->application->rsvpToMeetup(
            new RsvpToMeetup(
                $event->meetupId(),
                $event->organizerId()->asString()
            )
        );
    }
}
