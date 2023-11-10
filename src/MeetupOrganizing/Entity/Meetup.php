<?php

namespace MeetupOrganizing\Entity;

use App\Entity\UserId;
use Assert\Assert;
use Assert\Assertion;

/**
 * Entity
 */
class Meetup
{
    private UserId $organizerId;

    private string $name;
    private string $description;
    private ScheduledDate $scheduledFor;

    private bool $wasCancelled;

    private MeetupId $meetupId;

    private function __construct(MeetupId $meetupId, UserId $organizerId, string $name, string $description, ScheduledDate $scheduledFor, bool $wasCancelled)
    {
        Assertion::notBlank($name);
        Assertion::notBlank($description);
        Assertion::notBlank($scheduledFor);

        $this->meetupId = $meetupId;
        $this->organizerId = $organizerId;
        $this->name = $name;
        $this->description = $description;
        $this->scheduledFor = $scheduledFor;
        $this->wasCancelled = $wasCancelled;
    }

    public static function schedule(MeetupId $meetupId, UserId $organizerId, string $name, string $description, ScheduledDate $scheduledFor, \DateTimeImmutable $now): self
    {
        // checks for "scheduling"
        // date should be in the future

        if ($scheduledFor->isInThePast($now)) {
            throw CanNotScheduleMeetup::becauseTheDateIsInThePast();
        }

        return new self($meetupId, $organizerId, $name, $description, $scheduledFor, false);
    }

    public static function fromRecord(array $record): self
    {
        return new self(
            MeetupId::fromString($record['meetupId']),
            UserId::fromString($record['organizerId']),
            $record['name'],
            $record['description'],
            ScheduledDate::createWithFormat($record['scheduledFor']),
            (bool) $record['wasCancelled'],
        );
    }

    /**
     * @return array<string,string|int>
     */
    public function toRecord(): array
    {
        return [
            'meetupId' => $this->meetupId->asString(),
            'organizerId' => $this->organizerId->asString(),
            'name' => $this->name,
            'description' => $this->description,
            'scheduledFor' => $this->scheduledFor->toString(),
            'wasCancelled' => (int) $this->wasCancelled,
        ];
    }

    public function meetupId(): MeetupId
    {
        return $this->meetupId;
    }

    public function cancel(): void
    {
        // TODO validation

        $this->wasCancelled = true;
    }

    public function reschedule(ScheduledDate $scheduleFor, UserId $userId, \DateTimeImmutable $now): void
    {
        // assertions: for the arguments on their own
        // ...

        // runtime checks
        if (!$userId->equals($this->organizerId)) {
            throw CanNotRescheduleMeetup::becauseCurrentUserIsNotTheOrganizer();
        }

        if ($this->wasCancelled) {
            throw CanNotRescheduleMeetup::becauseItWasCancelled();
        }

        if ($this->scheduledFor->isInThePast($now)) {
            throw CanNotRescheduleMeetup::becauseItAlreadyTookPlace();
        }

        $this->scheduledFor = $scheduleFor;
    }
}
