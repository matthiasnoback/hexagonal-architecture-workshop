<?php
declare(strict_types=1);

namespace Billing;

use PHPUnit\Framework\Assert;

final class MeetupRepositoryForTesting implements MeetupRepository
{
    private string $organizerId;
    private int $year;
    private int $month;
    private int $return;

    public function mockNumberOfMeetups(string $organizerId, int $year, int $month, int $return): void
    {
        $this->organizerId = $organizerId;
        $this->year = $year;
        $this->month = $month;
        $this->return = $return;
    }

    public function getNumberOfMeetups(string $organizerId, int $year, int $month): int
    {
        Assert::assertSame(
            [$this->organizerId, $this->year, $this->month],
            func_get_args(),
            'Wrongly set meetup arguments'
        );

        return $this->return;
    }
}
