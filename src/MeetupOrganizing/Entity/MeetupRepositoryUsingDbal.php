<?php
declare(strict_types=1);

namespace MeetupOrganizing\Entity;

use Assert\Assertion;
use Doctrine\DBAL\Connection;
use Ramsey\Uuid\Uuid;

final class MeetupRepositoryUsingDbal implements MeetupRepository
{
    /**
     * @var array<string,bool>
     */
    private array $knownIds = [];

    public function __construct(
        private readonly Connection $connection
    ) {
    }

    public function save(Meetup $meetup): void
    {
        if (isset($this->knownIds[$meetup->meetupId()->asString()])) {
            $this->connection->update('meetups', $meetup->toDatabaseRecord(), ['meetupId' => $meetup->meetupId()->asString()]);
        } else {
            $this->connection->insert('meetups', $meetup->toDatabaseRecord());
            $this->knownIds[$meetup->meetupId()->asString()] = true;
        }
    }

    public function nextIdentity(): MeetupId
    {
        return MeetupId::fromString(Uuid::uuid4()->toString());
    }

    public function getById(MeetupId $meetupId): Meetup
    {
        $record = $this->connection->fetchAssociative(
            'SELECT * FROM meetups WHERE meetupId = ?', [$meetupId->asString()]
        );
        Assertion::isArray($record);

        $this->knownIds[$meetupId->asString()] = true;

        return Meetup::fromDatabaseRecord($record);
    }
}
