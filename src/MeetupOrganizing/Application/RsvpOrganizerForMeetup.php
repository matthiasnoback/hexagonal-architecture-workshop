<?php
declare(strict_types=1);

namespace MeetupOrganizing\Application;

use App\ApplicationInterface;
use MeetupOrganizing\Entity\MeetupWasScheduled;

final class RsvpOrganizerForMeetup
{
    public function __construct(private ApplicationInterface $application)
    {
    }

    public function whenMeetupWasScheduled(MeetupWasScheduled $event): void
    {
        $this->application->rsvpForMeetup(
            new RsvpForMeetup($event->meetupId(), $event->organizerId()->asString())
        );
    }
}
