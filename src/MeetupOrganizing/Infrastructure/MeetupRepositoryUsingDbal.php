<?php
declare(strict_types=1);

namespace MeetupOrganizing\Infrastructure;

use Doctrine\DBAL\Connection;
use MeetupOrganizing\Entity\Meetup;
use MeetupOrganizing\Entity\MeetupId;
use MeetupOrganizing\Entity\MeetupRepository;
use Ramsey\Uuid\Uuid;

/**
 * @object-type Adapter
 */
final class MeetupRepositoryUsingDbal implements MeetupRepository
{
    public function __construct(private readonly Connection $connection)
    {
    }

    public function save(Meetup $meetup): void
    {
        try {
            $this->getById($meetup->meetupId);
            $this->connection->update(
                'meetups',
                $meetup->asDatabaseRecord(),
                [
                    'meetupId' => $meetup->meetupId->asString()
                ]
            );
        } catch (\RuntimeException) {
            $this->connection->insert('meetups', $meetup->asDatabaseRecord());
            // remember ID
        }
    }

    public function nextIdentity(): MeetupId
    {
        return MeetupId::fromString(Uuid::uuid4()->toString());
    }

    public function getById(MeetupId $meetupId): Meetup
    {
        $record = $this->connection->fetchAssociative(
            'SELECT * FROM meetups WHERE meetupId = ?',
            [$meetupId->asString()]
        );
        if ($record === false) {
            throw new \RuntimeException('Meetup not found');
        }

        // remember ID

        return Meetup::fromDatabaseRecord($record);
    }
}
