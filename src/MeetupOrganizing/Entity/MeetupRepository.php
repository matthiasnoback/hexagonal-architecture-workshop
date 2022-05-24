<?php
declare(strict_types=1);

namespace MeetupOrganizing\Entity;

use Doctrine\DBAL\Connection;

final class MeetupRepository
{
    public function __construct(
        private readonly Connection $connection,
    ) {
    }

    public function save(Meetup $meetup): void
    {
        $this->connection->insert('meetups', $meetup->asDatabaseRecord());

        $meetup->setMeetupId((int)$this->connection->lastInsertId());
    }
}
