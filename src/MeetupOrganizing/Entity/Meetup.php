<?php
declare(strict_types=1);

namespace MeetupOrganizing\Entity;

use App\Entity\UserId;
use App\Mapping;
use Assert\Assertion;
use MeetupOrganizing\ValueObjects\ScheduledDate;

final class Meetup
{
    private function __construct(
        private readonly MeetupId $meetupId,
        private readonly UserId $organizerId,
        private readonly string $name,
        private readonly string $description,
        private ScheduledDate $scheduledDateTime,
        private bool $wasCancelled = false,
    )
    {
        Assertion::notEmpty($this->name, 'Name cannot be empty');
        Assertion::notEmpty($this->description, 'Description cannot be empty');
    }

    public static function schedule(
        MeetupId $meetupId,
        UserId $organizerId,
        string $name,
        string $description,
        ScheduledDate $scheduledDateTime
    ): self {
        if ($scheduledDateTime->inThePast()) {
            throw CouldNotScheduleMeetup::becauseTheDateIsInThePast();
        }

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
            ScheduledDate::create($record['scheduledFor']),
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
            'scheduledFor' => $this->scheduledDateTime->asString(),
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

    public function reschedule(UserId $currentUserId, ScheduledDate $dateAndTime): void
    {
        Assertion::true($this->organizerId->equals($currentUserId));
        Assertion::false($this->wasCancelled, 'Meetup cannot be rescheduled because it was cancelled');
        Assertion::false($this->scheduledDateTime->inThePast(), 'Meetup cannot be rescheduled because it already took place');
        Assertion::false($dateAndTime->inThePast(), 'Meetup cannot be rescheduled because the new date is in the past');

        $this->scheduledDateTime = $dateAndTime;
    }
}
