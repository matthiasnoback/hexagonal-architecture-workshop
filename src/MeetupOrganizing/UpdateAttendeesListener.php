<?php
declare(strict_types=1);

namespace MeetupOrganizing;

use Doctrine\DBAL\Connection;
use MeetupOrganizing\Entity\RsvpWasCancelled;
use MeetupOrganizing\Entity\UserHasRsvpd;

final class UpdateAttendeesListener
{
    public function __construct(private readonly Connection $connection)
    {
    }

    public function whenUserHasRsvped(UserHasRsvpd $event): void
    {
        $this->connection->executeQuery(
            <<<SQL
            UPDATE meetups SET attendees = attendees + 1 WHERE meetupId = ? 
            SQL,
            [
                $event->meetupId()
            ]
        );
    }

    public function whenRsvpWasCancelled(RsvpWasCancelled $event): void
    {
        $this->connection->executeQuery(
            <<<SQL
            UPDATE meetups SET attendees = attendees - 1 WHERE meetupId = ? 
            SQL,
            [
                $event->meetupId
            ]
        );
    }
}
