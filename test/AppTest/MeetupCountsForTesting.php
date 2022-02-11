<?php
declare(strict_types=1);

namespace AppTest;

use Billing\MeetupCounts;

final class MeetupCountsForTesting implements MeetupCounts
{
    private int $count = 0;

    public function setCount(int $count): void
    {
        $this->count = $count;
    }

    public function getTotalNumberOfMeetups(string $organizerId, int $year, int $month,): int
    {
        return $this->count;
    }
}
