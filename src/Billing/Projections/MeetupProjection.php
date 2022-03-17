<?php
declare(strict_types=1);

namespace Billing\Projections;

use App\ExternalEventReceived;
use App\Mapping;
use Doctrine\DBAL\Connection;
use MeetupOrganizing\Entity\ScheduledDate;

final class MeetupProjection
{
    public function __construct(private readonly Connection $connection)
    {
    }

    public function whenConsumerRestarted(): void
    {
        $this->connection->executeQuery('DELETE FROM billing_meetups WHERE 1');
    }

    public function whenExternalEventReceived(ExternalEventReceived $event): void
    {
        if ($event->eventType() === 'meetup.scheduled') {
            $this->handleMeetupWasScheduledEvent($event->eventData());
        } elseif ($event->eventType() === 'meetup.cancelled') {
            $this->handleMeetupWasCancelledEvent($event->eventData());
        }
    }

    private function handleMeetupWasScheduledEvent(array $eventData): void
    {
        $scheduledDate = ScheduledDate::fromString(Mapping::getString($eventData, 'scheduledDate'));

        $this->connection->insert('billing_meetups', [
            'organizerId' => Mapping::getString($eventData, 'organizerId'),
            'meetupId' => Mapping::getInt($eventData, 'meetupId'),
            'year' => $scheduledDate->year(),
            'month' => $scheduledDate->month(),
        ]);
    }

    private function handleMeetupWasCancelledEvent(array $eventData): void
    {
        $this->connection->delete(
            'billing_meetups',
            [
                'meetupId' => Mapping::getInt($eventData, 'meetupId'),
            ]
        );
    }
}
