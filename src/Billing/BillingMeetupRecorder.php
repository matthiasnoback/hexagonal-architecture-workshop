<?php
declare(strict_types=1);

namespace Billing;

use App\ExternalEvents\ExternalEventConsumer;
use App\Mapping;
use Assert\Assertion;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\DBAL\Connection;

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
        if ($eventType === 'meetup_organizing.public.meetup.meetup_was_scheduled') {
            $this->whenMeetupWasScheduled($eventData);
        }

        if ($eventType === 'meetup_organizing.public.meetup.meetup_was_cancelled') {
            $this->whenMeetupWasCancelled($eventData);
        }
    }

    private function whenMeetupWasScheduled(
        array $eventData,
    ): void {
        $dateTimeImmutable = DateTimeImmutable::createFromFormat(
            DateTimeInterface::ATOM,
            Mapping::getString($eventData, 'scheduledDate'),
        );
        Assertion::isInstanceOf($dateTimeImmutable, DateTimeImmutable::class);

        $this->connection->insert(
            'billing_meetups',
            [
                'organizerId' => Mapping::getString($eventData, 'organizerId'),
                'meetupId' => Mapping::getString($eventData, 'meetupId'),
                'year' => $dateTimeImmutable->format('Y'),
                'month' => $dateTimeImmutable->format('n'),
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
