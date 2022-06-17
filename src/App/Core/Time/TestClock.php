<?php
declare(strict_types=1);

namespace App\Core\Time;

use DateTimeImmutable;
use DateTimeInterface;

final class TestClock implements Clock
{
    public function whatIsTheTime(): DateTimeInterface
    {
        if (!isset($_SERVER['HTTP_X_CURRENT_TIME'])) {
            throw new \RuntimeException('Call setServerTime() first');
        }

        return new DateTimeImmutable($_SERVER['HTTP_X_CURRENT_TIME']);
    }
}
