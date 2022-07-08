<?php

declare(strict_types=1);

namespace App;

use MeetupOrganizing\Application\RsvpForMeetup;
use MeetupOrganizing\Application\ScheduleMeetup;
use MeetupOrganizing\Application\SignUp;
use MeetupOrganizing\ViewModel\MeetupDetails;
use MeetupOrganizing\ViewModel\MeetupForList;

interface ApplicationInterface
{
    public function signUp(SignUp $command): string;

    public function meetupDetails(string $id): MeetupDetails;

    public function rsvpForMeetup(RsvpForMeetup $command): void;

    public function cancelRsvp(string $meetupId, string $userId): void;

    public function scheduleMeetup(ScheduleMeetup $command): string;

    /**
     * @method-type command
     */
    public function cancelMeetup(string $meetupId, string $userId): void;

    /**
     * @return array<MeetupForList>
     * @method-type query
     */
    public function listMeetups(bool $showPastMeetups): array;
}
