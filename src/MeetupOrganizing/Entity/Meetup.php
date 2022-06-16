<?php
declare(strict_types=1);

namespace MeetupOrganizing\Entity;

use App\Entity\UserId;
use DateTimeImmutable;
use InvalidArgumentException;

final class Meetup
{
    private bool $wasCancelled = false;

    private function __construct(
        private readonly MeetupId $meetupId,
        private readonly UserId $organizerId,
        private readonly string $name,
        private readonly string $description,
        private readonly DateTimeImmutable $scheduledFor,
    ) {
        if ($name === '') {
            throw new InvalidArgumentException('...');
        }
        if ($description === '') {
            throw new InvalidArgumentException('...');
        }
    }

    public static function fromDatabaseRecord(array $record): self
    {
        $scheduledFor = DateTimeImmutable::createFromFormat('Y-m-d H:i', $record['scheduledFor']);
        if ($scheduledFor === false) {
            throw new InvalidArgumentException('...');
        }

        return new self(
            MeetupId::fromString($record['meetupId']),
            UserId::fromString($record['organizerId']),
            $record['name'],
            $record['description'],
            $scheduledFor
        );
    }

    public static function schedule(
        MeetupId $meetupId,
        UserId $organizerId,
        string $name,
        string $description,
        DateTimeImmutable $scheduledFor,
    ): self {
        // TODO validate scheduledFor; should be in the future

        return new self(
            $meetupId,
            $organizerId,
            $name,
            $description,
            $scheduledFor,
        );
    }

    /**
     * @return array<string,string|int>
     */
    public function toDatabaseRecord(): array
    {
        return [
            'meetupId' => $this->meetupId->asString(),
            'organizerId' => $this->organizerId->asString(),
            'name' => $this->name,
            'description' => $this->description,
            'scheduledFor' => $this->scheduledFor->format('Y-m-d H:i'),
            'wasCancelled' => (int) $this->wasCancelled,
        ];
    }

    public function meetupId(): MeetupId
    {
        return $this->meetupId;
    }

    public function organizerId(): UserId
    {
        return $this->organizerId;
    }

    public function cancel(UserId $userId): void
    {
        if (! $this->organizerId()->equals($userId)) {
            throw new \Exception('...');
        }

        $this->wasCancelled = true;
    }
}
