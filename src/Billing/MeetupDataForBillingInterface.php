<?php
declare(strict_types=1);

namespace Billing;

interface MeetupDataForBillingInterface
{
    public function getNumberOfMeetups(
        int $year,
        int $month,
        string $organizerId,
    ): int;
}
