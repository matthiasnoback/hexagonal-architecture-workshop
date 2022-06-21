<?php
declare(strict_types=1);

namespace App\Time;

use DateTimeImmutable;

interface Clock
{
    public function getCurrentTime(): DateTimeImmutable;
}
