<?php

namespace App;

class SystemClock implements Clock
{
    public function now(): \DateTimeInterface
    {
        return new \DateTimeImmutable('now');
    }
}
