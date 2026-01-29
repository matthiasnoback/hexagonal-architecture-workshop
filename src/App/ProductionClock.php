<?php

namespace App;

use DateTimeImmutable;

class ProductionClock implements ClockInterface
{
    public function now(): DateTimeImmutable
    {
        return new DateTimeImmutable();
    }
}
