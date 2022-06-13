<?php
declare(strict_types=1);

namespace MeetupOrganizing\Infrastructure;

use Doctrine\DBAL\Connection;
use MeetupOrganizing\Entity\Meetup;
use MeetupOrganizing\Entity\Meetups;

final class MeetupsUsingDbal implements Meetups
{
    public function __construct(private readonly Connection $connection)
    {

    }

    public function add(Meetup $meetup): void
    {
        $record = $meetup->toArray();

        $this->connection->insert('meetups', $record);

        $meetup->setMeetupId((int) $this->connection->lastInsertId());
    }
}
