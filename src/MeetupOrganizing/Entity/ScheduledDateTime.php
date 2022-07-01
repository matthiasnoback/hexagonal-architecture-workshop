<?php
declare(strict_types=1);

namespace MeetupOrganizing\Entity;

use Assert\Assertion;
use DateTimeImmutable;

final class ScheduledDateTime
{
    public const DATE_TIME_FORMAT = 'Y-m-d H:i';

    private function __construct(private readonly DateTimeImmutable $dateTimeImmutable)
    {
    }

    public static function fromString(string $dateTime): self
    {
        $dateTimeImmutable = DateTimeImmutable::createFromFormat(
            self::DATE_TIME_FORMAT,
            $dateTime
        );
        Assertion::isInstanceOf($dateTimeImmutable, DateTimeImmutable::class);

        return new self($dateTimeImmutable);
    }

    public function toString(): string
    {
        return $this->dateTimeImmutable->format(self::DATE_TIME_FORMAT);
    }

    public function isInThePast(): bool
    {
        return $this->dateTimeImmutable < new DateTimeImmutable('now');
    }

    public function isInTheFuture(): bool
    {
        return ! $this->isInThePast();
    }
}
