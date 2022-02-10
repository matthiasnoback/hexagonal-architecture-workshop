<?php
declare(strict_types=1);

namespace App;

final class ScheduleMeetup
{
    public function __construct(
        private readonly string $organizerId,
        private readonly string $name,
        private readonly string $description,
        private readonly string $dateAndTime,
    ) {
    }

    public function organizerId(): string
    {
        return $this->organizerId;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function dateAndTime(): string
    {
        return $this->dateAndTime;
    }
}
