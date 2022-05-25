<?php
declare(strict_types=1);

namespace App;

use DateTimeImmutable;

final class RealClock implements Clock
{
    public function currentTime(): DateTimeImmutable
    {
        return new DateTimeImmutable('now');
    }
}
