<?php

declare(strict_types=1);

namespace MeetupOrganizing\Entity;

use Assert\Assert;
use DateTimeImmutable;
use InvalidArgumentException;
use RuntimeException;
use Throwable;

final class ScheduledDate
{
    final public const DATE_TIME_FORMAT = 'Y-m-d H:i';

    private function __construct(
        private readonly string $dateTime
    ) {
    }

    public static function fromString(string $dateTime): self
    {
        try {
            $dateTimeImmutable = DateTimeImmutable::createFromFormat(self::DATE_TIME_FORMAT, $dateTime);
            if ($dateTimeImmutable === false) {
                throw new RuntimeException('The provided date/time string did not match the expected format');
            }
        } catch (Throwable $throwable) {
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid date/time format. Provided: %s, expected format: %s',
                    $dateTime,
                    self::DATE_TIME_FORMAT
                ),
                0,
                $throwable
            );
        }

        return self::fromDateTime($dateTimeImmutable);
    }

    public static function fromDateTime(DateTimeImmutable $dateTime): self
    {
        return new self($dateTime->format(self::DATE_TIME_FORMAT));
    }

    public function asString(): string
    {
        return $this->dateTime;
    }

    public function isInTheFuture(DateTimeImmutable $now): bool
    {
        return $now < $this->toDateTimeImmutable();
    }

    public function toDateTimeImmutable(): DateTimeImmutable
    {
        $dateTimeImmutable = DateTimeImmutable::createFromFormat(self::DATE_TIME_FORMAT, $this->dateTime);
        Assert::that($dateTimeImmutable)->isInstanceOf(DateTimeImmutable::class);

        return $dateTimeImmutable;
    }

    public function month(): int
    {
        return (int) $this->toDateTimeImmutable()->format('n');
    }

    public function year(): int
    {
        return (int) $this->toDateTimeImmutable()->format('Y');
    }
}
