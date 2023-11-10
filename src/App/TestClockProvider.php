<?php

namespace App;

use DateTimeImmutable;

class TestClockProvider implements ClockProvider
{
    public function now(): DateTimeImmutable
    {
        return new DateTimeImmutable($_SERVER['HTTP_X_CURRENT_TIME'] ?? 'now');
    }
}
