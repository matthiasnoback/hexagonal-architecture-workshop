<?php
declare(strict_types=1);

namespace App;

use Assert\Assert;
use DateTimeInterface;
use Doctrine\DBAL\Connection;

final class MeetupRepositoryUsingSharedDatabase implements MeetupRepository
{
    public function __construct(private readonly Connection $connection)
    {
    }

    public function countActiveMeetups(DateTimeInterface $from, DateTimeInterface $until, string $organizerId): int
    {
        // Load the data directly from the database
        $result = $this->connection->executeQuery(
            'SELECT COUNT(meetupId) as numberOfMeetups FROM meetups WHERE organizerId = :organizerId AND scheduledFor >= :from AND scheduledFor <= :until',
            [
                'organizerId' => $organizerId,
                'from' => $from->format('Y-m-d'),
                'until' => $until->format('Y-m-d'),
            ]
        );

        $record = $result->fetchAssociative();
        Assert::that($record)->isArray();

        return Mapping::getInt($record, 'numberOfMeetups');
    }
}
