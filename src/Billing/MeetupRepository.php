<?php
declare(strict_types=1);

namespace Billing;

interface MeetupRepository
{
    public function getNumberOfMeetups(
        string $organizerId,
        int $year,
        int $month,
    ): int;
}
