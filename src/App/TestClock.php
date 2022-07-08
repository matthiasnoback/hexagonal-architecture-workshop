<?php
declare(strict_types=1);

namespace App;

use DateTimeImmutable;

final class TestClock implements Clock
{
    public function __construct(private DateTimeImmutable $now)
    {
    }

    public function now(): DateTimeImmutable
    {
        return $this->now;
    }
}
