<?php
declare(strict_types=1);

namespace MeetupOrganizing\Entity;

use App\Entity\User;
use App\Entity\UserId;
use App\Mapping;
use Assert\Assert;
use DateTimeImmutable;
use MeetupOrganizing\Infrastructure\MeetupsTable;

final class Meetup
{

    private function __construct(
        private MeetupId $meetupId,
        private UserId $organizerId,
        private string $name,
        private string $description,
        private DateTimeImmutable $scheduledFor,
        private bool $wasCancelled,
    ) {
    }

    public static function schedule(
        MeetupId $meetupId,
        UserId $organizerId,
        string $name,
        string $description,
        DateTimeImmutable $scheduledFor
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
        $scheduledFor = DateTimeImmutable::createFromFormat(
            'Y-m-d H:i',
            Mapping::getString($record, 'scheduledFor')
        );
        Assert::that($scheduledFor)->isInstanceOf(DateTimeImmutable::class);

        return new self(
            MeetupId::fromString(Mapping::getString($record, MeetupsTable::MEETUP_ID_COLUMN)),
            UserId::fromString(Mapping::getString($record, 'organizerId')),
            Mapping::getString($record, 'name'),
            Mapping::getString($record, 'description'),
            $scheduledFor,
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
            'scheduledFor' => $this->scheduledFor->format('Y-m-d H:i'),
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

