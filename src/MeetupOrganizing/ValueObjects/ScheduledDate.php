<?php
declare(strict_types=1);

namespace MeetupOrganizing\ValueObjects;

use App\Clock;
use Assert\Assertion;
use DateTimeImmutable;

final class ScheduledDate
{
    private const FORMAT = 'Y-m-d H:i';

    private function __construct(private readonly DateTimeImmutable $dateTimeImmutable)
    {
    }

    public static function create(string $dateTime): self
    {
        $scheduledDateTime = DateTimeImmutable::createFromFormat(
            self::FORMAT,
            $dateTime
        );
        Assertion::isInstanceOf($scheduledDateTime, DateTimeImmutable::class);

        return new self($scheduledDateTime);
    }

    public function asString(): string
    {
        return $this->dateTimeImmutable->format(self::FORMAT);
    }

    public function inThePast(Clock $clock): bool
    {
        return $this->dateTimeImmutable < $clock->now();
    }
}
