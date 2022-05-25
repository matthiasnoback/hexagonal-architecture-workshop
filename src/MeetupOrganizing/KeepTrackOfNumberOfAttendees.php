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

    public function whenUserHasRsvpd(UserHasRsvpd $event): void
    {
        $this->connection->executeQuery('UPDATE meetups SET numberOfAttendees = numberOfAttendees + 1 WHERE main.meetups.meetupId = ?', [$event->meetupId()]);
    }
}
