<?php
declare(strict_types=1);

namespace Billing;

interface Meetups
{
    public function countScheduledMeetupsFor(
        int $year,
        int $month,
        string $organizerId
    ): int;
}
