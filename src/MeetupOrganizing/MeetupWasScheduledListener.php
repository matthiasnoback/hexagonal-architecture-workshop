<?php
declare(strict_types=1);

namespace MeetupOrganizing;

use App\ApplicationInterface;
use MeetupOrganizing\Application\RsvpForMeetup;

final class MeetupWasScheduledListener
{
    private ApplicationInterface $application;

    public function __construct(ApplicationInterface $application)
    {
        $this->application = $application;
    }

    public function rsvpForOrganizer(MeetupWasScheduled $event): void
    {
        $this->application->rsvpForMeetup(
            new RsvpForMeetup(
                $event->meetupId(),
                $event->organizerId()->asString(),
            )
        );
    }
}
