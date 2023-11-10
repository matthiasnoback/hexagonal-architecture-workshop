<?php

namespace MeetupOrganizing\Entity;

use DateTimeImmutable;

final class ScheduledDate
{
    private const FORMAT = 'Y-m-d H:i';

    private DateTimeImmutable $date;

    private function __construct(DateTimeImmutable $date)
    {
        $this->date = $date;
    }

    public static function createWithFormat(string $date): self
    {
        $dateTime = DateTimeImmutable::createFromFormat(
            self::FORMAT,
            $date
        );

        if ($dateTime === false) {
            throw new \InvalidArgumentException('Invalid date/time');
        }

        return new self($dateTime);
    }

    public function toString(): string
    {
        return $this->date->format(self::FORMAT);
    }

    public function isInThePast(): bool
    {
        return $this->date < new DateTimeImmutable('now');
    }
}
