<?php

declare(strict_types=1);

namespace App;

use Billing\ViewModel\Invoice;
use MeetupOrganizing\Application\SignUp;
use MeetupOrganizing\ViewModel\MeetupDetails;
use MeetupOrganizing\ViewModel\UpcomingMeetup;

interface ApplicationInterface
{
    public function signUp(SignUp $command): string;

    public function meetupDetails(string $id): MeetupDetails;

    public function scheduleMeetup(string $name, string $description, string $scheduledFor, string $organizerId): int;

    /**
     * @return array<UpcomingMeetup>
     */
    public function listUpcomingMeetings(\DateTimeImmutable $now): array;

    public function createInvoice(int $year, int $month, string $organizerId): bool;

    /**
     * @return array<Invoice>
     */
    public function listInvoices(string $organizerId): array;
}
