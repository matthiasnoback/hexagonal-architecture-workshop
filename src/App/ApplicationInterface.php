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

    public function scheduleMeetup(
        string $organizerId,
        string $name,
        string $description,
        string $scheduledFor,
    ): int;

    /**
     * @return array<Meetup>
     */
    public function listUpcomingMeetups(): array;

    // @TODO move methods below to BillingApplicationInterface
    public function createInvoice(string $organizerId, int $year, int $month): bool;

    /**
     * @return array<Invoice>
     */
    public function listInvoices(string $organizerId): array;
}
