<?php
declare(strict_types=1);

namespace App;

use DateTimeImmutable;

final class TestClock implements Clock
{
    public function __construct(private DateTimeImmutable $currentTime)
    {
    }
    public function getCurrentTime(): DateTimeImmutable
    {
        return $this->currentTime;
    }

    public function setCurrentTime(DateTimeImmutable $currentTime): void
    {
        $this->currentTime = $currentTime;
    }

}
