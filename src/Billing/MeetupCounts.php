<?php
declare(strict_types=1);

namespace Billing;

interface MeetupCounts
{
    public function getTotalNumberOfMeetups(
        string $organizerId,
        int $year,
        int $month,
    ): int;
}
