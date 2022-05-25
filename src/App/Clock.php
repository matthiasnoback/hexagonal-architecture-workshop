<?php
declare(strict_types=1);

namespace App;

use DateTimeImmutable;

interface Clock
{
    public function currentTime(): DateTimeImmutable;
}
