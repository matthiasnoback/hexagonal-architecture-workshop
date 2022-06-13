<?php
declare(strict_types=1);

namespace MeetupOrganizing\Infrastructure;

use Doctrine\DBAL\Connection;
use MeetupOrganizing\Entity\Meetup;
use MeetupOrganizing\Entity\MeetupId;
use MeetupOrganizing\Entity\Meetups;
use Ramsey\Uuid\Uuid;

final class MeetupsUsingDbal implements Meetups
{
    public function __construct(private readonly Connection $connection)
    {

    }

    public function nextMeetupId(): MeetupId
    {
        return MeetupId::fromString(Uuid::uuid4()->toString());
    }

    public function get(string $meetupId): Meetup
    {
        $record = $this->connection->fetchAssociative(
            'SELECT * FROM meetups WHERE meetupId = ?',
            [
                $meetupId
            ]
        );

        if ($record === false) {
            throw new \RuntimeException('Meetup not found');
        }

        return Meetup::fromArray($record);
    }

    public function save(Meetup $meetup): void
    {
        try {
            $this->get($meetup->meetupId()->asString());
        } catch (\RuntimeException) {
            $this->connection->insert('meetups', $meetup->toArray());
            return;
        }

        $this->connection->update(
            'meetups',
            $meetup->toArray(),
            [
                'meetupId' => $meetup->meetupId()->asString()
            ]
        );
    }
}
