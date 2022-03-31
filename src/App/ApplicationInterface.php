<?php

declare(strict_types=1);

namespace App;

use MeetupOrganizing\Application\RsvpToMeetup;
use MeetupOrganizing\Application\SignUp;
use MeetupOrganizing\ViewModel\MeetupDetails;

interface ApplicationInterface
{
    public function signUp(SignUp $command): string;

    public function meetupDetails(string $id): MeetupDetails;

    public function rsvpToMeetup(RsvpToMeetup $rsvp): void;
}
