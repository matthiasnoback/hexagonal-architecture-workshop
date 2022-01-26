<?php
declare(strict_types=1);

namespace Billing;

interface MeetupRepositoryInterface
{
    public function countMeetupsPerMonth(
        int $month,
        int $year,
        string $organizerId
    ): int;
}
