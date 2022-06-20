<?php
declare(strict_types=1);

namespace MeetupOrganizing\Entity;

use App\Entity\UserId;
use Webmozart\Assert\Assert;

final class Meetup
{
    private function __construct(
        public readonly MeetupId $meetupId,
        private readonly UserId $organizerId,
        private readonly string $name,
        private readonly string $description,
        private readonly \DateTimeImmutable $scheduledFor,
        private readonly bool $wasCancelled = false,
    ) {
        Assert::notEq($name, '');
        Assert::notEq($description, '');
    }

    public static function schedule(
        MeetupId $meetupId,
        UserId $organizerId,
        string $name,
        string $description,
        \DateTimeImmutable $scheduledFor,
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
}
