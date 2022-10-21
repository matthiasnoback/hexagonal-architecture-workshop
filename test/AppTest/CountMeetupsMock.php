<?php
declare(strict_types=1);

namespace AppTest;

use Billing\CountMeetups;
use PHPUnit\Framework\Assert;

final class CountMeetupsMock implements CountMeetups
{
    private int $year;
    private int $month;
    private string $organizerId;
    private int $count;

    public function forOrganizer(int $year, int $month, string $organizerId): int
    {
        Assert::assertSame($this->year, $year);
        Assert::assertSame($this->month, $month);
        Assert::assertSame($this->organizerId, $organizerId);

        return $this->count;
    }

    public function countMethodShouldReturn(int $year, int $month, string $organizerId, int $count): void
    {
        $this->year = $year;
        $this->month = $month;
        $this->organizerId = $organizerId;
        $this->count = $count;
    }
}
