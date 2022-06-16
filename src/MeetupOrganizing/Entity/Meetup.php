<?php
declare(strict_types=1);

namespace MeetupOrganizing\Entity;

use App\Entity\UserId;
use DateTimeImmutable;
use InvalidArgumentException;

final class Meetup
{
    private function __construct(
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
        UserId $organizerId,
        string $name,
        string $description,
        DateTimeImmutable $scheduledFor,
    ): self {
        // TODO validate scheduledFor; should be in the future

        return new self(
            $organizerId,
            $name,
            $description,
            $scheduledFor,
        );
    }
}
