<?php
declare(strict_types=1);

namespace Billing;

use App\ExternalEvents\ExternalEventConsumer;
use Assert\Assertion;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\DBAL\Connection;

final class KeepTrackOfMeetupsForBilling implements ExternalEventConsumer
{
    public function __construct(private Connection $connection)
    {
    }

    public function whenConsumerRestarted(): void
    {
        $this->connection->executeQuery('DELETE FROM billing_meetups WHERE 1');
    }

    public function whenExternalEventReceived(string $eventType, array $eventData): void
    {
        if ($eventType === 'meetup_organizing.meetup.was_scheduled') {
            $this->whenMeetupWasScheduled($eventData);
        } elseif ($eventType === 'meetup_organizing.meetup.was_cancelled') {
            $this->whenMeetupWasCancelled($eventData);
        }
    }

    private function whenMeetupWasScheduled(array $event): void
    {
        $scheduledDate = DateTimeImmutable::createFromFormat(DateTimeInterface::ATOM, $event['scheduledDate']);

        Assertion::isInstanceOf($scheduledDate, DateTimeImmutable::class);

        $this->connection->insert(
            'billing_meetups',
            [
                'meetupId' => $event['meetupId'],
                'month' => $scheduledDate->format('n'),
                'year' => $scheduledDate->format('Y'),
                'organizerId' => $event['organizerId'],
            ]
        );
    }

    private function whenMeetupWasCancelled(array $event): void
    {
        $this->connection->delete(
            'billing_meetups',
            [
                'meetupId' => $event['meetupId'],
            ]
        );
    }
}
