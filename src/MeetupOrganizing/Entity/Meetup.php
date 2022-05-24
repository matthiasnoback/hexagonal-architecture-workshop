<?php
declare(strict_types=1);

namespace MeetupOrganizing\Entity;

use Assert\Assert;

final class Meetup
{
    const SCHEDULED_FOR_FORMAT = 'Y-m-d H:i';
    private int $meetupId;

    private function __construct(
        private string $organizerId,
        private string $name,
        private string $description,
        private \DateTimeImmutable $scheduledFor,
    ) {
    }

    public static function schedule(
        string $organizerId,
        string $name,
        string $description,
        string $scheduledFor,
    ): self {
        Assert::that($organizerId)->uuid();
        Assert::that($name)->notBlank();
        Assert::that($description)->notBlank();

        $dt = \DateTimeImmutable::createFromFormat(self::SCHEDULED_FOR_FORMAT, $scheduledFor);
        Assert::that($dt)->isInstanceOf(\DateTimeImmutable::class);

        return new self($organizerId, $name, $description, $dt);
    }

    /**
     * @internal Only to be used by MeetupRepository
     */
    public function setMeetupId(int $meetupId): void
    {
        $this->meetupId = $meetupId;
    }

    /**
     * @return array<string,string|int>
     */
    public function asDatabaseRecord(): array
    {
        return [
            'organizerId' => $this->organizerId,
            'name' => $this->name,
            'description' => $this->description,
            'scheduledFor' => $this->scheduledFor->format(self::SCHEDULED_FOR_FORMAT),
            'wasCancelled' => 0,
        ];
    }

    public function meetupId(): int
    {
        return $this->meetupId;
    }
}
