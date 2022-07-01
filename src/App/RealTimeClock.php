<?php
declare(strict_types=1);

namespace App;

use DateTimeImmutable;

final class RealTimeClock implements Clock
{
    public function getCurrentTime(): DateTimeImmutable
    {
        return new DateTimeImmutable('now');
    }
}
