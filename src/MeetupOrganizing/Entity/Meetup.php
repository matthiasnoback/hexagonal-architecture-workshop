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
    private string $scheduledFor;

    private bool $wasCancelled;

    private MeetupId $meetupId;

    private function __construct(MeetupId $meetupId, UserId $organizerId, string $name, string $description, string $scheduledFor, bool $wasCancelled)
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

    public static function schedule(MeetupId $meetupId, UserId $organizerId, string $name, string $description, string $scheduledFor): self
    {
        // checks for "scheduling"
        // date should be in the future

        return new self($meetupId, $organizerId, $name, $description, $scheduledFor, false);
    }

    public static function fromRecord(array $record): self
    {
        return new self(
            MeetupId::fromString($record['meetupId']),
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
    public function toRecord(): array
    {
        return [
            'meetupId' => $this->meetupId->asString(),
            'organizerId' => $this->organizerId->asString(),
            'name' => $this->name,
            'description' => $this->description,
            'scheduledFor' => $this->scheduledFor,
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

    public function reschedule(string $scheduleFor, UserId $userId): void
    {
        Assertion::true($userId->equals($this->organizerId));

        $this->scheduledFor = $scheduleFor;
    }
}
