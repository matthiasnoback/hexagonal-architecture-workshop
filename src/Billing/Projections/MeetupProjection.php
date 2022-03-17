<?php
declare(strict_types=1);

namespace Billing\Projections;

use App\ExternalEventReceived;
use Doctrine\DBAL\Connection;
use MeetupOrganizing\Entity\ScheduledDate;
use Shared\ExternalEvents\MeetupOrganizingMeetupWasCancelled;
use Shared\ExternalEvents\MeetupOrganizingMeetupWasScheduled;

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
        if ($event->eventType() === MeetupOrganizingMeetupWasScheduled::NAME) {
            $this->handleMeetupWasScheduledEvent(MeetupOrganizingMeetupWasScheduled::fromArray($event->eventData()));
        } elseif ($event->eventType() === 'meetup.cancelled') {
            $this->handleMeetupWasCancelledEvent(MeetupOrganizingMeetupWasCancelled::fromArray($event->eventData()));
        }
    }

    private function handleMeetupWasScheduledEvent(MeetupOrganizingMeetupWasScheduled $event): void
    {
        $scheduledDate = ScheduledDate::fromString($event->scheduledDate());

        $this->connection->insert('billing_meetups', [
            'organizerId' => $event->organizerId(),
            'meetupId' => $event->meetupId(),
            'year' => $scheduledDate->year(),
            'month' => $scheduledDate->month(),
        ]);
    }

    private function handleMeetupWasCancelledEvent(MeetupOrganizingMeetupWasCancelled $event): void
    {
        $this->connection->delete(
            'billing_meetups',
            [
                'meetupId' => $event->meetupId(),
            ]
        );
    }
}
