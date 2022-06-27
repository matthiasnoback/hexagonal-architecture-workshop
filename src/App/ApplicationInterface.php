<?php

declare(strict_types=1);

namespace App;

use DateTimeImmutable;
use MeetupOrganizing\Application\RsvpForMeetup;
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
        string $organizerId,
        string $name,
        string $description,
        string $scheduledFor,
    ): int;

    /**
     * @return list<MeetupForList>
     */
    public function listMeetups(bool $showPastMeetups, string $now): array;

    /**
     * @throws InvoiceNotRequired
     */
    public function createInvoice(string $organizerId, int $year, int $month): void;
}
