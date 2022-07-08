<?php
declare(strict_types=1);

namespace MeetupOrganizing\Infrastructure;

use Doctrine\DBAL\Connection;
use MeetupOrganizing\Entity\CouldNotFindMeetup;
use MeetupOrganizing\Entity\Meetup;
use MeetupOrganizing\Entity\MeetupId;
use MeetupOrganizing\Entity\MeetupRepository;
use Ramsey\Uuid\Uuid;

final class DatabaseMeetupRepository implements MeetupRepository
{
    private array $knownIds = [];

    public function __construct(
        private readonly Connection $connection
    ) {
    }

    public function save(Meetup $meetup): void
    {
        if (in_array($meetup->meetupId()->asString(), $this->knownIds)) {
            $this->connection->update(
                'meetups',
                $meetup->asDatabaseRecord(),
                [
                    'meetupId' => $meetup->meetupId()->asString()
                ]
            );
        } else {
            $this->connection->insert(
                'meetups',
                $meetup->asDatabaseRecord()
            );

            $this->knownIds[] = $meetup->meetupId()->asString();
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
            [
                $meetupId->asString()
            ]
        );

        if ($record === false) {
            throw CouldNotFindMeetup::withId($meetupId->asString());
        }

        $this->knownIds[] = $meetupId->asString();

        return Meetup::fromDatabaseRecord($record);
    }
}
