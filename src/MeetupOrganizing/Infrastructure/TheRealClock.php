<?php
declare(strict_types=1);

namespace MeetupOrganizing\Infrastructure;

use App\Time\Clock;
use DateTimeImmutable;

final class TheRealClock implements Clock
{
    public function getCurrentTime(): DateTimeImmutable
    {
        return new DateTimeImmutable('now');
    }
}
