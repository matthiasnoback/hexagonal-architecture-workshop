<?php

declare(strict_types=1);

namespace App;

use Billing\ViewModel\Invoice;
use MeetupOrganizing\Application\SignUp;
use MeetupOrganizing\ViewModel\Meetup;
use MeetupOrganizing\ViewModel\MeetupDetails;

interface ApplicationInterface
{
    public function signUp(SignUp $command): string;

    public function meetupDetails(string $id): MeetupDetails;

    public function scheduleMeetup(ScheduleMeetup $command): int;

    /**
     * @return array<Meetup>
     */
    public function listMeetups(): array;

    // Here starts the Billing context

    /**
     * @throws InvoiceNotNeeded
     */
    public function createInvoice(int $year, int $month, string $organizerId): int;

    /**
     * @return array<Invoice>
     */
    public function listInvoices(string $organizerId): array;
}
