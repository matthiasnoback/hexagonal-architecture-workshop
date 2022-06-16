<?php
declare(strict_types=1);

namespace MeetupOrganizing\Infrastructure;

use Doctrine\DBAL\Connection;
use MeetupOrganizing\Entity\Meetup;
use MeetupOrganizing\Entity\MeetupId;
use MeetupOrganizing\Entity\MeetupRepository;
use Ramsey\Uuid\Uuid;

final class MeetupRepositoryUsingDbal implements MeetupRepository
{
    public function __construct(private readonly Connection $connection)
    {
    }

    public function save(Meetup $meetup): void
    {
        $this->connection->insert('meetups', $meetup->toDatabaseRecord());
    }

    public function nextId(): MeetupId
    {
        return MeetupId::fromString(Uuid::uuid4()->toString());
    }
}
