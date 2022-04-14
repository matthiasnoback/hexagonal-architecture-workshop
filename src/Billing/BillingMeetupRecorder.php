<?php
declare(strict_types=1);

namespace Billing;

use App\ExternalEvents\ExternalEventConsumer;
use App\Mapping;
use Doctrine\DBAL\Connection;
use MeetupOrganizingPublished\Event\MeetupWasScheduledDto;

/**
 * This creates a projection
 */
final class BillingMeetupRecorder implements ExternalEventConsumer
{
    public function __construct(
        private Connection $connection,
    ) {
    }

    public function whenConsumerRestarted(): void
    {
        $this->connection->executeQuery('DELETE FROM billing_meetups WHERE 1');
    }

    public function whenExternalEventReceived(string $eventType, array $eventData): void
    {
        if ($eventType === MeetupWasScheduledDto::EVENT_NAME) {
            $this->whenMeetupWasScheduled(
                MeetupWasScheduledDto::fromEventData($eventData)
            );
        }

        if ($eventType === 'meetup_organizing.public.meetup.meetup_was_cancelled') {
            $this->whenMeetupWasCancelled($eventData);
        }
    }

    private function whenMeetupWasScheduled(
        MeetupWasScheduledDto $event,
    ): void {
        $this->connection->insert(
            'billing_meetups',
            [
                'organizerId' => $event->organizerId(),
                'meetupId' => $event->meetupId(),
                'year' => $event->scheduledDate()->format('Y'),
                'month' => $event->scheduledDate()->format('n'),
            ]
        );
    }

    private function whenMeetupWasCancelled(
        array $eventData,
    ): void {
        $this->connection->delete(
            'billing_meetups',
            [
                'meetupId' => Mapping::getString($eventData, 'meetupId'),
            ]
        );
    }
}
