<?php
declare(strict_types=1);

namespace App;

use DateTimeImmutable;

final class FakeClock implements Clock
{
    public function now(): DateTimeImmutable
    {
        if (!isset($_SERVER['HTTP_X_CURRENT_TIME'])) {
            throw new \RuntimeException('Call setServerTime() first');
        }

        return new DateTimeImmutable($_SERVER['HTTP_X_CURRENT_TIME']);
    }
}
