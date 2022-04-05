<?php
declare(strict_types=1);

namespace Billing;

interface Meetups
{
    public function organizedInPeriod(
        string $organizerId,
        int $year,
        int $month
    ): int;
}
