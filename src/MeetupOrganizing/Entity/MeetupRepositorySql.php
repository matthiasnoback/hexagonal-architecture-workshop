<?php

namespace MeetupOrganizing\Entity;

use Doctrine\DBAL\Connection;
use Ramsey\Uuid\Uuid;

class MeetupRepositorySql implements MeetupRepository
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function save(Meetup $entity): void
    {
        $this->connection->insert('meetups', $entity->toRecord());
    }

    public function nextIdentity(): MeetupId
    {
        return MeetupId::fromString(Uuid::uuid4()->toString());
    }
}
