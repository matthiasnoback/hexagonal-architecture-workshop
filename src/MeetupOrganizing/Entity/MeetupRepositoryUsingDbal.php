<?php

namespace MeetupOrganizing\Entity;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception as DBALException;
use Ramsey\Uuid\Uuid;

final class MeetupRepositoryUsingDbal implements MeetupRepository
{
    public function __construct(private readonly Connection $connection)
    {

    }

    public function save(Meetup $entity): void
    {
        try {
            $this->getById($entity->meetupId());

            $this->connection->update('meetups', $entity->toRecord(), ['meetupId' => $entity->meetupId()->asString()]);
        } catch (CouldNotFindMeetup $exception) {
            $this->connection->insert('meetups', $entity->toRecord());
        }
    }

    public function getById(MeetupId $meetupId): Meetup
    {
        try {
            $record = $this->connection->fetchAssociative('SELECT * FROM meetups WHERE meetupId = ?', [$meetupId->asString()]);
            if ($record === false) {
                throw CouldNotFindMeetup::withId($meetupId->asString());
            }
            // update a list of known entity IDs, $this->existingEntityIds[] = $meetupId
        } catch (DBALException $exception) {
            throw CouldNotFindMeetup::withId($meetupId->asString());
        }

        return Meetup::fromRecord($record);
    }

    public function nextId(): MeetupId
    {
        return MeetupId::fromString(Uuid::uuid4()->toString());
    }
}
