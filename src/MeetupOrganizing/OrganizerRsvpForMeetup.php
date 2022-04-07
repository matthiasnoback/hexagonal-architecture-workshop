<?php
declare(strict_types=1);

namespace MeetupOrganizing;

use App\ApplicationInterface;
use MeetupOrganizing\Application\RsvpForMeetup;

final class OrganizerRsvpForMeetup
{
    public function __construct(
        private ApplicationInterface $application
    ) {
    }

    public function whenMeetupWasScheduled(
        MeetupWasScheduled $event
    ): void {
        $this->application->rsvpForMeetup(
            new RsvpForMeetup(
                $event->meetupId->asString(),
                $event->organizerId->asString()
            )
        );
    }
}
