<?php

namespace App;

interface ClockProvider
{
    public function now(): \DateTimeImmutable;
}
