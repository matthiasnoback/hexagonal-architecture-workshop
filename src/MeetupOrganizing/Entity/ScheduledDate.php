<?php
declare(strict_types=1);

namespace MeetupOrganizing\Entity;

use Assert\Assert;
use DateTimeImmutable;

final class ScheduledDate
{
    const FORMAT = 'Y-m-d H:i';

    private function __construct(private readonly DateTimeImmutable $dateTimeImmutable)
    {
    }

    public static function fromString(string $string): self
    {
        $dateTimeImmutable = DateTimeImmutable::createFromFormat(
            self::FORMAT,
            $string
        );
        Assert::that($dateTimeImmutable)->isInstanceOf(DateTimeImmutable::class);

        return new self($dateTimeImmutable);
    }

    public function toString(): string
    {
        return $this->dateTimeImmutable->format(self::FORMAT);
    }
}
