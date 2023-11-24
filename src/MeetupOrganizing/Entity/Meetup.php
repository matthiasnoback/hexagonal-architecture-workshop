<?php

namespace MeetupOrganizing\Entity;

use App\Entity\UserId;
use Assert\Assertion;

/**
 * This is *the* write model for meetups
 *
 * Object type: Entity
 */
final class Meetup
{
    private UserId $organizerId;
    private string $name;
    private string $description;
    private ScheduledDate $scheduledFor;
    private bool $wasCancelled;
    private MeetupId $meetupId;

    private function __construct(
        MeetupId      $meetupId,
        UserId        $organizerId,
        string        $name,
        string        $description,
        ScheduledDate $scheduledFor,
        bool          $wasCancelled
    )
    {
        if ($name === '') {
            throw new \InvalidArgumentException();
        }
        if ($description === '') {
            throw new \InvalidArgumentException();
        }

        $this->meetupId = $meetupId;
        $this->organizerId = $organizerId;
        $this->name = $name;
        $this->description = $description;
        $this->scheduledFor = $scheduledFor;
        $this->wasCancelled = $wasCancelled;
    }

    public static function schedule(
        MeetupId      $meetupId,
        UserId        $organizerId,
        string        $name,
        string        $description,
        ScheduledDate $scheduledFor,
        \DateTimeInterface $now,
    ): self
    {
        if ($scheduledFor->hasPassed($now)) {
            throw CouldNotScheduleMeetup::becauseTheDateIsInThePast();
        }

        return new self(
            $meetupId,
            $organizerId,
            $name,
            $description,
            $scheduledFor,
            false
        );
    }

    public static function fromRecord(array $record): self
    {
        return new self(
            MeetupId::fromString($record['meetupId']),
            UserId::fromString($record['organizerId']),
            $record['name'],
            $record['description'],
            ScheduledDate::fromString($record['scheduledFor']),
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
            'wasCancelled' => (int)$this->wasCancelled,
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

    public function reschedule(ScheduledDate $scheduleFor, UserId $userId, \DateTimeInterface $now): void
    {
        Assertion::true($userId->equals($this->organizerId));

        if ($this->wasCancelled) {
            throw CouldNotRescheduleMeetup::becauseTheMeetupWasCancelled();
        }

        if ($this->scheduledFor->hasPassed($now)) {
            throw CouldNotRescheduleMeetup::becauseTheMeetupAlreadyTookPlace();
        }

        $this->scheduledFor = $scheduleFor;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
