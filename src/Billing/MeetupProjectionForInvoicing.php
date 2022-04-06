<?php
declare(strict_types=1);

namespace Billing;

use App\ExternalEventReceived;
use App\Mapping;
use Assert\Assert;
use Assert\AssertionFailedException;
use Doctrine\DBAL\Connection;

final class MeetupProjectionForInvoicing implements Meetups
{
    public function __construct(
        private Connection $connection
    ) {
    }

    public function whenConsumerRestarted(): void
    {
        $this->connection->executeQuery('DELETE FROM billing_meetups WHERE 1');
    }

    public function whenExternalEventReceived(ExternalEventReceived $event): void
    {
        if ($event->eventType() === 'meetup.scheduled') {
            $this->whenMeetupWasScheduled($event->eventData());
        }
        if ($event->eventType() === 'meetup.cancelled') {
            $this->whenMeetupWasCancelled($event->eventData());
        }
    }

    private function whenMeetupWasScheduled(
        array $eventData,
    ): void {
        $scheduledDate = \DateTimeImmutable::createFromFormat(
            \DateTimeInterface::ISO8601,
            $eventData['scheduledDate'],
        );

        $this->connection->insert(
            'billing_meetups',
            [
                'organizerId' => $eventData['organizerId'],
                'meetupId' => $eventData['meetupId'],
                'year' => $scheduledDate->format('Y'),
                'month' => $scheduledDate->format('n'),
            ]
        );
    }

    private function whenMeetupWasCancelled(
        array $eventData,
    ): void {
        $this->connection->delete(
            'billing_meetups',
            [
                'meetupId' => $eventData['meetupId']
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
