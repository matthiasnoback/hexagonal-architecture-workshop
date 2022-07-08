<?php
declare(strict_types=1);

namespace MeetupOrganizing;

use Doctrine\DBAL\Connection;
use MeetupOrganizing\Entity\UserHasRsvpd;

final class KeepTrackOfNumberOfAttendees
{
    public function __construct(private readonly Connection $connection)
    {

    }

    public function whenUserHasRsvped(UserHasRsvpd $event): void
    {
        // repository::getById()
        // increment()
        // save

        $this->connection->executeQuery(
            'UPDATE meetups SET numberOfAttendees = numberOfAttendees + 1 WHERE meetupId = ?',
            [
                $event->meetupId()
            ]
        );
    }
}
