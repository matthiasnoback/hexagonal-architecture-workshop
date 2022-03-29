<?php
declare(strict_types=1);

namespace Shared\DTOs\MeetupOrganizing;

use App\Mapping;
use Assert\Assertion;
use DateTimeImmutable;

final class MeetupWasScheduledByOrganizer
{
    public function __construct(
        public readonly int $meetupId,
        public readonly string $organizerId,
        public readonly DateTimeImmutable $scheduledDate,
    ) {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->meetupId,
            'organizerId' => $this->organizerId,
            'scheduledDate' => $this->scheduledDate->format(
                DateTimeImmutable::RFC3339
            )
        ];
    }

    public static function fromArray(array $data): self
    {
        $dateTime = DateTimeImmutable::createFromFormat(
            DateTimeImmutable::RFC3339,
            Mapping::getString($data, 'scheduledDate')
        );
        Assertion::isInstanceOf($dateTime, DateTimeImmutable::class);

        return new self(
            Mapping::getInt($data, 'id'),
            Mapping::getString($data, 'organizerId'),
            $dateTime
        );
    }
}
