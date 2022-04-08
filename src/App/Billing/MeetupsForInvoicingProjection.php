<?php
declare(strict_types=1);

namespace App\Billing;

use App\ExternalEvents\ExternalEventConsumer;
use Doctrine\DBAL\Connection;
use Shared\MeetupWasScheduled;

final class MeetupsForInvoicingProjection implements ExternalEventConsumer
{
    public function __construct(
        private Connection $connection
    ) {
    }

    public function whenConsumerRestarted(): void
    {
        $this->connection->executeQuery('DELETE FROM billing_meetups WHERE 1');
    }

    public function whenExternalEventReceived(string $eventType, array $eventData): void
    {
        if ($eventType === \Shared\MeetupWasScheduled::EVENT_TYPE) {
            $this->whenMeetupWasScheduled(MeetupWasScheduled::fromPayload($eventData));
        } elseif ($eventType === 'public.meetup_organizing.meetup_was_cancelled') {
            $this->whenMeetupWasCancelled($eventData);
        }
    }

    public function whenMeetupWasScheduled(
        MeetupWasScheduled $event,
    ): void {
        $this->connection->insert(
            'billing_meetups',
            [
                'organizerId' => $event->organizerId,
                'meetupId' => $event->meetupId,
                'year' => (int) $event->scheduledFor->format('Y'),
                'month' => (int) $event->scheduledFor->format('m'),
            ]
        );
    }

    public function whenMeetupWasCancelled(
        array $eventData
    ): void {
        $this->connection->delete(
            'billing_meetups',
            [
                'meetupId' => $eventData['meetupId'],
            ]
        );
    }
}
