<?php
declare(strict_types=1);

namespace MeetupOrganizing;

use Doctrine\DBAL\Connection;
use MeetupOrganizing\Entity\MeetupWasCancelled;
use MeetupOrganizing\Entity\MeetupWasScheduled;

final class BillingMeetups
{
    public function __construct(private Connection $connection)
    {
    }

    public function whenMeetupWasScheduled(MeetupWasScheduled $event): void
    {
        $this->connection->insert('billing_meetups', [
            'organizerId' => $event->organizerId()->asString(),
            'meetupId' => (string) $event->meetupId(),
            'year' => $event->scheduledDate()->year(),
            'month' => $event->scheduledDate()->month(),
        ]);
    }

    public function whenMeetupWasCancelled(MeetupWasCancelled $event): void
    {
        $this->connection->delete(
            'billing_meetups',
            [
                'meetupId' => $event->meetupId()
            ]
        );
    }
}
