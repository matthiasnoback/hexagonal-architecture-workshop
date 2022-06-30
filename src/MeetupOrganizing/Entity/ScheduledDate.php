<?php
declare(strict_types=1);

namespace MeetupOrganizing\Entity;

use Assert\Assert;
use DateTimeImmutable;

final class ScheduledDate
{
    private const FORMAT = 'Y-m-d H:i';

    private function __construct(
        private readonly DateTimeImmutable $dateTimeImmutable
    )
    {
    }

    public static function fromString(string $date): self
    {
        $scheduledDate = DateTimeImmutable::createFromFormat(self::FORMAT, $date);
        Assert::that($scheduledDate)->isInstanceOf(DateTimeImmutable::class);

        return new self($scheduledDate);
    }

    public function toString(): string
    {
        return $this->dateTimeImmutable->format(self::FORMAT);
    }

    public function isBefore(DateTimeImmutable $otherDateTime): bool
    {
        return $this->dateTimeImmutable < $otherDateTime;
    }
}
