<?php
declare(strict_types=1);

namespace Billing;

use DateTimeImmutable;

interface MeetupCounts
{
    public function getTotalNumberOfMeetups(
        string $organizerId,
        DateTimeImmutable $startDate,
        DateTimeImmutable $endDate
    ): int;
}
