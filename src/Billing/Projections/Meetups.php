<?php
declare(strict_types=1);

namespace Billing\Projections;

use App\ExternalEventReceived;
use App\Mapping;
use Doctrine\DBAL\Connection;
use Shared\DTOs\MeetupOrganizing\MeetupWasScheduledByOrganizer;

final class Meetups implements Projection
{
    public function __construct(private readonly Connection $connection)
    {
    }

    public function whenConsumerRestarted(): void
    {
        $this->connection->executeQuery('DELETE FROM billing_meetups WHERE 1');
    }

    public function whenExternalEventReceived(
        ExternalEventReceived $event
    ): void {
        match($event->eventType()) {
            'meetup_organizing.meetup_was_cancelled' => $this->whenMeetupWasCancelled(
                $event->eventData()
            ),
            'meetup_organizing.meetup_was_scheduled_by_organizer' => $this->whenMeetupWasScheduledByOrganizer(
                MeetupWasScheduledByOrganizer::fromArray($event->eventData())
            ),
            default => null
        };
    }

    private function whenMeetupWasCancelled(array $data): void
    {
        $this->connection->delete(
            'billing_meetups',
            [
                'meetupId' => Mapping::getInt($data, 'id')
            ]
        );
    }

    private function whenMeetupWasScheduledByOrganizer(MeetupWasScheduledByOrganizer $event): void
    {
        $this->connection->insert(
            'billing_meetups',
            [
                'organizerId' => $event->organizerId,
                'meetupId' => $event->meetupId,
                'year' => $event->scheduledDate->format('Y'),
                'month' => $event->scheduledDate->format('m'),
            ]
        );
    }
}
