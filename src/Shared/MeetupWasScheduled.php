<?php
declare(strict_types=1);

namespace Shared;

use App\Mapping;
use Assert\Assertion;
use DateTimeImmutable;
use DateTimeInterface;

final class MeetupWasScheduled
{
    public const EVENT_TYPE = 'public.meetup_organizing.meetup_was_scheduled';

    public function __construct(
        public readonly string $meetupId,
        public readonly string $organizerId,
        public readonly DateTimeImmutable $scheduledFor,
    ) {
    }

    public static function fromPayload(array $data): self
    {
        $scheduledFor = DateTimeImmutable::createFromFormat(
            DateTimeInterface::ATOM,
            Mapping::getString($data, 'scheduledDate'),
        );
        Assertion::isInstanceOf($scheduledFor, DateTimeImmutable::class);

        return new self(
            Mapping::getString($data, 'meetupId'),
            Mapping::getString($data, 'organizerId'),
            $scheduledFor,
        );
    }

    public function toJsonSerializableArray(): array
    {
        return [
            'meetupId' => $this->meetupId,
            'organizerId' => $this->organizerId,
            'scheduledDate' => $this->scheduledFor->format(DateTimeInterface::ATOM),
        ];
    }
}
