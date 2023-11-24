<?php

namespace App;

interface Clock
{
    public function now(): \DateTimeInterface;
}
