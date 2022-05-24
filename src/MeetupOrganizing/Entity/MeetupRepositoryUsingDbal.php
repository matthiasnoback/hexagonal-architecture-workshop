<?php
declare(strict_types=1);

namespace MeetupOrganizing\Entity;

use Doctrine\DBAL\Connection;
use Ramsey\Uuid\Uuid;
use RuntimeException;

final class MeetupRepositoryUsingDbal implements MeetupRepository
{
    /**
     * @var array<string,bool>
     */
    private array $persistedMeetupIds = [];

    public function __construct(
        private readonly Connection $connection,
    ) {
    }

    public function save(Meetup $meetup): void
    {
        if (isset($this->persistedMeetupIds[$meetup->meetupId()->asString()])) {
            $this->connection->update(
                'meetups',
                $meetup->asDatabaseRecord(),
                [
                    'meetupId' => $meetup->meetupId()->asString()
                ]
            );
        }
        else {
            $this->connection->insert('meetups', $meetup->asDatabaseRecord());
            $this->persistedMeetupIds[$meetup->meetupId()->asString()] = true;
        }
    }

    public function nextIdentity(): MeetupId
    {
        return MeetupId::fromString(Uuid::uuid4()->toString());
    }

    public function getById(MeetupId $meetupId): Meetup
    {
        $record = $this->connection->fetchAssociative('SELECT * FROM meetups WHERE meetupId = ?', [$meetupId->asString()]);

        if ($record === false) {
            throw new RuntimeException('Could not find meetup: ' . $meetupId->asString());
        }

        $this->persistedMeetupIds[$meetupId->asString()] = true;

        return Meetup::fromDatabaseRecord($record);
    }
}
