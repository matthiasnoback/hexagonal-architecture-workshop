<?php
declare(strict_types=1);

namespace MeetupOrganizing\Entity;

use App\Entity\UserId;
use App\Mapping;
use Assert\Assert;
use DateTimeImmutable;
use RuntimeException;

final class Meetup
{
    const SCHEDULED_FOR_FORMAT = 'Y-m-d H:i';

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
        string $scheduledFor,
    ): self {
        Assert::that($name)->notBlank();
        Assert::that($description)->notBlank();

        $dt = DateTimeImmutable::createFromFormat(self::SCHEDULED_FOR_FORMAT, $scheduledFor);
        Assert::that($dt)->isInstanceOf(DateTimeImmutable::class);

        return new self($meetupId, $organizerId, $name, $description, $dt, false);
    }

    public static function fromDatabaseRecord(array $record): self
    {
        $dt = DateTimeImmutable::createFromFormat(
            self::SCHEDULED_FOR_FORMAT,
            Mapping::getString($record, 'scheduledFor')
        );
        Assert::that($dt)->isInstanceOf(DateTimeImmutable::class);

        return new self(
            MeetupId::fromString(Mapping::getString($record, 'meetupId')),
            UserId::fromString(Mapping::getString($record, 'organizerId')),
            Mapping::getString($record, 'name'),
            Mapping::getString($record, 'description'),
            $dt,
            (bool) Mapping::getInt($record, 'wasCancelled'),
        );
    }

    /**
     * @return array<string,string|int>
     */
    public function asDatabaseRecord(): array
    {
        return [
            'meetupId' => $this->meetupId->asString(),
            'organizerId' => $this->organizerId->asString(),
            'name' => $this->name,
            'description' => $this->description,
            'scheduledFor' => $this->scheduledFor->format(self::SCHEDULED_FOR_FORMAT),
            'wasCancelled' => (int) $this->wasCancelled,
        ];
    }

    public function meetupId(): MeetupId
    {
        return $this->meetupId;
    }

    public function cancel(UserId $userId): void
    {
        if (!$userId->equals($this->organizerId)) {
            throw new RuntimeException('Only the organizer can cancel');
        }

        $this->wasCancelled = true;
    }
}
