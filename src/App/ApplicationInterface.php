<?php

declare(strict_types=1);

namespace App;

use Billing\ViewModel\Invoice;
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

    public function scheduleMeetup(
        ScheduleMeetup $command
    ): int;

    /**
     * @return list<MeetupForList>
     */
    public function listMeetups(bool $showPastMeetups): array;

    public function createInvoice(string $organizerId, int $month, int $year): void;

    /**
     * @return array<Invoice>
     */
    public function listInvoices(string $organizerId): array;
}
