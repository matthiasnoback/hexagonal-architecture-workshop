<?php

declare(strict_types=1);

namespace App;

use MeetupOrganizing\Application\SignUp;
use MeetupOrganizing\ViewModel\MeetupDetails;
use MeetupOrganizing\ViewModel\MeetupSummary;

interface ApplicationInterface
{
    public function signUp(SignUp $command): string;

    public function meetupDetails(string $id): MeetupDetails;

    public function scheduleMeetup(ScheduleMeetupCommand $command): int;

    /**
     * @return array<MeetupSummary>
     */
    public function listUpcomingMeetups(
        ListUpcomingMeetups $query
    ): array;
}
