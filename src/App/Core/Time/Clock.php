<?php
declare(strict_types=1);

namespace App\Core\Time;

use DateTimeInterface;

interface Clock
{
    public function whatIsTheTime(): DateTimeInterface;
}
