<?php

declare(strict_types=1);

namespace App;

use Billing\ViewModel\Invoice;
use MeetupOrganizing\Application\RsvpForMeetup;
use MeetupOrganizing\Application\SignUp;
use MeetupOrganizing\ViewModel\MeetupDetails;

interface ApplicationInterface
{
    public function signUp(SignUp $command): string;

    public function meetupDetails(string $id): MeetupDetails;

    public function rsvpForMeetup(RsvpForMeetup $command): void;

    public function cancelRsvp(string $meetupId, string $userId): void;

    public function scheduleMeetup(
        string $organizerId,
        string $name,
        string $description,
        string $dateTime
    ): string;

    /**
     * @return list<Meetup>
     */
    public function listMeetups(bool $showPastMeetups): array;

    public function createInvoice(string $organizerId, int $year, int $month): ?int;

    /**
     * @return list<Invoice>
     */
    public function listInvoices(string $organizerId): array;
}
