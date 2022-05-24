<?php
declare(strict_types=1);

namespace MeetupOrganizing\Entity;

use Assert\Assert;

final class Meetup
{
    const SCHEDULED_FOR_FORMAT = 'Y-m-d H:i';

    private function __construct(
        private MeetupId $meetupId,
        private string $organizerId,
        private string $name,
        private string $description,
        private \DateTimeImmutable $scheduledFor,
    ) {
    }

    public static function schedule(
        MeetupId $meetupId,
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

        return new self($meetupId, $organizerId, $name, $description, $dt);
    }

    /**
     * @return array<string,string|int>
     */
    public function asDatabaseRecord(): array
    {
        return [
            'meetupId' => $this->meetupId->asString(),
            'organizerId' => $this->organizerId,
            'name' => $this->name,
            'description' => $this->description,
            'scheduledFor' => $this->scheduledFor->format(self::SCHEDULED_FOR_FORMAT),
            'wasCancelled' => 0,
        ];
    }

    public function meetupId(): MeetupId
    {
        return $this->meetupId;
    }
}
