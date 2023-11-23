<?php

namespace App;

/**
 * DTO Data Transfer Object
 */
class ScheduleMeeting
{
    public function __construct(
        private string $organizerId,
        private string $name,
        private string $description,
        private string $scheduledFor
    )
    {
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

    public function scheduledFor(): string
    {
        return $this->scheduledFor;
    }
}
