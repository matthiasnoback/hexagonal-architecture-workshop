<?php
declare(strict_types=1);

namespace MeetupOrganizing\Entity;

use Assert\Assert;
use DateTimeImmutable;

final class ScheduledDate
{
    private const FORMAT = 'Y-m-d H:i';

    private function __construct(private readonly \DateTimeImmutable $dateTime)
    {
    }

    public static function fromString(string $dateTime): self
    {
        $dt = DateTimeImmutable::createFromFormat(self::FORMAT, $dateTime);

        Assert::that($dt)->isInstanceOf(DateTimeImmutable::class);

        return new self($dt);
    }

    public function asString(): string
    {
        return $this->dateTime->format(self::FORMAT);
    }

    public function isInThePast(DateTimeImmutable $now): bool
    {
        return $this->dateTime < $now;
    }
}
