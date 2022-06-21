<?php
declare(strict_types=1);

namespace MeetupOrganizing\Domain\Model\Meetup;

use DateTimeImmutable;

final class ScheduledDate
{
    private function __construct(public readonly DateTimeImmutable $dateTime)
    {
    }

    public static function fromString(string $dateTime): self
    {
        $dateTimeImmutable = DateTimeImmutable::createFromFormat(
            'Y-m-d H:i',
            $dateTime
        );

        if ($dateTimeImmutable === false) {
            throw new \InvalidArgumentException('Invalid date/time');
        }

        return new self($dateTimeImmutable);
    }

    public function equals(self $other): bool
    {
        return $this->dateTime->getTimestamp() === $other->dateTime->getTimestamp();
    }

    public function asString(): string
    {
        return $this->dateTime->format('Y-m-d H:i');
    }

    public function isBefore(DateTimeImmutable $otherTime): bool
    {
        return $this->dateTime < $otherTime;
    }
}
