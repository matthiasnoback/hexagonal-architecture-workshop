<?php
declare(strict_types=1);

namespace Shared;

use Assert\Assertion;
use DateTimeImmutable;
use DateTimeInterface;

final class MeetupWasScheduledData implements PublishedExternalEvent
{
    private const EVENT_TYPE = 'meetup.scheduled';

    public function __construct(
        public readonly string $organizerId,
        public readonly string $meetupId,
        public readonly DateTimeImmutable $scheduledDate,
    ) {
    }

    public static function eventType(): string
    {
        return self::EVENT_TYPE;
    }

    public static function fromArray(array $eventData): static
    {
        $scheduledDate = DateTimeImmutable::createFromFormat(
            DateTimeInterface::ISO8601,
            $eventData['scheduledDate']
        );
        Assertion::isInstanceOf($scheduledDate, DateTimeImmutable::class);

        return new self(
            $eventData['organizerId'],
            $eventData['meetupId'],
            $scheduledDate,
        );
    }

    public function toArray(): array
    {
        return [
            'organizerId' => $this->organizerId,
            'meetupId' => $this->meetupId,
            'scheduledDate' => $this->scheduledDate->format(
                DateTimeInterface::ISO8601
            ),
        ];
    }
}
