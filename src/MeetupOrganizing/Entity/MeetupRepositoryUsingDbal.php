<?php
declare(strict_types=1);

namespace MeetupOrganizing\Entity;

use Doctrine\DBAL\Connection;

final class MeetupRepositoryUsingDbal implements MeetupRepository
{
    public function __construct(
        private readonly Connection $connection
    ) {
    }

    public function save(Meetup $meetup): string
    {
        $this->connection->insert('meetups', $meetup->toDatabaseRecord());

        return (string) $this->connection->lastInsertId();
    }
}
