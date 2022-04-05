<?php
declare(strict_types=1);

namespace Billing;

use Doctrine\DBAL\Connection;
use MeetupOrganizing\Entity\MeetupWasScheduled;

final class TrackMeetupScheduled
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
                'meetupId' => $event->meetupId()->asString(),
                'year' => $event->scheduledDate()->format('Y'),
                'month' => $event->scheduledDate()->format('n'),
            ]
        );
    }
}
