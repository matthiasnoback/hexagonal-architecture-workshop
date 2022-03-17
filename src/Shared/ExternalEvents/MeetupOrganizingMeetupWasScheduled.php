<?php
declare(strict_types=1);

namespace Shared\ExternalEvents;

use App\Mapping;

final class MeetupOrganizingMeetupWasScheduled
{
    public const NAME = 'meetup.scheduled';

    public function __construct(
        private int $meetupId,
        private string $organizerId,
        private string $scheduledDate,
    ) {
    }

    public function name(): string
    {
        return self::NAME;
    }

    public function meetupId(): int
    {
        return $this->meetupId;
    }

    public function organizerId(): string
    {
        return $this->organizerId;
    }

    public function scheduledDate(): string
    {
        return $this->scheduledDate;
    }

    public static function fromArray(array $eventData): self
    {
        return new self(
            Mapping::getInt($eventData, 'meetupId'),
            Mapping::getString($eventData, 'organizerId'),
            Mapping::getString($eventData, 'scheduledDate'),
        );
    }

    public function toArray(): array
    {
        return [
            'meetupId' => $this->meetupId,
            'organizerId' => $this->organizerId,
            'scheduledDate' => $this->scheduledDate,
        ];
    }
}
