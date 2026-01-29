<?php

namespace App;

use DateTimeImmutable;

class HttpHeaderClock implements ClockInterface
{
    public function now(): DateTimeImmutable
    {
        $now = $_SERVER['HTTP_X_CURRENT_TIME'] ?? 'now';

        return new DateTimeImmutable($now);
    }
}
