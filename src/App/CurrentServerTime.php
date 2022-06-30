<?php
declare(strict_types=1);

namespace App;

use DateTimeImmutable;

final class CurrentServerTime implements CurrentTimeAccessor
{
    public function getCurrentTime(): DateTimeImmutable
    {
        return new DateTimeImmutable('now');
    }
}
