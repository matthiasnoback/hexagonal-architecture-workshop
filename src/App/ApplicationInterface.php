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

    public function scheduleMeetup(string $name, string $description, string $scheduledFor, string $organizerId): int;

    /**
     * @return array<UpcomingMeetup>
     */
    public function listUpcomingMeetings(\DateTimeImmutable $now): array;
}
