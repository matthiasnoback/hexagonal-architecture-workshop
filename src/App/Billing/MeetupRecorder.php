<?php
declare(strict_types=1);

namespace App\Billing;

use Doctrine\DBAL\Connection;
use MeetupOrganizing\MeetupWasScheduled;

final class MeetupRecorder
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
                'organizerId' => $event->organizerId->asString(),
                'meetupId' => $event->meetupId->asString(),
                'year' => (int) $event->scheduledFor
                    ->toDateTimeImmutable()->format('Y'),
                'month' => (int) $event->scheduledFor
                    ->toDateTimeImmutable()->format('m'),
            ]
        );
    }
}
