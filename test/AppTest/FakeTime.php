<?php
declare(strict_types=1);

namespace AppTest;

use App\CurrentTimeAccessor;

final class FakeTime implements CurrentTimeAccessor
{
    private \DateTimeImmutable $currentTime;

    public function __construct(string $dateTimeImmutable)
    {
        $this->currentTime = new \DateTimeImmutable($dateTimeImmutable);
    }

    public function getCurrentTime(): \DateTimeImmutable
    {
        return $this->currentTime;
    }
}
