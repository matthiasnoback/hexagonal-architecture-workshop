<?php

namespace MeetupOrganizing\Entity;

use Doctrine\DBAL\Connection;
use Ramsey\Uuid\Uuid;

final class MeetupRepositoryUsingDbal implements MeetupRepository
{
    public function __construct(private readonly Connection $connection)
    {

    }

    public function save(Meetup $meetup): void
    {
        $this->connection->insert('meetups', $meetup->asRecord());
    }

    public function nextId(): MeetupId
    {
        return MeetupId::fromString(Uuid::uuid4()->toString());
    }
}
