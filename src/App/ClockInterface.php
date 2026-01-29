<?php

namespace App;

use DateTimeImmutable;

interface ClockInterface
{
    public function now(): DateTimeImmutable;
}
