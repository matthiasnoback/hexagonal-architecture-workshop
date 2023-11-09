<?php

namespace MeetupOrganizing\Entity;

use Doctrine\DBAL\Connection;

class MeetupRepositorySql implements MeetupRepository
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function save(Meetup $entity): int
    {
        $this->connection->insert('meetups', $entity->toRecord());

        return (int)$this->connection->lastInsertId();
    }
}
