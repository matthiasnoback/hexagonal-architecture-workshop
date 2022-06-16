<?php
declare(strict_types=1);

namespace MeetupOrganizing\Entity;

use App\Entity\UserId;
use DateTimeImmutable;
use InvalidArgumentException;

final class Meetup
{
    private function __construct(
        private readonly MeetupId $meetupId,
        private readonly UserId $organizerId,
        private readonly string $name,
        private readonly string $description,
        private readonly DateTimeImmutable $scheduledFor,
    ) {
        if ($name === '') {
            throw new InvalidArgumentException('...');
        }
        if ($description === '') {
            throw new InvalidArgumentException('...');
        }
    }

    public static function schedule(
        MeetupId $meetupId,
        UserId $organizerId,
        string $name,
        string $description,
        DateTimeImmutable $scheduledFor,
    ): self {
        // TODO validate scheduledFor; should be in the future

        return new self(
            $meetupId,
            $organizerId,
            $name,
            $description,
            $scheduledFor,
        );
    }

    /**
     * @return array<string,string|int>
     */
    public function toDatabaseRecord(): array
    {
        return [
            'meetupId' => $this->meetupId->asString(),
            'organizerId' => $this->organizerId->asString(),
            'name' => $this->name,
            'description' => $this->description,
            'scheduledFor' => $this->scheduledFor->format('Y-m-d H:i'),
            'wasCancelled' => 0,
        ];
    }

    public function meetupId(): MeetupId
    {
        return $this->meetupId;
    }
}
