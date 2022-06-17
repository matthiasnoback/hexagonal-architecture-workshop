<?php
declare(strict_types=1);

namespace MeetupOrganizing\Entity;

use DateTimeImmutable;
use InvalidArgumentException;

final class ScheduledDate
{
    private const FORMAT = 'Y-m-d H:i';

    private function __construct(private readonly DateTimeImmutable $dateTimeImmutable)
    {
    }

    public static function fromString(string $dateTime): self
    {
        $scheduledFor = DateTimeImmutable::createFromFormat(
            self::FORMAT,
            $dateTime
        );
        if ($scheduledFor === false) {
            throw new InvalidArgumentException('Sorry, could not create DateTimeImmutable from $dateTime');
        }

        return new self($scheduledFor);
    }

    public function asString(): string
    {
        return $this->dateTimeImmutable->format(self::FORMAT);
    }

    public function hasAlreadyPassed(): bool
    {
        return $this->dateTimeImmutable < (new DateTimeImmutable('now'));
    }
}
