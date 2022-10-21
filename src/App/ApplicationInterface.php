<?php

declare(strict_types=1);

namespace App;

use Billing\InvoiceNotRequired;
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

    public function scheduleMeetup(ScheduleMeetup $command): int;

    /**
     * @return array<MeetupForList>
     */
    public function listMeetups(string $now, bool $showPastMeetups): array;

    /**
     * @throws InvoiceNotRequired
     */
    public function createInvoice(int $year, int $month, string $organizerId): void;

    /**
     * @return array<Invoice>
     */
    public function listInvoices(string $organizerId): array;
}
