<?php
declare(strict_types=1);

namespace AppTest\Integration;

use Billing\BillableMeetups;
use PHPUnit\Framework\Assert;

final class BillableMeetupsForTest implements BillableMeetups
{
    private int $count;
    private string $expectedOrganizerId;
    private int $expectedYear;
    private int $expectedMonth;

    public function howManyBillableMeetupsDoesThisOrganizerHaveInTheGivenMonth(
        string $organizerId,
        int $year,
        int $month,
    ): int {
        Assert::assertEquals($this->expectedOrganizerId, $organizerId);
        Assert::assertEquals($this->expectedYear, $year);
        Assert::assertEquals($this->expectedMonth, $month);

        return $this->count;
    }

    public function setCount(string $organizerId, int $year,
        int $month, int $count): void
    {
        $this->expectedOrganizerId = $organizerId;
        $this->expectedYear = $year;
        $this->expectedMonth = $month;
        $this->count = $count;
    }
}
