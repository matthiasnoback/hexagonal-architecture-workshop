<?php
declare(strict_types=1);

namespace Billing;

use App\ExternalEventReceived;
use App\Mapping;
use Assert\Assert;
use Assert\AssertionFailedException;
use Doctrine\DBAL\Connection;
use Shared\MeetupWasScheduledData;

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
        if ($event->eventType() === MeetupWasScheduledData::eventType()) {
            $this->whenMeetupWasScheduled(
                MeetupWasScheduledData::fromArray($event->eventData())
            );
        }
        if ($event->eventType() === 'meetup.cancelled') {
            $this->whenMeetupWasCancelled($event->eventData());
        }
    }

    private function whenMeetupWasScheduled(
        MeetupWasScheduledData $event,
    ): void {
        $this->connection->insert(
            'billing_meetups',
            [
                'organizerId' => $event->organizerId,
                'meetupId' => $event->meetupId,
                'year' => $event->scheduledDate->format('Y'),
                'month' => $event->scheduledDate->format('n'),
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
