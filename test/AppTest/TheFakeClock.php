<?php
declare(strict_types=1);

namespace AppTest;

use DateTimeImmutable;
use App\Time\Clock;

final class TheFakeClock implements Clock
{
    public function getCurrentTime(): DateTimeImmutable
    {
        if (!isset($_SERVER['HTTP_X_CURRENT_TIME'])) {
            throw new \RuntimeException('Call setServerTime() in your test');
        }

        return new DateTimeImmutable($_SERVER['HTTP_X_CURRENT_TIME']);
    }
}
