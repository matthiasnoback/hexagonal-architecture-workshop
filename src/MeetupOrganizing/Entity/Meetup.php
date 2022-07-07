<?php
declare(strict_types=1);

namespace MeetupOrganizing\Entity;

use App\Entity\UserId;
use Assert\Assert;
use DateTimeImmutable;

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
            'scheduledFor' => $this->scheduledFor->format('Y-m-d H:i'),
            'wasCancelled' => (int) $this->wasCancelled,
        ];
    }

    public function meetupId(): MeetupId
    {
        return $this->meetupId;
    }
}

