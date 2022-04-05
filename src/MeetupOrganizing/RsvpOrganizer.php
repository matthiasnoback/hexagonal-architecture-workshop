<?php
declare(strict_types=1);

namespace MeetupOrganizing;

use App\ApplicationInterface;
use MeetupOrganizing\Entity\MeetupWasScheduled;

final class RsvpOrganizer
{
    public function __construct(
        private ApplicationInterface $application
    ) {

    }

    public function whenMeetupWasScheduled(MeetupWasScheduled $event): void
    {
        $this->application->rsvpMeetup(
            $event->meetupId()->asString(),
            $event->organizerId()->asString()
        );
    }
}
