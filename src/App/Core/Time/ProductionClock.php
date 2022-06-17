<?php
declare(strict_types=1);

namespace App\Core\Time;

use DateTimeImmutable;
use DateTimeInterface;

final class ProductionClock implements Clock
{
    public function whatIsTheTime(): DateTimeInterface
    {
        return new DateTimeImmutable('now');
    }
}
