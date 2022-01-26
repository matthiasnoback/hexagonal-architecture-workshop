<?php
declare(strict_types=1);

namespace Billing;

use Assert\Assert;
use DateTimeImmutable;

final class InvoicePeriod
{
    private function __construct(
        private readonly int $year,
        private readonly int $month,
    ) {
        Assert::that($this->year)->greaterThan(2000);
        Assert::that($this->month)->range(1, 12);
    }

    public static function createFromYearAndMonth(int $year, int $month): self
    {
        return new self($year, $month);
    }

    public function firstDayOfPeriod(): DateTimeImmutable
    {
        $firstDayOfMonth = DateTimeImmutable::createFromFormat(
            'Y-m-d', $this->year . '-' . $this->month . '-1',
            new \DateTimeZone('Europe/Amsterdam')
        );
        Assert::that($firstDayOfMonth)->isInstanceOf(DateTimeImmutable::class);

        return $firstDayOfMonth;
    }

    public function firstDayOfNextPeriod(): DateTimeImmutable
    {
        return $this->firstDayOfPeriod()->modify('first day of next month');
    }
}
