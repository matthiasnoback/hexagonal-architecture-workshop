<?php
declare(strict_types=1);

namespace MeetupOrganizing\Entity;

use App\Entity\UserId;
use App\Mapping;
use Assert\Assert;
use MeetupOrganizing\Infrastructure\MeetupsTable;

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
        ScheduledDate $scheduledFor
    ): self {
        Assert::that($name)->notBlank();
        Assert::that($description)->notBlank();

        return new self(
            $meetupId,
            $organizerId,
            $name,
            $description,
            $scheduledFor,
            false
        );
    }

    public static function fromDatabaseRecord(array $record): self
    {
        return new self(
            MeetupId::fromString(Mapping::getString($record, MeetupsTable::MEETUP_ID_COLUMN)),
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
            MeetupsTable::MEETUP_ID_COLUMN => $this->meetupId->asString(),
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
        $this->wasCancelled = true;
    }

    public function organizerId(): UserId
    {
        return $this->organizerId;
    }
}

