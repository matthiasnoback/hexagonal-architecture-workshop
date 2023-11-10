<?php

namespace App;

class SystemClockProvider implements ClockProvider
{
    public function now(): \DateTimeImmutable
    {
        return new \DateTimeImmutable('now');
    }
}
