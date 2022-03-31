<?php
declare(strict_types=1);

namespace Billing\Projections;

use Doctrine\DBAL\Connection;
use MeetupOrganizing\MeetupWasCancelled;
use MeetupOrganizing\MeetupWasScheduled;

final class MeetupProjection
{
    public function __construct(
        private Connection $connection
    ) {
    }

    public function whenMeetupWasScheduled(
        MeetupWasScheduled $event
    ): void {
        $this->connection->insert(
            'billing_meetups',
            [
                'organizerId' => $event->organizerId()->asString(),
                'meetupId' => $event->meetupId(),
                'year' => $event->scheduledDate()->year(),
                'month' => $event->scheduledDate()->month(),
            ]
        );
    }

    public function whenMeetupWasCancelled(
        MeetupWasCancelled $event
    ): void {
        $this->connection->delete(
            'billing_meetups',
            [
                'meetupId' => $event->meetupId()
            ]
        );
    }
}
