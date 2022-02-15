<?php

declare(strict_types=1);

namespace App;

use Billing\InvoiceNotNeeded;
use Billing\ViewModel\Invoice;
use MeetupOrganizing\Application\SignUp;
use MeetupOrganizing\ViewModel\MeetupDetails;
use MeetupOrganizing\ViewModel\MeetupListing;

interface ApplicationInterface
{
    public function signUp(SignUp $command): string;

    public function meetupDetails(string $id): MeetupDetails;

    public function scheduleMeetup(ScheduleMeetup $command): int;

    /**
     * @return array<MeetupListing>
     */
    public function listMeetups(): array;

    // ports for Billing:

    /**
     * @return array<Invoice>
     */
    public function listInvoices(string $organizerId): array;

    /**
     * @throws InvoiceNotNeeded
     */
    public function createInvoice(
        string $organizerId,
        int $year,
        int $month,
    ): void;
}
