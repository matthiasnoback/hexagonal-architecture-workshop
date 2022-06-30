<?php
declare(strict_types=1);

namespace MeetupOrganizing\Entity;

use App\Application;
use App\Entity\UserId;
use App\Mapping;
use Assert\Assertion;
use DateTimeImmutable;

final class Meetup
{
    private UserId $organizerId;
    private string $name;
    private string $description;
    private DateTimeImmutable $scheduledFor;
    private bool $wasCancelled = false;
    private MeetupId $meetupId;

    private function __construct(
        MeetupId $meetupId,
        UserId $organizerId,
        string $name,
        string $description,
        DateTimeImmutable $scheduledFor,
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
        DateTimeImmutable $scheduledFor,
    ): self {
        // TODO check if organizer exists
        // TODO check if date is in the future

        return new self($meetupId, $organizerId, $name, $description, $scheduledFor);
    }

    /**
     * @param array<string,mixed> $record
     * @can-only-be-called-by(MeetupRepository)
     */
    public static function fromDatabaseRecord(array $record): self
    {
        $scheduledFor = DateTimeImmutable::createFromFormat(
            Application::DATE_TIME_FORMAT,
            Mapping::getString($record, 'scheduledFor'),
        );
        Assertion::isInstanceOf($scheduledFor, DateTimeImmutable::class);

        return new self(
            MeetupId::fromString(Mapping::getString($record, 'meetupId')),
            UserId::fromString(Mapping::getString($record, 'organizerId')),
            Mapping::getString($record, 'name'),
            Mapping::getString($record, 'description'),
            $scheduledFor,
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
            'scheduledFor' => $this->scheduledFor->format(Application::DATE_TIME_FORMAT),
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
}
