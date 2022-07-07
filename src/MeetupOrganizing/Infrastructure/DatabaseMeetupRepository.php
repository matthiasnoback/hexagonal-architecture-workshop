<?php
declare(strict_types=1);

namespace MeetupOrganizing\Infrastructure;

use Doctrine\DBAL\Connection;
use MeetupOrganizing\Entity\Meetup;
use MeetupOrganizing\Entity\MeetupRepository;

final class DatabaseMeetupRepository implements MeetupRepository
{
    public function __construct(
        private readonly Connection $connection
    )
    {
    }

    public function save(Meetup $meetup): int
    {
        $this->connection->insert(
            'meetups',
            $meetup->asDatabaseRecord()
        );

        return (int) $this->connection->lastInsertId();
    }
}
