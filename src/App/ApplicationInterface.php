<?php

declare(strict_types=1);

namespace App;

use Billing\ViewModel\Invoice;
use DateTimeImmutable;
use MeetupOrganizing\Application\RsvpForMeetup;
use MeetupOrganizing\Application\SignUp;
use MeetupOrganizing\ViewModel\MeetupDetails;
use MeetupOrganizing\ViewModel\MeetupListElement;

interface ApplicationInterface
{
    public function signUp(SignUp $command): string;

    public function scheduleMeetup(
        string $organizerId,
        string $name,
        string $description,
        string $scheduleForDate,
        string $scheduleForTime
    ): int;

    public function meetupDetails(string $id): MeetupDetails;

    public function rsvpForMeetup(RsvpForMeetup $command): void;

    public function cancelRsvp(string $meetupId, string $userId): void;

    /**
     * @return array<MeetupListElement>
     */
    public function listMeetups(?DateTimeImmutable $startingDate): array;

    public function createInvoice(int $year, int $month, string $organizerId): bool;

    /**
     * @return array<Invoice>
     */
    public function listInvoices(string $organizerId): array;
}
