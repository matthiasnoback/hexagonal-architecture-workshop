<?php

namespace MeetupOrganizing;

use Doctrine\DBAL\Connection;
use MeetupOrganizing\Entity\UserHasRsvpd;

class UpdateNumberOfAttendees
{
    public function __construct(private readonly Connection $connection)
    {
        // TODO use abstraction
    }

    public function whenUserHasRsvped(UserHasRsvpd $event): void
    {
        // TODO increase by 1 the value of attendeesNumber column
        $this->connection->executeQuery(
            'UPDATE meetups SET attendeesNumber = attendeesNumber + 1 WHERE meetupId = ?',
            [$event->meetupId()]
        );
    }
}
