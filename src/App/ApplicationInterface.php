<?php

declare(strict_types=1);

namespace App;

use MeetupOrganizing\Application\SignUp;
use MeetupOrganizing\ViewModel\MeetupDetails;
use MeetupOrganizing\ViewModel\UpcomingMeetup;

interface ApplicationInterface
{
    public function signUp(SignUp $command): void;

    public function meetupDetails(string $id): MeetupDetails;

    public function scheduleMeetup(string $organizerId, string $name, string $description, string $scheduledFor): int;

    /**
     * @return array<UpcomingMeetup>
     */
    public function listUpcomingMeetups(): array;
}
