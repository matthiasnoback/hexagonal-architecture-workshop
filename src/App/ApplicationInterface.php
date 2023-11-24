<?php

declare(strict_types=1);

namespace App;

use App\Entity\CouldNotFindUser;
use MeetupOrganizing\Application\RsvpForMeetup;
use MeetupOrganizing\Application\SignUp;
use MeetupOrganizing\ViewModel\MeetupDetails;

interface ApplicationInterface
{
    public function signUp(SignUp $command): string;

    public function meetupDetails(string $id): MeetupDetails;

    public function rsvpForMeetup(RsvpForMeetup $command): void;

    public function cancelRsvp(string $meetupId, string $userId): void;

    /**
     * @throws CouldNotFindUser
     */
    public function scheduleMeeting(
        ScheduleMeeting $command
    ): string;

    /**
     * @return array<MeetupForList>
     */
    public function listUpcomingMeetups(string $now, bool $showPastMeetups): array;

    public function cancelMeetup(string $meetupId, string $userId): void;

    public function rescheduleMeetup(string $meetupId, string $scheduleFor, string $organizerId): void;
}
