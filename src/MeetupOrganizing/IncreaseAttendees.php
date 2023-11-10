<?php

namespace MeetupOrganizing;

use Doctrine\DBAL\Connection;
use MeetupOrganizing\Entity\UserHasRsvpd;

class IncreaseAttendees
{
    public function __construct(private Connection $connection)
    {
    }

    public function whenUserHasRsvped(UserHasRsvpd $event): void
    {
        // Option 1: Aggregate inside Meetup: fetch Meetup, then call incremementAttendees()
        // Option 2: Aggregate participants inside Meetup (The Doctrine Way)
        // Option 3: This one
        // Option 4: Separate service to calculate this
        // Option 5: additional aggregation select when presenting the data

        $this->connection->executeQuery(
            'UPDATE meetups SET numberOfAttendees = numberOfAttendees + 1 WHERE meetupId = ?',
            [$event->meetupId()]
        );
    }
}
