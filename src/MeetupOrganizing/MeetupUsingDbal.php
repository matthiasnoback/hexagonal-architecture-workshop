<?php
declare(strict_types=1);

namespace MeetupOrganizing;

use App\Mapping;
use Assert\Assert;
use Billing\InvoicePeriod;
use Doctrine\DBAL\Connection;

final class MeetupUsingDbal implements MeetupInterface
{
    public function __construct(
        private Connection $connection
    )
    {

    }
    public function countMeetupsPerMonth(int $year, int $month, string $organizerId): int
    {
        $invoicePeriod = InvoicePeriod::createFromYearAndMonth($year, $month);
        // Load the data directly from the database
        $result = $this->connection->executeQuery(
            'SELECT COUNT(meetupId) as numberOfMeetups FROM meetups WHERE organizerId = :organizerId AND scheduledFor >= :firstDayOfMonth AND scheduledFor < :lastDayOfMonth',
            [
                'organizerId' => $organizerId,
                'firstDayOfMonth' => $invoicePeriod->firstDayOfPeriod()->format('Y-m-d'),
                'lastDayOfMonth' => $invoicePeriod->firstDayOfNextPeriod()->format('Y-m-d'),
            ]
        );

        $record = $result->fetchAssociative();
        Assert::that($record)->isArray();

        return Mapping::getInt($record, 'numberOfMeetups');
    }
}
