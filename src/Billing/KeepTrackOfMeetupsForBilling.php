<?php
declare(strict_types=1);

namespace Billing;

use Doctrine\DBAL\Connection;
use MeetupOrganizing\Handler\MeetupWasCancelled;
use MeetupOrganizing\MeetupWasScheduled;

final class KeepTrackOfMeetupsForBilling
{
    public function __construct(private Connection $connection)
    {
    }

    public function whenMeetupWasScheduled(MeetupWasScheduled $event): void
    {
        $this->connection->insert(
            'billing_meetups',
            [
                'meetupId' => $event->meetupId(),
                'month' => $event->scheduledDate()->month(),
                'year' => $event->scheduledDate()->year(),
                'organizerId' => $event->organizerId()->asString(),
            ]
        );
    }

    public function whenMeetupWasCancelled(MeetupWasCancelled $event): void
    {
        $this->connection->delete(
            'billing_meetups',
            [
                'meetupId' => $event->meetupId(),
            ]
        );
    }
}
