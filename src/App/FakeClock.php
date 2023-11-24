<?php

namespace App;

class FakeClock implements Clock
{
    public function __construct(private readonly string $dateThatYouWant)
    {

    }

    public function now(): \DateTimeInterface
    {
        return new \DateTimeImmutable($this->dateThatYouWant);
    }
}
