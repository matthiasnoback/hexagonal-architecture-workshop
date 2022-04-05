<?php

declare(strict_types=1);

namespace App;

use MeetupOrganizing\Entity\MeetupWasCancelled;
use MeetupOrganizing\Entity\MeetupWasScheduled;
use MeetupOrganizing\Entity\UserHasRsvpd;

final class AddFlashMessage
{
    public function __construct(
        private readonly Session $session
    ) {
    }

    public function whenUserHasRsvped(UserHasRsvpd $event): void
    {
        $this->session->addSuccessFlash('You have successfully RSVP-ed to this meetup');
    }

    public function whenMeetupWasScheduled(MeetupWasScheduled $event): void
    {
        $this->session->addSuccessFlash('Your meetup was scheduled successfully');
    }

    public function whenMeetupWasCancelled(
        MeetupWasCancelled $event
    ): void {
        $this->session->addSuccessFlash('You have cancelled the meetup');
    }
}
