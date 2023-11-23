<?php

namespace MeetupOrganizing\Entity;

use Doctrine\DBAL\Connection;

final class MeetupRepositoryUsingDbal implements MeetupRepository
{
    public function __construct(private readonly Connection $connection)
    {

    }

    public function save(Meetup $meetup): int
    {
        $this->connection->insert('meetups', $meetup->asRecord());

        return (int)$this->connection->lastInsertId();
    }
}
