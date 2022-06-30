<?php
declare(strict_types=1);

namespace MeetupOrganizing\Entity;

use App\Entity\UserId;
use App\Mapping;
use Assert\Assert;
use DateTimeImmutable;
use DomainException;

/**
 * @object-type Entity
 */
final class Meetup
{
    private function __construct(
        private readonly MeetupId $meetupId,
        private readonly UserId $organizerId,
        private readonly string $name,
        private readonly string $description,
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
        DateTimeImmutable $now
    ): self {
        Assert::that($name)->notBlank();
        Assert::that($description)->notBlank();

        if ($scheduledFor->isBefore($now)) {
            throw new DomainException('Can\'t plan a meetup in the past');
        }

        return new Meetup(
            $meetupId,
            $organizerId,
            $name,
            $description,
            $scheduledFor,
            false
        );
    }

    public function asRecord(): array
    {
        return [
            'meetupId' => $this->meetupId->asString(),
            'organizerId' => $this->organizerId->asString(),
            'name' => $this->name,
            'description' => $this->description,
            'scheduledFor' => $this->scheduledFor->toString(),
            'wasCancelled' => (int)$this->wasCancelled,
        ];
    }

    /**
     * @param array<string,mixed> $record
     */
    public static function fromRecord(array $record): self
    {
        return new self(
            MeetupId::fromString(
                Mapping::getString($record, 'meetupId')
            ),
            UserId::fromString(Mapping::getString($record, 'organizerId')),
            Mapping::getString($record, 'name'),
            Mapping::getString($record, 'description'),
            ScheduledDate::fromString(Mapping::getString($record, 'scheduledFor')),
            (bool) Mapping::getInt($record, 'wasCancelled')
        );
    }

    public function cancel(UserId $organizerId): void
    {
        if (!$this->organizerId->equals($organizerId)) {
            throw new DomainException('Only the organizer can cancel the meetup');
        }

        $this->wasCancelled = true;
    }

    public function meetupId(): MeetupId
    {
        return $this->meetupId;
    }

    public function reschedule(UserId $userId, ScheduledDate $newDate, DateTimeImmutable $now): void
    {
        $this->assertOrganizedBy($userId);

        if ($this->wasCancelled) {
            throw new DomainException('Can\'t reschedule a cancelled meetup');
        }

        if ($this->scheduledFor->isBefore($now)) {
            throw new DomainException('The meetup already happened');
        }

        $this->scheduledFor = $newDate;
    }

    private function assertOrganizedBy(UserId $userId): void
    {
        if (!$this->organizerId->equals($userId)) {
            throw new DomainException('Only the organizer can reschedule the meetup');
        }
    }
}
