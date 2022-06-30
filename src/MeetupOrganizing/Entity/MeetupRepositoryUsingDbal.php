<?php
declare(strict_types=1);

namespace MeetupOrganizing\Entity;

use Doctrine\DBAL\Connection;
use Ramsey\Uuid\Uuid;

final class MeetupRepositoryUsingDbal implements MeetupRepository
{
    public function __construct(
        private readonly Connection $connection
    ) {
    }

    public function save(Meetup $meetup): void
    {
        $this->connection->insert('meetups', $meetup->toDatabaseRecord());
    }

    public function nextIdentity(): MeetupId
    {
        return MeetupId::fromString(Uuid::uuid4()->toString());
    }
}
