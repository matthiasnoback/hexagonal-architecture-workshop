<?php
declare(strict_types=1);

namespace Billing;

use App\Mapping;
use Assert\Assert;
use DateTimeImmutable;
use Doctrine\DBAL\Connection;

final class MeetupRepositoryDbal implements MeetupRepositoryInterface
{
    public function __construct(
        private readonly Connection $connection
    )
    {

    }
    public function countMeetupsPerMonth(int $month, int $year, string $organizerId): int
    {
        $firstDayOfMonth = DateTimeImmutable::createFromFormat('Y-m-d', $year . '-' . $month . '-1');
        Assert::that($firstDayOfMonth)->isInstanceOf(DateTimeImmutable::class);
        $lastDayOfMonth = $firstDayOfMonth->modify('last day of this month');

        // Load the data directly from the database
        $result = $this->connection->executeQuery(
            'SELECT COUNT(meetupId) as numberOfMeetups FROM meetups WHERE organizerId = :organizerId AND scheduledFor >= :firstDayOfMonth AND scheduledFor <= :lastDayOfMonth',
            [
                'organizerId' => $organizerId,
                'firstDayOfMonth' => $firstDayOfMonth->format('Y-m-d'),
                'lastDayOfMonth' => $lastDayOfMonth->format('Y-m-d'),
            ]
        );

        $record = $result->fetchAssociative();
        Assert::that($record)->isArray();

        return Mapping::getInt($record, 'numberOfMeetups');
    }
}
