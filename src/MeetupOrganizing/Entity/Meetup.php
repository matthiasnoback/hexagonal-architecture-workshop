<?php
declare(strict_types=1);

namespace MeetupOrganizing\Entity;

use App\Entity\UserId;
use App\Mapping;
use Assert\Assertion;

final class Meetup
{
    private UserId $organizerId;
    private string $name;
    private string $description;
    private ScheduledDateTime $scheduledFor;
    private bool $wasCancelled = false;
    private MeetupId $meetupId;

    private function __construct(
        MeetupId $meetupId,
        UserId $organizerId,
        string $name,
        string $description,
        ScheduledDateTime $scheduledFor,
    ) {
        Assertion::notBlank($name, 'Can not schedule a meetup without a name');
        Assertion::notBlank($description, 'Can not schedule a meetup without a description');

        $this->meetupId = $meetupId;
        $this->organizerId = $organizerId;
        $this->name = $name;
        $this->description = $description;
        $this->scheduledFor = $scheduledFor;
    }

    public static function schedule(
        MeetupId $meetupId,
        UserId $organizerId,
        string $name,
        string $description,
        ScheduledDateTime $scheduledFor,
    ): self {
        // TODO check if organizer exists

        Assertion::true($scheduledFor->isInTheFuture(), 'Date should be in the future');

        return new self($meetupId, $organizerId, $name, $description, $scheduledFor);
    }

    /**
     * @param array<string,mixed> $record
     * @can-only-be-called-by(MeetupRepository)
     */
    public static function fromDatabaseRecord(array $record): self
    {
        return new self(
            MeetupId::fromString(Mapping::getString($record, 'meetupId')),
            UserId::fromString(Mapping::getString($record, 'organizerId')),
            Mapping::getString($record, 'name'),
            Mapping::getString($record, 'description'),
            ScheduledDateTime::fromString(Mapping::getString($record, 'scheduledFor')),
        );
    }

    /**
     * @return array<string,string|int|null>
     * @can-only-be-called-by(MeetupRepository)
     */
    public function toDatabaseRecord(): array
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

    public function cancel(UserId $currentUserId): void
    {
        Assertion::true($currentUserId->equals($this->organizerId));

        $this->wasCancelled = true;
    }

    public function reschedule(UserId $currentUserId, ScheduledDateTime $newScheduledFor): void
    {
        Assertion::true($currentUserId->equals($this->organizerId));
        Assertion::false($this->wasCancelled);
        Assertion::true($newScheduledFor->isInTheFuture());
        Assertion::false($this->meetupAlreadyTookPlace());

        $this->scheduledFor = $newScheduledFor;
    }

    private function meetupAlreadyTookPlace(): bool
    {
        return $this->scheduledFor->isInThePast();
    }
}
