<?php
declare(strict_types=1);

namespace Billing;

use App\Mapping;
use Assert\Assert;
use Doctrine\DBAL\Connection;

final class MeetupRepositoryDbal implements MeetupRepositoryInterface
{
    public function __construct(
        private readonly Connection $connection
    )
    {

    }
    public function countMeetupsPerMonth(
        InvoicePeriod $invoicePeriod,
        string $organizerId
    ): int {
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
