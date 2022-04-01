<?php
declare(strict_types=1);

namespace Billing\Projections;

use App\ExternalEventReceived;
use App\Mapping;
use Assert\Assertion;
use DateTimeImmutable;
use Doctrine\DBAL\Connection;

final class MeetupProjection
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
        switch ($event->eventType()) {
            case 'meetup_organizing.meetup.scheduled':
                $this->whenMeetupWasScheduled($event->eventData());
                break;
            case 'meetup_organizing.meetup.cancelled':
                $this->whenMeetupWasCancelled($event->eventData());
                break;
        }
    }

    private function whenMeetupWasScheduled(
        array $event
    ): void {
        $scheduledDate = Mapping::getString($event, 'scheduledDate');

        $dateTime = DateTimeImmutable::createFromFormat('Y-m-d H:i', $scheduledDate);
        Assertion::isInstanceOf($dateTime, DateTimeImmutable::class);

        $this->connection->insert(
            'billing_meetups',
            [
                'organizerId' => Mapping::getString($event, 'organizerId'),
                'meetupId' => Mapping::getString($event, 'meetupId'),
                'year' => (int) $dateTime->format('Y'),
                'month' => (int) $dateTime->format('n'),
            ]
        );
    }

    private function whenMeetupWasCancelled(
        array $event
    ): void {
        $this->connection->delete(
            'billing_meetups',
            [
                'meetupId' => Mapping::getString($event, 'meetupId'),
            ]
        );
    }
}
