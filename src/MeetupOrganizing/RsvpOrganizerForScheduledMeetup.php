<?php
declare(strict_types=1);

namespace MeetupOrganizing;

use App\ApplicationInterface;
use MeetupOrganizing\Application\RsvpForMeetup;
use MeetupOrganizing\Handler\MeetupWasScheduled;

final class RsvpOrganizerForScheduledMeetup
{
    public function __construct(
        private readonly ApplicationInterface $application,
    ) {
    }

    public function whenMeetupWasScheduled(
        MeetupWasScheduled $event,
    ): void {
        $this->application->rsvpForMeetup(
            new RsvpForMeetup(
                $event->meetupId(),
                $event->organizerId()->asString(),
            )
        );
    }
}
