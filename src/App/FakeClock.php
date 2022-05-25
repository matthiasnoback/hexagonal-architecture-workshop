<?php
declare(strict_types=1);

namespace App;

use DateTimeImmutable;

final class FakeClock implements Clock
{
    public function currentTime(): DateTimeImmutable
    {
        return new DateTimeImmutable($_SERVER['HTTP_X_CURRENT_TIME'] ?? 'now');
    }
}
