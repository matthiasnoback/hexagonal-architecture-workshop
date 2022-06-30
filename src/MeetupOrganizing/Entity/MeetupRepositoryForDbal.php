<?php
declare(strict_types=1);

namespace MeetupOrganizing\Entity;

use Assert\Assert;
use Doctrine\DBAL\Connection;
use Ramsey\Uuid\Uuid;

final class MeetupRepositoryForDbal implements MeetupRepository
{
    /**
     * @var list<string>
     */
    private array $knownIds = [];

    public function __construct(private readonly Connection $connection)
    {
    }

    public function save(Meetup $meetup): void
    {
        if (in_array($meetup->meetupId()->asString(), $this->knownIds, true)) {
            $this->connection->update(
                'meetups',
                $meetup->asRecord(),
                [
                    'meetupId' => $meetup->meetupId()->asString()
                ]
            );
        } else {
            $this->connection->insert('meetups', $meetup->asRecord());
            $this->knownIds[] = $meetup->meetupId()->asString();
        }
    }

    public function nextId(): MeetupId
    {
        return MeetupId::fromUuid(Uuid::uuid4());
    }

    public function getById(string $meetupId): Meetup
    {
        $record = $this->connection->fetchAssociative(
            <<<SQL
            SELECT * FROM meetups WHERE meetupId = ?
            SQL,
            [
                $meetupId
            ]
        );

        Assert::that($record)->isArray();

        $this->knownIds[] = $meetupId;

        return Meetup::fromRecord($record);
    }
}
