<?php
declare(strict_types=1);

namespace Shared;

use App\Mapping;

/**
 * @DTO
 */
final class MeetupWasScheduledData
{
    public const NAME = 'meetup_organizing.meetup.scheduled';

    public function __construct(
        public readonly string $meetupId,
        public readonly string $organizerId,
        public readonly string $scheduledDate,
    ) {
    }

    public static function fromEventData(array $data): self
    {
        return new self(
            Mapping::getString($data, 'meetupId'),
            Mapping::getString($data, 'organizerId'),
            Mapping::getString($data, 'scheduledDate'),
        );
    }

    public function toEventData(): array
    {
        return [
            'meetupId' => $this->meetupId,
            'organizerId' => $this->organizerId,
            'scheduledDate' => $this->scheduledDate,
        ];
    }
}
