<?php
declare(strict_types=1);

namespace App;

use Assert\Assert;
use MeetupOrganizing\Entity\ScheduledDate;

final class ListUpcomingMeetups
{
    public function __construct(
        private readonly string $date
    )
    {
    }

    public function date(): \DateTimeImmutable
    {
        $dateTimeImmutable = \DateTimeImmutable::createFromFormat(
            ScheduledDate::DATE_TIME_FORMAT,
            $this->date
        );
        Assert::that($dateTimeImmutable)->isInstanceOf(\DateTimeImmutable::class);
        
        return $dateTimeImmutable;
    }
}
