<?php

declare(strict_types=1);

namespace App;

use MeetupOrganizing\Entity\RsvpWasCancelled;
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

    public function whenRsvpWasCancelled(RsvpWasCancelled $event): void
    {
        $this->session->addSuccessFlash('You have successfully cancelled your RSVP for this meetup');
    }
}
