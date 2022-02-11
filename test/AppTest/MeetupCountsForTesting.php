<?php
declare(strict_types=1);

namespace AppTest;

use Billing\MeetupCounts;
use PHPUnit\Framework\Assert;

final class MeetupCountsForTesting implements MeetupCounts
{
    private int $willReturnCount;
    private int $expectedYear;
    private int $expectedMonth;
    private string $expectedOrganizerId;

    public function setExpectationsForGetTotalNumberOfMeetups(string $organizerId, int $year, int $month, int $count): void
    {
        $this->expectedOrganizerId = $organizerId;
        $this->expectedYear = $year;
        $this->expectedMonth = $month;
        $this->willReturnCount = $count;
    }

    public function getTotalNumberOfMeetups(string $organizerId, int $year, int $month,): int
    {
        Assert::assertEquals($this->expectedOrganizerId, $organizerId);
        Assert::assertEquals($this->expectedYear, $year);
        Assert::assertEquals($this->expectedMonth, $month);

        return $this->willReturnCount;
    }
}
