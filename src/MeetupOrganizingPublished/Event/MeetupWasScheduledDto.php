<?php
declare(strict_types=1);

namespace MeetupOrganizingPublished\Event;

use App\Mapping;
use Assert\Assertion;
use DateTimeImmutable;
use DateTimeInterface;

final class MeetupWasScheduledDto
{
    public const EVENT_NAME = 'meetup_organizing.public.meetup.meetup_was_scheduled';

    public function __construct(
        private readonly string $meetupId,
        private readonly string $organizerId,
        private readonly string $scheduledDate,
    ) {
    }

    public static function fromEventData(
        array $eventData,
    ): self {
        return new self(
            Mapping::getString($eventData, 'meetupId'),
            Mapping::getString($eventData, 'organizerId'),
            Mapping::getString($eventData, 'scheduledDate'),
        );
    }

    public function eventData(): array
    {
        return [
            'meetupId' => $this->meetupId,
            'organizerId' => $this->organizerId,
            'scheduledDate' => $this->scheduledDate,
        ];
    }

    public function meetupId(): string
    {
        return $this->meetupId;
    }

    public function organizerId(): string
    {
        return $this->organizerId;
    }

    public function scheduledDate(): DateTimeImmutable
    {
        $dateTimeImmutable = DateTimeImmutable::createFromFormat(
            DateTimeInterface::ATOM,
            $this->scheduledDate,
        );
        Assertion::isInstanceOf(
            $dateTimeImmutable,
            DateTimeImmutable::class
        );

        return $dateTimeImmutable;
    }

    public function eventName(): string
    {
        return self::EVENT_NAME;
    }
}
