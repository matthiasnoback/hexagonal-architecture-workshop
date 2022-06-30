<?php
declare(strict_types=1);

namespace App;

interface CurrentTimeAccessor
{
    public function getCurrentTime(): \DateTimeImmutable;
}
