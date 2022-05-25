<?php
declare(strict_types=1);

namespace MeetupOrganizing\Entity;

use App\Entity\UserId;
use App\Mapping;
use Assert\Assert;
use DateTimeImmutable;
use LogicException;
use RuntimeException;

final class Meetup
{
    private function __construct(
        private MeetupId $meetupId,
        private UserId $organizerId,
        private string $name,
        private string $description,
        private ScheduledDate $scheduledFor,
        private bool $wasCancelled,
    ) {
    }

    public static function schedule(
        MeetupId $meetupId,
        UserId $organizerId,
        string $name,
        string $description,
        ScheduledDate $scheduledFor,
    ): self {
        Assert::that($name)->notBlank();
        Assert::that($description)->notBlank();

        if ($scheduledFor->isInThePast(new DateTimeImmutable('now'))) {
            throw CouldNotScheduleMeetup::becauseTheDateIsInThePast();
        }

        return new self($meetupId, $organizerId, $name, $description, $scheduledFor, false);
    }

    public static function fromDatabaseRecord(array $record): self
    {
        return new self(
            MeetupId::fromString(Mapping::getString($record, 'meetupId')),
            UserId::fromString(Mapping::getString($record, 'organizerId')),
            Mapping::getString($record, 'name'),
            Mapping::getString($record, 'description'),
            ScheduledDate::fromString(Mapping::getString($record, 'scheduledFor')),
            (bool) Mapping::getInt($record, 'wasCancelled'),
        );
    }

    /**
     * @return array<string,string|int>
     */
    public function asDatabaseRecord(): array
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

    public function cancel(UserId $userId): void
    {
        if (!$userId->equals($this->organizerId)) {
            throw new RuntimeException('Only the organizer can cancel');
        }

        $this->wasCancelled = true;
    }

    public function reschedule(ScheduledDate $newDate, UserId $userId, DateTimeImmutable $now): void
    {
        if ($this->wasCancelled) {
            throw new LogicException('Can not reschedule a cancelled meetup');
        }

        if ($newDate->isInThePast($now)) {
            throw new LogicException('Can not reschedule a meetup that already took place');
        }

        if (!$userId->equals($this->organizerId)) {
            throw new RuntimeException('Only the organizer can cancel');
        }

        $this->scheduledFor = $newDate;
    }
}
