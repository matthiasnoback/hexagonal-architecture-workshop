<?php
declare(strict_types=1);

namespace Billing\Integration;

use App\Mapping;
use Assert\Assert;
use Billing\MeetupCounts;
use DateTimeImmutable;
use Doctrine\DBAL\Connection;

final class MeetupOrganizingMeetupCounts implements MeetupCounts
{
    public function __construct(
        private readonly Connection $connection
    )
    {
    }

    public function getTotalNumberOfMeetups(
        string $organizerId,
        DateTimeImmutable $startDate,
        DateTimeImmutable $endDate
    ): int {
        // Load the data directly from the database
        $result = $this->connection->executeQuery(
            'SELECT COUNT(meetupId) as numberOfMeetups FROM meetups WHERE organizerId = :organizerId AND scheduledFor >= :firstDayOfMonth AND scheduledFor <= :lastDayOfMonth',
            [
                'organizerId' => $organizerId,
                'firstDayOfMonth' => $startDate->format('Y-m-d'),
                'lastDayOfMonth' => $endDate->format('Y-m-d'),
            ]
        );

        $record = $result->fetchAssociative();
        Assert::that($record)->isArray();

        return Mapping::getInt($record, 'numberOfMeetups');
    }
}
