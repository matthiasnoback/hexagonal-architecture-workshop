<?php
declare(strict_types=1);

namespace MeetupOrganizing\Entity;

use App\Entity\UserId;
use App\Mapping;
use Assert\Assertion;
use DateTimeImmutable;

final class Meetup
{
    private DateTimeImmutable $scheduledDateTime;

    private function __construct(
        private readonly MeetupId $meetupId,
        private readonly UserId $organizerId,
        private readonly string $name,
        private readonly string $description,
        string $scheduledDateTime,
        private bool $wasCancelled = false,
    )
    {
        Assertion::notEmpty($this->name, 'Name cannot be empty');
        Assertion::notEmpty($this->description, 'Description cannot be empty');

        $scheduledDateTime = DateTimeImmutable::createFromFormat(
            'Y-m-d H:i',
            $scheduledDateTime
        );
        Assertion::isInstanceOf($scheduledDateTime, DateTimeImmutable::class);
        $this->scheduledDateTime = $scheduledDateTime;
    }

    public static function schedule(
        MeetupId $meetupId,
        UserId $organizerId,
        string $name,
        string $description,
        string $scheduledDateTime
    ): self {
        return new self($meetupId, $organizerId, $name, $description, $scheduledDateTime);
    }

    public static function fromArray(array $record): self
    {
        return new self(
            MeetupId::fromString(
                Mapping::getString($record, 'meetupId')
            ),
            UserId::fromString($record['organizerId']),
            $record['name'],
            $record['description'],
            $record['scheduledFor'],
            (bool) $record['wasCancelled'],
        );
    }

    /**
     * @return array<string,string|int>
     */
    public function toArray(): array
    {
        return [
            'meetupId' => $this->meetupId->asString(),
            'organizerId' => $this->organizerId->asString(),
            'name' => $this->name,
            'description' => $this->description,
            'scheduledFor' => $this->scheduledDateTime
                ->format('Y-m-d H:i'),
            'wasCancelled' => (int) $this->wasCancelled,
        ];
    }

    public function meetupId(): MeetupId
    {
        return $this->meetupId;
    }

    public function cancel(UserId $currentUserId): void
    {
        Assertion::true($this->organizerId->equals($currentUserId));

        $this->wasCancelled = true;
    }
}
