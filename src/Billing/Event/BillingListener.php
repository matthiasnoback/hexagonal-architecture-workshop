<?php
declare(strict_types=1);

namespace Billing\Event;

use Doctrine\DBAL\Connection;
use MeetupOrganizing\Event\MeetupWasCancelled;
use MeetupOrganizing\Event\MeetupWasScheduledByOrganizer;

final class BillingListener
{
    public function __construct(
        private Connection $connection
    ) {
    }

    public function whenMeetupWasScheduledByOrganizer(
        MeetupWasScheduledByOrganizer $event
    ): void {
        $this->connection->insert(
            'billing_meetups',
            [
                'organizerId' => $event->userId,
                'meetupId' => $event->meetupId,
                'year' => $event->scheduledDate->year(),
                'month' => $event->scheduledDate->month(),
            ]
        );
    }

    public function whenMeetupWasCancelled(
        MeetupWasCancelled $event
    ): void {
        $this->connection->delete(
            'billing_meetups',
            [
                'meetupId' => $event->meetupId
            ]
        );
    }
}
