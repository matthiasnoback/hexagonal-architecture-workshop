<?php
declare(strict_types=1);

namespace MeetupOrganizing\Entity;

use App\Entity\UserId;
use App\Mapping;
use Assert\Assertion;
use MeetupOrganizing\Domain\Model\Meetup\ScheduledDate;
use Webmozart\Assert\Assert;

final class Meetup
{
    private function __construct(
        public readonly MeetupId $meetupId,
        private readonly UserId $organizerId,
        private readonly string $name,
        private readonly string $description,
        private ScheduledDate $scheduledFor,
        private bool $wasCancelled = false,
    ) {
        Assert::notEq($name, '');
        Assert::notEq($description, '');
    }

    public static function schedule(
        MeetupId $meetupId,
        UserId $organizerId,
        string $name,
        string $description,
        ScheduledDate $scheduledFor,
    ): self
    {
        return new self(
            $meetupId,
            $organizerId,
            $name,
            $description,
            $scheduledFor
        );
    }

    public function cancel(UserId $cancelledBy): void
    {
        Assertion::true($cancelledBy->equals($this->organizerId));
        if ($this->wasCancelled) {
            return;
        }

        $this->wasCancelled = true;
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

    /**
     * Command method on entity:
     * - Return early
     * - Throw exception
     * - Record any number of events
     * - State change
     */
    public function reschedule(UserId $userId, ScheduledDate $newDateTime): void
    {
        Assertion::true($userId->equals($this->organizerId));

        if ($newDateTime->equals($this->scheduledFor)) {
            return;
        }

        $this->scheduledFor = $newDateTime;
    }
}
