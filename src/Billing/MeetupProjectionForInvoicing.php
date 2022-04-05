<?php
declare(strict_types=1);

namespace Billing;

use App\Mapping;
use Assert\Assert;
use Assert\AssertionFailedException;
use Doctrine\DBAL\Connection;
use MeetupOrganizing\Entity\MeetupWasCancelled;
use MeetupOrganizing\Entity\MeetupWasScheduled;

final class MeetupProjectionForInvoicing implements Meetups
{
    public function __construct(
        private Connection $connection
    ) {
    }

    public function whenMeetupWasScheduled(
        MeetupWasScheduled $event
    ): void {
        $this->connection->insert(
            'billing_meetups',
            [
                'organizerId' => $event->organizerId()->asString(),
                'meetupId' => $event->meetupId()->asString(),
                'year' => $event->scheduledDate()->format('Y'),
                'month' => $event->scheduledDate()->format('n'),
            ]
        );
    }

    public function whenMeetupWasCancelled(
        MeetupWasCancelled $event
    ): void {
        $this->connection->delete(
            'billing_meetups',
            [
                'meetupId' => $event->meetupId()->asString()
            ],
        );
    }

    /**
     * @throws AssertionFailedException
     */
    public function organizedInPeriod(
        string $organizerId,
        int $year,
        int $month
    ): int {
        $result = $this->connection->executeQuery(
            'SELECT COUNT(meetupId) as numberOfMeetups FROM billing_meetups WHERE organizerId = :organizerId AND year = :year AND month = :month',
            [
                'organizerId' => $organizerId,
                'year' => $year,
                'month' => $month,
            ]
        );

        $record = $result->fetchAssociative();
        Assert::that($record)->isArray();

        return Mapping::getInt($record, 'numberOfMeetups');
    }
}
