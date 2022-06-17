<?php
declare(strict_types=1);

namespace MeetupOrganizing\Entity;

use App\Entity\UserId;
use InvalidArgumentException;

final class Meetup
{
    private bool $wasCancelled = false;

    private function __construct(
        private readonly MeetupId $meetupId,
        private readonly UserId $organizerId,
        private readonly string $name,
        private readonly string $description,
        private ScheduledDate $scheduledFor,
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
        return new self(
            MeetupId::fromString($record['meetupId']),
            UserId::fromString($record['organizerId']),
            $record['name'],
            $record['description'],
            ScheduledDate::fromString($record['scheduledFor'])
        );
    }

    public static function schedule(
        MeetupId $meetupId,
        UserId $organizerId,
        string $name,
        string $description,
        ScheduledDate $scheduledFor,
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
            'scheduledFor' => $this->scheduledFor->asString(),
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

        if ($this->wasCancelled) {
            throw new \Exception('This meetup was already cancelled');
        }

        $this->wasCancelled = true;
    }

    public function reschedule(UserId $userId, ScheduledDate $newScheduledForDate): void
    {
        if (! $this->organizerId()->equals($userId)) {
            throw new \Exception('...');
        }

        if ($this->wasCancelled) {
            throw new \Exception('This meetup was already cancelled');
        }

        if ($this->scheduledFor->hasAlreadyPassed()) {
            throw new \Exception('This meetup was already took place');
        }

        if ($newScheduledForDate->hasAlreadyPassed()) {
            throw new \Exception('The new meetup date has already passed');
        }

        // if the date is different, record event "meetup was rescheduled"

        $this->scheduledFor = $newScheduledForDate;
    }
}
