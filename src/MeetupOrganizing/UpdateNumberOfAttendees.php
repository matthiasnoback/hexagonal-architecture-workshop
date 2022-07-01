<?php
declare(strict_types=1);

namespace MeetupOrganizing;

use Doctrine\DBAL\Connection;
use MeetupOrganizing\Entity\RsvpWasCancelled;
use MeetupOrganizing\Entity\UserHasRsvpd;

final class UpdateNumberOfAttendees
{
    public function __construct(private readonly Connection $connection)
    {
    }

    public function whenUserHasRsvpd(UserHasRsvpd $event): void
    {
        // increase
        $this->connection->executeQuery(
            'UPDATE meetups SET numberOfAttendees = numberOfAttendees + 1 WHERE meetupId = ?',
            [
                $event->meetupId()
            ]
        );
    }

    public function whenRsvpWasCancelled(RsvpWasCancelled $event): void
    {
        // decrease
        $this->connection->executeQuery(
            'UPDATE meetups SET numberOfAttendees = numberOfAttendees - 1 WHERE meetupId = ?',
            [
                $event->meetupId
            ]
        );
    }
}
