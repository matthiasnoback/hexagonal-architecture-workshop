<?php
declare(strict_types=1);

namespace MeetupOrganizing\Entity;

use Assert\Assert;

final class Meetup
{
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

        $dt = \DateTimeImmutable::createFromFormat('Y-m-d H:i', $scheduledFor);
        Assert::that($dt)->isInstanceOf(\DateTimeImmutable::class);

        return new self($organizerId, $name, $description, $dt);
    }
}
