<?php

declare(strict_types=1);

namespace App;

use Billing\ViewModel\Invoice;
use MeetupOrganizing\Application\RsvpForMeetup;
use MeetupOrganizing\Application\SignUp;
use MeetupOrganizing\Entity\MeetupId;
use MeetupOrganizing\ViewModel\MeetupDetails;

interface ApplicationInterface
{
    public function signUp(SignUp $command): string;

    public function meetupDetails(string $id): MeetupDetails;

    public function rsvpForMeetup(RsvpForMeetup $command): void;

    public function cancelRsvp(string $meetupId, string $userId): void;

    public function scheduleMeeting(
        ScheduleMeetup $scheduleMeetup
    ): MeetupId;

    /**
     * @return array<MeetupForList>
     */
    public function listUpcomingMeetups(bool $showPastMeetups): array;

    public function createInvoice(int $year, int $month, string $organizerId): ?int;

    /**
     * @return array<Invoice>
     */
    public function listInvoices(string $organizerId): array;

    public function cancelMeetup(string $meetupId, string $userId): void;

    public function rescheduleMeetup(string $meetupId, string $scheduleFor, string $organizerId): void;
}
