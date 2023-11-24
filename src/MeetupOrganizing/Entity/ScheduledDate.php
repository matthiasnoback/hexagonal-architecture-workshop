<?php

namespace MeetupOrganizing\Entity;

use DateTimeImmutable;

final class ScheduledDate
{
    private DateTimeImmutable $dateTime;

    private function __construct(DateTimeImmutable $dateTime)
    {
        $this->dateTime = $dateTime;
    }

    public static function fromString(string $dateTime): self
    {
        $dateTime = DateTimeImmutable::createFromFormat('Y-m-d H:i', $dateTime);
        if ($dateTime === false) {
            throw new \InvalidArgumentException('Date/time uses invalid format');
        }

        return new self($dateTime);
    }

    public function toString(): string
    {
        return $this->dateTime->format('Y-m-d H:i');
    }
}
